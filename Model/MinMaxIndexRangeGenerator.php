<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Model;

readonly class MinMaxIndexRangeGenerator implements IndexRangeGenerator
{
    public function __construct(private int $start, private int $end)
    {

    }

    public function ranges(int $batchSize): iterable
    {
        for ($i = $this->start; $i <= $this->end; $i += $batchSize) {
            yield $i => min($i + $batchSize - 1, $this->end);
        }
    }
}
