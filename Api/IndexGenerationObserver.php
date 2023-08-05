<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Api;

interface IndexGenerationObserver
{
    public function beforeGeneration(IndexScope $scope);

    public function afterGeneration(IndexScope $scope);
}
