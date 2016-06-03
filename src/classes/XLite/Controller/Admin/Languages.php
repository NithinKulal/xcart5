<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Languages page controller
 */
class Languages extends \XLite\Controller\Admin\AAdmin
{

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Languages');
    }

    /**
     * Do action update languages
     *
     * @return void
     */
    protected function doActionUpdateItemsList()
    {
        // Update 'enabled' and 'added' properties editable in the item list
        parent::doActionUpdateItemsList();

        // Update default languages settings

        $defaultCustomerLanguage = \XLite\Core\Request::getInstance()->defaultCustomer;
        $defaultAdminLanguage = \XLite\Core\Request::getInstance()->defaultAdmin;

        if ($defaultCustomerLanguage != \XLite\Core\Config::getInstance()->General->default_language) {

            $lng = \XLite\Core\Database::getRepo('XLite\Model\Language')->findOneBy(array('code' => $defaultCustomerLanguage));

            if ($lng && $lng->getEnabled()) {

                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                    array(
                        'name'     => 'default_language',
                        'category' => 'General',
                        'value'    => $defaultCustomerLanguage,
                    )
                );

            } else {
                \XLite\Core\TopMessage::addWarning('Disabled language cannot be default.');
            }
        }

        if ($defaultAdminLanguage != \XLite\Core\Config::getInstance()->General->default_admin_language) {

            $lng = \XLite\Core\Database::getRepo('XLite\Model\Language')->findOneBy(array('code' => $defaultAdminLanguage));

            if ($lng && $lng->getEnabled()) {

                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                    array(
                        'name'     => 'default_admin_language',
                        'category' => 'General',
                        'value'    => $defaultAdminLanguage,
                    )
                );

            } else {
                \XLite\Core\TopMessage::addWarning('Disabled language cannot be default.');
            }
        }
    }

    /**
     * Get CSV file for specified language
     *
     * @return void
     */
    protected function doActionGetCSV()
    {
        $code = \XLite\Core\Request::getInstance()->code;

        $lng = \XLite\Core\Database::getRepo('XLite\Model\Language')->findOneBy(array('code' => $code));

        if ($lng) {

            $labels = \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')->findLabelsTranslatedToCode($code);

            if ($labels) {

                $fileName = LC_DIR_TMP . 'labels-' . $code . '.' . time() . '.php';

                $f = fopen($fileName, 'w');

                foreach($labels as $label => $translation) {
                    $row = array(
                        $code,
                        $label,
                        $translation,
                    );
                    fputcsv($f, $row, ',');
                }

                fclose($f);

                $name = $code . '-translation-' . gmdate('Y-m-d') . '.csv';

                header('Content-Type: text/csv; charset=UTF-8');
                header('Content-Disposition: attachment; filename="' . $name . '"; modification-date="' . gmdate('r') . ';');
                header('Content-Length: ' . filesize($fileName));

                readfile($fileName);

                unlink($fileName);

                exit();

            } else {
                \XLite\Core\TopMessage::addWarning('There are no labels translated to X', array('language' => $lng->getName()));
            }

        } else {
            \XLite\Core\TopMessage::addWarning('Unknown language: X', array('code' => htmlspecialchars($code)));
        }

        $this->redirect();
    }

    /**
     * Active (add) language
     *
     * @return void
     */
    protected function doActionActive()
    {
        $id = intval(\XLite\Core\Request::getInstance()->lng_id);
        $language = \XLite\Core\Database::getRepo('\XLite\Model\Language')->find($id);

        if (!$language) {

            \XLite\Core\TopMessage::addError(
                'The language you want to add has not been found'
            );

        } elseif ($language->added) {

            \XLite\Core\TopMessage::addError(
                'The language you want to add has already been added'
            );

        } else {

            $language->added = true;
            \XLite\Core\Database::getEM()->persist($language);
            \XLite\Core\Database::getEM()->flush();

            \XLite\Core\TopMessage::addInfo(
                'The X language has been added successfully',
                array('language' => $language->name)
            );

            \XLite\Core\Translation::getInstance()->reset();
        }

        $this->setReturnURL($this->buildURL('languages'));
    }
}
