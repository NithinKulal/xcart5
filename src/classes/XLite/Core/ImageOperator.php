<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Image operator
 */
class ImageOperator extends \XLite\Base\SuperClass
{
    /**
     * Engine
     *
     * @var \XLite\Core\ImageOperator\AImageOperator
     */
    protected static $engine;

    /**
     * Model
     *
     * @var \XLite\Model\Base\Image
     */
    protected $model;

    /**
     * Prepared flag
     *
     * @var boolean
     */
    protected $prepared = false;


    /**
     * Call engine (static)
     *
     * @param string $method Method name
     * @param array  $args   Arguments OPTIONAL
     *
     * @return mixed
     */
    public static function __callStatic($method, array $args = array())
    {
        return call_user_func_array(array(get_class(static::getEngine()), $method), $args);
    }


    /**
     * Get engine
     *
     * @return \XLite\Core\ImageOperator\AImageOperator
     */
    protected static function getEngine()
    {
        // Binary ImageMagic
        if (!isset(static::$engine)) {
            if (\XLite\Core\ImageOperator\ImageMagic::isEnabled()) {
                static::$engine = new \XLite\Core\ImageOperator\ImageMagic;

            } elseif (\XLite\Core\ImageOperator\GD::isEnabled()) {
                static::$engine = new \XLite\Core\ImageOperator\GD;
            } else {
                static::$engine = new \XLite\Core\ImageOperator\DefaultOperator;
            }
        }

        return static::$engine;
    }

    /**
     * Constructor
     *
     * @param \XLite\Model\Base\Image $image Image
     *
     * @return void
     */
    public function __construct(\XLite\Model\Base\Image $image)
    {
        $this->model = $image;
    }

    /**
     * Call engine
     *
     * @param string $method Method name
     * @param array  $args   Arguments OPTIONAL
     *
     * @return mixed
     */
    public function __call($method, array $args = array())
    {
        $this->prepare();

        return $this->prepare() ? call_user_func_array(array(static::getEngine(), $method), $args) : false;
    }


    /**
     * Prepare image
     *
     * @return boolean
     */
    protected function prepare()
    {
        $result = true;
        if (!$this->prepared) {
            $result = static::getEngine()->setImage($this->model);
            $this->prepared = true;
        }

        return $result;
    }
}
