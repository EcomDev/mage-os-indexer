<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Test\Unit\Model;

use MageOS\Indexer\Model\MinMaxIndexRangeGenerator;
use PHPUnit\Framework\TestCase;

class MinMaxIndexRangeGeneratorTest extends TestCase
{
    private MinMaxIndexRangeGenerator $rangeGenerator;

    protected function setUp(): void
    {
        $this->rangeGenerator = new MinMaxIndexRangeGenerator(1, 10);
    }

    /** @test */
    public function generatesOneBatchWhenStartAndEndAreBiggerThenBatchSize()
    {
        $this->assertEquals(
            [1 => 10],
            iterator_to_array(
                $this->rangeGenerator->ranges(20)
            )
        );
    }

    /** @test */
    public function splitsRangesWhenBatchSizeIsSmallerThenMaxAndMin()
    {
        $this->assertEquals(
            [1 => 3, 4 => 6, 7 => 9, 10 => 10],
            iterator_to_array(
                $this->rangeGenerator->ranges(3)
            )
        );
    }

    /** @test */
    public function whenBatchSizeIsOneItWillStillWork()
    {
        $this->assertEquals(
            [
                1 => 1,
                2 => 2,
                3 => 3,
                4 => 4,
                5 => 5,
                6 => 6,
                7 => 7,
                8 => 8,
                9 => 9,
                10 => 10
            ],
            iterator_to_array(
                $this->rangeGenerator->ranges(1)
            )
        );
    }
}
