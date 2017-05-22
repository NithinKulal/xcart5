<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator;

/**
 * Abstract validator
 */
abstract class AValidator extends \XLite\Base\SuperClass
{
    /**
     * @var string
     */
    protected $formIdentifier;

    /**
     * Validate
     *
     * @param mixed $data Data
     *
     * @return void
     */
    abstract public function validate($data);

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Sanitize
     *
     * @param mixed $data Daa
     *
     * @return mixed
     */
    public function sanitize($data)
    {
        return $data;
    }

    /**
     * Throw error exception
     *
     * @param string $message    Message
     * @param array  $arguments  Language label arguments OPTIONAL
     * @param mixed  $pathItem   Path item key OPTIONAL
     * @param string $publicName Path item public name OPTIONAL
     *
     * @return \XLite\Core\Validator\Exception
     */
    protected function throwError($message, array $arguments = array(), $pathItem = null, $publicName = null)
    {
        $exception = new \XLite\Core\Validator\Exception($message);
        $exception->setLabelArguments($arguments);

        if (isset($pathItem)) {
            $exception->addPathItem($pathItem);
        }

        if ($publicName) {
            $exception->setPublicName($publicName);
        }

        if ($this->getFormIdentifier()) {
            $exception->setFormIdentifier(
                $this->getFormIdentifier()
            );
        }

        return $exception;
    }

    /**
     * Throw internal error exception
     *
     * @param string $message   Message
     * @param array  $arguments Language label arguments OPTIONAL
     *
     * @return \XLite\Core\Validator\Exception
     */
    protected function throwInternalError($message, array $arguments = array())
    {
        $exception = new \XLite\Core\Validator\Exception($message);
        $exception->setLabelArguments($arguments);
        $exception->markAsInternal();

        return $exception;
    }

    /**
     * @param string $formIdentifier
     */
    public function setFormIdentifier($formIdentifier)
    {
        $this->formIdentifier = $formIdentifier;
    }

    /**
     * @return string
     */
    public function getFormIdentifier()
    {
        return $this->formIdentifier;
    }
}
