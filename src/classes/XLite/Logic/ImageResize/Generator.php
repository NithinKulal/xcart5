<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\ImageResize;

/**
 * ImageResize
 */
class Generator extends \XLite\Base implements \SeekableIterator, \Countable
{
    const MODEL_PRODUCT = 'XLite\Model\Image\Product\Image';
    const MODEL_CATEGORY = 'XLite\Model\Image\Category\Image';

    /**
     * Default export process tick duration
     */
    const DEFAULT_TICK_DURATION = 0.5;

    /**
     * Image sizes
     *
     * @var array
     */
    protected static $imageSizes = array();

    /**
     * Image sizes cache
     *
     * @var array
     */
    protected static $imageSizesCache = null;

    /**
     * Options
     *
     * @var \ArrayObject
     */
    protected $options;

    /**
     * Returns available image sizes
     *
     * @return array
     */
    public static function defineImageSizes()
    {
        return array(
            static::MODEL_PRODUCT => array(
                'XXSThumbnail'     => array(40, 40),
                'XSThumbnail'      => array(60, 60), // Product thumbnail in the list of detailed images (details page)
                'SMThumbnail'      => array(80, 80), // Product thumbnail on the cart items list
                'MDThumbnail'      => array(122, 122),
                'CommonThumbnail'  => array(110, 110), // Products list thumbnail (mainly for sidebar lists)
                'SBSmallThumbnail' => array(160, 160), // Sidebar products list small thumbnail
                'SBBigThumbnail'   => array(160, 160), // Sidebar products list big thumbnail
                'LGThumbnailList'  => array(160, 160), // Center products list thumbnail
                'LGThumbnailGrid'  => array(160, 160), // Center products grid thumbnail
                'Default'          => array(300, 300), // Product thumbnail on the details page
                'LGDefault'        => array(600, 600), // Product detailed image on the details page
            ),
            static::MODEL_CATEGORY => array(
                'XXSThumbnail' => array(40, 40),
                'MDThumbnail'  => array(122, 122),
                'LGThumbnail'  => array(160, 160),
                'Default'      => array(160, 160), // Category thumbnail
            )
        );
    }

    /**
     * Get list of images sizes which administrator can edit via web interface
     *
     * @return array
     */
    public static function getEditableImageSizes()
    {
        return array(
            static::MODEL_PRODUCT => array(
                'LGThumbnailList',
                'LGThumbnailGrid',
                'Default',
            ),
            static::MODEL_CATEGORY => array(
                'Default',
            )
        );
    }

    /**
     * Add new sizes (or rewrite existing)
     *
     * @param array $sizes Image sizes
     */
    public static function addImageSizes(array $sizes)
    {
        static::$imageSizes = static::mergeImageSizes(static::$imageSizes, $sizes);
    }

    /**
     * Merge two sizes arrays
     *
     * @param array $baseSizes Base sizes
     * @param array $newSizes  New sizes
     *
     * @return array
     */
    public static function mergeImageSizes(array $baseSizes, array $newSizes)
    {
        foreach ($newSizes as $model => $modelSizes) {
            if (!is_array($modelSizes)) {
                continue;
            }

            foreach ($modelSizes as $name => $size) {
                if (!isset($baseSizes[$model])) {
                    $baseSizes[$model] = array();
                }

                if (is_numeric($name)) {
                    $baseSizes[$model][] = $size;

                } else {
                    $baseSizes[$model][$name] = $size;
                }
            }
        }

        return $baseSizes;
    }

    /**
     * Returns sizes for given class
     *
     * @param string $class Class
     *
     * @return array
     */
    public static function getModelImageSizes($class)
    {
        $sizes = static::getImageSizes();

        return isset($sizes[$class]) ? $sizes[$class] : array();
    }

