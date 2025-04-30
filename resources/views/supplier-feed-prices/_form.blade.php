{{-- resources/views/supplier-feed-prices/_form.blade.php --}}

<div class="row mb-3">
    {{-- Supplier --}}
    <div class="col-md-4">
        <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
        <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
            <option value="">Select Supplier</option>
            {{-- $suppliers should be passed from the controller --}}
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}"
                    {{ (old('supplier_id', $supplierFeedPrice->supplier_id ?? null) == $supplier->id) ? 'selected' : '' }}
                    {{ !$supplier->is_active ? 'disabled' : '' }} {{-- Optionally disable inactive suppliers --}}
                >
                    {{ $supplier->name }} {{ !$supplier->is_active ? '(Inactive)' : '' }}
                </option>
            @endforeach
        </select>
        @error('supplier_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Feed Type --}}
    <div class="col-md-4">
        <label for="feed_type_id" class="form-label">Feed Type <span class="text-danger">*</span></label>
        <select class="form-select @error('feed_type_id') is-invalid @enderror" id="feed_type_id" name="feed_type_id" required>
            <option value="">Select Feed Type</option>
            {{-- $feedTypes should be passed from the controller --}}
            @foreach($feedTypes as $feedType)
                <option value="{{ $feedType->id }}"
                    {{ (old('feed_type_id', $supplierFeedPrice->feed_type_id ?? null) == $feedType->id) ? 'selected' : '' }}
                >
                    {{ $feedType->name }}
                </option>
            @endforeach
        </select>
        @error('feed_type_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Purchase Unit --}}
    <div class="col-md-4">
        <label for="purchase_unit_id" class="form-label">Purchase Unit <span class="text-danger">*</span></label>
        <select class="form-select @error('purchase_unit_id') is-invalid @enderror" id="purchase_unit_id" name="purchase_unit_id" required>
            <option value="">Select Unit</option>
            {{-- $purchaseUnits should be passed from the controller --}}
            @foreach($purchaseUnits as $unit)
                <option value="{{ $unit->id }}"
                    {{ (old('purchase_unit_id', $supplierFeedPrice->purchase_unit_id ?? null) == $unit->id) ? 'selected' : '' }}
                >
                    {{ $unit->name }}
                </option>
            @endforeach
        </select>
        @error('purchase_unit_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    {{-- Price --}}
    <div class="col-md-6">
        <label for="supplier_price" class="form-label">Supplier Price (per Unit) <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">{{ config('app.currency_symbol', '$') }}</span> {{-- Use currency symbol from config or default --}}
            <input type="number" step="0.01" min="0" class="form-control @error('supplier_price') is-invalid @enderror" id="supplier_price" name="supplier_price"
                   value="{{ old('supplier_price', $supplierFeedPrice->supplier_price ?? '') }}" required placeholder="0.00">
        </div>
        @error('supplier_price')
        <div class="invalid-feedback d-block">{{ $message }}</div> {{-- Needs d-block for input-group --}}
        @enderror
    </div>

    {{-- Effective Date --}}
    <div class="col-md-6">
        <label for="effective_date" class="form-label">Effective Date <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('effective_date') is-invalid @enderror" id="effective_date" name="effective_date"
               value="{{ old('effective_date', isset($supplierFeedPrice) && $supplierFeedPrice->effective_date ? $supplierFeedPrice->effective_date->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('effective_date')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>


{{-- Description --}}
<div class="mb-3">
    <label for="description" class="form-label">Description / Notes</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Optional: Any notes about this price (e.g., special offer, bulk discount condition)">{{ old('description', $supplierFeedPrice->description ?? '') }}</textarea>
    @error('description')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-success"> {{-- Changed color --}}
        <i class="fas fa-save"></i> {{ isset($supplierFeedPrice) ? 'Update Price' : 'Save Price' }}
    </button>
    <a href="{{ route('supplier-feed-prices.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>
