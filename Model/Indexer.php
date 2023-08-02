<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Model;

use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use MageOS\Indexer\Api\IndexAction;
use MageOS\Indexer\Api\IndexGenerationObserver;
use MageOS\Indexer\Api\IndexScopeProvider;
use MageOS\Indexer\Api\IndexStorageWriterFactory;

readonly class Indexer
    implements MviewActionInterface, IndexerActionInterface
{
    public const DEFAULT_LIVE_INDEX_BATCH_SIZE = 1000;

    public function __construct(
        private IndexScopeProvider $indexScopeProvider,
        private IndexGenerationObserver $indexGenerationObserver,
        private IndexAction $indexAction,
        private IndexStorageWriterFactory $indexStorageWriterFactory,
        private int $liveIndexBatchSize = self::DEFAULT_LIVE_INDEX_BATCH_SIZE
    ) {

    }

    public function executeFull()
    {
        foreach ($this->indexScopeProvider->getScopes() as $scope) {
            $this->indexGenerationObserver->beforeGeneration($scope);
            $this->indexAction->reindexFull(
                $scope,
                $this->indexStorageWriterFactory->createFullReindex($scope)
            );
            $this->indexGenerationObserver->afterGeneration($scope);
        }
    }

    public function executeList(array $ids)
    {
        $this->reindexByIds($ids);
    }

    public function executeRow($id)
    {
        $this->reindexByIds([$id]);
    }

    public function execute($ids)
    {
        $this->reindexByIds($ids);
    }

    private function reindexByIds(array $ids): void
    {
        $idChunks = array_chunk(
            array_values(array_unique(array_map('intval', $ids), SORT_REGULAR)),
            $this->liveIndexBatchSize
        );

        foreach ($this->indexScopeProvider->getScopes() as $scope) {
            $this->indexGenerationObserver->beforeGeneration($scope);
            foreach ($idChunks as $ids) {
                $this->indexAction->reindexPartial(
                    $scope,
                    $this->indexStorageWriterFactory->createPartialReindex($scope),
                    $ids
                );
            }
            $this->indexGenerationObserver->afterGeneration($scope);
        }
    }
}
