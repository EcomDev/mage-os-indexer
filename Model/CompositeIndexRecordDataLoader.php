<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Model;

use MageOS\Indexer\Api\IndexRecordDataLoader;
use MageOS\Indexer\Api\IndexRecordMutableData;
use MageOS\Indexer\Api\IndexScope;

readonly class CompositeIndexRecordDataLoader implements IndexRecordDataLoader
{
    /**
     * @param IndexRecordDataLoader[] $loaders
     */
    public function __construct(private array $loaders) {

    }

    public function loadByRange(IndexScope $indexScope, IndexRecordMutableData $data, int $minEntityId, int $maxEntityId): void
    {
        foreach ($this->loaders as $loader) {
            $loader->loadByRange($indexScope, $data, $minEntityId, $maxEntityId);
        }
    }

    public function loadByIds(IndexScope $indexScope, IndexRecordMutableData $data, array $entityIds): void
    {
        foreach ($this->loaders as $loader) {
            $loader->loadByIds($indexScope, $data, $entityIds);
        }
    }
}
