<?php
/**
 * Copyright © Mage-OS Team. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace MageOS\Indexer\Model;

use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use MageOS\Indexer\Api\IndexGenerationObserver;
use MageOS\Indexer\Api\IndexGenerator;
use MageOS\Indexer\Api\IndexRecordLoader;
use MageOS\Indexer\Api\IndexScopeProvider;
use MageOS\Indexer\Api\IndexStorageWriterFactory;

readonly class Indexer
    implements MviewActionInterface, IndexerActionInterface
{
    public const DEFAULT_LIVE_INDEX_BATCH_SIZE = 1000;
    public const DEFAULT_FULL_INDEX_BATCH_SIZE = 2000;

    public function __construct(
        private IndexScopeProvider        $indexScopeProvider,
        private IndexGenerationObserver   $indexGenerationObserver,
        private IndexGenerator            $indexGenerator,
        private IndexRecordLoader         $indexRecordLoader,
        private IndexRangeGenerator       $indexRangeGenerator,
        private IndexStorageWriterFactory $indexStorageWriterFactory,
        private ArrayIndexRecordFactory   $arrayIndexRecordFactory,
        private int                       $liveIndexBatchSize = self::DEFAULT_LIVE_INDEX_BATCH_SIZE,
        private int                       $fullIndexBatchSize = self::DEFAULT_FULL_INDEX_BATCH_SIZE
    )
    {

    }

    public function executeFull()
    {
        $data = $this->arrayIndexRecordFactory->create();

        foreach ($this->indexScopeProvider->getScopes() as $scope) {
            $this->indexGenerationObserver->beforeGeneration($scope);
            $writer = $this->indexStorageWriterFactory->createFullReindex($scope);
            foreach ($this->indexRangeGenerator->ranges($this->fullIndexBatchSize) as $minEntityId => $maxEntityId) {
                $this->indexRecordLoader->loadByRange($scope, $data, $minEntityId, $maxEntityId);
                $this->indexGenerator->process($scope, $data, $writer);
                $data->reset();
            }
            $writer->finish();
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

        $data = $this->arrayIndexRecordFactory->create();

        foreach ($this->indexScopeProvider->getScopes() as $scope) {
            $this->indexGenerationObserver->beforeGeneration($scope);
            $writer = $this->indexStorageWriterFactory->createPartialReindex($scope);
            foreach ($idChunks as $ids) {
                $writer->clear($ids);
                $this->indexRecordLoader->loadByIds($scope, $data, $ids);
                $this->indexGenerator->process($scope, $data, $writer);
                $data->reset();
            }
            $writer->finish();
            $this->indexGenerationObserver->afterGeneration($scope);
        }
    }
}
