<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Test;

use MageOS\Indexer\Api\IndexStorageWriter;

class FakeIndexStorageWriter implements IndexStorageWriter
{
    private array $pendingRows = [];
    private array $finishedRows = [];

    public function add($row): void
    {
        $this->pendingRows[] = $row;
    }

    public function clear(array $entityIds): void
    {
        // TODO: Implement clear() method.
    }

    public function finish(): void
    {
        $this->finishedRows[] = $this->pendingRows;
        $this->pendingRows = [];
    }

    public static function create(): self
    {
        return new self();
    }

    public function withPendingRows(array ...$row): self
    {
        $storage = clone $this;
        $storage->pendingRows = array_merge($storage->pendingRows, $row);
        return $storage;
    }

    public function withFinishedRows(array ...$row): self
    {
        $storage = clone $this;
        $storage->finishedRows[] = $row;

        return $storage;
    }
}
