{{-- resources/views/purchase-order-statuses/_form.blade.php --}}

{{-- Name --}}
<div class="mb-3">
    <label for="name" class="form-label">Status Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
           value="{{ old('name', $purchaseOrderStatus->name ?? '') }}" required placeholder="e.g., Pending, Processing, Shipped, Delivered, Cancelled">
    @error('name')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Description --}}
<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Optional: Explain what this status means">{{ old('description', $purchaseOrderStatus->description ?? '') }}</textarea>
    @error('description')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($purchaseOrderStatus) ? 'Update Status' : 'Save Status' }}
    </button>
    <a href="{{ route('purchase-order-statuses.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>
