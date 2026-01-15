@props([
    'options' => [],
    'selected' => [],
    'placeholder' => 'Select options...',
    'label' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
    'id' => 'multiselect-' . uniqid(),
])

@php
    $wireModel = $attributes->wire('model')->value();
@endphp

<div>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-slate-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-400">*</span>
            @endif
        </label>
    @endif
    
    <div 
        wire:ignore
        x-data="{
            tomSelect: null,
            init() {
                if (typeof TomSelect === 'undefined') {
                    console.error('TomSelect is not loaded');
                    return;
                }
                
                this.tomSelect = new TomSelect($refs.select, {
                    plugins: ['remove_button'],
                    placeholder: '{{ $placeholder }}',
                    searchField: ['text'],
                    maxOptions: null,
                    onChange: (values) => {
                        // Send array directly to Livewire
                        $wire.set('{{ $wireModel }}', values);
                    },
                    controlClass: 'ts-control',
                    dropdownClass: 'ts-dropdown'
                });
            }
        }"
    >
        <select 
            x-ref="select"
            id="{{ $id }}"
            multiple
            {{ $attributes->whereDoesntStartWith('wire:model') }}
            @if($disabled) disabled @endif
            class="w-full"
        >
            @foreach($options as $value => $text)
                <option value="{{ $value }}" @if(in_array($value, (array) $selected)) selected @endif>
                    {{ $text }}
                </option>
            @endforeach
        </select>
    </div>
    
    @if($error)
        <p class="mt-1 text-sm text-red-400">{{ $error }}</p>
    @endif
</div>

<style>
    /* Multi-select specific styling */
    .ts-wrapper.multi .ts-control {
        background-color: white !important;
        border: 1px solid rgb(203, 213, 225) !important;
        border-radius: 0.5rem !important;
        padding: 0.5rem !important;
        font-size: 0.875rem !important;
        min-height: 42px !important;
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
    }
    
    .ts-wrapper.multi .ts-control .item {
        background-color: rgb(204, 251, 241) !important;
        color: rgb(19, 78, 74) !important;
        border: 1px solid rgb(153, 246, 228) !important;
        border-radius: 0.375rem !important;
        padding: 0.25rem 0.5rem !important;
        font-size: 0.75rem !important;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .ts-wrapper.multi .ts-control .item .remove {
        color: rgb(19, 78, 74) !important;
        margin-left: 0.25rem;
    }
    
    .ts-wrapper.multi .ts-control .item .remove:hover {
        color: rgb(239, 68, 68) !important;
    }
    
    .ts-wrapper.multi .ts-control input {
        color: rgb(15, 23, 42) !important;
    }
    
    .ts-wrapper.multi .ts-control input::placeholder {
        color: rgb(148, 163, 184) !important;
    }
    
    .ts-wrapper.multi.focus .ts-control {
        border-color: rgb(20, 184, 166) !important;
        box-shadow: 0 0 0 2px rgba(20, 184, 166, 0.2) !important;
        outline: none !important;
    }
</style>
