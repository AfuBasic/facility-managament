{{-- Pending Files (selected but not uploaded to Cloudinary yet) --}}
@if(count($photos) > 0 && !$isUploading)
    <div class="mb-3">
        <p class="text-sm text-slate-600 mb-2">
            <span class="font-medium">{{ count($photos) }} file(s) selected</span> - will upload to Cloudinary when you save
        </p>
        <div class="grid grid-cols-5 gap-2">
            @foreach($photos as $index => $photo)
                <div class="relative group">
                    <img src="{{ $photo->temporaryUrl() }}" class="w-full h-24 object-cover rounded-lg border-2 border-amber-400">
                    <span class="absolute bottom-1 left-1 bg-amber-500 text-white text-xs px-1.5 py-0.5 rounded">Pending</span>
                    <div class="absolute top-1 right-1 bg-slate-800 bg-opacity-75 text-white text-xs px-1.5 py-0.5 rounded">
                        {{ number_format($photo->getSize() / 1024, 1) }} KB
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- Uploading to Cloudinary Progress --}}
@if($isUploading && count($photos) > 0)
    <div class="mb-3">
        <p class="text-sm font-medium text-blue-600 mb-2 flex items-center gap-2">
            <x-heroicon-o-arrow-path class="animate-spin h-4 w-4" />
            Uploading to Cloudinary...
        </p>
        <div class="grid grid-cols-5 gap-2">
            @foreach($photos as $index => $photo)
                @php
                    $status = $uploadProgress[$index] ?? 'pending';
                @endphp
                <div class="relative group">
                    <img src="{{ $photo->temporaryUrl() }}" class="w-full h-24 object-cover rounded-lg border-2 
                        {{ $status === 'uploading' ? 'border-blue-400' : '' }}
                        {{ $status === 'completed' ? 'border-green-400' : '' }}
                        {{ $status === 'failed' ? 'border-red-400' : 'border-slate-300' }}">
                    
                    @if($status === 'uploading')
                        <div class="absolute inset-0 bg-blue-500 bg-opacity-75 flex flex-col items-center justify-center rounded-lg">
                            <x-heroicon-o-arrow-path class="animate-spin h-6 w-6 text-white mb-1" />
                            <span class="text-white text-xs font-medium">Uploading...</span>
                        </div>
                    @elseif($status === 'completed')
                        <div class="absolute inset-0 bg-green-500 bg-opacity-20 flex items-center justify-center rounded-lg">
                            <x-heroicon-o-check-circle class="h-8 w-8 text-green-600" />
                        </div>
                    @elseif($status === 'failed')
                        <div class="absolute inset-0 bg-red-500 bg-opacity-75 flex flex-col items-center justify-center rounded-lg">
                            <x-heroicon-o-exclamation-circle class="h-6 w-6 text-white mb-1" />
                            <span class="text-white text-xs font-medium">Failed</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif
