<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Model\ResourceModel;

interface BatchInsertStatementRegistry
{
    public function hasStatement(string $tableName, int $batchSize): bool;

    public function createStatement(string $tableName, int $batchSize, string $sql): void;

    public function executeStatement(string $tableName, int $batchSize, array $params): void;
}
