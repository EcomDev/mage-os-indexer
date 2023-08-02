<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Model;

use MageOS\Indexer\Api\IndexRecordMutableData;

use Traversable;

class ArrayIndexRecordData implements \IteratorAggregate, IndexRecordMutableData
{
    public function __construct(
        private array $data = [],
        private array $scopeData = []
    ) {

    }

    public function getIterator(): Traversable
    {
        foreach ($this->data as $entityId => $item) {
            yield $entityId;
        }
    }

    public function reset(): void
    {
        $this->data = [];
        $this->scopeData = [];
    }

    public function setValue(int $entityId, array $data): void
    {
        $this->data[$entityId] = $data;
    }

    public function addValue(int $entityId, string $field, mixed $value): void
    {
        $this->data[$entityId][$field] = $value;
    }

    public function extendValue(int $entityId, string $field, string $key, mixed $value): void
    {
        $this->data[$entityId][$field][$key] = $value;
    }

    public function addScopeValue(int $entityId, int $storeId, string $field, mixed $value): void
    {
        if (!isset($this->data[$entityId])) {
            return;
        }

        $this->scopeData[$entityId][$storeId][$field] = $value;
    }

    public function getScopeValue(int $entityId, int $scopeId, string $field): mixed
    {
        return $this->scopeData[$entityId][$scopeId][$field]
            ?? $this->scopeData[$entityId][0][$field]
            ?? null;
    }

    public function getValue(int $entityId, string $field): mixed
    {
        return $this->data[$entityId][$field] ?? null;
    }
}
