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
        private readonly IndexStorageMap              $indexStorageMap,
        private readonly BatchInsertStatementRegistry $batchInsertStatementRegistry,
        private readonly IndexTableStructure          $indexTableStructure,
        private readonly int                          $batchSize
    ) {

    }

    public function add($row): void
    {
        $tableName = $this->indexStorageMap->getStorageName($row);
        $this->currentBatchByTable[$tableName] = ($this->currentBatchByTable[$tableName] ?? 0) + 1;
        if (!isset($this->parametersByTable[$tableName])) {
            $this->parametersByTable[$tableName] = [];
        }

        $this->indexTableStructure->prepareRow($this->parametersByTable[$tableName], $row);

        if ($this->currentBatchByTable[$tableName] === $this->batchSize) {
            if (!$this->batchInsertStatementRegistry->hasStatement($tableName, $this->batchSize)) {
                $this->batchInsertStatementRegistry->createStatement(
                    $tableName,
                    $this->batchSize,
                    $this->indexTableStructure->generateInsertOnDuplicate($this->batchSize)
                );
            }

            $this->batchInsertStatementRegistry->executeStatement(
                $tableName,
                $this->currentBatchByTable[$tableName],
                $this->parametersByTable[$tableName]
            );

            $this->currentBatchByTable[$tableName] = 0;
            $this->parametersByTable[$tableName] = [];
        }
    }

    public function finish(): void
    {
        foreach ($this->currentBatchByTable as $tableName => $batchSize) {
            if ($batchSize === 0) {
                continue;
            }

            if (!$this->batchInsertStatementRegistry->hasStatement($tableName, $batchSize)) {
                $this->batchInsertStatementRegistry->createStatement(
                    $tableName,
                    $batchSize,
                    $this->indexTableStructure->generateInsertOnDuplicate($batchSize)
                );
            }

            $this->batchInsertStatementRegistry
                ->executeStatement($tableName, $batchSize, $this->parametersByTable[$tableName]);
        }

        $this->currentBatchByTable = [];
        $this->parametersByTable = [];
    }
}
