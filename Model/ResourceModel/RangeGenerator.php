<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Sql\Expression;

class RangeGenerator
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        private readonly string $connectionName
    ) {

    }

    public function createRanges(
        string $entityTable,
        string $entityIdField, int $batchSize): iterable
    {
        $connection = $this->resourceConnection->getConnection($this->connectionName);
        $entityIdField = $connection->quoteIdentifier($entityIdField);

        $select = $connection->select()
            ->from(
                $entityTable,
                [
                    'min' => sprintf('MIN(%s)', $entityIdField),
                    'max' => sprintf('MAX(%s)', $entityIdField)
                ]
            )
            ->group(
                new Expression(sprintf(
                    'CEIL(%s / %d)', $entityIdField, $batchSize
                ))
            );


        foreach ($select->query() as $row) {
            yield (int)$row['min'] => (int)$row['max'];
        }
    }
}
