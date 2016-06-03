<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View;

/**
 * Page modify widget
 *
 * @Decorator\Depend ("CDev\SimpleCMS")
 */
class PageModify extends \XLite\Module\CDev\SimpleCMS\View\Model\Page implements \XLite\Base\IDecorator
{
    /**
     * Save current form reference and initialize the cache
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = array(), array $sections = array())
    {
        $this->schemaDefault['ogMeta'] = array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Simple',
            self::SCHEMA_LABEL    => 'Open graph tags',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_TRUSTED  => true,
        );
        $this->schemaDefault['showSocialButtons'] = array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Enabled',
            self::SCHEMA_LABEL    => 'Show social buttons',
            self::SCHEMA_REQUIRED => false,
        );

        parent::__construct($params, $sections);
    }
}
