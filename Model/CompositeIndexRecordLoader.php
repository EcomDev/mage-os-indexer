<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Model;

use MageOS\Indexer\Api\IndexRecordLoader;
use MageOS\Indexer\Api\IndexRecordMutable;
use MageOS\Indexer\Api\IndexScope;

readonly class CompositeIndexRecordLoader implements IndexRecordLoader
{
    /**
     * @param IndexRecordLoader[] $loaders
     */
    public function __construct(private array $loaders)
    {

    }

    public function loadByRange(IndexScope $indexScope, IndexRecordMutable $data, int $minEntityId, int $maxEntityId): void
    {
        foreach ($this->loaders as $loader) {
            $loader->loadByRange($indexScope, $data, $minEntityId, $maxEntityId);
        }
    }

    public function loadByIds(IndexScope $indexScope, IndexRecordMutable $data, array $entityIds): void
    {
        foreach ($this->loaders as $loader) {
            $loader->loadByIds($indexScope, $data, $entityIds);
        }
    }
}
