<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Model\ResourceModel;

interface BatchStatementRegistry
{
    public function hasInsertStatement(string $tableName, int $batchSize): bool;

    public function createInsertStatement(string $tableName, int $batchSize, string $sql): void;

    public function executeInsertStatement(string $tableName, int $batchSize, array $params): void;

    public function hasDeleteStatement(string $tableName, int $batchSize): bool;

    public function createDeleteStatement(string $tableName, int $batchSize, string $sql): void;

    public function executeDeleteStatement(string $tableName, int $batchSize, array $params): void;
}
