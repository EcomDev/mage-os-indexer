<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Test\Unit\Model;

use MageOS\Indexer\Api\IndexAction;
use MageOS\Indexer\Api\IndexGenerationObserver;
use MageOS\Indexer\Api\IndexScope;
use MageOS\Indexer\Api\IndexScopeProvider;
use MageOS\Indexer\Api\IndexStorageWriter;
use MageOS\Indexer\Api\IndexStorageWriterFactory;
use MageOS\Indexer\Model\Indexer;
use PHPUnit\Framework\TestCase;

class IndexerTest extends TestCase
    implements IndexScopeProvider,
        IndexAction,
        IndexGenerationObserver,
        IndexStorageWriterFactory
{
    private Indexer $indexer;

    private array $actions = [];
    private array $events = [];

    /**
     * @var IndexScope[]
     */
    private array $scopes = [];

    protected function setUp(): void
    {
        $this->indexer = new Indexer(
            $this,
            $this,
            $this,
            $this,
            3
        );

        $this->scopes['one'] = IndexScope::create([1]);
        $this->scopes['two'] = IndexScope::create([2]);
    }

    /** @test */
    public function executesActionOnFullReindexForEveryScope()
    {
        $this->indexer->executeFull();

        $this->assertSame(
            [
                ['full', $this->scopes['one']],
                ['full', $this->scopes['two']]
            ],
            $this->actions
        );
    }

    /** @test */
    public function executesIndexObserverAfterEachFullIndexationOnScope()
    {
        $this->indexer->executeFull();

        $this->assertSame(
            [
                ['before', $this->scopes['one']],
                ['after', $this->scopes['one']],
                ['before', $this->scopes['two']],
                ['after', $this->scopes['two']],
            ],
            $this->events
        );
    }

    /** @test */
    public function executesPartialActionOnSingleIdUpdate()
    {
        $this->indexer->executeRow(1);

        $this->assertEquals(
            [
                ['partial', $this->scopes['one'], [1]],
                ['partial', $this->scopes['two'], [1]]
            ],
            $this->actions
        );
    }

    /** @test */
    public function executesGenerationObserverOnSingleActionUpdate()
    {
        $this->indexer->executeRow(1);

        $this->assertSame(
            [
                ['before', $this->scopes['one']],
                ['after', $this->scopes['one']],
                ['before', $this->scopes['two']],
                ['after', $this->scopes['two']]
            ],
            $this->events
        );
    }

    /** @test */
    public function executesPartialActionOnMultipleIdUpdate()
    {
        $this->indexer->execute([1, 2]);

        $this->assertSame(
            [
                ['partial', $this->scopes['one'], [1, 2]],
                ['partial', $this->scopes['two'], [1, 2]]
            ],
            $this->actions
        );
    }

    /** @test */
    public function executesGenerationObserverOnMultipleActionUpdate()
    {
        $this->indexer->execute([3, 4]);

        $this->assertSame(
            [
                ['before', $this->scopes['one']],
                ['after', $this->scopes['one']],
                ['before', $this->scopes['two']],
                ['after', $this->scopes['two']]
            ],
            $this->events
        );
    }

    /** @test */
    public function executesPartialActionOnExecuteListUpdate()
    {
        $this->indexer->executeList([3, 4]);

        $this->assertSame(
            [
                ['partial', $this->scopes['one'], [3, 4]],
                ['partial', $this->scopes['two'], [3, 4]]
            ],
            $this->actions
        );
    }

    /** @test */
    public function executesGenerationObserverOnExecuteListUpdate()
    {
        $this->indexer->executeList([1, 2]);

        $this->assertSame(
            [
                ['before', $this->scopes['one']],
                ['after', $this->scopes['one']],
                ['before', $this->scopes['two']],
                ['after', $this->scopes['two']]
            ],
            $this->events
        );
    }

    /** @test */
    public function fixesInputDataOnPartialUpdate()
    {
        $this->indexer->execute(['3', '4']);
        $this->indexer->executeList(['1', '2']);
        $this->indexer->executeRow('1');

        $this->assertSame(
            [
                ['partial', $this->scopes['one'], [3, 4]],
                ['partial', $this->scopes['two'], [3, 4]],
                ['partial', $this->scopes['one'], [1, 2]],
                ['partial', $this->scopes['two'], [1, 2]],
                ['partial', $this->scopes['one'], [1]],
                ['partial', $this->scopes['two'], [1]]
            ],
            $this->actions
        );
    }

    /** @test */
    public function removesDuplicateIdsFromList()
    {
        $this->indexer->execute(['3', '3', '4']);

        $this->assertSame(
            [
                ['partial', $this->scopes['one'], [3, 4]],
                ['partial', $this->scopes['two'], [3, 4]],
            ],
            $this->actions
        );
    }

    /** @test */
    public function splitsIndexationByIdsByMaximumBatchSize()
    {
        $this->indexer->execute([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        $this->assertSame(
            [
                ['partial', $this->scopes['one'], [1, 2, 3]],
                ['partial', $this->scopes['one'], [4, 5, 6]],
                ['partial', $this->scopes['one'], [7, 8, 9]],
                ['partial', $this->scopes['one'], [10]],
                ['partial', $this->scopes['two'], [1, 2, 3]],
                ['partial', $this->scopes['two'], [4, 5, 6]],
                ['partial', $this->scopes['two'], [7, 8, 9]],
                ['partial', $this->scopes['two'], [10]],
            ],
            $this->actions
        );
    }

    public function getScopes(): iterable
    {
        return [
            $this->scopes['one'],
            $this->scopes['two'],
        ];
    }

    public function reindexFull(IndexScope $scope, IndexStorageWriter $writer): void
    {
        $this->actions[] = ['full', $scope];
    }

    public function reindexPartial(IndexScope $scope, IndexStorageWriter $writer, array $entityIds): void
    {
        $this->actions[] = ['partial', $scope, $entityIds];
    }

    public function beforeGeneration(IndexScope $scope)
    {
        $this->events[] = ['before', $scope];
    }

    public function afterGeneration(IndexScope $scope)
    {
        $this->events[] = ['after', $scope];
    }

    public function createPartialReindex(IndexScope $indexScope): IndexStorageWriter
    {
        return new class implements IndexStorageWriter {
            public function add($row): void {}

            public function finish(): void {}
        };
    }

    public function createFullReindex(IndexScope $indexScope): IndexStorageWriter
    {
        return new class implements IndexStorageWriter {
            public function add($row): void {}

            public function finish(): void {}
        };
    }
}
