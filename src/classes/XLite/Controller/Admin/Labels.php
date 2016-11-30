<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Language labels controller
 */
class Labels extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     * FIXME: to remove
     *
     * @var string
     */
    protected $params = array('target', 'code');

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), array('searchItemsList'));
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Language labels');
    }

    /**
     * Get return URL
     *
     * @return string
     */
    public function getReturnURL()
    {
        if (\XLite\Core\Request::getInstance()->action) {

            $data = array();

            if (\XLite\Core\Request::getInstance()->code) {
                $data['code'] = \XLite\Core\Request::getInstance()->code;
            }

            $url = $this->buildURL('labels', '', $data);

        } else {
            $url = parent::getReturnURL();
        }

        return $url;
    }

    /**
     * Get all active languages
     *
     * @return array
     */
    public function getLanguages()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Language')->findAddedLanguages();
    }

    /**
     * Get current language code
     *
     * @return string
     */
    public function getCode()
    {
        return \XLite\Core\Request::getInstance()->code ?: parent::getDefaultLanguage();
    }

    /**
     * Update labels
     *
     * @return void
     */
    protected function doActionUpdateItemsList()
    {
        // Update 'enabled' and 'added' properties editable in the item list
        parent::doActionUpdateItemsList();

        $requestData = \XLite\Core\Request::getInstance()->getNonFilteredData();

        $current = !empty($requestData['current']) ? $requestData['current'] : null;

        // Edit labels for current language
        if ($current && is_array($current)) {
            $this->saveLabels(
                $current,
                static::getDefaultLanguage()
            );
        }
        unset($current);

        $translated = !empty($requestData['translated']) ? $requestData['translated'] : null;
        $translateFail = false;
        if ($translated && is_array($translated)) {

            $language = \XLite\Core\Request::getInstance()->code;

            if (!$language) {

                \XLite\Core\TopMessage::addWarning(
                    'Text labels have not been updated successfully: the translation language has not been specified'
                );
                $translateFail = true;

            } elseif (!\XLite\Core\Database::getRepo('\XLite\Model\Language')->findOneByCode($language)) {

                \XLite\Core\TopMessage::addWarning(
                    'Text labels have not been updated successfully: the translation language has not been found'
                );
                $translateFail = true;

            } else {
                $this->saveLabels(
                    $translated,
                    $language
                );
            }
        }
        unset($translated);

        if (!$translateFail) {
            \XLite\Core\TopMessage::addInfo('Text labels have been updated successfully');
        }
    }

    /**
     * Add label
     *
     * @return void
     */
    protected function doActionAdd()
    {
        $requestData = \XLite\Core\Request::getInstance()->getNonFilteredData();

        $name = substr($requestData['name'], 0, 255);
        $label = $requestData['label'];

        if (!$name) {
            $this->valid = false;
            \XLite\Core\TopMessage::addError(
                'The text label has not been added, because its name has not been specified'
            );

        } elseif (\XLite\Core\Database::getRepo('\XLite\Model\LanguageLabel')->findOneByName($name)) {
            $this->valid = false;
            \XLite\Core\TopMessage::addError(
                'The text label has not been added, because such a text label already exists'
            );

        } else {

            $lbl = new \XLite\Model\LanguageLabel();
            $lbl->setName($name);

            foreach ($label as $code => $text) {
                if (!empty($text)) {
                    $lbl->setEditLanguage($code)->setLabel($text);
                }
            }

            $lbl = \XLite\Core\Database::getRepo('\XLite\Model\LanguageLabel')->insert($lbl);

            if ($lbl && $lbl->getLabelId()) {

                // Save added label ID in session
                $addedLabels = \XLite\Core\Session::getInstance()->added_labels;

                if (is_array($addedLabels)) {
                    array_push($addedLabels, $lbl->getLabelId());

                } else {
                    $addedLabels =  array($lbl->getLabelId());
                }

                \XLite\Core\Session::getInstance()->added_labels = $addedLabels;
                $this->setHardRedirect();
            }

            \XLite\Core\Translation::getInstance()->reset();

            \XLite\Core\TopMessage::addInfo('The text label has been added successfully');
        }
    }

    /**
     * Edit label
     *
     * @return void
     */
    protected function doActionEdit()
    {
        $requestData = \XLite\Core\Request::getInstance()->getNonFilteredData();

        $label = $requestData['label'];
        $labelId = intval(\XLite\Core\Request::getInstance()->label_id);
        $lbl = \XLite\Core\Database::getRepo('\XLite\Model\LanguageLabel')->find($labelId);

        if (!$lbl) {
            \XLite\Core\TopMessage::addError('The edited language has not been found');

        } else {
            $objects = array('insert' => array(), 'delete' => array());
            
            foreach ($label as $code => $text) {
                if (!empty($text)) {
                    $translation = $lbl->getTranslation($code);
                    $translation->setLabel($text);

                    $objects['insert'][] = $translation;
                    
                } elseif ($lbl->hasTranslation($code)) {
                    $objects['delete'][] = $lbl->getTranslation($code);
                }   
            }

            \XLite\Core\Translation::getInstance()->reset();
            \XLite\Core\TopMessage::addInfo('The text label has been modified successfully');

            \XLite\Core\Database::getRepo('\XLite\Model\LanguageLabel')->insertInBatch($objects['insert']);
            \XLite\Core\Database::getRepo('\XLite\Model\LanguageLabel')->deleteInBatch($objects['delete']);

            $this->setSilenceClose();
        }
    }

    /**
     * Save labels from array
     *
     * @param array  $values Array
     * @param string $code   Language code
     *
     * @return void
     */
    protected function saveLabels(array $values, $code)
    {
        $labels = \XLite\Core\Database::getRepo('\XLite\Model\LanguageLabel')->findByIds(
            array_keys($values)
        );

        $list = array();

        foreach ($labels as $label) {
            if (!empty($values[$label->getLabelId()])) {
                $label->setEditLanguage($code)->setLabel($values[$label->getLabelId()]);
                $list[] = $label;

            } elseif ($label->hasTranslation($code)) {
                $translation = $label->getTranslation($code);
                $label->getTranslations()->removeElement($translation);
                \XLite\Core\Database::getRepo('\XLite\Model\LanguageLabelTranslation')->delete($translation, false);
                $list[] = $label;
            }   
        }   

        \XLite\Core\Translation::getInstance()->reset();
        
        \XLite\Core\Database::getRepo('\XLite\Model\LanguageLabel')->updateInBatch($list);
    }

    protected function doNoAction()
    {
        $sessionCellName = \XLite\View\ItemsList\Model\Translation\Labels::getSearchSessionCellName();
        $sessionCell = \XLite\Core\Session::getInstance()->{$sessionCellName};

        if ($sessionCell && \XLite\Core\Request::getInstance()->substring) {
            $sessionCell['substring'] = \XLite\Core\Request::getInstance()->substring;

            \XLite\Core\Session::getInstance()->{$sessionCellName} = $sessionCell;
        }

        parent::doNoAction();
    }


}
