<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Clean URL
 */
class CleanURL extends \XLite\View\FormField\Input\Text
{
    const PARAM_OBJECT_CLASS_NAME = 'objectClassName';
    const PARAM_OBJECT_ID_NAME    = 'objectIdName';
    const PARAM_OBJECT_ID         = 'objectId';
    const PARAM_EXTENSION         = 'extension';

    /**
     * Conflict object
     *
     * @var \XLite\Model\AEntity
     */
    protected $conflict = null;

    /**
     * Prepare request data (typecasting)
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function prepareRequestData($value)
    {
        $data = $this->getExtension()
            ? preg_replace('/\.' . $this->getExtension() . '$/', '', parent::prepareRequestData($value)) 
            : parent::prepareRequestData($value) ;

        return $data ? ($data . ($this->getExtension() ? '.' . $this->getExtension() : '')) : '';
    }

    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->prepareRequestData(parent::getValue());
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_OBJECT_CLASS_NAME => new \XLite\Model\WidgetParam\TypeString('Object class name'),
            self::PARAM_OBJECT_ID_NAME    => new \XLite\Model\WidgetParam\TypeString('Object Id name', 'id'),
            self::PARAM_OBJECT_ID    => new \XLite\Model\WidgetParam\TypeInt('Object Id'),
            self::PARAM_EXTENSION         => new \XLite\Model\WidgetParam\TypeString('Extension', ''),
        );
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return '/input/text/clean_url.twig';
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/input/text/clean_url.js';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/input/text/clean_url.css';

        return $list;
    }

    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        $rules = parent::assembleValidationRules();
        $rules[] = 'maxSize[' . $this->getParam(self::PARAM_MAX_LENGTH) . ']';

        return $rules;
    }

    /**
     * Check field validity
     *
     * @return boolean
     */
    protected function checkFieldValidity()
    {
        $result = parent::checkFieldValidity();

        if ($result
            && $this->getValue()
        ) {
            $validator = new \XLite\Core\Validator\String\CleanURL(
                false,
                null,
                $this->getParam(self::PARAM_OBJECT_CLASS_NAME),
                $this->getObjectId()
            );
            try {
                $validator->validate($this->getValue());

            } catch (\XLite\Core\Validator\Exception $exception) {
                $result = false;
                $this->errorMessage = static::t($exception->getMessage(), $exception->getLabelArguments());

                if ($exception->getData()->conflict) {
                    $this->conflict = $exception->getData()->conflict;
                }
            }
        }

        return $result;
    }

    /**
     * Check if CleanURL functionality is disabled
     *
     * @return boolean
     */
    protected function isCleanURLDisabled()
    {
        return !LC_USE_CLEAN_URLS;
    }

    /**
     * Return extension
     *
     * @return string
     */
    protected function getExtension()
    {
        return $this->getParam(static::PARAM_EXTENSION);
    }

    /**
     * Return true if extension is present
     *
     * @return boolean
     */
    protected function hasExtension()
    {
        return '' !== $this->getParam(static::PARAM_EXTENSION);
    }

    /**
     * Get fake URL
     *
     * @return string
     */
    protected function getFakeURL()
    {
        $className = $this->getParam(static::PARAM_OBJECT_CLASS_NAME);
        $id = $this->getObjectId();

        $entity = \Xlite\Core\Database::getRepo($className)->find($id);
        /** @var \XLite\Model\Repo\CleanURL $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\CleanURL');

        return $this->getPreparedURL($repo->buildFakeURL($entity));
    }

    /**
     * Get fake URL
     *
     * @return string
     */
    protected function getURL()
    {
        $className = $this->getParam(static::PARAM_OBJECT_CLASS_NAME);
        $id = $this->getObjectId();

        /** @var \XLite\Model\Repo\CleanURL $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\CleanURL');

        return $this->getPreparedURL(
            $repo->buildURL(
                $className,
                array($this->getParam(static::PARAM_OBJECT_ID_NAME) => $id)
            )
        );
    }

    /**
     * Return full URL
     *
     * @param string $uri URI
     *
     * @return string
     */
    protected function getPreparedURL($uri)
    {
        return \XLite::getInstance()->getShopURL($uri);
    }

    /**
     * Get saved value
     *
     * @return string
     */
    protected function getSavedValue()
    {
        $className = $this->getParam(static::PARAM_OBJECT_CLASS_NAME);
        $id = $this->getObjectId();

        $entity = \Xlite\Core\Database::getRepo($className)->find($id);

        return $entity ? $entity->getCleanURL() : '';
    }

    /**
     * Is conflict object present
     *
     * @return boolean
     */
    protected function hasConflict()
    {
        return isset($this->conflict);
    }

    /**
     * Is conflict in history
     *
     * @return bool
     */
    protected function isHistoryConflict()
    {
        return $this->hasConflict()
            && $this->conflict->getCleanURL() !== $this->getValue();
    }

    /**
     * Returns resolve hint
     *
     * @return string
     */
    protected function getResolveHint()
    {
        $hints = array();

        $hints[] = static::t('Enter a different Clean URL value for this page');

        if ($this->isHistoryConflict()) {
            $hints[] = static::t('Enable the option "Assign entered Clean URL to this page anyway" to dissociate the entered Clean URL from the page it is currently used for and assign it to the page of the object being edited.');
        }

        return sprintf('<ul><li>' . implode('</li><li>', $hints) . '</li></ul>');
    }

    /**
     * Returns object id
     *
     * @return integer
     */
    protected function getObjectId()
    {
        return $this->getParam(static::PARAM_OBJECT_ID)
            ? $this->getParam(static::PARAM_OBJECT_ID)
            : \XLite\Core\Request::getInstance()->{$this->getParam(static::PARAM_OBJECT_ID_NAME)};
    }
}
