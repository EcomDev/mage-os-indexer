<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Model\ResourceModel;

use MageOS\Indexer\Api\IndexStorageMap;
use MageOS\Indexer\Api\IndexStorageWriter;

class TableBatchIndexStorageWriter implements IndexStorageWriter
{
    private array $parametersByTable = [];
    private array $currentBatchByTable = [];

    public function __construct(
        private readonly IndexStorageMap        $indexStorageMap,
        private readonly BatchStatementRegistry $batchInsertStatementRegistry,
        private readonly IndexTableStructure    $indexTableStructure,
        private readonly int                    $batchSize
    )
    {

    }

    public function add($row): void
    {
        $tableName = $this->indexStorageMap->getStorageByRow($row);
        $this->currentBatchByTable[$tableName] = ($this->currentBatchByTable[$tableName] ?? 0) + 1;
        if (!isset($this->parametersByTable[$tableName])) {
            $this->parametersByTable[$tableName] = [];
        }

        $this->indexTableStructure->prepareRow($this->parametersByTable[$tableName], $row);

        if ($this->currentBatchByTable[$tableName] === $this->batchSize) {
            $batchSize = $this->currentBatchByTable[$tableName];
            $this->executeBatch($tableName, $batchSize);
        }
    }

    public function clear(array $entityIds): void
    {
        // TODO: Implement clear() method.
    }

    public function finish(): void
    {
        foreach ($this->currentBatchByTable as $tableName => $batchSize) {
            if ($batchSize === 0) {
                continue;
            }

            $this->executeBatch($tableName, $batchSize);
        }
    }

    private function executeBatch(string $tableName, mixed $batchSize): void
    {
        if (!$this->batchInsertStatementRegistry->hasInsertStatement($tableName, $batchSize)) {
            $this->batchInsertStatementRegistry->createInsertStatement(
                $tableName,
                $batchSize,
                $this->indexTableStructure->generateInsertOnDuplicate($tableName, $batchSize)
            );
        }

        $this->batchInsertStatementRegistry->executeInsertStatement(
            $tableName,
            $batchSize,
            $this->parametersByTable[$tableName]
        );

        $this->currentBatchByTable[$tableName] = 0;
        $this->parametersByTable[$tableName] = [];
    }
}
