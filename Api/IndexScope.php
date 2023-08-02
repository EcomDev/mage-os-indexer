<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Api;

/**
 * Current index data scope
 *
 * Used to slice index generation into multiple clusters like:
 * `product_id`, `website_id`, `customer_group_id`
 * or
 * `product_id`, `store_id`
 */
readonly final class IndexScope
{

    private function __construct(
        public array $storeIds,
        public array $websiteIds,
        public array $customerGroupIds
    )
    {

    }

    /**
     * Creates index scope from provided values
     *
     * Typecasts non integer values if they exists
     *
     * @param int[] $storeIds
     * @param int[] $websiteIds
     * @param int[] $customerGroupIds
     */
    public static function create(array $storeIds, array $websiteIds = [], array $customerGroupIds = []): self
    {
        $storeIds = array_map('intval', $storeIds);
        $websiteIds = array_map('intval', $websiteIds);
        $customerGroupIds = array_map('intval', $customerGroupIds);

        return new self($storeIds, $websiteIds, $customerGroupIds);
    }
}
