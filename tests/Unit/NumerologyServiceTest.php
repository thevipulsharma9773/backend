<?php

namespace Tests\Unit;

use App\Services\NumerologyService;
use PHPUnit\Framework\TestCase;

class NumerologyServiceTest extends TestCase
{
    public function test_it_calculates_life_path_and_name_numbers(): void
    {
        $service = new NumerologyService();

        $result = $service->calculate('John Doe', '1990-12-15');

        $this->assertSame(1, $result['life_path_number']);
        $this->assertSame(8, $result['name_number']);
    }
}
