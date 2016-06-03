<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\Form;

/**
 * "Set the sale price" dialog form class
 */
class SaleSelectedDialog extends \XLite\View\Form\AForm
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'sale_selected';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'set_sale_price';
    }

    /**
     * Get validator
     *
     * @return \XLite\Core\Validator\HashArray
     */
    protected function getValidator()
    {
        $validator = parent::getValidator();

        $data = $validator->addPair('postedData', new \XLite\Core\Validator\HashArray());
        $this->setDataValidators($data);

        return $validator;
    }

    /**
     * Set validators pairs for products data
     *
     * @param mixed &$data Data
     *
     * @return void
     */
    protected function setDataValidators(&$data)
    {
        $this->setSaleDataValidators($data);
    }

    /**
     * Called before the includeCompiledFile()
     *
     * @return void
     */
    protected function initView()
    {
        parent::initView();

        $toDelete = array();

        if (is_array(\XLite\Core\Request::getInstance()->select)) {
            foreach (\XLite\Core\Request::getInstance()->select as $productId => $value) {
                $toDelete['select[' . $productId . ']'] = $productId;
            }
        }

        $postedData = array(
            'postedData[participateSale]' => true,
        );

        $this->widgetParams[self::PARAM_FORM_PARAMS]->appendValue($toDelete);
        $this->widgetParams[self::PARAM_FORM_PARAMS]->appendValue($postedData);
    }


}
