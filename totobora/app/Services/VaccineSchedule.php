<?php

namespace App\Services;

use Carbon\Carbon;

class VaccineSchedule
{
    /**
     * Kenya MOH immunization schedule.
     * Each entry: vaccine name => [dose_number => weeks_from_birth]
     */
    public static function schedule(): array
    {
        return [
            'BCG'           => [1 => 0],
            'OPV'           => [1 => 0,  2 => 6,  3 => 10, 4 => 14],
            'DPT-HepB-Hib'  => [1 => 6,  2 => 10, 3 => 14],
            'PCV'           => [1 => 6,  2 => 10, 3 => 14],
            'Rota'          => [1 => 6,  2 => 10],
            'IPV'           => [1 => 14],
            'Measles'       => [1 => 36, 2 => 72],
            'Yellow Fever'  => [1 => 36],
            'Vitamin A'     => [1 => 36, 2 => 52],
        ];
    }

    /**
     * Given a vaccine name and dose number just administered,
     * return the date the next dose is due (or null if this was the last dose).
     */
    public static function nextDueDate(
        string $vaccineName,
        int $doseNumber,
        Carbon $dateOfBirth
    ): ?Carbon {
        $schedule = self::schedule();

        if (!isset($schedule[$vaccineName])) {
            return null;
        }

        $doses    = $schedule[$vaccineName];
        $nextDose = $doseNumber + 1;

        if (!isset($doses[$nextDose])) {
            return null; // No more doses
        }

        return $dateOfBirth->copy()->addWeeks($doses[$nextDose]);
    }

    /**
     * Return all vaccines due at a given age in weeks.
     */
    public static function dueAt(int $weeksOld): array
    {
        $due = [];
        foreach (self::schedule() as $vaccine => $doses) {
            foreach ($doses as $dose => $week) {
                if ($week === $weeksOld) {
                    $due[] = ['vaccine' => $vaccine, 'dose' => $dose];
                }
            }
        }
        return $due;
    }

    /**
     * Return all upcoming vaccines for a child based on their
     * immunization history and date of birth.
     */
    public static function upcomingForChild(
        Carbon $dateOfBirth,
        array $givenVaccines // [['vaccine_name' => ..., 'dose_number' => ...]]
    ): array {
        $given = [];
        foreach ($givenVaccines as $v) {
            $given[$v['vaccine_name']][] = $v['dose_number'];
        }

        $upcoming = [];
        foreach (self::schedule() as $vaccine => $doses) {
            foreach ($doses as $dose => $week) {
                $alreadyGiven = isset($given[$vaccine])
                    && in_array($dose, $given[$vaccine]);

                if (!$alreadyGiven) {
                    $dueDate = $dateOfBirth->copy()->addWeeks($week);
                    $upcoming[] = [
                        'vaccine'  => $vaccine,
                        'dose'     => $dose,
                        'due_date' => $dueDate,
                        'overdue'  => $dueDate->isPast(),
                    ];
                    break; // Only show next pending dose per vaccine
                }
            }
        }

        // Sort by due date
        usort($upcoming, fn($a, $b) => $a['due_date'] <=> $b['due_date']);

        return $upcoming;
    }

    /**
     * Get a human-readable label for a dose.
     */
    public static function doseLabel(string $vaccine, int $dose): string
    {
        $suffixes = ['', 'dose 1', 'dose 2', 'dose 3', 'dose 4'];
        return $vaccine . ' ' . ($suffixes[$dose] ?? "dose {$dose}");
    }
}