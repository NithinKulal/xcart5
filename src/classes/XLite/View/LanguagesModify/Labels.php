<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LanguagesModify;

/**
 * Languages and language labels modification
 */
class Labels extends \XLite\View\Dialog
{
    /**
     * Translate language
     *
     * @var \XLite\Model\Language or false
     */
    protected $translateLanguage = null;

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'labels';

        return $result;
    }

    /**
     * Get translate language
     *
     * @return \XLite\Model\Language|void
     */
    public function getTranslatedLanguage()
    {
        if (!isset($this->translateLanguage)) {
            $this->translateLanguage = false;

            if (\XLite\Core\Request::getInstance()->language) {
                $language = \XLite\Core\Database::getRepo('\XLite\Model\Language')->findOneByCode(
                    \XLite\Core\Request::getInstance()->language
                );
                if ($language && $language->added) {
                    $this->translateLanguage = $language;
                }
            }
        }

        return $this->translateLanguage ? $this->translateLanguage : null;
    }

    /**
     * Check - is translate language selected or not
     *
     * @return boolean
     */
    public function isTranslatedLanguageSelected()
    {
        return \XLite\Core\Request::getInstance()->language && $this->getTranslatedLanguage();
    }

    /**
     * Get label translation with application default language
     *
     * @param \XLite\Model\LanguageLabel $label Label
     *
     * @return string
     */
    public function getLabelDefaultValue(\XLite\Model\LanguageLabel $label)
    {
        return $label->getTranslation(static::getDefaultLanguage())->getLabel();
    }

    /**
     * Get label translation with translate language
     *
     * @param \XLite\Model\LanguageLabel $label Label
     *
     * @return string
     */
    public function getTranslation(\XLite\Model\LanguageLabel $label)
    {
        return $this->getTranslatedLanguage()
            ? $label->getTranslation($this->getTranslatedLanguage()->code)->getLabel()
            : '';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/functions.js';

        return $list;
    }


    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/labels/body.twig';
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'languages';
    }

    /**
     * Get labels
     *
     * @return array
     */
    protected function getLabels()
    {
        $this->defineLabels();

        return $this->labels;
    }

    /**
     * Define (search) labels
     *
     * :FIXME: simplify
     *
     * @return void
     */
    protected function defineLabels()
    {
        if (!isset($this->labels)) {
            $this->labelsCount = 0;
            $this->labels = array();

            $data = \XLite\Core\Session::getInstance()->get('labelsSearch');

            if (is_array($data)) {

                // Get total count
                if (isset($data['name'])) {
                    $this->labelsCount = \XLite\Core\Database::getRepo('\XLite\Model\LanguageLabel')
                        ->countByName($data['name']);

                } else {
                    $this->labelsCount = \XLite\Core\Database::getRepo('\XLite\Model\LanguageLabel')->count();
                }

                $page = \XLite\Core\Request::getInstance()->page
                    ? \XLite\Core\Request::getInstance()->page
                    : $data['page'];

                list($this->pagesCount, $data['page']) = \XLite\Core\Operator::calculatePagination(
                    $this->labelsCount,
                    $page,
                    $this->limit
                );
                $start = ($data['page'] - 1) * $this->limit;

                // Get frame
                if (!$this->labelsCount) {
                    $this->labels = array();

                } elseif (isset($data['name'])) {
                    $this->labels = \XLite\Core\Database::getRepo('\XLite\Model\LanguageLabel')
                        ->findLikeName($data['name'], $start, $this->limit);

                } else {
                    $this->labels = \XLite\Core\Database::getRepo('\XLite\Model\LanguageLabel')
                        ->findFrame($start, $this->limit);
                }

                \XLite\Core\Session::getInstance()->set('labelsSearch', $data);
            }
        }
    }
}
