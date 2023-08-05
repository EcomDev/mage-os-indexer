<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Api;

interface IndexRecordLoader
{
    /**
     * Loads data into index record by entity id range
     */
    public function loadByRange(
        IndexScope         $indexScope,
        IndexRecordMutable $data,
        int                $minEntityId,
        int                $maxEntityId
    ): void;

    /**
     * Loads data into index record by entity ids
     *
     * @param int[] $entityIds
     */
    public function loadByIds(
        IndexScope         $indexScope,
        IndexRecordMutable $data,
        array              $entityIds
    ): void;
}
