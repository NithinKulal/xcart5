<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator;

/**
 * Validate exception
 */
class Exception extends \XLite\Core\Exception
{
    /**
     * Path
     *
     * @var array
     */
    protected $path = array();

    /**
     * Internal flag
     *
     * @var boolean
     */
    protected $internal = null;

    /**
     * Message label arguments
     *
     * @var array
     */
    protected $arguments = array();

    /**
     * Public name
     *
     * @var string
     */
    protected $publicName;

    /**
     * Some additional data
     *
     * @var \XLite\Core\CommonCell
     */
    protected $data;

    /**
     * Form identifier
     *
     * @var string
     */
    protected $formIdentifier;

    /**
     * Add path item
     *
     * @param mixed $item Path item key
     *
     * @return void
     */
    public function addPathItem($item)
    {
        array_unshift($this->path, $item);
    }

    /**
     * Set public name
     *
     * @param string $name Public name
     *
     * @return void
     */
    public function setPublicName($name)
    {
        $this->publicName = $name;
    }

    /**
     * Set public name
     *
     * @param $uid
     */
    public function setFormIdentifier($uid)
    {
        $this->formIdentifier = $uid;
    }

    /**
     * Get public name
     *
     * @return string
     */
    public function getPublicName()
    {
        return $this->publicName;
    }

    /**
     * Get path as string
     *
     * @return string
     */
    public function getPath()
    {
        $path = isset($this->path[0])
            ? $this->path[0]
            : [];

        if (1 < count($this->path)) {
            $path .= '[' . implode('][', array_slice($this->path, 1)) . ']';
        }

        return $path;
    }

    /**
     * Mark exception as internal error exception
     *
     * @return void
     */
    public function markAsInternal()
    {
        $this->internal = true;
    }

    /**
     * Check - exception is internal or not
     *
     * @return boolean
     */
    public function isInternal()
    {
        return $this->internal;
    }

    /**
     * Set message arguments
     *
     * @param array $arguments Arguments
     *
     * @return void
     */
    public function setLabelArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Get message arguments
     *
     * @return array
     */
    public function getLabelArguments()
    {
        return $this->arguments;
    }

    /**
     * Set additional data
     *
     * @param \XLite\Core\CommonCell $data Data
     *
     * @return void
     */
    public function setData(\XLite\Core\CommonCell $data)
    {
        $this->data = $data;
    }

    /**
     * Returns additional data
     *
     * @return \XLite\Core\CommonCell
     */
    public function getData()
    {
        return !isset($this->data)
            ? ($this->data = new \XLite\Core\CommonCell())
            : $this->data;
    }

    /**
     * @return string
     */
    public function getFormIdentifier()
    {
        return $this->formIdentifier;
    }
}
