<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Test\Unit\Model;

use MageOS\Indexer\Model\ArrayIndexRecord;
use PHPUnit\Framework\TestCase;

class ArrayIndexRecordTest extends TestCase
{
    private ArrayIndexRecord $indexRecord;

    protected function setUp(): void
    {
        $this->indexRecord = new ArrayIndexRecord();
    }

    /** @test */
    public function returnsEmptyContainer()
    {
        $this->assertEquals(
            [],
            iterator_to_array($this->indexRecord->listEntityIds())
        );
    }

    /** @test */
    public function createsMainEntityEntry()
    {
        $this->indexRecord->set(1, ['sku' => 123]);
        $this->indexRecord->set(2, ['sku' => 124]);

        $this->assertEquals(
            [1, 2],
            iterator_to_array($this->indexRecord->listEntityIds())
        );
    }

    /** @test */
    public function extendsMainEntityEntry()
    {
        $this->indexRecord->set(1, ['sku' => 123]);
        $this->indexRecord->set(2, ['sku' => 124]);
        $this->indexRecord->add(1, 'name', 'Name 1');

        $this->assertEquals('Name 1', $this->indexRecord->get(1, 'name'));
    }

    /** @test */
    public function extendsArrayValueOfEntity()
    {
        $this->indexRecord->set(1, ['tier_price' => []]);
        $this->indexRecord->append(1, 'tier_price', 'all_websites', 1);
        $this->indexRecord->append(1, 'tier_price', 'all_groups', 2);
        $this->indexRecord->append(1, 'tier_price', 'website_1', 3);


        $this->assertEquals(
            [
                'all_websites' => 1,
                'all_groups' => 2,
                'website_1' => 3
            ],
            $this->indexRecord->get(1, 'tier_price')
        );
    }


    /** @test */
    public function dataIsResetWhenRequested()
    {
        $this->indexRecord->set(1, ['item' => 1]);
        $this->indexRecord->set(2, ['item' => 2]);
        $this->indexRecord->reset();

        $this->assertEquals(
            [],
            iterator_to_array($this->indexRecord->listEntityIds())
        );
    }

    /** @test */
    public function takesValueFromStoreOneWhenItIsProvided()
    {
        $this->indexRecord->set(1, ['sku' => 123]);
        $this->indexRecord->addInScope(1, 0, 'name', 'Name in Store Default');
        $this->indexRecord->addInScope(1, 1, 'name', 'Name in Store 1');

        $this->assertEquals(
            'Name in Store 1',
            $this->indexRecord->getInScope(1, 1, 'name')
        );
    }

    /** @test */
    public function defaultsToDefaultStoreViewIfStoreSpecificValueIsNotFound()
    {
        $this->indexRecord->set(3, []);
        $this->indexRecord->addInScope(3, 0, 'status', 1);
        $this->indexRecord->addInScope(3, 1, 'status', 2);

        $this->assertEquals(
            1,
            $this->indexRecord->getInScope(3, 2, 'status')
        );
    }

    /** @test */
    public function defaultToNullWhenNoStoreValuesProvided()
    {
        $this->assertEquals(
            null,
            $this->indexRecord->getInScope(1, 2, 'name')
        );
    }

    /** @test */
    public function doesNotPopulateStoreValueWhenMainEntityIsNotSelected()
    {
        $this->indexRecord->addInScope(1, 2, 'name', 'Name in Store 2');

        $this->assertEquals(
            null,
            $this->indexRecord->getInScope(1, 2, 'name')
        );
    }

    /** @test */
    public function returnsDataForMainField()
    {
        $this->indexRecord->set(1, ['level' => 2, 'path' => '1/2/3']);
        $this->assertEquals(
            2,
            $this->indexRecord->get(1, 'level')
        );
    }

    /** @test */
    public function fallsBackToNullWhenEntityFieldIsNotPresent()
    {
        $this->indexRecord->set(1, ['level' => 3]);

        $this->assertEquals(
            null,
            $this->indexRecord->get(1, 'path')
        );
    }

    /** @test */
    public function storeDataIsReset()
    {
        $this->indexRecord->set(1, []);
        $this->indexRecord->addInScope(1, 0, 'status', 1);
        $this->indexRecord->reset();

        $this->assertEquals(
            null,
            $this->indexRecord->getInScope(1, 1, 'status')
        );
    }

}
