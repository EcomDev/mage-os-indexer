<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Test\Unit\Model\ResourceModel;

use MageOS\Indexer\Api\IndexScope;
use MageOS\Indexer\Api\IndexStorageMap;
use MageOS\Indexer\Model\ResourceModel\TableBatchIndexStorageWriter;
use MageOS\Indexer\Test\FakeBatchStatementRegistry;
use MageOS\Indexer\Test\FakeIndexTableStructure;
use PHPUnit\Framework\TestCase;

class TableBatchIndexStorageWriterTest extends TestCase
    implements IndexStorageMap
{
    private FakeBatchStatementRegistry $statementRegistry;
    private TableBatchIndexStorageWriter $batchIndexStorageWriter;

    protected function setUp(): void
    {
        $this->statementRegistry = FakeBatchStatementRegistry::create();
        $this->batchIndexStorageWriter = new TableBatchIndexStorageWriter(
            $this,
            $this->statementRegistry,
            new FakeIndexTableStructure(),
            5,
        );
    }

    /** @test */
    public function statementsAreEmptyWhenNothingIsWrittenToStorage()
    {
        $this->batchIndexStorageWriter->finish();

        $this->assertEquals(
            FakeBatchStatementRegistry::create(),
            $this->statementRegistry
        );
    }

    /** @test */
    public function executesStatementsAsSoonAsBatchSizeIsReached()
    {
        $this->batchIndexStorageWriter->add(['entity_id' => 1, 'store_id' => 2, 'visibility' => 3]);
        $this->batchIndexStorageWriter->add(['entity_id' => 2, 'store_id' => 2, 'visibility' => 4]);
        $this->batchIndexStorageWriter->add(['entity_id' => 3, 'store_id' => 2, 'visibility' => 4]);
        $this->batchIndexStorageWriter->add(['entity_id' => 4, 'store_id' => 2, 'visibility' => 2]);
        $this->batchIndexStorageWriter->add(['entity_id' => 5, 'store_id' => 2, 'visibility' => 1]);
        $this->batchIndexStorageWriter->add(['entity_id' => 6, 'store_id' => 2, 'visibility' => 3]);
        $this->batchIndexStorageWriter->add(['entity_id' => 7, 'store_id' => 2, 'visibility' => 4]);
        $this->batchIndexStorageWriter->add(['entity_id' => 8, 'store_id' => 2, 'visibility' => 4]);
        $this->batchIndexStorageWriter->add(['entity_id' => 9, 'store_id' => 2, 'visibility' => 2]);
        $this->batchIndexStorageWriter->add(['entity_id' => 10, 'store_id' => 2, 'visibility' => 1]);

        $this->assertEquals(
            FakeBatchStatementRegistry::create()
                ->withInsertStatement('storage_2', 5, 'BATCH INSERT SQL storage_2 5')
                ->withInsertExecuted('storage_2', 5,
                    [
                        1, 2, 3,
                        2, 2, 4,
                        3, 2, 4,
                        4, 2, 2,
                        5, 2, 1
                    ]
                )
                ->withInsertExecuted('storage_2', 5,
                    [
                        6, 2, 3,
                        7, 2, 4,
                        8, 2, 4,
                        9, 2, 2,
                        10, 2, 1
                    ]
                )
            ,
            $this->statementRegistry
        );
    }

    /** @test */
    public function whenBatchSizeIsNotReachedStatementIsNotExecuted()
    {
        $this->batchIndexStorageWriter->add(['entity_id' => 2, 'store_id' => 2, 'visibility' => 3]);
        $this->batchIndexStorageWriter->add(['entity_id' => 3, 'store_id' => 2, 'visibility' => 4]);
        $this->batchIndexStorageWriter->add(['entity_id' => 4, 'store_id' => 2, 'visibility' => 4]);
        $this->batchIndexStorageWriter->add(['entity_id' => 5, 'store_id' => 2, 'visibility' => 2]);

        $this->assertEquals(
            FakeBatchStatementRegistry::create(),
            $this->statementRegistry
        );
    }


    /** @test */
    public function executesBatchesForEachScopeSeparately()
    {
        $this->batchIndexStorageWriter->add(['entity_id' => 1, 'store_id' => 1, 'visibility' => 3]);
        $this->batchIndexStorageWriter->add(['entity_id' => 1, 'store_id' => 2, 'visibility' => 3]);
        $this->batchIndexStorageWriter->add(['entity_id' => 2, 'store_id' => 1, 'visibility' => 4]);
        $this->batchIndexStorageWriter->add(['entity_id' => 2, 'store_id' => 2, 'visibility' => 4]);
        $this->batchIndexStorageWriter->add(['entity_id' => 3, 'store_id' => 1, 'visibility' => 4]);
        $this->batchIndexStorageWriter->add(['entity_id' => 3, 'store_id' => 2, 'visibility' => 4]);
        $this->batchIndexStorageWriter->add(['entity_id' => 4, 'store_id' => 1, 'visibility' => 2]);
        $this->batchIndexStorageWriter->add(['entity_id' => 4, 'store_id' => 2, 'visibility' => 2]);
        $this->batchIndexStorageWriter->add(['entity_id' => 5, 'store_id' => 1, 'visibility' => 1]);
        $this->batchIndexStorageWriter->add(['entity_id' => 5, 'store_id' => 2, 'visibility' => 1]);

        $this->assertEquals(
            FakeBatchStatementRegistry::create()
                ->withInsertStatement('storage_1', 5, 'BATCH INSERT SQL storage_1 5')
                ->withInsertStatement('storage_2', 5, 'BATCH INSERT SQL storage_2 5')
                ->withInsertExecuted('storage_1', 5,
                    [
                        1, 1, 3,
                        2, 1, 4,
                        3, 1, 4,
                        4, 1, 2,
                        5, 1, 1
                    ]
                )
                ->withInsertExecuted('storage_2', 5,
                    [
                        1, 2, 3,
                        2, 2, 4,
                        3, 2, 4,
                        4, 2, 2,
                        5, 2, 1
                    ]
                )
            ,
            $this->statementRegistry
        );
    }


    /** @test */
    public function createsAndExecutesStatementsForLeftOverRowsOnFinish()
    {
        $this->batchIndexStorageWriter->add(['entity_id' => 2, 'store_id' => 1, 'visibility' => 3]);
        $this->batchIndexStorageWriter->add(['entity_id' => 3, 'store_id' => 1, 'visibility' => 4]);
        $this->batchIndexStorageWriter->add(['entity_id' => 4, 'store_id' => 2, 'visibility' => 4]);
        $this->batchIndexStorageWriter->add(['entity_id' => 5, 'store_id' => 2, 'visibility' => 4]);
        $this->batchIndexStorageWriter->add(['entity_id' => 5, 'store_id' => 3, 'visibility' => 2]);
        $this->batchIndexStorageWriter->finish();

        $this->assertEquals(
            FakeBatchStatementRegistry::create()
                ->withInsertStatement('storage_1', 2, 'BATCH INSERT SQL storage_1 2')
                ->withInsertStatement('storage_2', 2, 'BATCH INSERT SQL storage_2 2')
                ->withInsertStatement('storage_3', 1, 'BATCH INSERT SQL storage_3 1')
                ->withInsertExecuted('storage_1', 2, [2, 1, 3, 3, 1, 4])
                ->withInsertExecuted('storage_2', 2, [4, 2, 4, 5, 2, 4])
                ->withInsertExecuted('storage_3', 1, [5, 3, 2]),
            $this->statementRegistry
        );
    }

    public function getStorageByRow(array $row): string
    {
        return 'storage_' . $row['store_id'];
    }

    public function getStorageListByScope(IndexScope $scope): iterable
    {
        // TODO: Implement getStorageListByScope() method.
    }
}
