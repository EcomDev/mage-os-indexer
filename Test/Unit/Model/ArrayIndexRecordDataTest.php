<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Test\Unit\Model;

use MageOS\Indexer\Model\ArrayIndexRecordData;
use PHPUnit\Framework\TestCase;

class ArrayIndexRecordDataTest extends TestCase
{
    private ArrayIndexRecordData $entityData;

    protected function setUp(): void
    {
        $this->entityData = new ArrayIndexRecordData();
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
        $this->entityData->setValue(1, ['sku' => 123]);
        $this->entityData->setValue(2, ['sku' => 124]);

        $this->assertEquals(
            [1, 2],
            iterator_to_array($this->entityData)
        );
    }

    /** @test */
    public function extendsMainEntityEntry()
    {
        $this->entityData->setValue(1, ['sku' => 123]);
        $this->entityData->setValue(2, ['sku' => 124]);
        $this->entityData->addValue(1, 'name', 'Name 1');

        $this->assertEquals('Name 1', $this->entityData->getValue(1, 'name'));
    }

    /** @test */
    public function extendsArrayValueOfEntity()
    {
        $this->entityData->setValue(1, ['tier_price' => []]);
        $this->entityData->extendValue(1, 'tier_price', 'all_websites', 1);
        $this->entityData->extendValue(1, 'tier_price', 'all_groups', 2);
        $this->entityData->extendValue(1, 'tier_price', 'website_1', 3);


        $this->assertEquals(
            [
                'all_websites' => 1,
                'all_groups' => 2,
                'website_1' => 3
            ],
            $this->entityData->getValue(1, 'tier_price')
        );
    }


    /** @test */
    public function dataIsResetWhenRequested()
    {
        $this->entityData->setValue(1, ['item' => 1]);
        $this->entityData->setValue(2, ['item' => 2]);
        $this->entityData->reset();

        $this->assertEquals(
            [],
            iterator_to_array($this->entityData)
        );
    }

    /** @test */
    public function takesValueFromStoreOneWhenItIsProvided()
    {
        $this->entityData->setValue(1, ['sku' => 123]);
        $this->entityData->addScopeValue(1, 0, 'name', 'Name in Store Default');
        $this->entityData->addScopeValue(1, 1, 'name', 'Name in Store 1');

        $this->assertEquals(
            'Name in Store 1',
            $this->entityData->getScopeValue(1, 1, 'name')
        );
    }

    /** @test */
    public function defaultsToDefaultStoreViewIfStoreSpecificValueIsNotFound()
    {
        $this->entityData->setValue(3, []);
        $this->entityData->addScopeValue(3, 0, 'status', 1);
        $this->entityData->addScopeValue(3, 1, 'status', 2);

        $this->assertEquals(
            1,
            $this->entityData->getScopeValue(3, 2, 'status')
        );
    }

    /** @test */
    public function defaultToNullWhenNoStoreValuesProvided()
    {
        $this->assertEquals(
            null,
            $this->entityData->getScopeValue(1, 2, 'name')
        );
    }

    /** @test */
    public function doesNotPopulateStoreValueWhenMainEntityIsNotSelected()
    {
        $this->entityData->addScopeValue(1, 2, 'name', 'Name in Store 2');

        $this->assertEquals(
            null,
            $this->entityData->getScopeValue(1, 2, 'name')
        );
    }

    /** @test */
    public function returnsDataForMainField()
    {
        $this->entityData->setValue(1, ['level' => 2, 'path' => '1/2/3']);
        $this->assertEquals(
            2,
            $this->entityData->getValue(1, 'level')
        );
    }

    /** @test */
    public function fallsBackToNullWhenEntityFieldIsNotPresent()
    {
        $this->entityData->setValue(1, ['level' => 3]);

        $this->assertEquals(
            null,
            $this->entityData->getValue(1, 'path')
        );
    }

    /** @test */
    public function storeDataIsReset()
    {
        $this->entityData->setValue(1, []);
        $this->entityData->addScopeValue(1, 0, 'status', 1);
        $this->entityData->reset();

        $this->assertEquals(
            null,
            $this->entityData->getScopeValue(1, 1, 'status')
        );
    }

}
