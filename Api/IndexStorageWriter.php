<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Api;

interface IndexStorageWriter extends IndexWriter
{
    /**
     * Clear index storage by entity ids
     *
     * @param int[] $entityIds
     */
    public function clear(array $entityIds): void;

    /**
     * Finalize indexation
     */
    public function finish(): void;
}
