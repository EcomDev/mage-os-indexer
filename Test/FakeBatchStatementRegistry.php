<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Test;

use MageOS\Indexer\Model\ResourceModel\BatchStatementRegistry;

class FakeBatchStatementRegistry implements BatchStatementRegistry
{
    private array $insertStatements = [];
    private array $executedInsertStatements = [];

    private array $deleteStatements = [];
    private array $executedDeleteStatements = [];

    public function hasInsertStatement(string $tableName, int $batchSize): bool
    {
        return isset($this->insertStatements[$tableName][$batchSize]);
    }

    public function createInsertStatement(string $tableName, int $batchSize, string $sql): void
    {
        $this->insertStatements[$tableName][$batchSize] = $sql;
    }

    public function executeInsertStatement(string $tableName, int $batchSize, array $params): void
    {
        $this->executedInsertStatements[$tableName][$batchSize] = $params;
    }


    public function hasDeleteStatement(string $tableName, int $batchSize): bool
    {
        return isset($this->deleteStatements[$tableName][$batchSize]);
    }

    public function createDeleteStatement(string $tableName, int $batchSize, string $sql): void
    {
        $this->deleteStatements[$tableName][$batchSize] = $sql;
    }

    public function executeDeleteStatement(string $tableName, int $batchSize, array $params): void
    {
        $this->executedDeleteStatements[$tableName][$batchSize] = $params;
    }

    public function withInsertStatement(string $tableName, int $batchSize, string $sql): self
    {
        $registry = clone $this;
        $registry->createInsertStatement($tableName, $batchSize, $sql);

        return $registry;
    }

    public function withInsertExecuted(string $tableName, int $batchSize, array $parameters): self
    {
        $registry = clone $this;
        $registry->executeInsertStatement($tableName, $batchSize, $parameters);

        return $registry;
    }

    public function withDeleteStatement(string $tableName, int $batchSize, string $sql): self
    {
        $registry = clone $this;
        $registry->createDeleteStatement($tableName, $batchSize, $sql);

        return $registry;
    }

    public function withDeleteExecuted(string $tableName, int $batchSize, array $parameters): self
    {
        $registry = clone $this;
        $registry->executeDeleteStatement($tableName, $batchSize, $parameters);

        return $registry;
    }

    public static function create(): self
    {
        return new self();
    }
}
