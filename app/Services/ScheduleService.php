<?php
namespace App\Services;

use Carbon\Carbon;

class ScheduleService
{
    public function calendarDueDate(?string $lastCompletionDate, int $intervalDays): ?Carbon
    {
        if (!$lastCompletionDate) return null;
        return Carbon::parse($lastCompletionDate)->addDays($intervalDays);
    }

    public function hoursDueAt(?int $lastCompletionHours, int $intervalHours): ?int
    {
        if ($lastCompletionHours === null) return null;
        return $lastCompletionHours + $intervalHours;
    }

    public function status(string $triggerType, ?string $lastCompletionDate, ?int $lastCompletionHours, int $intervalDays, int $intervalHours, ?string $today, ?int $currentHours): array
    {
        $today = $today ? Carbon::parse($today) : Carbon::now('Asia/Kuala_Lumpur')->startOfDay();
        $result = ['status' => 'Not due', 'trigger' => null, 'missing_meter' => false, 'calendar_due_date' => null, 'hours_due_at' => null];

        if ($triggerType === 'calendar' || $triggerType === 'dual') {
            if ($lastCompletionDate) {
                $due = $this->calendarDueDate($lastCompletionDate, $intervalDays);
                $result['calendar_due_date'] = $due->toDateString();
                if ($today->greaterThanOrEqualTo($due)) {
                    $result['status'] = $today->greaterThan($due) ? 'Overdue' : 'Due';
                    $result['trigger'] = 'calendar';
                }
            }
        }

        if ($triggerType === 'hours' || $triggerType === 'dual') {
            if ($lastCompletionHours === null || $currentHours === null) {
                if ($triggerType === 'hours') {
                    $result['missing_meter'] = true;
                } elseif ($triggerType === 'dual' && $result['status'] === 'Not due') {
                    $result['missing_meter'] = true;
                }
            } else {
                $threshold = $this->hoursDueAt($lastCompletionHours, $intervalHours);
                $result['hours_due_at'] = $threshold;
                if ($currentHours >= $threshold) {
                    $overdue = $currentHours > $threshold;
                    if ($result['status'] === 'Not due' || $result['status'] === 'Due') {
                        $result['status'] = $overdue ? 'Overdue' : 'Due';
                    }
                    if (!$result['trigger']) $result['trigger'] = 'hours';
                }
            }
        }

        if ($triggerType === 'dual' && $result['status'] !== 'Not due') {
            // keep whichever trigger set it; if both, prefer whichever is true
        }
        return $result;
    }
}
