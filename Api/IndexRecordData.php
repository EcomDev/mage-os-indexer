<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Api;

interface IndexRecordData
{
    public function getScopeValue(int $entityId, int $scopeId, string $field): mixed;

    public function getValue(int $entityId, string $field): mixed;
}
