{{-- resources/views/sales-records/_form.blade.php --}}
@php
    $currencySymbol = config('app.currency_symbol', '$');
@endphp

{{-- Hidden data attribute for prices --}}
<div id="sales-price-data" style="display: none;"
     @if(isset($salesPrices))
         @foreach($salesPrices as $price)
             data-price-{{ $price->id }}="{{ $price->price }}"
     data-unit-{{ $price->id }}="{{ $price->salesUnit->name ?? 'Unit' }}"
    @endforeach
    @endif
></div>


<div class="row mb-3">
    {{-- Sales Person (User) --}}
    <div class="col-md-6">
        <label for="sales_person_id" class="form-label">Sales Person <span class="text-danger">*</span></label>
        <select class="form-select @error('sales_person_id') is-invalid @enderror" id="sales_person_id" name="sales_person_id" required>
            <option value="">Select Sales Person</option>
            {{-- $salesPeople (Users) should be passed from the controller --}}
            @foreach($salesPeople as $person)
                <option value="{{ $person->id }}"
                    {{ (old('sales_person_id', $salesRecord->sales_person_id ?? auth()->id()) == $person->id) ? 'selected' : '' }}
                    {{-- Default to logged-in user if available --}}
                    {{ isset($person->is_active) && !$person->is_active ? 'disabled' : '' }}
                >
                    {{ $person->name }} {{ isset($person->is_active) && !$person->is_active ? '(Inactive)' : '' }}
                </option>
            @endforeach
        </select>
        @error('sales_person_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Sale Date --}}
    <div class="col-md-6">
        <label for="sale_date" class="form-label">Sale Date <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('sale_date') is-invalid @enderror" id="sale_date" name="sale_date"
               value="{{ old('sale_date', isset($salesRecord) && $salesRecord->sale_date ? $salesRecord->sale_date->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('sale_date')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    {{-- Customer Name --}}
    <div class="col-md-6">
        <label for="customer_name" class="form-label">Customer Name</label>
        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name"
               value="{{ old('customer_name', $salesRecord->customer_name ?? '') }}" placeholder="Optional">
        @error('customer_name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Customer Phone --}}
    <div class="col-md-6">
        <label for="customer_phone" class="form-label">Customer Phone</label>
        <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" id="customer_phone" name="customer_phone"
               value="{{ old('customer_phone', $salesRecord->customer_phone ?? '') }}" placeholder="Optional">
        @error('customer_phone')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<hr>

<div class="row mb-3 align-items-end"> {{-- Use align-items-end --}}
    {{-- Sales Price Item --}}
    <div class="col-md-4">
        <label for="sales_price_id" class="form-label">Item & Price <span class="text-danger">*</span></label>
        <select class="form-select @error('sales_price_id') is-invalid @enderror" id="sales_price_id" name="sales_price_id" required>
            <option value="" data-unit-price="0">Select Item</option>
            {{-- $salesPrices should be passed from the controller --}}
            @foreach($salesPrices as $price)
                <option value="{{ $price->id }}"
                        {{ (old('sales_price_id', $salesRecord->sales_price_id ?? null) == $price->id) ? 'selected' : '' }}
                        data-unit-price="{{ $price->price }}" {{-- Store price in data attribute --}}
                >
                    {{-- Display Item Type and details --}}
                    @if($price->item_type === 'bird' && $price->batch)
                        {{ ucfirst($price->item_type) }} (Batch: {{ $price->batch->batch_code }})
                    @else
                        {{ ucfirst($price->item_type) }}
                    @endif
                    - {{ $price->salesUnit->name ?? 'Unit' }}
                    ({{ $currencySymbol }}{{ number_format($price->price, 2) }})
                </option>
            @endforeach
        </select>
        @error('sales_price_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Quantity --}}
    <div class="col-md-2">
        <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0.01" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity"
               value="{{ old('quantity', $salesRecord->quantity ?? '1') }}" required placeholder="e.g., 1">
        @error('quantity')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Total Amount (Calculated) --}}
    <div class="col-md-3">
        <label for="total_amount" class="form-label">Total Amount <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">{{ $currencySymbol }}</span>
            {{-- Readonly, calculated by JS --}}
            <input type="number" step="0.01" class="form-control @error('total_amount') is-invalid @enderror" id="total_amount" name="total_amount"
                   value="{{ old('total_amount', $salesRecord->total_amount ?? '0.00') }}" required readonly>
            {{-- Hidden input to ensure value is submitted even if JS fails --}}
            <input type="hidden" id="total_amount_hidden" name="total_amount" value="{{ old('total_amount', $salesRecord->total_amount ?? '0.00') }}">
        </div>
        @error('total_amount')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- Amount Paid --}}
    <div class="col-md-3">
        <label for="amount_paid" class="form-label">Amount Paid <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">{{ $currencySymbol }}</span>
            <input type="number" step="0.01" min="0" class="form-control @error('amount_paid') is-invalid @enderror" id="amount_paid" name="amount_paid"
                   value="{{ old('amount_paid', $salesRecord->amount_paid ?? '0.00') }}" required placeholder="0.00">
        </div>
        @error('amount_paid')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

</div>

{{-- Notes --}}
<div class="mb-3">
    <label for="notes" class="form-label">Notes</label>
    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $salesRecord->notes ?? '') }}</textarea>
    @error('notes')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-success">
        <i class="fas fa-save"></i> {{ isset($salesRecord) ? 'Update Sales Record' : 'Save Sales Record' }}
    </button>
    <a href="{{ route('sales-records.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>

{{-- Script for total amount calculation --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const salesPriceSelect = document.getElementById('sales_price_id');
            const quantityInput = document.getElementById('quantity');
            const totalAmountInput = document.getElementById('total_amount');
            const totalAmountHiddenInput = document.getElementById('total_amount_hidden');
            const amountPaidInput = document.getElementById('amount_paid'); // Get amount paid input

            function calculateTotal() {
                const selectedOption = salesPriceSelect.options[salesPriceSelect.selectedIndex];
                const unitPrice = parseFloat(selectedOption.dataset.unitPrice) || 0; // Get price from data attribute
                const quantity = parseFloat(quantityInput.value) || 0;
                const total = unitPrice * quantity;

                totalAmountInput.value = total.toFixed(2);
                totalAmountHiddenInput.value = total.toFixed(2); // Update hidden input too

                // Optionally set amount paid to total when total changes, if not already set
                // if (!amountPaidInput.value || parseFloat(amountPaidInput.value) === 0) {
                //    amountPaidInput.value = total.toFixed(2);
                // }
            }

            // Calculate on page load if editing or if old values exist
            if (salesPriceSelect.value && quantityInput.value) {
                calculateTotal();
            }

            salesPriceSelect.addEventListener('change', calculateTotal);
            quantityInput.addEventListener('input', calculateTotal);

            // Optionally set amount paid = total amount when item/qty changes if amount paid is 0
            function updateAmountPaid() {
                const total = parseFloat(totalAmountInput.value) || 0;
                const paid = parseFloat(amountPaidInput.value) || 0;
                if (paid === 0 || amountPaidInput.value === '') { // Only if amount paid is zero or empty
                    amountPaidInput.value = total.toFixed(2);
                }
            }
            // Uncomment below if you want amount_paid to auto-fill
            // salesPriceSelect.addEventListener('change', updateAmountPaid);
            // quantityInput.addEventListener('input', updateAmountPaid);

        });
    </script>
@endpush
