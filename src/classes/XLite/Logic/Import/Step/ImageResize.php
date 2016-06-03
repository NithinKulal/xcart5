<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Import\Step;

/**
 * Resize images step
 */
class ImageResize extends \XLite\Logic\Import\Step\AStep
{

    /**
     * Constructor
     *
     * @param \XLite\Logic\Import\Importer $importer Importer
     * @param integer                      $index    Step index
     *
     * @return void
     */
    public function __construct(\XLite\Logic\Import\Importer $importer, $index)
    {
        parent::__construct($importer, $index);

        \XLite\Model\Repo\Base\Image::setResizeOnlyMarkedFlag(true);
    }

    /**
     * Get final note
     *
     * @return string
     */
    public function getFinalNote()
    {
        return static::t('Images resized');
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return static::t('Resizing images...');
    }

    /**
     * Process row
     *
     * @return boolean
     */
    public function process()
    {
        $result = $this->getImageResizeGenerator()->current()->run();

        if ($result) {
            if (empty($this->getOptions()->commonData['irProcessed'])) {
                $this->getOptions()->commonData['irProcessed'] = 0;
            }

            $this->getOptions()->commonData['irProcessed']++;
        }

        return $result;
    }

    /**
     * \Counable::count
     *
     * @return integer
     */
    public function count()
    {
        if (!isset($this->getOptions()->commonData['irCount'])) {
            $this->getOptions()->commonData['irCount'] = $this->getImageResizeGenerator()->count();
        }

        return $this->getOptions()->commonData['irCount'];
    }

    /**
     * \SeekableIterator::seek
     *
     * @param integer $position Position
     *
     * @return void
     */
    public function seek($position)
    {
        parent::seek($position);

        $this->getImageResizeGenerator()->seek($position);
    }

    /**
     * Check - allowed step or not
     *
     * @return boolean
     */
    public function isAllowed()
    {
        return parent::isAllowed()
            && $this->count() > 0;
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
            'Image resized: X out of Y with errors',
            array(
                'X'      => $options->position,
                'Y'      => $this->count(),
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
            'Image resized: X out of Y',
            array(
                'X' => $options->position,
                'Y' => $this->count(),
            )
        );
    }

    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
        parent::finalize();

        $this->getImageResizeGenerator()->finalize();        
    }

    /**
     * Get messages
     *
     * @return array
     */
    public function getMessages()
    {
        $list = parent::getMessages();

        if (!empty($this->getOptions()->commonData['irProcessed'])) {
            $list[] = array(
                'text' => static::t('Images resized: {{count}}', array('count' => $this->getOptions()->commonData['irProcessed'])),
            );
        }

        return $list;
    }

    /**
     * Get image resize generator 
     * 
     * @return \XLite\Logic\ImageResize\Generator
     */
    protected function getImageResizeGenerator()
    {
        if (!isset($this->imageResizeGenerator)) {
            $this->imageResizeGenerator = new \XLite\Logic\ImageResize\Generator();
        }

        return $this->imageResizeGenerator;
    }
}
