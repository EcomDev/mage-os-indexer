<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Test\Unit\Model;

use MageOS\Indexer\Api\IndexRecordLoader;
use MageOS\Indexer\Api\IndexRecordMutable;
use MageOS\Indexer\Api\IndexScope;
use MageOS\Indexer\Model\ArrayIndexRecord;
use MageOS\Indexer\Model\CompositeIndexRecordLoader;
use PHPUnit\Framework\TestCase;

class CompositeIndexRecordLoaderTest extends TestCase
    implements IndexRecordLoader
{
    private int $timesCalled = 0;

    private CompositeIndexRecordLoader $recordLoader;

    protected function setUp(): void
    {
        $this->recordLoader = new CompositeIndexRecordLoader([
            $this,
            $this,
            $this
        ]);
    }

    /** @test */
    public function invokesLoadByRangeOnAllChildLoaders()
    {
        $arrayRecordData = new ArrayIndexRecord();
        $this->recordLoader->loadByRange(
            IndexScope::create([]),
            $arrayRecordData,
            1,
            1000
        );

        $this->assertEquals(
            new ArrayIndexRecord(
                [
                    1 => ['loadByRange' => true],
                    2 => ['loadByRange' => true],
                    3 => ['loadByRange' => true]
                ]
            ),
            $arrayRecordData,
        );
    }

    /** @test */
    public function invokesLoadByIdsOnAllChildLoaders()
    {
        $arrayRecordData = new ArrayIndexRecord();
        $this->recordLoader->loadByIds(
            IndexScope::create([]),
            $arrayRecordData,
            [1, 2, 3]
        );

        $this->assertEquals(
            new ArrayIndexRecord(
                [
                    1 => ['loadByIds' => true],
                    2 => ['loadByIds' => true],
                    3 => ['loadByIds' => true]
                ]
            ),
            $arrayRecordData,
        );
    }

    public function loadByRange(IndexScope $indexScope, IndexRecordMutable $data, int $minEntityId, int $maxEntityId): void
    {
        $data->set(++$this->timesCalled, ['loadByRange' => true]);
    }

    public function loadByIds(IndexScope $indexScope, IndexRecordMutable $data, array $entityIds): void
    {
        $data->set(++$this->timesCalled, ['loadByIds' => true]);
    }
}
