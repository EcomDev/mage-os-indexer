<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Test\Unit\Model\ResourceModel;

use MageOS\Indexer\Model\ResourceModel\BatchInsertStatementRegistry;

class FakeBatchInsertStatementRegistry implements BatchInsertStatementRegistry
{
    private array $statements = [];
    private array $executedStatements = [];

    public function hasStatement(string $tableName, int $batchSize): bool
    {
        return isset($this->statements[$tableName][$batchSize]);
    }

    public function createStatement(string $tableName, int $batchSize, string $sql): void
    {
        $this->statements[$tableName][$batchSize] = $sql;
    }

    public function executeStatement(string $tableName, int $batchSize, array $params): void
    {
        $this->executedStatements[$tableName][$batchSize] = $params;
    }

    public static function create(): self
    {
        return new self();
    }

    public function withStatement(string $tableName, int $batchSize, string $sql): self
    {
        $registry = clone $this;
        $registry->createStatement($tableName, $batchSize, $sql);

        return $registry;
    }

    public function withExecuted(string $tableName, int $batchSize, array $parameters): self
    {
        $registry = clone $this;
        $registry->executeStatement($tableName, $batchSize, $parameters);

        return $registry;
    }
}
