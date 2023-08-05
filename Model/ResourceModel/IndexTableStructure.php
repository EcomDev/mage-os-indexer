<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Model\ResourceModel;

interface IndexTableStructure
{
    public function generateInsertOnDuplicate(string $tableName, int $batchSize): string;

    public function prepareRow(array &$batchParameters, array $row): void;
}