    /**
     * Returns all sizes
     *
     * @param string $model Model OPTIONAL
     * @param string $code  Code OPTIONAL
     *
     * @return array
     */
    public static function getImageSizes($model = null, $code = null)
    {
        if (!isset(static::$imageSizesCache)) {
            $baseSizes = static::defineImageSizes();
            static::$imageSizesCache = static::mergeImageSizes($baseSizes, static::$imageSizes);

            $dbImageSizes = static::getDbImageSizes();
            if ($dbImageSizes) {
                static::$imageSizesCache = static::mergeImageSizes(static::$imageSizesCache, $dbImageSizes);
            }
        }

        if (!is_null($model) && !is_null($code)) {
            $result = isset(static::$imageSizesCache[$model][$code]) ? static::$imageSizesCache[$model][$code] : null;

        } else {
            $result = static::$imageSizesCache;
        }

        return $result;
    }

    /**
     * Get images sizes from database
     *
     * @return array
     */
    public static function getDbImageSizes()
    {
        $result = array();

        $sizes = \XLite\Core\Layout::getInstance()->getCurrentImagesSettings();

        if ($sizes) {
            foreach ($sizes as $size) {
                $result[$size->getModel()][$size->getCode()] = array(
                    $size->getWidth(),
                    $size->getHeight(),
                );
            }
        }

        return $result;
    }

    /**
     * Run
     *
     * @param array $options Options
     *
     * @return void
     */
    public static function run(array $options)
    {
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setVar(static::getResizeCancelFlagVarName(), false);
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->initializeEventState(
            static::getEventName(),
            array('options' => $options)
        );
        call_user_func(array('\XLite\Core\EventTask', static::getEventName()));
    }

    /**
     * Cancel
     *
     * @return void
     */
    public static function cancel()
    {
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setVar(static::getResizeCancelFlagVarName(), true);
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->removeEventState(static::getEventName());
    }

    /**
     * Constructor
     *
     * @param array $options Options OPTIONAL
     */
    public function __construct(array $options = array())
    {
        $this->options = array(
            'include'   => isset($options['include']) ? $options['include'] : array(),
            'position'  => isset($options['position']) ? intval($options['position']) + 1 : 0,
            'errors'    => isset($options['errors']) ? $options['errors'] : array(),
            'warnings'  => isset($options['warnings']) ? $options['warnings'] : array(),
            'time'      => isset($options['time']) ? intval($options['time']) : 0,
        ) + $options;

        $this->options = new \ArrayObject($this->options, \ArrayObject::ARRAY_AS_PROPS);

        if (0 == $this->getOptions()->position) {
            $this->initialize();
        }
    }

    /**
     * Get options
     *
     * @return \ArrayObject
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setVar(
            static::getResizeTickDurationVarName(),
            $this->count() ? round($this->getOptions()->time / $this->count(), 3) : 0
        );

        foreach ($this->getSteps() as $step) {
            $step->finalize();
        }
    }

    /**
     * Get time remain
     *
     * @return integer
     */
    public function getTimeRemain()
    {
        return $this->getTickDuration() * ($this->count() - $this->getOptions()->position);
    }

    /**
     * Get export process tick duration
     *
     * @return float
     */
    public function getTickDuration()
    {
        $result = null;
        if ($this->getOptions()->time && 1 < $this->getOptions()->position) {
            $result = $this->getOptions()->time / $this->getOptions()->position;

        } else {
            $tick = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar(static::getResizeTickDurationVarName());
            if ($tick) {
                $result = $tick;
            }
        }

        return $result ? (ceil($result * 1000) / 1000) : static::DEFAULT_TICK_DURATION;
    }

    /**
     * Initialize
     *
     * @return void
     */
    protected function initialize()
    {
    }

    // {{{ Steps

    /**
     * Get steps
     *
     * @return array
     */
    public function getSteps()
    {
        if (!isset($this->steps)) {
            $this->steps = $this->defineSteps();
            $this->processSteps();
        }

        return $this->steps;
    }

