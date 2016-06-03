<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Shipping;

/**
 * Shipping carriers list
 */
class Carriers extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return array
     */
    protected function getFormTarget()
    {
        return 'shipping_methods';
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'shipping_methods';

        return $result;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && !\XLite::getController()->getProcessorId();
    }

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'items_list/model/table/shipping/carriers/style.css';

        $editMethod = new \XLite\View\Button\Shipping\EditMethod();
        $list = array_merge($list, $editMethod->getCSSFiles());

        return $list;
    }

    /**
     * Get a list of JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'items_list/model/table/shipping/carriers/controller.js';

        $editMethod = new \XLite\View\Button\Shipping\EditMethod();
        $list = array_merge($list, $editMethod->getJSFiles());

        return $list;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Shipping\Method';
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = array(
            'name' => array(
                static::COLUMN_NAME      => static::t('Shipping method'),
                static::COLUMN_ORDERBY   => 100,
                static::COLUMN_TEMPLATE  => 'items_list/model/table/shipping/carriers/cell.name.twig',
            ),
            'deliveryTime' => array(
                static::COLUMN_NAME      => static::t('Delivery time'),
                static::COLUMN_CLASS     => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_TEMPLATE  => 'items_list/model/table/shipping/carriers/cell.deliveryTime.dash.twig',
                static::COLUMN_HEAD_HELP => static::t('deliveryTime.help'),
                static::COLUMN_ORDERBY   => 200,
            ),
            'handlingFee' => array(
                static::COLUMN_NAME      => static::t('Handling fee'),
                static::COLUMN_CLASS     => 'XLite\View\FormField\Inline\Input\Text\Price',
                static::COLUMN_TEMPLATE  => 'items_list/model/table/shipping/carriers/cell.handlingFee.restriction.twig',
                static::COLUMN_ORDERBY   => 300,
            ),
        );

        if (0 < \XLite\Core\Database::getRepo('XLite\Model\TaxClass')->count()) {
            $columns['taxClass'] = array(
                static::COLUMN_NAME      => static::t('Tax class'),
                static::COLUMN_CLASS     => 'XLite\View\FormField\Inline\Select\ShippingTaxClass',
                static::COLUMN_PARAMS    => array(
                    'fieldOnly' => true,
                ),
                static::COLUMN_ORDERBY   => 400,
            );
        }

        return $columns;
    }

    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return array('shipping-carrier');
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' shipping-carriers';
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * Check - switch entity or not
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntitySwitch(\XLite\Model\AEntity $entity)
    {
        /** @var \XLite\Model\Shipping\Method $entity */
        return parent::isAllowEntitySwitch($entity)
            && (null === $entity->getProcessorObject() || $entity->getProcessorObject()->isConfigured());
    }

    /**
     * Check - switch entity or not
     *
     * @param \XLite\Model\Shipping\Method $entity Entity
     *
     * @return boolean
     */
    protected function showConfigurationWarning(\XLite\Model\Shipping\Method $entity)
    {
        return $entity->getProcessorObject() && !$entity->getProcessorObject()->isConfigured();
    }

    /**
     * Returns method settings URL
     *
     * @param \XLite\Model\Shipping\Method $entity Entity
     *
     * @return string
     */
    protected function getSettingsURL(\XLite\Model\Shipping\Method $entity)
    {
        return $entity->getProcessorObject()
            ? $entity->getProcessorObject()->getSettingsURL()
            : '';
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * Template for switcher action definition
     *
     * @return string
     */
    protected function getSwitcherActionTemplate()
    {
        return 'items_list/model/table/shipping/carriers/switcher.twig';
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Get top actions
     *
     * @return array
     */
    protected function getTopActions()
    {
        $actions = parent::getTopActions();
        if (!$this->isStatic()) {
            $actions[] = 'items_list/model/table/shipping/carriers/action.create.twig';
        }

        $actions[] = 'items_list/model/table/shipping/carriers/automate_routine.button.twig';

        return $actions;
    }

    /**
     * Get icon for automate shipping routine link
     *
     * @return string
     */
    protected function getAutomateRoutineIcon()
    {
        return \XLite\Core\Layout::getInstance()->getResourceWebPath('items_list/model/table/shipping/carriers/print-labels.svg');
    }

    /**
     * Get text for automate shipping routine link
     *
     * @return string
     */
    protected function getAutomateRoutineText()
    {
        return static::t('Automate shipping');
    }

    /**
     * Get automate shipping routine link
     *
     * @return string
     */
    protected function getAutomateRoutineLink()
    {
        return $this->buildURL('automate_shipping_routine');
    }

    /**
     * Check if the column template is used for widget displaying
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isTemplateColumnVisible(array $column, \XLite\Model\AEntity $entity)
    {
        $result = parent::isTemplateColumnVisible($column, $entity);

        /** @var \XLite\Model\Shipping\Method $entity */
        switch ($column[static::COLUMN_CODE]) {
            case 'deliveryTime':
                $result = 'offline' !== $entity->getProcessor();
                break;

            case 'handlingFee':
                $result = \XLite::isFreeLicense();
                break;

            default:
                break;
        }

        return $result;
    }

    /**
     * Check if the simple class is used for widget displaying
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isClassColumnVisible(array $column, \XLite\Model\AEntity $entity)
    {
        $result = parent::isClassColumnVisible($column, $entity);

        /** @var \XLite\Model\Shipping\Method $entity */
        switch ($column[static::COLUMN_CODE]) {
            case 'deliveryTime':
                $result = 'offline' === $entity->getProcessor();
                break;

            case 'handlingFee':
                $result = !\XLite::isFreeLicense();
                break;

            default:
                break;
        }

        return $result;
    }

    /**
     * Get column cell class
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model OPTIONAL
     *
     * @return string
     */
    protected function getColumnClass(array $column, \XLite\Model\AEntity $entity = null)
    {
        /** @var \XLite\Model\Shipping\Method $entity */
        $result = parent::getColumnClass($column, $entity);

        if ('actions left' === $column[static::COLUMN_CODE]
            && $entity->getProcessorObject()
            && $entity->getProcessorObject()->isTestMode()
        ) {
            $result .= ' test-mode';
        }

        return $result;
    }

    /**
     * Return true if 'Edit' link should be displayed in column line
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isLink(array $column, \XLite\Model\AEntity $entity)
    {
        /** @var \XLite\Model\Shipping\Method $entity */
        return !$this->isOffline($entity) && 'name' === $column[static::COLUMN_CODE]
            ? true
            : parent::isLink($column, $entity);
    }

    /**
     * Build entity page URL
     * @todo: reorder params
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        /** @var \XLite\Model\Shipping\Method $entity */
        return !$this->isOffline($entity) && 'name' === $column[static::COLUMN_CODE]
            ? $result = $entity->getProcessorObject()->getSettingsURL()
            : parent::buildEntityURL($entity, $column);
    }

    /**
     * Remove entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        /** @var \XLite\Model\Shipping\Method $entity */
        if ('offline' !== $entity->getProcessor() && '' === $entity->getCarrier()) {
            $entity->setAdded(false);
            $result = true;

        } else {
            $result = parent::removeEntity($entity);
        }

        return $result;
    }

    // {{{ Search

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();
        $result->{\XLite\Model\Repo\Shipping\Method::P_CARRIER} = '';
        $result->{\XLite\Model\Repo\Shipping\Method::P_ADDED} = true;
        $result->{\XLite\Model\Repo\Shipping\Method::P_ORDER_BY} = array('m.position', 'ASC');

        return $result;
    }

    /**
     * Get page data
     *
     * @return array
     */
    protected function getPageData()
    {
        return array_filter(parent::getPageData(), function ($item) {
            /** @var \XLite\Model\Shipping\Method $item */
            return (bool) $item->getProcessorObject();
        });
    }

    // }}}

    /**
     * Get template name to display when list is empty
     *
     * @return string
     */
    protected function getEmptyListTemplate()
    {
        return $this->getDir() . '/' . $this->getPageBodyDir() . '/shipping/methods/empty.twig';
    }

    /**
     * Check if method is offline
     *
     * @param \XLite\Model\Shipping\Method $entity Shipping method
     *
     * @return boolean
     */
    protected function isOffline($entity)
    {
        return 'offline' === $entity->getProcessor();
    }

    /**
     * Returns list of zones as a string
     *
     * @param \XLite\Model\Shipping\Method $entity Shipping method
     *
     * @return string
     */
    protected function getZonesList($entity)
    {
        $result = '';

        $zones = array();
        foreach ($entity->getShippingMarkups() as $markup) {
            if ($markup && $markup->getZone() && !in_array($markup->getZone()->getZoneName(), $zones, true)) {
                $zones[] = $markup->getZone()->getZoneName();
            }
        }

        if (count($zones)) {
            sort($zones);
            $result = implode(', ', $zones);
        }

        return $result;
    }

    /**
     * Returns handling fee column restriction message
     *
     * @return string
     */
    protected function getHandlingFeeRestrictionMessage()
    {
        return static::t(
            'This feature is available only for paid (non-free) X-Cart license plans',
            array(
                'pricingUrl' => \XLite::getController()->getPricingURL(),
            )
        );
    }
}
