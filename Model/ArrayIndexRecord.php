<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Model;

use MageOS\Indexer\Api\IndexRecordMutable;
use Traversable;

class ArrayIndexRecord implements IndexRecordMutable
{
    public function __construct(
        private array $data = [],
        private array $scopeData = []
    )
    {

    }

    public function reset(): void
    {
        $this->data = [];
        $this->scopeData = [];
    }

    public function set(int $entityId, array $data): void
    {
        $this->data[$entityId] = $data;
    }

    public function add(int $entityId, string $field, mixed $value): void
    {
        $this->data[$entityId][$field] = $value;
    }

    public function append(int $entityId, string $field, string $key, mixed $value): void
    {
        $this->data[$entityId][$field][$key] = $value;
    }

    public function addInScope(int $entityId, int $storeId, string $field, mixed $value): void
    {
        if (!isset($this->data[$entityId])) {
            return;
        }

        $this->scopeData[$entityId][$storeId][$field] = $value;
    }

    public function getInScope(int $entityId, int $scopeId, string $field): mixed
    {
        return $this->scopeData[$entityId][$scopeId][$field]
            ?? $this->scopeData[$entityId][0][$field]
            ?? null;
    }

    public function get(int $entityId, string $field): mixed
    {
        return $this->data[$entityId][$field] ?? null;
    }

    public function listEntityIds(): iterable
    {
        foreach ($this->data as $entityId => $item) {
            yield $entityId;
        }
    }
}
