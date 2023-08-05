<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Model;

interface IndexRangeGenerator
{
    public function ranges(int $batchSize): iterable;
}
