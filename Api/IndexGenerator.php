<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Api;

interface IndexGenerator
{
    public function process(
        IndexScope  $scope,
        IndexRecord $record,
        IndexWriter $writer
    );
}
