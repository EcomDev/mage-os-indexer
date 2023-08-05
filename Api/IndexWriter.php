<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Api;

/**
 * Writer for index generator
 *
 * Should store generated index rows into items
 */
interface IndexWriter
{
    public function add($row): void;
}
