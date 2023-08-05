<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Api;

/**
 * Provider of independent scope for indexer
 *
 *
 */
interface IndexScopeProvider
{
    /**
     * Returns list of scope slices for an indexer
     *
     * @return IndexScope[]
     */
    public function getScopes(): iterable;
}
