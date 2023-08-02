<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Api;

interface IndexRecordMutableData extends IndexRecordData
{
    public function setValue(int $entityId, array $data): void;

    public function addValue(int $entityId, string $field, mixed $value): void;

    public function extendValue(int $entityId, string $field, string $key, mixed $value): void;

    public function addScopeValue(int $entityId, int $storeId, string $field, mixed $value): void;
}
