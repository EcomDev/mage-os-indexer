<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Api;

interface IndexRecord
{
    /**
     * Lists all entity within index record
     *
     * @return int[]
     */
    public function listEntityIds(): iterable;

    /**
     * Retrieves entity field in specific scope
     *
     * Should return value from scopeId 0 if it does not exist for current scope
     */
    public function getInScope(int $entityId, int $scopeId, string $field): mixed;

    /**
     * Retrieves entity field in global scope
     */
    public function get(int $entityId, string $field): mixed;
}
