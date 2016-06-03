<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\View\Model;

/**
 * Product AuctionInc related data model
 */
class ProductAuctionInc extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = array(
        'calculationMethod' => array(
            self::SCHEMA_CLASS => 'XLite\Module\XC\AuctionInc\View\FormField\Select\CalculationMethodProduct',
            self::SCHEMA_LABEL => 'Calculation method',
        ),
        'package' => array(
            self::SCHEMA_CLASS      => 'XLite\Module\XC\AuctionInc\View\FormField\Select\Package',
            self::SCHEMA_LABEL      => 'Package',
            self::SCHEMA_HELP       => 'Select "Together" for items that can be packed in the same box with other items from the same origin.',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'calculationMethod' => array('C', 'CI'),
                ),
            ),
        ),
        'weight' => array(
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Input\Text\Weight',
            self::SCHEMA_LABEL      => 'Weight',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'calculationMethod' => array('C', 'CI'),
                ),
            ),
        ),
        'dimensions' => array(
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Input\Text\Dimensions',
            self::SCHEMA_LABEL      => 'Dimensions',
            self::SCHEMA_HELP       => 'Set your item dimensions. If item is packaged separately, use your box dimensions.',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'calculationMethod' => array('C', 'CI'),
                ),
            ),
        ),
        'weightUOM' => array(
            self::SCHEMA_CLASS      => 'XLite\Module\XC\AuctionInc\View\FormField\Select\WeightUOM',
            self::SCHEMA_LABEL      => 'Weight UOM',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'calculationMethod' => array('C', 'CI'),
                ),
            ),
        ),
        'dimensionsUOM' => array(
            self::SCHEMA_CLASS      => 'XLite\Module\XC\AuctionInc\View\FormField\Select\DimensionsUOM',
            self::SCHEMA_LABEL      => 'Dimensions UOM',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'calculationMethod' => array('C', 'CI'),
                ),
            ),
        ),
        'insurable' => array(
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Select\RadioButtonsList\YesNo',
            self::SCHEMA_LABEL      => 'Insurable',
            self::SCHEMA_HELP       => 'Include product value for insurance calculation based on AuctionInc settings.',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'calculationMethod' => array('C', 'CI'),
                ),
            ),
        ),
        'originCode' => array(
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL      => 'Origin code',
            self::SCHEMA_HELP       => 'If item is not shipped from your default AuctionInc location, enter your AuctionInc-configured origin code here.',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'calculationMethod' => array('C', 'CI'),
                ),
            ),
        ),
        'onDemand' => array(
            self::SCHEMA_CLASS      => 'XLite\Module\XC\AuctionInc\View\FormField\Select\OnDemandServices',
            self::SCHEMA_LABEL      => 'On-demand services',
            self::SCHEMA_HELP       => 'Flag your product as eligible for any services you have configured to \'On Demand\'. Hold [Ctrl] Key for multiple selections.',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'calculationMethod' => array('C', 'CI'),
                ),
            ),
        ),
        'supplementalItemHandlingMode' => array(
            self::SCHEMA_CLASS      => 'XLite\Module\XC\AuctionInc\View\FormField\Select\SupplementalItemHandlingMode',
            self::SCHEMA_LABEL      => 'Supplemental Item Handling Mode',
            self::SCHEMA_HELP       => 'Additional handling charge supplements your AuctionInc-configured package and order handling. If code is selected, enter your AuctionInc-configured Handling code.',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'calculationMethod' => array('C', 'CI'),
                ),
            ),
        ),
        'supplementalItemHandlingCode' => array(
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL      => 'Supplemental Item Handling Code',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'calculationMethod' => array('C', 'CI'),
                    'supplementalItemHandlingMode' => array('C'),
                ),
            ),
        ),
        'supplementalItemHandlingFee' => array(
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Input\Text\Price',
            self::SCHEMA_LABEL      => 'Supplemental Item Handling Fee',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'calculationMethod' => array('C', 'CI'),
                    'supplementalItemHandlingMode' => array('F'),
                ),
            ),
        ),
        'carrierAccessorialFees' => array(
            self::SCHEMA_CLASS      => 'XLite\Module\XC\AuctionInc\View\FormField\Select\CarrierAccessorialFees',
            self::SCHEMA_LABEL      => 'Carrier Accessorial Fees',
            self::SCHEMA_HELP       => 'Add preferred special carrier fees. Hold [Ctrl] key for multiple selections.',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'calculationMethod' => array('C', 'CI'),
                ),
            ),
        ),
        'fixedFeeMode' => array(
            self::SCHEMA_CLASS      => 'XLite\Module\XC\AuctionInc\View\FormField\Select\FixedFeeMode',
            self::SCHEMA_LABEL      => 'Mode',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'calculationMethod' => array('F'),
                ),
            ),
        ),
        'fixedFeeCode' => array(
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL      => 'Code',
            self::SCHEMA_HELP       => 'Enter your AuctionInc-configured fixed fee code.',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'calculationMethod' => array('F'),
                    'fixedFeeMode' => array('C'),
                ),
            ),
        ),
        'fixedFee1' => array(
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Input\Text\Price',
            self::SCHEMA_LABEL      => 'Fee 1',
            self::SCHEMA_HELP       => 'Enter fee for first item',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'calculationMethod' => array('F'),
                    'fixedFeeMode' => array('F'),
                ),
            ),
        ),
        'fixedFee2' => array(
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Input\Text\Price',
            self::SCHEMA_LABEL      => 'Fee 2',
            self::SCHEMA_HELP       => 'Enter fee for additional items and quantities',
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    'calculationMethod' => array('F'),
                    'fixedFeeMode' => array('F'),
                ),
            ),
        ),
    );

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/AuctionInc/product.css';

        return $list;
    }

    /**
     * Return current model ID
     *
     * @return integer
     */
    public function getModelId()
    {
        return \XLite\Core\Request::getInstance()->product_id;
    }

    /**
     * getFieldBySchema
     *
     * @param string $name Field name
     * @param array  $data Field description
     *
     * @return \XLite\View\FormField\AFormField
     */
    protected function getFieldBySchema($name, array $data)
    {
        return ('originCode' !== $name || $this->isSSAvailable())
            ? parent::getFieldBySchema($name, $data)
            : null;
    }

    /**
     * Check if SS available
     *
     * @return boolean
     */
    protected function isSSAvailable()
    {
        return \XLite\Module\XC\AuctionInc\Main::isSSAvailable();
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Module\XC\AuctionInc\Model\ProductAuctionInc
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('XLite\Module\XC\AuctionInc\Model\ProductAuctionInc')->findOneBy(
                array('product' => $this->getModelId())
            )
            : null;

        $model = $model ?: new \XLite\Module\XC\AuctionInc\Model\ProductAuctionInc();
        $model->setProduct($this->getProduct());

        return $model;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\Module\XC\AuctionInc\View\Form\ProductAuctionInc';
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();
        $result['submit'] = new \XLite\View\Button\Submit(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Update',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            )
        );

        if ($this->getDefaultModelObject()->isPersistent()) {
            $url = $this->buildURL(
                'product',
                'restore_auction_inc',
                array(
                    'product_id' => $this->getModelId(),
                )
            );
            $result['restore'] = new \XLite\View\Button\Regular(
                array(
                    \XLite\View\Button\AButton::PARAM_LABEL => 'Restore to module defaults',
                    \XLite\View\Button\AButton::PARAM_STYLE => 'action always-enabled',
                    \XLite\View\Button\Regular::PARAM_JS_CODE => 'self.location=\'' . $url . '\'',
                )
            );
        }

        return $result;
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
        if (!isset($data['onDemand'])) {
            $data['onDemand'] = array();
        }

        if (!isset($data['carrierAccessorialFees'])) {
            $data['carrierAccessorialFees'] = array();
        }

        parent::setModelProperties($data);
    }
}