    /**
     * Get current step
     *
     * @param boolean $reset Reset flag OPTIONAL
     *
     * @return \XLite\Logic\Export\Step\AStep
     */
    public function getStep($reset = false)
    {
        if (!isset($this->currentStep) || $reset) {
            $this->currentStep = $this->defineStep();
        }

        $steps = $this->getSteps();

        return isset($this->currentStep) && isset($steps[$this->currentStep]) ? $steps[$this->currentStep] : null;
    }

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return array(
            'XLite\Logic\ImageResize\Step\Categories',
            'XLite\Logic\ImageResize\Step\Products',
        );
    }

    /**
     * Process steps
     *
     * @return void
     */
    protected function processSteps()
    {
        if ($this->getOptions()->include) {
            foreach ($this->steps as $i => $step) {
                if (!in_array($step, $this->getOptions()->include)) {
                    unset($this->steps[$i]);
                }
            }
        }

        foreach ($this->steps as $i => $step) {
            if (\XLite\Core\Operator::isClassExists($step)) {
                $this->steps[$i] = new $step($this);

            } else {
                unset($this->steps[$i]);
            }
        }

        $this->steps = array_values($this->steps);
    }

    /**
     * Define current step
     *
     * @return integer
     */
    protected function defineStep()
    {
        $currentStep = null;

        if (!\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar(static::getResizeCancelFlagVarName())) {
            $i = $this->getOptions()->position;
            foreach ($this->getSteps() as $n => $step) {
                if ($i < $step->count()) {
                    $currentStep = $n;
                    $step->seek($i);
                    break;

                } else {
                    $i -= $step->count();
                }
            }
        }

        return $currentStep;
    }

    // }}}

    // {{{ SeekableIterator, Countable

    /**
     * \SeekableIterator::seek
     *
     * @param integer $position Position
     *
     * @return void
     */
    public function seek($position)
    {
        if ($position < $this->count()) {
            $this->getOptions()->position = $position;
            $this->getStep(true);
        }
    }

    /**
     * \SeekableIterator::current
     *
     * @return \XLite\Logic\Export\Step\AStep
     */
    public function current()
    {
        return $this->getStep()->current();
    }

    /**
     * \SeekableIterator::key
     *
     * @return integer
     */
    public function key()
    {
        return $this->getOptions()->position;
    }

    /**
     * \SeekableIterator::next
     *
     * @return void
     */
    public function next()
    {
        $this->getOptions()->position++;
        $this->getStep()->next();
        if ($this->getStep()->key() >= $this->getStep()->count()) {
            $this->getStep(true);
        }
    }

    /**
     * \SeekableIterator::rewind
     *
     * @return void
     */
    public function rewind()
    {
    }

    /**
     * \SeekableIterator::valid
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->getStep() && $this->getStep()->valid() && !$this->hasErrors();
    }

    /**
     * \Counable::count
     *
     * @return integer
     */
    public function count()
    {
        if (!isset($this->countCache)) {
            $this->countCache = 0;
            foreach ($this->getSteps() as $step) {
                $this->countCache += $step->count();
            }
        }

        return $this->countCache;
    }

    // }}}

    // {{{ Error / warning routines

    /**
     * Add error
     *
     * @param string $title Title
     * @param string $body  Body
     *
     * @return void
     */
    public function addError($title, $body)
    {
        $this->getOptions()->errors[] = array(
            'title' => $title,
            'body'  => $body,
        );
    }

    /**
     * Get registered errors
     *
     * @return array
     */
    public function getErrors()
    {
        return empty($this->getOptions()->errors) ? array() : $this->getOptions()->errors;
    }

    /**
     * Check - has registered errors or not
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return !empty($this->getOptions()->errors);
    }

    // }}}

    // {{{ Service variable names

    /**
     * Get resizeTickDuration TmpVar name
     *
     * @return string
     */
    public static function getResizeTickDurationVarName()
    {
        return 'resizeTickDuration';
    }

    /**
     * Get resize cancel flag name
     *
     * @return string
     */
    public static function getResizeCancelFlagVarName()
    {
        return 'resizeCancelFlag';
    }

    /**
     * Get export event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return 'imageResize';
    }

    // }}}
}
