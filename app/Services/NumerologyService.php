<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

class NumerologyService
{
    private const MASTER_NUMBERS = [11, 22];

    private const INTERPRETATIONS = [
        1 => 'Independent, ambitious, and suited to leading new beginnings.',
        2 => 'Diplomatic, intuitive, and strongest when building harmony.',
        3 => 'Expressive, optimistic, and naturally creative in communication.',
        4 => 'Grounded, disciplined, and effective when building stability.',
        5 => 'Adaptable, adventurous, and energized by change and freedom.',
        6 => 'Responsible, nurturing, and deeply aligned with service and care.',
        7 => 'Reflective, spiritual, and motivated by analysis and wisdom.',
        8 => 'Driven, strategic, and capable of strong material achievement.',
        9 => 'Compassionate, idealistic, and focused on contribution and healing.',
        11 => 'A visionary master number associated with insight and inspiration.',
        22 => 'A builder master number linked to execution at a larger scale.',
    ];

    public function calculate(string $fullName, string $dateOfBirth): array
    {
        $lifePathNumber = $this->calculateLifePathNumber($dateOfBirth);
        $nameNumber = $this->calculateNameNumber($fullName);

        return [
            'life_path_number' => $lifePathNumber,
            'life_path_interpretation' => self::INTERPRETATIONS[$lifePathNumber],
            'name_number' => $nameNumber,
            'name_number_interpretation' => self::INTERPRETATIONS[$nameNumber],
        ];
    }

    public function calculateLifePathNumber(string $dateOfBirth): int
    {
        $date = CarbonImmutable::parse($dateOfBirth);
        $digits = preg_replace('/\D/', '', $date->format('Ymd'));

        return $this->reduceNumber($this->sumDigits($digits));
    }

    public function calculateNameNumber(string $fullName): int
    {
        $normalized = Str::upper($fullName);
        $sum = 0;

        foreach (str_split($normalized) as $character) {
            if ($character < 'A' || $character > 'Z') {
                continue;
            }

            $alphabetPosition = ord($character) - 64;
            $sum += (($alphabetPosition - 1) % 9) + 1;
        }

        return $this->reduceNumber($sum);
    }

    private function reduceNumber(int $value): int
    {
        while ($value > 9 && ! in_array($value, self::MASTER_NUMBERS, true)) {
            $value = $this->sumDigits((string) $value);
        }

        return $value;
    }

    private function sumDigits(string $value): int
    {
        return collect(str_split($value))
            ->map(fn (string $digit) => (int) $digit)
            ->sum();
    }
}
