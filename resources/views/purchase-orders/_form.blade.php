{{-- resources/views/purchase-orders/_form.blade.php --}}
@php
    // Define currency symbol (consider moving this to a config or helper)
    $currencySymbol = config('app.currency_symbol', '$');
@endphp

<div class="row mb-3">
    {{-- Purchase Order No --}}
    <div class="col-md-6">
        <label for="purchase_order_no" class="form-label">Purchase Order # <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('purchase_order_no') is-invalid @enderror" id="purchase_order_no" name="purchase_order_no"
               value="{{ old('purchase_order_no', $purchaseOrder->purchase_order_no ?? '') }}" required placeholder="e.g., PO-2025-001">
        @error('purchase_order_no')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Supplier --}}
    <div class="col-md-6">
        <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
        <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
            <option value="">Select Supplier</option>
            {{-- $suppliers should be passed from the controller --}}
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}"
                    {{ (old('supplier_id', $purchaseOrder->supplier_id ?? null) == $supplier->id) ? 'selected' : '' }}
                    {{ !$supplier->is_active ? 'disabled' : '' }}>
                    {{ $supplier->name }} {{ !$supplier->is_active ? '(Inactive)' : '' }}
                </option>
            @endforeach
        </select>
        @error('supplier_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    {{-- Feed Type --}}
    <div class="col-md-6">
        <label for="feed_type_id" class="form-label">Feed Type <span class="text-danger">*</span></label>
        <select class="form-select @error('feed_type_id') is-invalid @enderror" id="feed_type_id" name="feed_type_id" required>
            <option value="">Select Feed Type</option>
            {{-- $feedTypes should be passed from the controller --}}
            @foreach($feedTypes as $feedType)
                <option value="{{ $feedType->id }}"
                    {{ (old('feed_type_id', $purchaseOrder->feed_type_id ?? null) == $feedType->id) ? 'selected' : '' }}>
                    {{ $feedType->name }}
                </option>
            @endforeach
        </select>
        @error('feed_type_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    {{-- Purchase Unit --}}
    <div class="col-md-6">
        <label for="purchase_unit_id" class="form-label">Purchase Unit <span class="text-danger">*</span></label>
        <select class="form-select @error('purchase_unit_id') is-invalid @enderror" id="purchase_unit_id" name="purchase_unit_id" required>
            <option value="">Select Unit</option>
            {{-- $purchaseUnits should be passed from the controller --}}
            @foreach($purchaseUnits as $unit)
                <option value="{{ $unit->id }}"
                    {{ (old('purchase_unit_id', $purchaseOrder->purchase_unit_id ?? null) == $unit->id) ? 'selected' : '' }}>
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
    {{-- Quantity --}}
    <div class="col-md-4">
        <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0.01" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity"
               value="{{ old('quantity', $purchaseOrder->quantity ?? '') }}" required placeholder="e.g., 50">
        @error('quantity')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Unit Price --}}
    <div class="col-md-4">
        <label for="unit_price" class="form-label">Unit Price <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">{{ $currencySymbol }}</span>
            <input type="number" step="0.01" min="0" class="form-control @error('unit_price') is-invalid @enderror" id="unit_price" name="unit_price"
                   value="{{ old('unit_price', $purchaseOrder->unit_price ?? '') }}" required placeholder="0.00">
        </div>
        @error('unit_price')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- Total Price (Calculated) --}}
    <div class="col-md-4">
        <label for="total_price" class="form-label">Total Price <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">{{ $currencySymbol }}</span>
            <input type="number" step="0.01" min="0" class="form-control @error('total_price') is-invalid @enderror" id="total_price" name="total_price"
                   value="{{ old('total_price', $purchaseOrder->total_price ?? '') }}" required readonly placeholder="Auto-calculated">
        </div>
        @error('total_price')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Quantity x Unit Price</small>
    </div>
</div>

<div class="row mb-3">
    {{-- Order Date --}}
    <div class="col-md-4">
        <label for="order_date" class="form-label">Order Date <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('order_date') is-invalid @enderror" id="order_date" name="order_date"
               value="{{ old('order_date', isset($purchaseOrder) && $purchaseOrder->order_date ? $purchaseOrder->order_date->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('order_date')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Expected Delivery Date --}}
    <div class="col-md-4">
        <label for="expected_delivery_date" class="form-label">Expected Delivery Date</label>
        <input type="date" class="form-control @error('expected_delivery_date') is-invalid @enderror" id="expected_delivery_date" name="expected_delivery_date"
               value="{{ old('expected_delivery_date', isset($purchaseOrder) && $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('Y-m-d') : '') }}">
        @error('expected_delivery_date')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Actual Delivery Date --}}
    <div class="col-md-4">
        <label for="actual_delivery_date" class="form-label">Actual Delivery Date</label>
        <input type="date" class="form-control @error('actual_delivery_date') is-invalid @enderror" id="actual_delivery_date" name="actual_delivery_date"
               value="{{ old('actual_delivery_date', isset($purchaseOrder) && $purchaseOrder->actual_delivery_date ? $purchaseOrder->actual_delivery_date->format('Y-m-d') : '') }}">
        @error('actual_delivery_date')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    {{-- Status --}}
    <div class="col-md-6">
        <label for="purchase_order_status_id" class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-select @error('purchase_order_status_id') is-invalid @enderror" id="purchase_order_status_id" name="purchase_order_status_id" required>
            <option value="">Select Status</option>
            {{-- $statuses should be passed from the controller --}}
            @foreach($statuses as $status)
                <option value="{{ $status->id }}"
                    {{ (old('purchase_order_status_id', $purchaseOrder->purchase_order_status_id ?? null) == $status->id) ? 'selected' : '' }}>
                    {{ $status->name }}
                </option>
            @endforeach
        </select>
        @error('purchase_order_status_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>


{{-- Notes --}}
<div class="mb-3">
    <label for="notes" class="form-label">Notes</label>
    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $purchaseOrder->notes ?? '') }}</textarea>
    @error('notes')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($purchaseOrder) ? 'Update Purchase Order' : 'Save Purchase Order' }}
    </button>
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>

{{-- Add script for total price calculation --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInput = document.getElementById('quantity');
            const unitPriceInput = document.getElementById('unit_price');
            const totalPriceInput = document.getElementById('total_price');

            function calculateTotal() {
                const quantity = parseFloat(quantityInput.value) || 0;
                const unitPrice = parseFloat(unitPriceInput.value) || 0;
                const total = quantity * unitPrice;
                // Format to 2 decimal places
                totalPriceInput.value = total.toFixed(2);
            }

            quantityInput.addEventListener('input', calculateTotal);
            unitPriceInput.addEventListener('input', calculateTotal);

            // Calculate on page load if editing
            if (quantityInput.value && unitPriceInput.value) {
                calculateTotal();
            }
        });
    </script>
@endpush
