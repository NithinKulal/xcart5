<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin;

/**
 * Flexy to Twig templates converter page controller
 */
class FlexyToTwig extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        $list = parent::defineFreeFormIdActions();
        $list[] = 'get_flexy_content';
        $list[] = 'save_twig_content';

        return $list;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Flexy to twig converter');
    }

    /**
     * 'Search flexy-templates' action
     *
     * @return void
     */
    protected function doActionSearchFlexy()
    {
        \XLite\Module\XC\ThemeTweaker\Core\Flexy::getInstance()->detectFlexyTemplates();

        $this->setReturnURL($this->buildURL('flexy_to_twig'));
    }

    /**
     * 'Remove flexy-templates' action
     *
     * @return void
     */
    protected function doActionRemoveFlexy()
    {
        $result = \XLite\Module\XC\ThemeTweaker\Core\Flexy::getInstance()->removeFlexyTemplates();

        if (empty($result)) {
            \XLite\Core\TopMessage::addInfo('Flexy templates have been removed');

        } else {
            if (5 < count($result)) {
                $result = array_slice($result, 0, 5);
            }
            \XLite\Core\TopMessage::addError(
                'Some flexy-templates cannot be removed. Please correct file permissions or remove them manually',
                ['list' => implode('<br />', $result)]
            );
        }

        \XLite\Module\XC\ThemeTweaker\Core\Flexy::getInstance()->detectFlexyTemplates();

        $this->setReturnURL($this->buildURL('flexy_to_twig'));
    }

    /**
     * 'Get flexy-template content' action
     *
     * @return void
     */
    protected function doActionGetFlexyContent()
    {
        $content = null;

        if (\XLite\Core\Request::getInstance()->flexyTemplate) {
            $content = \XLite\Module\XC\ThemeTweaker\Core\Flexy::getInstance()->getFlexyContent(
                \XLite\Core\Request::getInstance()->flexyTemplate
            );
        }

        $data = [
            'flexyTemplate' => \XLite\Core\Request::getInstance()->flexyTemplate,
            'content'       => $content,
        ];

        header('Content-type: application/json');
        $data = json_encode($data);
        header('Content-Length: ' . strlen($data));

        echo $data;

        exit;
    }

    /**
     * 'Save twig-template content' action
     *
     * @return void
     */
    protected function doActionSaveTwigContent()
    {
        $request = \XLite\Core\Request::getInstance()->getNonFilteredData();

        if (!empty($request['flexyTemplate']) && !empty($request['content'])) {
            $result = \XLite\Module\XC\ThemeTweaker\Core\Flexy::getInstance()->saveTwigContent(
                \XLite\Core\Request::getInstance()->flexyTemplate,
                $request['content']
            );
        }

        header('Content-type: application/json');
        $result = json_encode($result);
        header('Content-Length: ' . strlen($result));

        echo $result;

        exit;
    }
}
