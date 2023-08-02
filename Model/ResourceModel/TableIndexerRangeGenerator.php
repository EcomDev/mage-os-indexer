<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Sql\Expression;

readonly class TableIndexerRangeGenerator implements IndexerRangeGenerator
{
    public function __construct(
        private ResourceConnection $resourceConnection,
        private string             $connectionName,
        private string             $tableName,
        private string             $primaryKey,
    ) {

    }

    public function ranges(int $batchSize): iterable
    {
        $connection = $this->resourceConnection->getConnection($this->connectionName);
        $primaryKey = $connection->quoteIdentifier($this->primaryKey);

        $select = $connection->select()
            ->from(
                $this->tableName,
                [
                    'min' => sprintf('MIN(%s)', $primaryKey),
                    'max' => sprintf('MAX(%s)', $primaryKey)
                ]
            )
            ->group(
                new Expression(sprintf(
                    'CEIL(%s / %d)', $primaryKey, $batchSize
                ))
            );

        foreach ($select->query() as $row) {
            yield (int)$row['min'] => (int)$row['max'];
        }
    }
}
