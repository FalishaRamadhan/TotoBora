<?php

namespace App\Services;

class WHOReference
{
    /**
     * WHO weight-for-age median and -2SD values by month (0–24 months).
     * Source: WHO Child Growth Standards.
     * Format: month => ['male' => [median, sd2_minus], 'female' => [median, sd2_minus]]
     */
    public static function weightForAge(): array
    {
        return [
            0  => ['male' => [3.3, 2.1], 'female' => [3.2, 2.0]],
            1  => ['male' => [4.5, 2.9], 'female' => [4.2, 2.7]],
            2  => ['male' => [5.6, 3.8], 'female' => [5.1, 3.4]],
            3  => ['male' => [6.4, 4.4], 'female' => [5.8, 3.9]],
            4  => ['male' => [7.0, 4.9], 'female' => [6.4, 4.4]],
            5  => ['male' => [7.5, 5.3], 'female' => [6.9, 4.8]],
            6  => ['male' => [7.9, 5.7], 'female' => [7.3, 5.2]],
            7  => ['male' => [8.3, 5.9], 'female' => [7.6, 5.5]],
            8  => ['male' => [8.6, 6.2], 'female' => [7.9, 5.7]],
            9  => ['male' => [8.9, 6.4], 'female' => [8.2, 5.9]],
            10 => ['male' => [9.2, 6.7], 'female' => [8.5, 6.1]],
            11 => ['male' => [9.4, 6.9], 'female' => [8.7, 6.3]],
            12 => ['male' => [9.6, 7.1], 'female' => [8.9, 6.5]],
            15 => ['male' => [10.3, 7.6], 'female' => [9.6, 7.0]],
            18 => ['male' => [10.9, 8.1], 'female' => [10.2, 7.5]],
            21 => ['male' => [11.5, 8.6], 'female' => [10.9, 8.0]],
            24 => ['male' => [12.2, 9.0], 'female' => [11.5, 8.5]],
        ];
    }

    /**
     * WHO height-for-age median and -2SD values by month (0–24 months).
     * Format: month => ['male' => [median, sd2_minus], 'female' => [median, sd2_minus]]
     */
    public static function heightForAge(): array
    {
        return [
            0  => ['male' => [49.9, 46.1], 'female' => [49.1, 45.4]],
            1  => ['male' => [54.7, 50.8], 'female' => [53.7, 49.8]],
            2  => ['male' => [58.4, 54.4], 'female' => [57.1, 53.0]],
            3  => ['male' => [61.4, 57.3], 'female' => [59.8, 55.6]],
            4  => ['male' => [63.9, 59.7], 'female' => [62.1, 57.8]],
            5  => ['male' => [65.9, 61.7], 'female' => [64.0, 59.6]],
            6  => ['male' => [67.6, 63.3], 'female' => [65.7, 61.2]],
            7  => ['male' => [69.2, 64.8], 'female' => [67.3, 62.7]],
            8  => ['male' => [70.6, 66.2], 'female' => [68.7, 64.0]],
            9  => ['male' => [72.0, 67.5], 'female' => [70.1, 65.3]],
            10 => ['male' => [73.3, 68.7], 'female' => [71.5, 66.5]],
            11 => ['male' => [74.5, 69.9], 'female' => [72.8, 67.7]],
            12 => ['male' => [75.7, 71.0], 'female' => [74.0, 68.9]],
            15 => ['male' => [79.1, 74.0], 'female' => [77.5, 72.4]],
            18 => ['male' => [82.3, 76.9], 'female' => [80.7, 75.2]],
            21 => ['male' => [85.1, 79.6], 'female' => [83.7, 77.5]],
            24 => ['male' => [87.8, 82.3], 'female' => [86.4, 80.8]],
        ];
    }

    /**
     * Find the closest month entry in the reference table.
     */
    public static function closestMonth(array $table, int $ageMonths): ?array
    {
        $keys = array_keys($table);
        $closest = null;
        $minDiff = PHP_INT_MAX;

        foreach ($keys as $month) {
            $diff = abs($month - $ageMonths);
            if ($diff < $minDiff) {
                $minDiff = $diff;
                $closest = $month;
            }
        }

        return $closest !== null ? $table[$closest] : null;
    }

    /**
     * Classify weight-for-age.
     * Returns: 'Normal' | 'At Risk' | 'Underweight'
     */
    public static function classifyWeight(
        float $weightKg,
        int $ageMonths,
        string $gender
    ): string {
        $table   = self::weightForAge();
        $ref     = self::closestMonth($table, $ageMonths);
        $gKey    = strtolower($gender) === 'male' ? 'male' : 'female';

        if (!$ref) return 'Normal';

        [$median, $sd2minus] = $ref[$gKey];
        $sd1minus = $median - (($median - $sd2minus) * 0.5);

        if ($weightKg < $sd2minus)  return 'Underweight';
        if ($weightKg < $sd1minus)  return 'At Risk';
        return 'Normal';
    }

    /**
     * Classify height-for-age.
     * Returns: 'Normal' | 'At Risk' | 'Stunted'
     */
    public static function classifyHeight(
        float $heightCm,
        int $ageMonths,
        string $gender
    ): string {
        $table   = self::heightForAge();
        $ref     = self::closestMonth($table, $ageMonths);
        $gKey    = strtolower($gender) === 'male' ? 'male' : 'female';

        if (!$ref) return 'Normal';

        [$median, $sd2minus] = $ref[$gKey];
        $sd1minus = $median - (($median - $sd2minus) * 0.5);

        if ($heightCm < $sd2minus) return 'Stunted';
        if ($heightCm < $sd1minus) return 'At Risk';
        return 'Normal';
    }

    /**
     * Build Chart.js dataset arrays for WHO reference bands
     * across the child's age range.
     */
    public static function chartBands(string $gender, string $type): array
    {
        $table = $type === 'weight'
            ? self::weightForAge()
            : self::heightForAge();

        $gKey    = strtolower($gender) === 'male' ? 'male' : 'female';
        $labels  = [];
        $median  = [];
        $sd2     = [];

        foreach ($table as $month => $values) {
            $labels[] = $month . ' mo';
            $median[] = $values[$gKey][0];
            $sd2[]    = $values[$gKey][1];
        }

        return compact('labels', 'median', 'sd2');
    }
}