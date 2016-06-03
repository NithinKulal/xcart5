<?php

// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\QSL\SpecialOffersBase\View\Model;

/**
 * Special offer view model
 */
abstract class ASpecialOffer extends \XLite\View\Model\AModel
{
    /**
     * Parameter used to order schema fields.
     */
    const SCHEMA_WEIGHT = 'schema_weight';

    /**
     * Shema default
     *
     * @var array
     */
    protected $schemaDefault;

    /**
     * Save current form reference and sections list, and initialize the cache
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = array(), array $sections = array())
    {
        $this->defineSchemaDefault();
        $this->alterSchemaDefault();
        $this->sortSchemaDefault();
        parent::__construct($params, $sections);
    }

    /**
     * Extra functions that alter the default schema array.
     * 
     * @return void
     */
    protected function alterSchemaDefault()
    {
    }

    /**
     * Orders schema fields by the SCHEMA_WEIGHT parameter in ascending order.
     * 
     * @return integer
     */
    protected function sortSchemaDefault()
    {
        uasort($this->schemaDefault, function ($a, $b) {
            $aw = isset($a[self::SCHEMA_WEIGHT]) ? intval($a[self::SCHEMA_WEIGHT]) : 0;
            $bw = isset($b[self::SCHEMA_WEIGHT]) ? intval($b[self::SCHEMA_WEIGHT]) : 0;
            return ($aw < $bw) ? -1 : (
                    ($aw > $bw) ? 1 : 0
                    );
        });
    }

