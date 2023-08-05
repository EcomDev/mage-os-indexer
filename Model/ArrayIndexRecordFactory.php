<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Model;

class ArrayIndexRecordFactory
{
    public function create(): ArrayIndexRecord
    {
        return new ArrayIndexRecord();
    }
}
