<?php

namespace App\Livewire\Client\StoreDetail;

use App\Livewire\Concerns\WithNotifications;
use App\Models\Asset;
use App\Models\AssetImage;
use App\Models\ClientAccount;
use App\Models\ClientMembership;
use App\Models\Contact;
use App\Models\Space;
use App\Models\Store;
use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class StoreAssets extends Component
{
    use WithFileUploads, WithNotifications;

    public Store $store;

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

    public $assetSupplierContactId = null;

    public $assetSpaceId = null;

    public $assetPurchasedAt = null;

    // Image upload
    public $newPhotos = []; // For the file input

    public $photos = [];    // For accumulating selected photos

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
        'assetSupplierContactId' => 'nullable|exists:contacts,id',
        'assetSpaceId' => 'nullable|exists:spaces,id',
        'assetPurchasedAt' => 'required|date',
        'newPhotos.*' => 'nullable|image|max:2048', // max 2MB
    ];

    public function hydrate()
    {
        if ($this->clientAccount) {
            setPermissionsTeamId($this->clientAccount->id);
        }
    }

    #[On('refresh-asset-list')]
    public function refreshList()
    {
        // Triggers re-render
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
            ->where('store_id', $this->store->id)
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

    public function getAvailableUsersProperty()
    {
        return ClientMembership::with('user')
            ->where('client_account_id', $this->clientAccount->id)
            ->get();
    }

    public function getAvailableContactsProperty()
    {
        return Contact::with('contactType')->where('client_account_id', $this->clientAccount->id)
            ->where('contact_type_id', '!=', null) // Only contacts with types
            ->get();
    }

    public function getAvailableSpacesProperty()
    {
        return Space::where('facility_id', $this->store->facility_id)
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

        $asset = Asset::with('images')->where('store_id', $this->store->id)->findOrFail($id);

        $this->editingAssetId = $asset->id;
        $this->assetName = $asset->name;
        $this->assetSerial = $asset->serial;
        $this->assetType = $asset->type;
        $this->assetUnits = $asset->units;
        $this->assetMinimum = $asset->minimum;
        $this->assetMaximum = $asset->maximum;
        $this->assetDescription = $asset->description ?? '';
        $this->assetNotes = $asset->notes ?? '';
        // $this->assetUserId = $asset->user_id; // Auto-assigned on save
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

    public function updatedNewPhotos()
    {
        $this->validate([
            'newPhotos.*' => 'image|max:2048',
        ]);

        // Merge new photos into the pending photos list
        if (! empty($this->newPhotos)) {
            foreach ($this->newPhotos as $photo) {
                $this->photos[] = $photo;
            }
        }

        // Check total images (existing + pending) doesn't exceed 5
        $totalImages = count($this->existingImages) + count($this->photos);
        if ($totalImages > 5) {
            $this->error('Maximum 5 images allowed per asset.');
            // Remove the excess photos from the end
            $excess = $totalImages - 5;
            array_splice($this->photos, -$excess);
        }

        // Reset the input so user can select more
        $this->newPhotos = [];
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

                // Upload with deduplication (preset handles folder structure)
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
        $this->uploadImages(); // Ensure images are uploaded
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
                $asset = Asset::where('store_id', $this->store->id)->findOrFail($this->editingAssetId);
                $asset->update($this->getAssetData());
                $message = 'Asset updated successfully!';
            } else {
                $this->authorize('create assets');
                $asset = Asset::create(array_merge($this->getAssetData(), [
                    'facility_id' => $this->store->facility_id,
                    'store_id' => $this->store->id,
                    'client_account_id' => $this->clientAccount->id,
                    // 'user_id' handled by getAssetData now
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
            $this->store->load('assets');
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
            'user_id' => Auth::id(), // Always assign to current user
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
            $asset = Asset::with('images')->where('store_id', $this->store->id)->findOrFail($id);

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
            $this->store->load('assets');
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
        // $this->assetUserId = null;
        $this->assetSupplierContactId = null;
        $this->assetSpaceId = null;
        $this->assetPurchasedAt = null;
        $this->isEditingAsset = false;
        $this->editingAssetId = null;
        $this->photos = [];
        $this->newPhotos = [];
        $this->uploadedImages = [];
        $this->existingImages = [];
        $this->uploadProgress = [];
        $this->isUploading = false;
    }

    public function render()
    {
        return view('livewire.client.store-detail.store-assets');
    }
}
