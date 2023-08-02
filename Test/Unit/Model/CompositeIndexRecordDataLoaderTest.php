<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Test\Unit\Model;

use MageOS\Indexer\Api\IndexRecordDataLoader;
use MageOS\Indexer\Api\IndexRecordMutableData;
use MageOS\Indexer\Api\IndexScope;
use MageOS\Indexer\Model\ArrayIndexRecordData;
use MageOS\Indexer\Model\CompositeIndexRecordDataLoader;
use PHPUnit\Framework\TestCase;

class CompositeIndexRecordDataLoaderTest extends TestCase
    implements IndexRecordDataLoader
{
    private int $timesCalled = 0;

    private CompositeIndexRecordDataLoader $recordLoader;

    protected function setUp(): void
    {
        $this->recordLoader = new CompositeIndexRecordDataLoader([
            $this,
            $this,
            $this
        ]);
    }

    /** @test */
    public function invokesLoadByRangeOnAllChildLoaders()
    {
        $arrayRecordData = new ArrayIndexRecordData();
        $this->recordLoader->loadByRange(
            IndexScope::create([]),
            $arrayRecordData,
            1,
            1000
        );

        $this->assertEquals(
            new ArrayIndexRecordData(
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
        $arrayRecordData = new ArrayIndexRecordData();
        $this->recordLoader->loadByIds(
            IndexScope::create([]),
            $arrayRecordData,
            [1, 2, 3]
        );

        $this->assertEquals(
            new ArrayIndexRecordData(
                [
                    1 => ['loadByIds' => true],
                    2 => ['loadByIds' => true],
                    3 => ['loadByIds' => true]
                ]
            ),
            $arrayRecordData,
        );
    }

    public function loadByRange(IndexScope $indexScope, IndexRecordMutableData $data, int $minEntityId, int $maxEntityId): void
    {
        $data->setValue(++$this->timesCalled, ['loadByRange' => true]);
    }

    public function loadByIds(IndexScope $indexScope, IndexRecordMutableData $data, array $entityIds): void
    {
        $data->setValue(++$this->timesCalled, ['loadByIds' => true]);
    }
}
