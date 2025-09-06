<?php

namespace App\Domain\DTO;

readonly final class SummaryDTO
{
    public function __construct(
        public string $period,
        public float  $totalHours,
        public float  $standardHours,
        public float  $overtimeHours,
        public float  $totalPay,
        public int    $baseRate,
        public ?float $overtimeRate
    ) {}

    public function toArray(): array
    {
        $response = [
            'response' => [
                'suma po przeliczeniu' => number_format($this->totalPay, 2) . ' PLN',
                'ilość godzin z danego okresu' => (float) number_format($this->totalHours, 1),
                'stawka' => $this->baseRate . ' PLN',
            ]
        ];

        if ($this->overtimeRate !== null) {
            $response['response']['ilość normalnych godzin z danego miesiąca'] = (float) number_format($this->standardHours, 1);
            $response['response']['ilość nadgodzin z danego miesiąca'] = (float) number_format($this->overtimeHours, 1);
            $response['response']['stawka nadgodzinowa'] = $this->overtimeRate . ' PLN';
        }

        return $response;
    }
}