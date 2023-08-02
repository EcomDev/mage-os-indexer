<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Model\ResourceModel;

use Magento\Framework\ObjectManagerInterface;
use MageOS\Indexer\Api\IndexStorageMap;

readonly class TableBatchIndexStorageWriterFactory
{
    public function __construct(private ObjectManagerInterface $objectManager)
    {
    }

    public function create(
        IndexStorageMap     $indexStorageMap,
        IndexTableStructure $indexTableStructure,
        int                 $batchSize
    ): TableBatchIndexStorageWriter {
        return $this->objectManager->create(
            TableBatchIndexStorageWriter::class,
            [
                'indexStorageMap' => $indexStorageMap,
                'indexTableStructure' => $indexTableStructure,
                'batchSize' => $batchSize
            ]
        );
    }
}
