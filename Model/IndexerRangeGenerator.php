<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Model\ResourceModel;

interface IndexerRangeGenerator
{
    public function ranges(int $batchSize): iterable;
}