    /**
     * Initializes the default schema declaration.
     * 
     * @return void
     */
    protected function defineSchemaDefault()
    {
        $this->schemaDefault = array(
            // General
            'group_general' => array(
                self::SCHEMA_CLASS => 'XLite\View\FormField\Separator\Regular',
                self::SCHEMA_LABEL => 'General settings',
                self::SCHEMA_WEIGHT => 1000,
            ),
            'offerTypeName' => array(
                self::SCHEMA_CLASS => 'XLite\Module\QSL\SpecialOffersBase\View\FormField\Label\OfferType',
                self::SCHEMA_LABEL => 'Offer type',
                self::SCHEMA_WEIGHT => 1010,
                self::SCHEMA_VALUE => $this->getOfferType()->getName(),
            ),
            'name' => array(
                self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL => 'Administrative name',
                self::SCHEMA_REQUIRED => true,
                self::SCHEMA_WEIGHT => 1020,
            ),
            'enabled' => array(
                self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Checkbox\Enabled',
                self::SCHEMA_LABEL => 'Enabled',
                self::SCHEMA_REQUIRED => false,
                self::SCHEMA_WEIGHT => 1030,
            ),
            // Dates
            'group_dates' => array(
                self::SCHEMA_CLASS => 'XLite\View\FormField\Separator\Regular',
                self::SCHEMA_LABEL => 'Special offer dates',
                self::SCHEMA_WEIGHT => 2000,
            ),
            'activeFromDate' => array(
                self::SCHEMA_CLASS => 'XLite\View\DatePicker',
                self::SCHEMA_LABEL => 'Begin offer date',
                self::SCHEMA_REQUIRED => false,
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => false,
                self::SCHEMA_WEIGHT => 2010,
            ),
            'activeFromHour' => array(
                self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL => 'Begin offer hour',
                self::SCHEMA_REQUIRED => false,
                \XLite\View\FormField\Input\Text\Integer::PARAM_MIN => 0,
                \XLite\View\FormField\Input\Text\Integer::PARAM_MAX => 23,
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => false,
                self::SCHEMA_WEIGHT => 2020,
            ),
            'activeTillDate' => array(
                self::SCHEMA_CLASS => 'XLite\View\DatePicker',
                self::SCHEMA_LABEL => 'End offer date',
                self::SCHEMA_REQUIRED => false,
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => false,
                self::SCHEMA_WEIGHT => 2030,
            ),
            'activeTillHour' => array(
                self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL => 'End offer hour',
                self::SCHEMA_REQUIRED => false,
                \XLite\View\FormField\Input\Text\Integer::PARAM_MIN => 0,
                \XLite\View\FormField\Input\Text\Integer::PARAM_MAX => 23,
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => false,
                self::SCHEMA_WEIGHT => 2040,
            ),
            // Exclusions
            'group_exclusions' => array(
                self::SCHEMA_CLASS => 'XLite\View\FormField\Separator\Regular',
                self::SCHEMA_LABEL => 'Special offer exclusions',
                self::SCHEMA_WEIGHT => 5000,
            ),
            'group_exclusions_comment' => array(
                self::SCHEMA_CLASS => '\XLite\View\FormField\Label',
                self::SCHEMA_LABEL => 'In this section you can select other special offers that will prevent this offer from being applied.',
                self::SCHEMA_REQUIRED => false,
                self::SCHEMA_WEIGHT => 5010,
            ),
            'exclusions' => array(
                self::SCHEMA_CLASS => 'XLite\Module\QSL\SpecialOffersBase\View\FormField\Select\Exclusions',
                self::SCHEMA_LABEL => 'Exclusion special offers',
                self::SCHEMA_REQUIRED => false,
                \XLite\Module\QSL\SpecialOffersBase\View\FormField\Select\Exclusions::PARAM_CURRENT_SPECIAL_OFFER => $this->getModelObject(),
                self::SCHEMA_WEIGHT => 5020,
            ),
            // Promotions
            'group_promotions' => array(
                self::SCHEMA_CLASS => 'XLite\View\FormField\Separator\Regular',
                self::SCHEMA_LABEL => 'Description & Promotions',
                self::SCHEMA_WEIGHT => 6000,
            ),
            'title' => array(
                self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL => 'Special offer title',
                self::SCHEMA_REQUIRED => true,
                self::SCHEMA_WEIGHT => 6010,
            ),
            'description' => array(
                self::SCHEMA_CLASS => 'XLite\View\FormField\Textarea\Advanced',
                self::SCHEMA_LABEL => 'Description',
                self::SCHEMA_REQUIRED => false,
                self::SCHEMA_WEIGHT => 6020,
            ),
            'image' => array(
                self::SCHEMA_CLASS => 'XLite\View\FormField\FileUploader\Image',
                self::SCHEMA_LABEL => 'Image',
                self::SCHEMA_REQUIRED => false,
                self::SCHEMA_WEIGHT => 6030,
            ),
            'shortPromoText' => array(
                self::SCHEMA_CLASS => 'XLite\View\FormField\Textarea\Advanced',
                self::SCHEMA_LABEL => 'Short promo text',
                self::SCHEMA_REQUIRED => false,
                self::SCHEMA_WEIGHT => 6040,
            ),
            'promoHome' => array(
                self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Checkbox\Enabled',
                self::SCHEMA_LABEL => 'Display short promo text and image on the home page',
                self::SCHEMA_REQUIRED => false,
                self::SCHEMA_WEIGHT => 6070,
            ),
            'promoOffers' => array(
                self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Checkbox\Enabled',
                self::SCHEMA_LABEL => 'Display short promo text and image on Special Offers page',
                self::SCHEMA_REQUIRED => false,
                self::SCHEMA_WEIGHT => 6100,
            ),
        );

        $conditions = $this->getConditionFields();
        if (!empty($conditions)) {
            $this->schemaDefault['group_conditions'] = array(
                self::SCHEMA_CLASS => 'XLite\View\FormField\Separator\Regular',
                self::SCHEMA_LABEL => 'Special offer conditions',
                self::SCHEMA_WEIGHT => 3000,
            );
            foreach ($conditions as $field => $info) {
                $this->schemaDefault[$field] = $info;
            }
        }

        $rewards = $this->getRewardFields();
        if (!empty($rewards)) {
            $this->schemaDefault['group_reward'] = array(
                self::SCHEMA_CLASS => 'XLite\View\FormField\Separator\Regular',
                self::SCHEMA_LABEL => 'Special offer reward',
                self::SCHEMA_WEIGHT => 4000,
            );
            foreach ($rewards as $field => $info) {
                $this->schemaDefault[$field] = $info;
            }
        }
    }

    /**
     * Returns schema fields for the Special Offer Conditions group.
     * 
     * @return array
     */
    protected function getConditionFields()
    {
        return array();
    }

    /**
     * Returns schema fields for the Special Offer Rewards group.
     * 
     * @return array
     */
    protected function getRewardFields()
    {
        return array();
    }

    /**
     * Return current model ID
     *
     * @return integer
     */
    public function getModelId()
    {
        return \XLite\Core\Request::getInstance()->offer_id;
    }

    /**
     * Returns the offer type model for the special offer.
     * 
     * @return \XLite\Module\QSL\SpecialOffersBase\Model\OfferType
     */
    protected function getOfferType()
    {
        return $this->getModelObject()->getOfferType();
    }

