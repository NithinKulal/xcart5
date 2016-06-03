<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Import\Step;

/**
 * Import step
 */
class Import extends \XLite\Logic\Import\Step\Base\DataStep
{

    /**
     * Get final note
     *
     * @return string
     */
    public function getFinalNote()
    {
        return static::t('Imported');
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return static::t('Importing data...');
    }

    /**
     * Process row
     *
     * @return boolean
     */
    public function process()
    {
        return $this->getProcessor()->processCurrentRow(\XLite\Logic\Import\Processor\AProcessor::MODE_IMPORT);
    }

    /**
     * Initialize
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->importer->getOptions()->columnsMetaData = array();

        foreach ($this->importer->getProcessors() as $processor) {
            $processor->markAllImagesAsProcessed();
        }
    }

    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
        parent::finalize();

        if ($this->getOptions()->clearImportDir) {
            $this->importer->deleteAllFiles();
        }
    }

    /**
     * Get error language label
     *
     * @return array
     */
    public function getErrorLanguageLabel()
    {
        $options = $this->getOptions();

        return static::t(
            'Lines imported: X out of Y with errors',
            array(
                'X'      => $options->position,
                'Y'      => $options->rowsCount,
                'errors' => $options->errorsCount,
                'warns'  => $options->warningsCount,
            )
        );
    }

    /**
     * Get normal language label
     *
     * @return array
     */
    public function getNormalLanguageLabel()
    {
        $options = $this->getOptions();

        return static::t(
            'Lines imported: X out of Y',
            array(
                'X' => $options->position,
                'Y' => $options->rowsCount,
            )
        );
    }

    // {{{ Result messages

    /**
     * Get messages
     *
     * @return array
     */
    public function getMessages()
    {
        $list = parent::getMessages();

        $data = $this->getOptions()->columnsMetaData;
        if ($data) {
            foreach (\XLite\Logic\Import\Importer::getProcessorList() as $processor) {
                if (isset($data[$processor])) {
                    $message = $processor::getResultMessage($data[$processor]);

                    if ($message) {
                        $list[] = $message;
                    }
                }
            }
        }

        return $list;
    }

    // }}}

}
