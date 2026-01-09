@props([
    'options' => [],
    'selected' => null,
    'placeholder' => 'Select an option...',
    'searchPlaceholder' => 'Search...',
    'label' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
    'id' => 'select-' . uniqid(),
])

<div>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-slate-300 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-400">*</span>
            @endif
        </label>
    @endif
    
    <div 
        wire:ignore
        x-data="{
            value: @entangle($attributes->wire('model')),
            tomSelect: null,
            init() {
                if (typeof TomSelect === 'undefined') {
                    console.error('TomSelect is not loaded');
                    return;
                }
                
                this.tomSelect = new TomSelect($refs.select, {
                    placeholder: '{{ $placeholder }}',
                    searchField: ['text'],
                    maxOptions: null,
                    onChange: (value) => {
                        this.value = value;
                    },
                    // Custom styling to match the modal inputs
                    controlClass: 'ts-control',
                    dropdownClass: 'ts-dropdown'
                });
                
                this.$watch('value', value => {
                    if (this.tomSelect && this.tomSelect.getValue() !== value) {
                        this.tomSelect.setValue(value, true);
                    }
                });
            }
        }"
    >
        <select 
            x-ref="select"
            id="{{ $id }}"
            {{ $attributes->whereDoesntStartWith('wire:model') }}
            @if($disabled) disabled @endif
            class="w-full"
        >
            <option value="">{{ $placeholder }}</option>
            @foreach($options as $value => $text)
                <option value="{{ $value }}" @if($selected == $value) selected @endif>
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
    /* Tom Select custom styling to match modal inputs */
    .ts-wrapper .ts-control {
        background-color: white !important;
        border: 1px solid rgb(203, 213, 225) !important;
        border-radius: 0.5rem !important;
        padding: 0.625rem 1rem !important;
        font-size: 0.875rem !important;
        line-height: 1.25rem !important;
        color: rgb(15, 23, 42) !important;
        min-height: auto !important;
    }
    
    .ts-wrapper.single .ts-control {
        background-color: white !important;
    }
    
    .ts-wrapper .ts-control:focus,
    .ts-wrapper.focus .ts-control {
        border-color: rgb(20, 184, 166) !important;
        box-shadow: 0 0 0 2px rgba(20, 184, 166, 0.2) !important;
        outline: none !important;
    }
    
    .ts-wrapper .ts-control .item {
        background-color: transparent !important;
        color: rgb(15, 23, 42) !important;
        border: none !important;
        padding: 0 !important;
    }
    
    .ts-wrapper .ts-control input {
        color: rgb(15, 23, 42) !important;
    }
    
    .ts-wrapper .ts-control input::placeholder {
        color: rgb(148, 163, 184) !important;
    }
    
    .ts-dropdown {
        background-color: white !important;
        border: 1px solid rgb(203, 213, 225) !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        margin-top: 0.25rem !important;
    }
    
    .ts-dropdown .option {
        padding: 0.625rem 1rem !important;
        font-size: 0.875rem !important;
        color: rgb(15, 23, 42) !important;
        cursor: pointer !important;
    }
    
    .ts-dropdown .option:hover,
    .ts-dropdown .option.active {
        background-color: rgb(240, 253, 250) !important;
        color: rgb(20, 184, 166) !important;
    }
    
    .ts-dropdown .option.selected {
        background-color: rgb(204, 251, 241) !important;
        color: rgb(19, 78, 74) !important;
    }
    
    .ts-dropdown .no-results {
        padding: 0.625rem 1rem !important;
        font-size: 0.875rem !important;
        color: rgb(100, 116, 139) !important;
    }
    
    /* Remove default Tom Select caret/arrow */
    .ts-wrapper.single .ts-control:after {
        border-color: rgb(100, 116, 139) transparent transparent !important;
    }
    
    /* Ensure proper sizing */
    .ts-wrapper {
        width: 100% !important;
    }
</style>