    /**
     * Returns the offer type model that is referenced by its ID in the request.
     * 
     * @return \XLite\Module\QSL\SpecialOffersBase\Model\OfferType
     */
    protected function findOfferTypeFromRequest()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\QSL\SpecialOffersBase\Model\OfferType')->find(
            \XLite\Core\Request::getInstance()->type_id
        );
    }

    /**
     * This object will be used if another one is not pased
     *
     * @return \XLite\Module\QSL\SpecialOffersBase\Model\SpecialOffer
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('XLite\Module\QSL\SpecialOffersBase\Model\SpecialOffer')->find($this->getModelId())
            : null;

        if (!$model) {
            $model = new \XLite\Module\QSL\SpecialOffersBase\Model\SpecialOffer;
            $model->setOfferType($this->findOfferTypeFromRequest());
        }

        return $model;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\Module\QSL\SpecialOffersBase\View\Form\Model\SpecialOffer';
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $label = $this->getModelObject()->getOfferId() ? 'Update' : 'Create';

        $result['submit'] = new \XLite\View\Button\Submit(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL => $label,
                \XLite\View\Button\AButton::PARAM_STYLE => 'action',
            )
        );

        return $result;
    }

    /**
     * Add top message
     *
     * @return void
     */
    protected function addDataSavedTopMessage()
    {
        if ('create' != $this->currentAction) {
            \XLite\Core\TopMessage::addInfo('The special offer has been updated');
        } else {
            \XLite\Core\TopMessage::addInfo('The special offer has been added');
        }
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        parent::setModelProperties(
            $this->combineDateFields(
                $this->injectEmptyArrayFields($data)
            )
        );
    }

    /**
     * Retrieve property from the model object.
     *
     * @param mixed $name Field/property name
     *
     * @return mixed
     */
    protected function getModelObjectValue($name)
    {
        switch ($name) {
            case 'activeFromDate':
                $time = parent::getModelObjectValue('activeFrom');
                $value = $time ? mktime(0, 0, 0, date('m', $time), date('d', $time), date('Y', $time)) : 0;
                break;
            case 'activeFromHour':
                $time = parent::getModelObjectValue('activeFrom');
                $value = $time ? date('G', $time) : 0;
                break;
            case 'activeTillDate':
                $time = parent::getModelObjectValue('activeTill');
                $value = $time ? mktime(0, 0, 0, date('m', $time), date('d', $time), date('Y', $time)) : 0;
                break;
            case 'activeTillHour':
                $time = parent::getModelObjectValue('activeTill');
                $value = $time ? date('G', $time) : 23;
                break;
            default:
                $value = parent::getModelObjectValue($name);
        }

        return $value;
    }

    /**
     * Get model value by name
     *
     * @param \XLite\Model\AEntity $model Model object
     * @param string               $name  Property name
     *
     * @return mixed
     */
    protected function getModelValue($model, $name)
    {
        $value = null;

        switch ($name) {
            case 'offerTypeName':
                $value = $model->getOfferType()->getName();
                break;
            default:
                $value = parent::getModelValue($model, $name);
        }

        return $value;
    }

    /**
     * Combines date fields into UNIX timestamps.
     * 
     * @param array $data Data
     * 
     * @return array
     */
    protected function combineDateFields(array $data)
    {
        // Begin date
        $from = (int) strtotime($data['activeFromDate']) ? : 0;
        $data['activeFrom'] = $from ? ($from + $data['activeFromHour'] * 3600) : 0;
        unset($data['activeFromDate']);
        unset($data['activeFromHour']);

        // End date
        $till = (int) strtotime($data['activeTillDate']) ? : 0;
        $data['activeTill'] = $till ? ($till + $data['activeTillHour'] * 3600) : 0;
        unset($data['activeTillDate']);
        unset($data['activeTillHour']);

        return $data;
    }

    /**
     * Adds empty arrays to missing parameters of Array type to make emptying multi-select lists possible.
     * 
     * @param array $data Data to set.
     * 
     * @return array
     */
    protected function injectEmptyArrayFields(array $data)
    {
        foreach ($this->getArrayFieldNames() as $name) {
            if (!isset($data[$name])) {
                $data[$name] = array();
            }
        }

        return $data;
    }

    /**
     * Returns list of multi-select array parameters.
     * 
     * @return array
     */
    protected function getArrayFieldNames()
    {
        return array('exclusions');
    }

}
