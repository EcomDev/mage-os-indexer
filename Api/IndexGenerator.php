<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Api;

use MageOS\Indexer\Model\ArrayIndexRecordData;

interface IndexGenerator
{
    public function generateRecord(
        int $entityId,
        IndexScope $indexScope,
        IndexRecordData $indexRecordData,
        IndexStorageWriter $indexStorageWriter
    );
}
