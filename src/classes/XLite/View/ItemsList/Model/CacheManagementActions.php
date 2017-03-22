<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * Class CacheManagementActions
 */
class CacheManagementActions extends \XLite\View\AView
{
    /**
     * Returns CSS Files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'cache_management_actions/style.less';

        return $list;
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultTemplate()
    {
        return 'cache_management_actions/body.twig';
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected static function defineColumns()
    {
        return array(
            'name' => [
                'name'     => static::t('Name'),
                'template' => 'cache_management_actions/cell/name.twig',
            ],
            'view' => [
                'name'     => static::t('Action'),
                'template' => 'cache_management_actions/cell/action.twig',
            ]
        );
    }

    /**
     * @return array
     */
    protected function getData()
    {
        return [
            [
                'name'          => static::t('Re-deploy the store'),
                'description'   => static::t('Re-deploy the store help text'),
                'view'          => '\XLite\View\Button\SimpleLink',
                'viewParams'    => [
                    \XLite\View\Button\AButton::PARAM_LABEL => static::t('Start'),
                    \XLite\View\Button\AButton::PARAM_STYLE => 'always-enabled regular-main-button btn',
                    \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('cache_management', 'rebuild'),
                    \XLite\View\Button\Regular::PARAM_JS_CODE => sprintf('if (confirm("' . static::t("Are you sure?") .'")) self.location="%s";', $this->buildURL('cache_management', 'rebuild')),
                ],
                'options'   => $this->getRebuildOptions()
            ],
            [
                'name'          => static::t('Calculate quick data'),
                'description'   => static::t('Calculate quick data help text'),
                'view'          => '\XLite\View\Button\SimpleLink',
                'viewParams'    => [
                    \XLite\View\Button\AButton::PARAM_LABEL => static::t('Start'),
                    \XLite\View\Button\AButton::PARAM_STYLE => 'btn always-enabled regular-button',
                    \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('cache_management', 'quick_data'),
                ],
            ],
            [
                'name'          => static::t('Clear all caches'),
                'description'   => static::t('Clear all caches text'),
                'view'          => '\XLite\View\Button\SimpleLink',
                'viewParams'    => [
                    \XLite\View\Button\AButton::PARAM_LABEL => static::t('Start'),
                    \XLite\View\Button\AButton::PARAM_STYLE => 'btn always-enabled regular-button',
                    \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('cache_management', 'clear_cache'),
                ],
            ],
            [
                'name'          => static::t('Recalculate ViewLists'),
                'description'   => static::t('Recalculate ViewLists text'),
                'view'          => '\XLite\View\Button\SimpleLink',
                'viewParams'    => [
                    \XLite\View\Button\AButton::PARAM_LABEL => static::t('Start'),
                    \XLite\View\Button\AButton::PARAM_STYLE => 'btn always-enabled regular-button',
                    \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('cache_management', 'rebuild_view_lists'),
                ],
            ]
        ];
    }

    /**
     * @return array
     */
    protected function getRebuildOptions()
    {
        $result = [];

        /** @var \XLite\Model\Config $quickDataToggleOption */
        $quickDataToggleOption = \XLite\Core\Database::getRepo('XLite\Model\Config')->findOneBy(
            array(
                'category'  => 'CacheManagement',
                'name'      => 'quick_data_rebuilding',
            )
        );

        if ($quickDataToggleOption) {
            $result[] = [
                'name'          => $quickDataToggleOption->getOptionName(),
                'description'   => $quickDataToggleOption->getOptionComment(),
                'view'          => '\XLite\View\Button\SwitcherStandaloneAction',
                'viewParams'    => [
                    'target'    => 'cache_management',
                    'action'    => 'quick_data_toggle',
                    'value'     => \XLite\Core\Config::getInstance()->CacheManagement->quick_data_rebuilding
                ],
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getBodyLines()
    {
        $lines = $this->getData();
        $columns = $this->getColumns();

        $result = [];
        foreach ($lines as $lineRaw) {
            $line = [
                'entity'    => $lineRaw,
                'columns'   => [],
            ];
            foreach ($columns as $columnRaw){
                $column = $columnRaw;
                $column['value'] = $lineRaw[$column['serviceName']];
                $line['columns'][] = $column;
            }
            $result[] = $line;
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getColumns()
    {
        $defaults = [
            'name'              => '',
            'serviceName'  => '',
            'template'          => null,
            'class'             => null,
        ];
        $result = [];
        
        foreach (static::defineColumns() as $serviceName => $columnRaw) {
            $column = array_merge($defaults, $columnRaw);
            $column['serviceName'] = $serviceName;
            $result[$serviceName] = $column;
        }

        return $result;        
    }
}
