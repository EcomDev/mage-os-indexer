<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Test\Unit\Model\ResourceModel;

use PHPUnit\Framework\TestCase;
use MageOS\Indexer\Model\ResourceModel\InsertOnDuplicateSqlGenerator;

class InsertOnDuplicateSqlGeneratorTest extends TestCase
{
    private InsertOnDuplicateSqlGenerator $insertOnDuplicateSqlGenerator;

    protected function setUp(): void
    {
        $this->insertOnDuplicateSqlGenerator = new InsertOnDuplicateSqlGenerator();
    }


    /** @test */
    public function generatesSingleRow()
    {
        $this->assertEquals(
            'INSERT INTO `table1` (`column_one`,`column_two`) VALUES (?,?)',
            $this->insertOnDuplicateSqlGenerator
                ->generate('table1', ['column_one', 'column_two'], 1)
        );
    }

    /** @test */
    public function generatesMultipleRows()
    {
        $this->assertEquals(
            'INSERT INTO `table1` (`column_one`,`column_two`) VALUES (?,?),(?,?),(?,?)',
            $this->insertOnDuplicateSqlGenerator
                ->generate('table1', ['column_one', 'column_two'], 3)
        );
    }

    /** @test */
    public function generatesSingleRowWithOnDuplicate()
    {
        $this->assertEquals(
            'INSERT INTO `table1` (`column_one`,`column_two`) VALUES (?,?) ON DUPLICATE KEY UPDATE `column_two` = VALUES(`column_two`)',
            $this->insertOnDuplicateSqlGenerator
                ->generate(
                    'table1',
                    ['column_one', 'column_two'],
                    1,
                    ['column_two']
                )
        );
    }
}
