<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\View\ItemsList;

/**
 * Volume discounts items list
 */
class VolumeDiscounts extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Maximum numeric value for absolute discount
     */
    const MAX_NUMERIC_VALUE = 9999999999;

    /**
     * Discount keys
     *
     * @var   array
     */
    protected $discountKeys = array();

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/VolumeDiscounts/discounts/list/style.css';

        return $list;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'subtotalRangeBegin' => array(
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('Subtotal'),
                static::COLUMN_CLASS => 'XLite\Module\CDev\VolumeDiscounts\View\FormField\SubtotalRangeBegin',
                static::COLUMN_CREATE_CLASS => 'XLite\Module\CDev\VolumeDiscounts\View\FormField\SubtotalRangeBegin',
                static::COLUMN_ORDERBY  => 100,
            ),
            'value' => array(
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('Discount'),
                static::COLUMN_CLASS => 'XLite\Module\CDev\VolumeDiscounts\View\FormField\DiscountValue',
                static::COLUMN_CREATE_CLASS => 'XLite\Module\CDev\VolumeDiscounts\View\FormField\DiscountValue',
                static::COLUMN_ORDERBY  => 200,
            ),
            'type' => array(
                static::COLUMN_NAME => '',
                static::COLUMN_CLASS => 'XLite\Module\CDev\VolumeDiscounts\View\FormField\DiscountType',
                static::COLUMN_CREATE_CLASS => 'XLite\Module\CDev\VolumeDiscounts\View\FormField\DiscountType',
                static::COLUMN_ORDERBY  => 300,
            ),
            'membership' => array(
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('Membership'),
                static::COLUMN_CLASS => 'XLite\Module\CDev\VolumeDiscounts\View\FormField\Membership',
                static::COLUMN_CREATE_CLASS => 'XLite\Module\CDev\VolumeDiscounts\View\FormField\Membership',
                static::COLUMN_ORDERBY  => 400,
            ),
        );
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount';
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildURL('volume_discounts');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add discount';
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return false;
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
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return array('volumeDiscounts');
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' volume-discounts';
    }

    /**
     * Post-validate new entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateNewEntity(\XLite\Model\AEntity $entity)
    {
        $result = parent::prevalidateNewEntity($entity);
        if ($result && $entity->getRepository()->findOneSimilarDiscount($entity)) {
            $this->errorMessages[] = static::t('Could not add the discount because another discount already exists for the specified subtotal range and membership level');
            $result = false;

        } elseif ($this->prevalidateVolumeDiscount($entity)) {
            $this->discountKeys[] = $entity->getFingerprint();

        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Pre-validate entities
     *
     * @return boolean
     */
    protected function prevalidateEntities()
    {
        $result = parent::prevalidateEntities();
        if ($result && count(array_unique($this->discountKeys)) !== count($this->discountKeys)) {
            $this->errorMessages[] = static::t('Could not update the discount because another discount already exists for the specified subtotal range and membership level');
            $result = false;
        }

        return $result;
    }

    /**
     * Pre-validate entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateEntity(\XLite\Model\AEntity $entity)
    {
        $result = parent::prevalidateEntity($entity);

        if ($result && $this->prevalidateVolumeDiscount($entity)) {
            $this->discountKeys[] = $entity->getFingerprint();

        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Pre-validate entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateVolumeDiscount(\XLite\Model\AEntity $entity)
    {
        $result = true;

        if (\XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount::TYPE_PERCENT == $entity->getType()) {
            if (100 < $entity->getValue()) {
                $this->errorMessages[] = static::t('Percent discount value cannot exceed 100%');
                $result = false;
            }

        } elseif (\XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount::TYPE_ABSOLUTE == $entity->getType()) {
            if (static::MAX_NUMERIC_VALUE < $entity->getValue()) {
                $this->errorMessages[] = static::t('Too large value for absolute discount');
                $result = false;
            }
        }

        return $result;
    }

    // {{{ Data

    /**
     * Return discounts list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $cnd->{\XLite\Module\CDev\VolumeDiscounts\Model\Repo\VolumeDiscount::P_ORDER_BY_MEMBERSHIP} = array('membership.membership_id', 'ASC');
        $cnd->{\XLite\Module\CDev\VolumeDiscounts\Model\Repo\VolumeDiscount::P_ORDER_BY_SUBTOTAL} = array('v.subtotalRangeBegin', 'ASC');

        return \XLite\Core\Database::getRepo('XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount')
            ->search($cnd, $countOnly);
    }

    // }}}
}
