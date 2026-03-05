<?php

namespace Tests\Unit;

use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    public function test_calculate_order_total()
    {
        $items = [
            ['price' => 100, 'qty' => 2],
            ['price' => 50, 'qty' => 1]
        ];

        $total = 0;

        foreach ($items as $item) {
            $total += $item['price'] * $item['qty'];
        }

        $this->assertEquals(250, $total);
    }
}