<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Api;

/**
 * Mapper of storages to scope
 */
interface IndexStorageMap
{
    /**
     * Returns index storage name based on current row
     */
    public function getStorageByRow(array $row): string;

    /**
     * Returns all index storage tables that match current index scope
     *
     * @return string[]
     */
    public function getStorageListByScope(IndexScope $scope): iterable;
}
