<?php

declare(strict_types=1);

namespace App\Application\Listeners;

use App\Application\Events\BatchCreated;
use App\Models\Vaccine;
use App\Models\VaccineSchedule;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Create Vaccination Schedule Listener
 *
 * Automatically creates vaccination schedules when a new batch is created.
 * Generates schedules based on bird type, breed, and age-appropriate vaccines.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
class CreateVaccinationSchedule implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The name of the queue the job should be sent to.
     */
    public string $queue = 'batch-processing';

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Handle the event.
     */
    public function handle(BatchCreated $event): void
    {
        $batch = $event->getBatch();

        Log::info('Creating vaccination schedule for batch', [
            'batch_id' => $batch->id,
            'batch_code' => $batch->batch_code,
            'bird_type' => $batch->birdType->name,
            'breed' => $batch->breed->name,
            'bird_age_days' => $batch->bird_age_days,
        ]);

        try {
            DB::transaction(function () use ($batch) {
                $this->createVaccinationSchedule($batch);
            });

            Log::info('Vaccination schedule created successfully', [
                'batch_id' => $batch->id,
                'batch_code' => $batch->batch_code,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create vaccination schedule', [
                'batch_id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to trigger job retry
            throw $e;
        }
    }

    /**
     * Create vaccination schedule based on batch characteristics
     */
    private function createVaccinationSchedule($batch): void
    {
        // Get applicable vaccines for this bird type/breed combination
        $vaccines = $this->getApplicableVaccines($batch);

        if ($vaccines->isEmpty()) {
            Log::warning('No applicable vaccines found for batch', [
                'batch_id' => $batch->id,
                'bird_type_id' => $batch->bird_type_id,
                'breed_id' => $batch->breed_id,
            ]);

            return;
        }

        $schedules = [];
        $currentAge = $batch->bird_age_days;
        $baseDate = $batch->hatch_date ?? $batch->date_received;

        foreach ($vaccines as $vaccine) {
            $vaccineSchedules = $this->calculateVaccineSchedule($vaccine, $currentAge, $baseDate);
            $schedules = array_merge($schedules, $vaccineSchedules);
        }

        // Sort schedules by due date
        usort($schedules, fn ($a, $b) => $a['due_date']->compare($b['due_date']));

        // Create schedule records
        foreach ($schedules as $schedule) {
            VaccineSchedule::create([
                'batch_id' => $batch->id,
                'vaccine_id' => $schedule['vaccine_id'],
                'due_date' => $schedule['due_date'],
                'bird_age_days' => $schedule['bird_age_days'],
                'status' => 'pending',
                'notes' => $schedule['notes'],
                'created_by' => auth()->id(),
            ]);
        }

        Log::info('Created vaccination schedules', [
            'batch_id' => $batch->id,
            'schedule_count' => count($schedules),
        ]);
    }

    /**
     * Get vaccines applicable to this batch
     */
    private function getApplicableVaccines($batch)
    {
        return Vaccine::where('is_active', true)
            ->where(function ($query) use ($batch) {
                // Vaccines for any bird type or specific bird type
                $query->whereNull('bird_type_id')
                    ->orWhere('bird_type_id', $batch->bird_type_id);
            })
            ->where(function ($query) use ($batch) {
                // Vaccines for any breed or specific breed
                $query->whereNull('breed_id')
                    ->orWhere('breed_id', $batch->breed_id);
            })
            ->orderBy('recommended_age_days', 'asc')
            ->get();
    }

    /**
     * Calculate vaccination schedule for a specific vaccine
     */
    private function calculateVaccineSchedule($vaccine, int $currentAge, Carbon $baseDate): array
    {
        $schedules = [];
        $recommendedAge = $vaccine->recommended_age_days;

        // Skip if birds are too old for this vaccine
        if ($currentAge > ($recommendedAge + ($vaccine->age_tolerance_days ?? 14))) {
            return $schedules;
        }

        // Calculate due date
        $dueDate = $baseDate->copy()->addDays($recommendedAge);

        // If vaccine is overdue but within tolerance, schedule immediately
        if ($dueDate->isPast() && $currentAge <= ($recommendedAge + ($vaccine->age_tolerance_days ?? 14))) {
            $dueDate = Carbon::now()->addDays(1); // Schedule for tomorrow
        }

        // Skip if vaccine is too far overdue
        if ($dueDate->isPast()) {
            return $schedules;
        }

        $schedules[] = [
            'vaccine_id' => $vaccine->id,
            'due_date' => $dueDate,
            'bird_age_days' => $recommendedAge,
            'notes' => $this->generateScheduleNotes($vaccine, $currentAge, $recommendedAge),
        ];

        // Add booster schedules if specified
        if ($vaccine->booster_interval_days && $vaccine->max_boosters > 0) {
            $this->addBoosterSchedules($schedules, $vaccine, $baseDate, $recommendedAge);
        }

        return $schedules;
    }

    /**
     * Add booster vaccination schedules
     */
    private function addBoosterSchedules(array &$schedules, $vaccine, Carbon $baseDate, int $initialAge): void
    {
        for ($boosterNum = 1; $boosterNum <= $vaccine->max_boosters; $boosterNum++) {
            $boosterAge = $initialAge + ($vaccine->booster_interval_days * $boosterNum);
            $boosterDate = $baseDate->copy()->addDays($boosterAge);

            // Don't schedule boosters too far in the future (beyond typical batch lifecycle)
            if ($boosterAge > 500) { // ~16 months
                break;
            }

            $schedules[] = [
                'vaccine_id' => $vaccine->id,
                'due_date' => $boosterDate,
                'bird_age_days' => $boosterAge,
                'notes' => "Booster #{$boosterNum} - ".$vaccine->name,
            ];
        }
    }

    /**
     * Generate appropriate notes for the vaccination schedule
     */
    private function generateScheduleNotes($vaccine, int $currentAge, int $recommendedAge): string
    {
        $notes = $vaccine->name;

        if ($currentAge > $recommendedAge) {
            $daysOverdue = $currentAge - $recommendedAge;
            $notes .= " (Scheduled late - birds are {$daysOverdue} days past recommended age)";
        } elseif ($currentAge < $recommendedAge) {
            $daysEarly = $recommendedAge - $currentAge;
            $notes .= " (Scheduled in {$daysEarly} days at recommended age)";
        }

        if ($vaccine->administration_method) {
            $notes .= " - Method: {$vaccine->administration_method}";
        }

        if ($vaccine->special_instructions) {
            $notes .= " - Instructions: {$vaccine->special_instructions}";
        }

        return $notes;
    }

    /**
     * Handle a job failure.
     */
    public function failed(BatchCreated $event, \Throwable $exception): void
    {
        Log::error('Failed to create vaccination schedule after all retries', [
            'batch_id' => $event->batch->id,
            'batch_code' => $event->batch->batch_code,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Could notify administrators about the failure
        // NotificationService::notifyAdmins('Vaccination schedule creation failed', [...]);
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [1, 5, 10]; // Retry after 1, 5, then 10 seconds
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(10);
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return ['vaccination', 'batch-processing', 'automated'];
    }
}
