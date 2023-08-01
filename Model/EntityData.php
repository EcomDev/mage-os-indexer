<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Model;

use Traversable;

class EntityData implements \IteratorAggregate
{
    private array $data = [];
    private array $storeData = [];

    public function getIterator(): Traversable
    {
        foreach ($this->data as $entityId => $item) {
            yield $entityId;
        }
    }

    public function add(int $entityId, array $data): void
    {
        $this->data[$entityId] = $data;
    }

    public function reset(): void
    {
        $this->data = [];
        $this->storeData = [];
    }

    public function addStoreValue(int $entityId, int $storeId, string $field, mixed $value): void
    {
        if (!isset($this->data[$entityId])) {
            return;
        }

        $this->storeData[$entityId][$storeId][$field] = $value;
    }

    public function getStoreValue(int $entityId, int $storeId, string $field): mixed
    {
        return $this->storeData[$entityId][$storeId][$field]
            ?? $this->storeData[$entityId][0][$field]
            ?? null;
    }

    public function getValue(int $entityId, string $field): mixed
    {
        return $this->data[$entityId][$field] ?? null;
    }
}
