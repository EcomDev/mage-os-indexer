<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Api;

/**
 * Mutable API of index record
 *
 * It is used in loaders and is separated to make sure index generator
 * does not rely on mutations
 */
interface IndexRecordMutable extends IndexRecord
{
    /**
     * Sets data for entity record
     *
     * If set was not called, rest of the calls should be ignored
     */
    public function set(int $entityId, array $data): void;

    /**
     * Adds field value for existing entity record
     *
     * If no entity data is available, it should be ignored
     */
    public function add(int $entityId, string $field, mixed $value): void;

    /**
     * Appends record to array entity field
     *
     * If no entity data is available, it should be ignored
     */
    public function append(int $entityId, string $field, string $key, mixed $value): void;

    /**
     * Adds field value in scope for entity
     *
     * If no entity data is available, it should be ignored
     */
    public function addInScope(int $entityId, int $storeId, string $field, mixed $value): void;
}
