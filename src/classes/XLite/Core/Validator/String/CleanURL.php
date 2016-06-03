<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator\String;

/**
 * CleanURL
 */
class CleanURL extends \XLite\Core\Validator\String\RegExp
{
    /**
     * Class name
     *
     * @var string
     */
    protected $class;

    /**
     * Entity id
     *
     * @var mixed
     */
    protected $id;

    /**
     * Constructor
     *
     * @param boolean $nonEmpty Non-empty flag OPTIONAL
     * @param string  $regExp   Regular expression OPTIONAL
     * @param string  $class    Entity class name
     * @param mixed   $id       Entity ID
     */
    public function __construct($nonEmpty = false, $regExp = null, $class = '', $id = null)
    {
        parent::__construct($nonEmpty, $this->getCleanURLPattern($class));

        if (empty($class)) {
            \Includes\ErrorHandler::fireError(
                static::t('Empty "class" parameter is passed to the {{method}}', array('method' => __METHOD__))
            );

        } else {
            $this->class = $class;
            $this->id    = $id;
        }
    }

    /**
     * Validate
     *
     * @param mixed $data Data
     *
     * @return void
     * @throws \XLite\Core\Validator\Exception
     */
    public function validate($data)
    {
        $data = $this->sanitize($data);

        if (!\XLite\Core\Converter::isEmptyString($data)) {
            parent::validate($data);

            /** @var \XLite\Model\Repo\CleanURL $repo */
            $repo = \XLite\Core\Database::getRepo('XLite\Model\CleanURL');

            $postedData = \XLite\Core\Request::getInstance()->postedData;
            if (!isset($postedData['forceCleanURL'])
                && !$repo->isURLUnique($data, $this->class, $this->id)
            ) {
                $this->throwCleanURLError($data);
            }
        }
    }

    /**
     * Sanitize
     *
     * @param mixed $data Data
     *
     * @return string
     */
    public function sanitize($data)
    {
        /** @var \XLite\Model\Repo\CleanURL $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\CleanURL');

        return substr(
            $data,
            0,
            $repo->getFieldInfo('cleanURL', 'length')
        );
    }

    /**
     * Clean URL pattern
     *
     * @param string $class Class name OPTIONAL
     *
     * @return string
     */
    protected function getCleanURLPattern($class = '')
    {
        /** @var \XLite\Model\Repo\CleanURL $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\CleanURL');

        return '/^' . $repo->getPattern($class) . '$/S';
    }

    /**
     * Wrapper
     *
     * @param mixed $data Data
     *
     * @return void
     * @throws \XLite\Core\Validator\Exception
     */
    protected function throwCleanURLError($data)
    {
        /** @var \XLite\Model\Repo\CleanURL $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\CleanURL');
        $conflict = $repo->getConflict($data, $this->class, $this->id);

        $message = $conflict && $conflict->getCleanURL() == $data
            ? 'The Clean URL entered is already in use.'
            : 'The Clean URL entered is a redirect to object.';

        $exception = $this->throwError(
            $message,
            array('entityURL' => $repo->buildEditURL($conflict))
        );

        $exception->getData()->conflict = $conflict;

        throw $exception;
    }

    /**
     * Return regexp match error message
     *
     * @return string
     */
    protected function getRegexpMatchError()
    {
        return 'Wrong format: Field contains unallowed characters';
    }
}
