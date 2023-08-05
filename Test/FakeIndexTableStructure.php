<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Test;

use MageOS\Indexer\Model\ResourceModel\IndexTableStructure;

class FakeIndexTableStructure implements IndexTableStructure
{
    public function generateInsertOnDuplicate(string $tableName, int $batchSize): string
    {
        return "BATCH INSERT SQL $tableName $batchSize";
    }

    public function prepareRow(array &$batchParameters, array $row): void
    {
        foreach ($row as $value) {
            $batchParameters[] = $value;
        }
    }
}
