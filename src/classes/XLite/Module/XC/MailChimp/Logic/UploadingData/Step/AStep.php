<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Logic\UploadingData\Step;


use XLite\Module\XC\MailChimp\Core\EventListener\UploadingData;
use XLite\Module\XC\MailChimp\Core\MailChimp;

abstract class AStep extends \XLite\Logic\ARepoStep
{
    // {{{ SeekableIterator, Countable

    /**
     * \Countable::count
     *
     * @return integer
     */
    public function count()
    {
        if (!isset($this->countCache)) {
            $options = $this->generator->getOptions();
            if (!isset($options['count' . get_class($this)])) {
                $options['count' . get_class($this)] = $this->getRepository()->countForExport();
                $this->generator->setOptions($options);
            }
            $this->countCache = $options['count' . get_class($this)];
        }

        return $this->countCache;
    }

    // }}}

    // {{{ Row processing

    /**
     * Run step
     *
     * @return boolean
     */
    public function run()
    {
        $time = microtime(true);

        $this->generator->setInProgress(true);
        
        $row = $this->getItems()->current();
        $this->processModel($row[0]);
        
        $this->generator->setInProgress(false);

        $this->generator->getOptions()->time += round(microtime(true) - $time, 3);

        return true;
    }
    
    protected function getBatchSettings()
    {
        return [
            'batchSize' => min(UploadingData::CHUNK_LENGTH, $this->count()),
            'waitSize'  => 10,
        ];
    }

    /**
     * Process model
     *
     * @param \XLite\Model\AEntity $model Model
     *
     * @return void
     */
    protected function processModel(\XLite\Model\AEntity $model)
    {
        $settings = $this->getBatchSettings();
        $this->addBatchModel($model);

        $models = $this->getBatchModels();

        if (count($models) > $settings['batchSize']) {
            $this->processBatch($models);
            $this->clearBatchModels();
        }

        $batches = $this->getBatchResults();
        if (count($batches) > $settings['waitSize']) {
            $this->waitBatchesToComplete();
            $this->clearBatchResults();
        }
    }

    /**
     * @inheritDoc
     */
    public function finalize()
    {
        parent::finalize();

        $models = $this->getBatchModels();
        if ($models) {
            $this->processBatch($models);
        }

        $this->clearBatchModels();

        $this->waitBatchesToComplete();
        $this->clearBatchResults();
    }

    /**
     * Wait for complete
     */
    protected function waitBatchesToComplete()
    {
        $maxTryCount = 10;

        $batches = array_filter($this->getBatchResults());

        foreach ($batches as $batchId) {
            for ($i = 0; $i < $maxTryCount; $i++) {
                $batchData = MailChimp::getInstance()->getBatch($batchId);

                if ($batchData['status']
                    && $batchData['status'] !== "finished"
                ) {
                    sleep(2);
                } elseif ($batchData['status'] === "finished") {
                    break;
                }
            }
        }
    }

    /**
     * @param array $models
     *
     * @return mixed
     */
    abstract protected function processBatch(array $models);

    /**
     * @param $model
     */
    public function addBatchModel($model)
    {
        $key = 'batch_data_' . get_class($this);
        $options = $this->generator->getOptions();

        if (!isset($options[$key])) {
            $options[$key] = [];
        }

        $options[$key][] = $model;
        $this->generator->setOptions($options);
    }

    /**
     * @return mixed
     */
    public function getBatchModels()
    {
        $key = 'batch_data_' . get_class($this);
        $options = $this->generator->getOptions();

        if (!isset($options[$key])) {
            $options[$key] = [];
        }

        return $options[$key];
    }

    /**
     * Clear batch models
     */
    public function clearBatchModels()
    {
        $key = 'batch_data_' . get_class($this);

        $options = $this->generator->getOptions();
        $options[$key] = [];
        $this->generator->setOptions($options);
    }

    // }}}

    // {{{ Data

    /**
     * Get items iterator
     *
     * @param boolean $reset Reset iterator OPTIONAL
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    protected function getItems($reset = false)
    {
        if (!isset($this->items) || $reset) {
            $this->items = $this->getRepository()->getExportIterator($this->position);
            $this->items->rewind();
        }

        return $this->items;
    }

    // }}}

    public function getStores()
    {
        $options = $this->generator->getOptions();

        return isset($options['stores'])
            ? $options['stores']
            : [];
    }

    /**
     * @param $id
     */
    protected function addBatchResult($id)
    {
        $options = $this->generator->getOptions();

        if (!isset($options['products_batches_results'])) {
            $options['products_batches_results'] = [];
        }

        $options['products_batches_results'][] = $id;

        $this->generator->setOptions($options);
    }

    /**
     * @return array|mixed
     */
    protected function getBatchResults()
    {
        $options = $this->generator->getOptions();
        return isset($options['products_batches_results'])
            ? $options['products_batches_results']
            : [];
    }

    /**
     * 
     */
    protected function clearBatchResults()
    {
        $options = $this->generator->getOptions();
        $options['products_batches_results'] = [];
        $this->generator->setOptions($options);
    }
}
