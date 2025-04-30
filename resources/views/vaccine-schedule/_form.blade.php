{{-- resources/views/vaccine-schedule/_form.blade.php --}}

<div class="row mb-3">
    {{-- Batch --}}
    <div class="col-md-6">
        <label for="batch_id" class="form-label">Batch <span class="text-danger">*</span></label>
        <select class="form-select @error('batch_id') is-invalid @enderror" id="batch_id" name="batch_id" required>
            <option value="">Select Batch</option>
            {{-- $batches should be passed from the controller --}}
            @foreach($batches as $batch)
                <option value="{{ $batch->id }}"
                    {{ (old('batch_id', $vaccineSchedule->batch_id ?? null) == $batch->id) ? 'selected' : '' }}
                    {{ $batch->status != 'active' ? 'disabled' : '' }}>
                    {{ $batch->batch_code }} {{ $batch->status != 'active' ? '('.ucfirst($batch->status).')' : '' }}
                </option>
            @endforeach
        </select>
        @error('batch_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Vaccine --}}
    <div class="col-md-6">
        <label for="vaccine_id" class="form-label">Vaccine <span class="text-danger">*</span></label>
        <select class="form-select @error('vaccine_id') is-invalid @enderror" id="vaccine_id" name="vaccine_id" required>
            <option value="">Select Vaccine</option>
            {{-- $vaccines should be passed from the controller --}}
            @foreach($vaccines as $vaccine)
                <option value="{{ $vaccine->id }}"
                    {{ (old('vaccine_id', $vaccineSchedule->vaccine_id ?? null) == $vaccine->id) ? 'selected' : '' }}
                >
                    {{ $vaccine->name }}
                </option>
            @endforeach
        </select>
        @error('vaccine_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    {{-- Date Due --}}
    <div class="col-md-6">
        <label for="date_due" class="form-label">Date Due <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('date_due') is-invalid @enderror" id="date_due" name="date_due"
               value="{{ old('date_due', isset($vaccineSchedule) && $vaccineSchedule->date_due ? $vaccineSchedule->date_due->format('Y-m-d') : '') }}" required>
        @error('date_due')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Status --}}
    <div class="col-md-6">
        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            {{-- Set default to 'scheduled' on create form --}}
            <option value="scheduled" {{ old('status', $vaccineSchedule->status ?? 'scheduled') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
            <option value="administered" {{ old('status', $vaccineSchedule->status ?? 'scheduled') == 'administered' ? 'selected' : '' }}>Administered</option>
            <option value="missed" {{ old('status', $vaccineSchedule->status ?? 'scheduled') == 'missed' ? 'selected' : '' }}>Missed</option>
        </select>
        @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Conditional Fields for 'Administered' Status --}}
<div id="administered-fields" class="row mb-3 border-top pt-3 mt-3" style="{{ old('status', $vaccineSchedule->status ?? 'scheduled') == 'administered' ? '' : 'display: none;' }}">
    <h5 class="text-success mb-3"><i class="fas fa-check-circle"></i> Administration Details</h5>
    {{-- Administered Date --}}
    <div class="col-md-6">
        <label for="administered_date" class="form-label">Administered Date <span id="administered_date_required" class="text-danger">*</span></label>
        <input type="date" class="form-control @error('administered_date') is-invalid @enderror" id="administered_date" name="administered_date"
               value="{{ old('administered_date', isset($vaccineSchedule) && $vaccineSchedule->administered_date ? $vaccineSchedule->administered_date->format('Y-m-d') : date('Y-m-d')) }}">
        @error('administered_date')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Link to Vaccination Log --}}
    <div class="col-md-6">
        <label for="vaccination_log_id" class="form-label">Link to Vaccination Log Entry <span id="vaccination_log_id_required" class="text-danger">*</span></label>
        <select class="form-select @error('vaccination_log_id') is-invalid @enderror" id="vaccination_log_id" name="vaccination_log_id">
            <option value="">Select Corresponding Log Entry</option>
            {{-- $vaccinationLogs should be passed from the controller --}}
            {{-- Ideally, filter this list based on selected Batch & Vaccine using JS if list is long --}}
            @foreach($vaccinationLogs ?? [] as $log)
                <option value="{{ $log->id }}"
                    {{ (old('vaccination_log_id', $vaccineSchedule->vaccination_log_id ?? null) == $log->id) ? 'selected' : '' }}
                >
                    Log #{{ $log->id }} ({{ $log->dailyRecord->record_date->format('Y-m-d') ?? 'N/A' }} - {{ $log->vaccine->name ?? 'N/A' }})
                </option>
            @endforeach
        </select>
        @error('vaccination_log_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Select the log entry created when this was administered.</small>
    </div>
</div>


{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($vaccineSchedule) ? 'Update Schedule' : 'Save Schedule' }}
    </button>
    <a href="{{ route('vaccine-schedule.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>

{{-- Script to show/hide Administered fields and manage required attribute --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusSelect = document.getElementById('status');
            const administeredFieldsDiv = document.getElementById('administered-fields');
            const administeredDateInput = document.getElementById('administered_date');
            const vaccinationLogSelect = document.getElementById('vaccination_log_id');
            const administeredDateRequiredSpan = document.getElementById('administered_date_required');
            const vaccinationLogRequiredSpan = document.getElementById('vaccination_log_id_required');


            function toggleAdministeredFields() {
                if (statusSelect.value === 'administered') {
                    administeredFieldsDiv.style.display = ''; // Show container
                    administeredDateInput.required = true;
                    vaccinationLogSelect.required = true;
                    administeredDateRequiredSpan.style.display = '';
                    vaccinationLogRequiredSpan.style.display = '';

                } else {
                    administeredFieldsDiv.style.display = 'none'; // Hide container
                    administeredDateInput.required = false;
                    vaccinationLogSelect.required = false;
                    administeredDateRequiredSpan.style.display = 'none';
                    vaccinationLogRequiredSpan.style.display = 'none';
                    // Clear values if hidden (optional)
                    // administeredDateInput.value = '';
                    // vaccinationLogSelect.value = '';
                }
            }

            // Initial check on page load
            toggleAdministeredFields();

            // Add event listener
            statusSelect.addEventListener('change', toggleAdministeredFields);
        });
    </script>
@endpush
