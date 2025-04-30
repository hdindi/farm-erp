{{-- resources/views/sales-prices/_form.blade.php --}}
@php
    $currencySymbol = config('app.currency_symbol', '$');
@endphp

<div class="row mb-3">
    {{-- Item Type --}}
    <div class="col-md-4">
        <label for="item_type" class="form-label">Item Type <span class="text-danger">*</span></label>
        <select class="form-select @error('item_type') is-invalid @enderror" id="item_type" name="item_type" required>
            <option value="">Select Item Type</option>
            {{-- Add more types if needed --}}
            <option value="egg" {{ old('item_type', $salesPrice->item_type ?? '') == 'egg' ? 'selected' : '' }}>Egg</option>
            <option value="bird" {{ old('item_type', $salesPrice->item_type ?? '') == 'bird' ? 'selected' : '' }}>Bird (from Batch)</option>
            <option value="manure" {{ old('item_type', $salesPrice->item_type ?? '') == 'manure' ? 'selected' : '' }}>Manure</option>
        </select>
        @error('item_type')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Item ID (Batch) - Conditionally Shown --}}
    <div class="col-md-4" id="item_id_container" style="{{ old('item_type', $salesPrice->item_type ?? '') == 'bird' ? '' : 'display: none;' }}">
        <label for="item_id" class="form-label">Select Batch (if Item Type is Bird) <span class="text-danger">*</span></label>
        <select class="form-select @error('item_id') is-invalid @enderror" id="item_id" name="item_id">
            <option value="">Select Batch</option>
            {{-- $batches should be passed from the controller --}}
            @foreach($batches as $batch)
                <option value="{{ $batch->id }}"
                    {{ (old('item_id', $salesPrice->item_id ?? null) == $batch->id) ? 'selected' : '' }}
                    {{ $batch->status != 'active' ? 'disabled' : '' }}>
                    {{ $batch->batch_code }} {{ $batch->status != 'active' ? '('.ucfirst($batch->status).')' : '' }}
                </option>
            @endforeach
        </select>
        @error('item_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Sales Unit --}}
    <div class="col-md-4">
        <label for="sales_unit_id" class="form-label">Sales Unit <span class="text-danger">*</span></label>
        <select class="form-select @error('sales_unit_id') is-invalid @enderror" id="sales_unit_id" name="sales_unit_id" required>
            <option value="">Select Unit</option>
            {{-- $salesUnits should be passed from the controller --}}
            @foreach($salesUnits as $unit)
                <option value="{{ $unit->id }}"
                    {{ (old('sales_unit_id', $salesPrice->sales_unit_id ?? null) == $unit->id) ? 'selected' : '' }}
                >
                    {{ $unit->name }}
                </option>
            @endforeach
        </select>
        @error('sales_unit_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>


<div class="row mb-3">
    {{-- Price --}}
    <div class="col-md-4">
        <label for="price" class="form-label">Price (per Unit) <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">{{ $currencySymbol }}</span>
            <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price"
                   value="{{ old('price', $salesPrice->price ?? '') }}" required placeholder="0.00">
        </div>
        @error('price')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- Effective Date --}}
    <div class="col-md-4">
        <label for="effective_date" class="form-label">Effective Date <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('effective_date') is-invalid @enderror" id="effective_date" name="effective_date"
               value="{{ old('effective_date', isset($salesPrice) && $salesPrice->effective_date ? $salesPrice->effective_date->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('effective_date')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Status --}}
    <div class="col-md-4">
        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            <option value="active" {{ old('status', $salesPrice->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $salesPrice->status ?? 'active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>


{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-success">
        <i class="fas fa-save"></i> {{ isset($salesPrice) ? 'Update Sales Price' : 'Save Sales Price' }}
    </button>
    <a href="{{ route('sales-prices.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>

{{-- Script to show/hide Batch dropdown --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const itemTypeSelect = document.getElementById('item_type');
            const itemIdContainer = document.getElementById('item_id_container');
            const itemIdSelect = document.getElementById('item_id'); // Batch select

            function toggleItemIdField() {
                if (itemTypeSelect.value === 'bird') {
                    itemIdContainer.style.display = ''; // Show container
                    itemIdSelect.required = true; // Make required
                } else {
                    itemIdContainer.style.display = 'none'; // Hide container
                    itemIdSelect.required = false; // Make not required
                    itemIdSelect.value = ''; // Clear value if hidden
                }
            }

            // Initial check on page load
            toggleItemIdField();

            // Add event listener
            itemTypeSelect.addEventListener('change', toggleItemIdField);
        });
    </script>
@endpush
