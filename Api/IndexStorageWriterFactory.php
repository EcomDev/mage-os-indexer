<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Api;

interface IndexStorageWriterFactory
{
    /**
     * Creates full reindex writer
     *
     * Should create real index storage for each scope dimension
     * Upon completion will switch real index storage with new one
     */
    public function createFullReindex(IndexScope $indexScope): IndexStorageWriter;

    /**
     * Creates partial reindex writer
     *
     * Should re-use live index storage for each scope dimension
     */
    public function createPartialReindex(IndexScope $indexScope): IndexStorageWriter;
}
