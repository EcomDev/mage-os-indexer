<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Test\Unit\Model;

use MageOS\Indexer\Api\IndexGenerator;
use MageOS\Indexer\Api\IndexRecord;
use MageOS\Indexer\Api\IndexRecordLoader;
use MageOS\Indexer\Api\IndexRecordMutable;
use MageOS\Indexer\Api\IndexWriter;
use MageOS\Indexer\Model\ArrayIndexRecordFactory;
use MageOS\Indexer\Api\IndexGenerationObserver;
use MageOS\Indexer\Api\IndexScope;
use MageOS\Indexer\Api\IndexScopeProvider;
use MageOS\Indexer\Api\IndexStorageWriter;
use MageOS\Indexer\Api\IndexStorageWriterFactory;
use MageOS\Indexer\Model\Indexer;
use MageOS\Indexer\Model\MinMaxIndexRangeGenerator;
use PHPUnit\Framework\TestCase;

class IndexerTest extends TestCase
    implements IndexScopeProvider,
    IndexRecordLoader,
    IndexGenerationObserver,
    IndexGenerator,
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
            new MinMaxIndexRangeGenerator(1, 7),
            $this,
            new ArrayIndexRecordFactory(),
            3,
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
                ['full', $this->scopes['one'], 1],
                ['full', $this->scopes['one'], 2],
                ['full', $this->scopes['one'], 3],
                ['full', $this->scopes['one'], 4],
                ['full', $this->scopes['one'], 5],
                ['full', $this->scopes['one'], 6],
                ['full', $this->scopes['one'], 7],
                ['finish_full'],
                ['full', $this->scopes['two'], 1],
                ['full', $this->scopes['two'], 2],
                ['full', $this->scopes['two'], 3],
                ['full', $this->scopes['two'], 4],
                ['full', $this->scopes['two'], 5],
                ['full', $this->scopes['two'], 6],
                ['full', $this->scopes['two'], 7],
                ['finish_full'],
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
                ['clear', [1]],
                ['partial', $this->scopes['one'], 1],
                ['finish_partial'],
                ['clear', [1]],
                ['partial', $this->scopes['two'], 1],
                ['finish_partial'],
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
                ['clear', [1, 2]],
                ['partial', $this->scopes['one'], 1],
                ['partial', $this->scopes['one'], 2],
                ['finish_partial'],
                ['clear', [1, 2]],
                ['partial', $this->scopes['two'], 1],
                ['partial', $this->scopes['two'], 2],
                ['finish_partial'],
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
                ['clear', [3, 4]],
                ['partial', $this->scopes['one'], 3],
                ['partial', $this->scopes['one'], 4],
                ['finish_partial'],
                ['clear', [3, 4]],
                ['partial', $this->scopes['two'], 3],
                ['partial', $this->scopes['two'], 4],
                ['finish_partial'],
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
                ['clear', [3, 4]],
                ['partial', $this->scopes['one'], 3],
                ['partial', $this->scopes['one'], 4],
                ['finish_partial'],
                ['clear', [3, 4]],
                ['partial', $this->scopes['two'], 3],
                ['partial', $this->scopes['two'], 4],
                ['finish_partial'],
                ['clear', [1, 2]],
                ['partial', $this->scopes['one'], 1],
                ['partial', $this->scopes['one'], 2],
                ['finish_partial'],
                ['clear', [1, 2]],
                ['partial', $this->scopes['two'], 1],
                ['partial', $this->scopes['two'], 2],
                ['finish_partial'],
                ['clear', [1]],
                ['partial', $this->scopes['one'], 1],
                ['finish_partial'],
                ['clear', [1]],
                ['partial', $this->scopes['two'], 1],
                ['finish_partial'],
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
                ['clear', [3, 4]],
                ['partial', $this->scopes['one'], 3],
                ['partial', $this->scopes['one'], 4],
                ['finish_partial'],
                ['clear', [3, 4]],
                ['partial', $this->scopes['two'], 3],
                ['partial', $this->scopes['two'], 4],
                ['finish_partial'],
            ],
            $this->actions
        );
    }

    /** @test */
    public function splitsIndexationByIdsByMaximumBatchSize()
    {
        $this->indexer->execute([1, 2, 3, 4, 5, 6, 7]);

        $this->assertSame(
            [
                ['clear', [1, 2, 3]],
                ['partial', $this->scopes['one'], 1],
                ['partial', $this->scopes['one'], 2],
                ['partial', $this->scopes['one'], 3],
                ['clear', [4, 5, 6]],
                ['partial', $this->scopes['one'], 4],
                ['partial', $this->scopes['one'], 5],
                ['partial', $this->scopes['one'], 6],
                ['clear', [7]],
                ['partial', $this->scopes['one'], 7],
                ['finish_partial'],
                ['clear', [1, 2, 3]],
                ['partial', $this->scopes['two'], 1],
                ['partial', $this->scopes['two'], 2],
                ['partial', $this->scopes['two'], 3],
                ['clear', [4, 5, 6]],
                ['partial', $this->scopes['two'], 4],
                ['partial', $this->scopes['two'], 5],
                ['partial', $this->scopes['two'], 6],
                ['clear', [7]],
                ['partial', $this->scopes['two'], 7],
                ['finish_partial'],
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
        return new class($this->actions) implements IndexStorageWriter {

            public function __construct(private array &$actions)
            {

            }

            public function add($row): void
            {
                $this->actions[] = ['partial', $row['scope'], $row['entity_id']];
            }

            public function clear(array $entityIds): void
            {
                $this->actions[] = ['clear', $entityIds];
            }

            public function finish(): void
            {
                $this->actions[] = ['finish_partial'];
            }
        };
    }

    public function createFullReindex(IndexScope $indexScope): IndexStorageWriter
    {
        return new class($this->actions) implements IndexStorageWriter {
            public function __construct(private array &$actions)
            {

            }

            public function add($row): void
            {
                $this->actions[] = ['full', $row['scope'], $row['entity_id']];
            }

            public function clear(array $entityIds): void
            {
                $this->actions[] = ['clear', $entityIds];
            }

            public function finish(): void
            {
                $this->actions[] = ['finish_full'];
            }
        };
    }

    public function loadByRange(IndexScope $indexScope, IndexRecordMutable $data, int $minEntityId, int $maxEntityId): void
    {
        foreach (range($minEntityId, $maxEntityId) as $entityId) {
            $data->set($entityId, ['type' => 'range']);
        }
    }

    public function loadByIds(IndexScope $indexScope, IndexRecordMutable $data, array $entityIds): void
    {
        foreach ($entityIds as $entityId) {
            $data->set($entityId, ['type' => 'partial']);
        }
    }

    public function process(IndexScope $scope, IndexRecord $record, IndexWriter $writer)
    {
        foreach ($record->listEntityIds() as $entityId) {
            $writer->add(['entity_id' => $entityId, 'scope' => $scope]);
        }
    }
}
