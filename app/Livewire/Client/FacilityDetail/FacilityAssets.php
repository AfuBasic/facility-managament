<?php

namespace App\Livewire\Client\FacilityDetail;

use App\Livewire\Concerns\WithNotifications;
use App\Models\Asset;
use App\Models\AssetImage;
use App\Models\ClientAccount;
use App\Models\ClientMembership;
use App\Models\Contact;
use App\Models\Facility;
use App\Models\Space;
use App\Models\Store;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class FacilityAssets extends Component
{
    use WithFileUploads, WithNotifications;

    public Facility $facility;

    public ClientAccount $clientAccount;

    // Filtering and search
    public $filterType = 'all'; // all, fixed, tools, consumable

    public $search = '';

    // Asset form fields
    public $showAssetModal = false;

    public $isEditingAsset = false;

    public $editingAssetId = null;

    public $assetName = '';

    public $assetSerial = '';

    public $assetType = 'fixed';

    public $assetUnits = 1;

    public $assetMinimum = 0;

    public $assetMaximum = 0;

    public $assetDescription = '';

    public $assetNotes = '';

    public $assetStoreId = null;

    public $assetUserId = null;

    public $assetSupplierContactId = null;

    public $assetSpaceId = null;

    public $assetPurchasedAt = null;

    // Image upload
    public $photos = [];

    public $uploadedImages = [];

    public $existingImages = [];

    public $isUploading = false;

    public $uploadProgress = [];

    protected $rules = [
        'assetName' => 'required|string|max:255',
        'assetSerial' => 'required|string|max:255',
        'assetType' => 'required|in:fixed,tools,consumable',
        'assetUnits' => 'required|integer|min:0',
        'assetMinimum' => 'required|integer|min:0',
        'assetMaximum' => 'required|integer|min:0',
        'assetDescription' => 'nullable|string',
        'assetNotes' => 'nullable|string',
        'assetStoreId' => 'nullable|exists:stores,id',
        'assetUserId' => 'nullable|exists:users,id',
        'assetSupplierContactId' => 'nullable|exists:contacts,id',
        'assetSpaceId' => 'nullable|exists:spaces,id',
        'assetPurchasedAt' => 'nullable|date',
        'photos.*' => 'nullable|image|max:2048', // max 2MB
    ];

    public function hydrate()
    {
        if ($this->clientAccount) {
            setPermissionsTeamId($this->clientAccount->id);
        }
    }

    public function mount()
    {
        if (! $this->clientAccount) {
            $this->clientAccount = app(ClientAccount::class);
        }
        setPermissionsTeamId($this->clientAccount->id);
    }

    public function getAssetsProperty()
    {
        $query = Asset::with(['store', 'user', 'supplierContact', 'space', 'images'])
            ->where('facility_id', $this->facility->id)
            ->where('client_account_id', $this->clientAccount->id);

        // Filter by type
        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('serial', 'like', "%{$this->search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getAvailableStoresProperty()
    {
        return Store::where('facility_id', $this->facility->id)
            ->where('client_account_id', $this->clientAccount->id)
            ->where('status', 'active')
            ->get();
    }

    public function getAvailableUsersProperty()
    {
        return ClientMembership::with('user')
            ->where('client_account_id', $this->clientAccount->id)
            ->get();
    }

    public function getAvailableContactsProperty()
    {
        return Contact::where('client_account_id', $this->clientAccount->id)
            ->where('contact_type_id', '!=', null) // Only contacts with types
            ->get();
    }

    public function getAvailableSpacesProperty()
    {
        return Space::where('facility_id', $this->facility->id)
            ->get();
    }

    public function createAsset()
    {
        $this->authorize('create assets');
        $this->resetAssetForm();
        $this->showAssetModal = true;
    }

    public function editAsset($id)
    {
        $this->authorize('edit assets');

        $asset = Asset::with('images')->where('facility_id', $this->facility->id)->findOrFail($id);

        $this->editingAssetId = $asset->id;
        $this->assetName = $asset->name;
        $this->assetSerial = $asset->serial;
        $this->assetType = $asset->type;
        $this->assetUnits = $asset->units;
        $this->assetMinimum = $asset->minimum;
        $this->assetMaximum = $asset->maximum;
        $this->assetDescription = $asset->description ?? '';
        $this->assetNotes = $asset->notes ?? '';
        $this->assetStoreId = $asset->store_id;
        $this->assetUserId = $asset->user_id;
        $this->assetSupplierContactId = $asset->supplier_contact_id;
        $this->assetSpaceId = $asset->space_id;
        $this->assetPurchasedAt = $asset->purchased_at?->format('Y-m-d');

        // Load existing images
        $this->existingImages = $asset->images->map(function ($image) {
            return [
                'id' => $image->id,
                'url' => $image->image,
            ];
        })->toArray();

        $this->isEditingAsset = true;
        $this->showAssetModal = true;
    }

    public function updatedPhotos()
    {
        $this->validate([
            'photos.*' => 'image|max:2048',
        ]);

        // Check total images (existing + new) doesn't exceed 5
        $totalImages = count($this->existingImages) + count($this->photos);
        if ($totalImages > 5) {
            $this->error('Maximum 5 images allowed per asset.');
            $this->photos = [];

            return;
        }

        $this->uploadImages();
    }

    private function uploadImages()
    {
        if (empty($this->photos)) {
            return;
        }

        $this->isUploading = true;
        $imageService = app(ImageUploadService::class);

        foreach ($this->photos as $index => $photo) {
            try {
                $this->uploadProgress[$index] = 'uploading';
                // Upload with deduplication
                $result = $imageService->uploadWithCache($photo);

                $this->uploadedImages[] = [
                    'url' => $result['secure_url'],
                    'public_id' => $result['public_id'],
                    'cached' => $result['cached'] ?? false,
                ];

                $this->uploadProgress[$index] = 'completed';
            } catch (\Exception $e) {
                Log::error('Image upload failed: '.$e->getMessage());
                $this->uploadProgress[$index] = 'failed';
                $this->error('Failed to upload image: '.$photo->getClientOriginalName());
            }
        }

        $this->isUploading = false;
        $this->photos = [];
    }

    public function removeUploadedImage($index)
    {
        if (isset($this->uploadedImages[$index])) {
            unset($this->uploadedImages[$index]);
            $this->uploadedImages = array_values($this->uploadedImages);
        }
    }

    public function deleteExistingImage($imageId)
    {
        $this->authorize('edit assets');

        $image = AssetImage::findOrFail($imageId);

        // Delete from Cloudinary (with usage tracking)
        $imageService = app(ImageUploadService::class);
        $imageService->deleteWithCache($image->public_id ?? '');

        // Delete from database
        $image->delete();

        // Remove from existingImages array
        $this->existingImages = array_filter($this->existingImages, function ($img) use ($imageId) {
            return $img['id'] !== $imageId;
        });
        $this->existingImages = array_values($this->existingImages);

        $this->success('Image deleted successfully.');
    }

    public function saveAsset()
    {
        $this->validate();

        // Check for duplicate serial within client account
        $duplicateQuery = Asset::where('client_account_id', $this->clientAccount->id)
            ->where('serial', $this->assetSerial);

        if ($this->isEditingAsset) {
            $duplicateQuery->where('id', '!=', $this->editingAssetId);
        }

        if ($duplicateQuery->exists()) {
            $this->error('An asset with this serial number already exists.');

            return;
        }

        DB::beginTransaction();

        try {
            if ($this->isEditingAsset) {
                $this->authorize('edit assets');
                $asset = Asset::where('facility_id', $this->facility->id)->findOrFail($this->editingAssetId);
                $asset->update($this->getAssetData());
                $message = 'Asset updated successfully!';
            } else {
                $this->authorize('create assets');
                $asset = Asset::create(array_merge($this->getAssetData(), [
                    'facility_id' => $this->facility->id,
                    'client_account_id' => $this->clientAccount->id,
                ]));
                $message = 'Asset created successfully!';
            }

            // Save uploaded images
            foreach ($this->uploadedImages as $imageData) {
                AssetImage::create([
                    'asset_id' => $asset->id,
                    'image' => $imageData['url'],
                ]);
            }

            DB::commit();
            $this->success($message);
            $this->closeAssetModal();
            $this->facility->load('assets');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Asset save failed: '.$e->getMessage());
            $this->error('Failed to save asset. Please try again.');
        }
    }

    private function getAssetData(): array
    {
        return [
            'name' => $this->assetName,
            'serial' => $this->assetSerial,
            'type' => $this->assetType,
            'units' => $this->assetUnits,
            'minimum' => $this->assetMinimum,
            'maximum' => $this->assetMaximum,
            'description' => $this->assetDescription,
            'notes' => $this->assetNotes,
            'store_id' => $this->assetStoreId,
            'user_id' => $this->assetUserId,
            'supplier_contact_id' => $this->assetSupplierContactId,
            'space_id' => $this->assetSpaceId,
            'purchased_at' => $this->assetPurchasedAt,
        ];
    }

    public function deleteAsset($id)
    {
        $this->authorize('delete assets');

        DB::beginTransaction();

        try {
            $asset = Asset::with('images')->where('facility_id', $this->facility->id)->findOrFail($id);

            // Delete images from Cloudinary
            $imageService = app(ImageUploadService::class);
            foreach ($asset->images as $image) {
                $imageService->deleteWithCache($image->public_id ?? '');
                $image->delete();
            }

            // Delete asset
            $asset->delete();

            DB::commit();
            $this->success('Asset deleted successfully.');
            $this->facility->load('assets');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Asset deletion failed: '.$e->getMessage());
            $this->error('Failed to delete asset. Please try again.');
        }
    }

    public function closeAssetModal()
    {
        $this->showAssetModal = false;
        $this->resetAssetForm();
    }

    private function resetAssetForm()
    {
        $this->assetName = '';
        $this->assetSerial = '';
        $this->assetType = 'fixed';
        $this->assetUnits = 1;
        $this->assetMinimum = 0;
        $this->assetMaximum = 0;
        $this->assetDescription = '';
        $this->assetNotes = '';
        $this->assetStoreId = null;
        $this->assetUserId = null;
        $this->assetSupplierContactId = null;
        $this->assetSpaceId = null;
        $this->assetPurchasedAt = null;
        $this->isEditingAsset = false;
        $this->editingAssetId = null;
        $this->photos = [];
        $this->uploadedImages = [];
        $this->existingImages = [];
        $this->uploadProgress = [];
        $this->isUploading = false;
    }

    public function render()
    {
        return view('livewire.client.facility-detail.facility-assets');
    }
}
