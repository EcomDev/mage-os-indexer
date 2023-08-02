<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Api;

interface IndexAction
{
    /**
     * Reindex full index storage for provided index scope
     *
     */
    public function reindexFull(
        IndexScope $scope,
        IndexStorageWriter $writer
    ): void;

    /**
     * Reindex partial data in live index
     *
     * @param int[] $entityIds
     */
    public function reindexPartial(
        IndexScope $scope,
        IndexStorageWriter $writer,
        array $entityIds
    ): void;
}
