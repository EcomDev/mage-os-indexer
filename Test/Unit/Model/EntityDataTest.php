<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Test\Unit\Model;

use MageOS\Indexer\Model\EntityData;
use PHPUnit\Framework\TestCase;

class EntityDataTest extends TestCase
{
    private EntityData $entityData;

    protected function setUp(): void
    {
        $this->entityData = new EntityData();
    }

    /** @test */
    public function returnsEmptyContainer()
    {
        $this->assertEquals(
            [],
            iterator_to_array($this->entityData)
        );
    }

    /** @test */
    public function createsMainEntityEntry()
    {
        $this->entityData->add(1, ['sku' => 123]);
        $this->entityData->add(2, ['sku' => 124]);

        $this->assertEquals(
            [1, 2],
            iterator_to_array($this->entityData)
        );
    }

    /** @test */
    public function dataIsResetWhenRequested()
    {
        $this->entityData->add(1, ['item' => 1]);
        $this->entityData->add(2, ['item' => 2]);
        $this->entityData->reset();

        $this->assertEquals(
            [],
            iterator_to_array($this->entityData)
        );
    }

    /** @test */
    public function takesValueFromStoreOneWhenItIsProvided()
    {
        $this->entityData->add(1, ['sku' => 123]);
        $this->entityData->addStoreValue(1, 0, 'name', 'Name in Store Default');
        $this->entityData->addStoreValue(1, 1, 'name', 'Name in Store 1');

        $this->assertEquals(
            'Name in Store 1',
            $this->entityData->getStoreValue(1, 1, 'name')
        );
    }

    /** @test */
    public function defaultsToDefaultStoreViewIfStoreSpecificValueIsNotFound()
    {
        $this->entityData->add(3, []);
        $this->entityData->addStoreValue(3, 0, 'status', 1);
        $this->entityData->addStoreValue(3, 1, 'status', 2);

        $this->assertEquals(
            1,
            $this->entityData->getStoreValue(3, 2, 'status')
        );
    }

    /** @test */
    public function defaultToNullWhenNoStoreValuesProvided()
    {
        $this->assertEquals(
            null,
            $this->entityData->getStoreValue(1, 2, 'name')
        );
    }

    /** @test */
    public function doesNotPopulateStoreValueWhenMainEntityIsNotSelected()
    {
        $this->entityData->addStoreValue(1, 2, 'name', 'Name in Store 2');

        $this->assertEquals(
            null,
            $this->entityData->getStoreValue(1, 2, 'name')
        );
    }

    /** @test */
    public function returnsDataForMainField()
    {
        $this->entityData->add(1, ['level' => 2, 'path' => '1/2/3']);
        $this->assertEquals(
            2,
            $this->entityData->getValue(1, 'level')
        );
    }

    /** @test */
    public function fallsBackToNullWhenEntityFieldIsNotPresent()
    {
        $this->entityData->add(1, ['level' => 3]);

        $this->assertEquals(
            null,
            $this->entityData->getValue(1, 'path')
        );
    }

    /** @test */
    public function storeDataIsReset()
    {
        $this->entityData->add(1, []);
        $this->entityData->addStoreValue(1, 0, 'status', 1);
        $this->entityData->reset();

        $this->assertEquals(
            null,
            $this->entityData->getStoreValue(1, 1, 'status')
        );
    }

}
