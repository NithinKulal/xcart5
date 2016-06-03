<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Features;

/**
 * FormModelControllerTrait
 */
trait FormModelControllerTrait
{
    protected $formName                     = 'form';
    protected $formModelDataSessionCellName = 'formModelData';

    /**
     * Returns object to get initial data and populate submitted data to
     *
     * @return \XLite\Model\DTO\Base\ADTO
     */
    abstract public function getFormModelObject();

    /**
     * Returns submitted data (used to show errors)
     *
     * @return array|\XLite\Model\DTO\Base\ADTO
     */
    public function getFormModelData()
    {
        $sessionData = $this->getFormModelTmpData();

        if ($sessionData) {
            $this->removeFormModelTmpData();

            return $sessionData;
        }

        $requestData = \XLite\Core\Request::getInstance()->getData();
        if (array_key_exists($this->formName, $requestData)) {

            return $requestData[$this->formName];
        }

        return [];
    }


    /**
     * Store data to session
     *
     * @return array|\XLite\Model\DTO\Base\ADTO
     */
    public function getFormModelTmpData()
    {
        return \XLite\Core\Session::getInstance()->{$this->formModelDataSessionCellName};
    }

    /**
     * Store data to session
     *
     * @param $data
     */
    public function saveFormModelTmpData($data)
    {
        \XLite\Core\Session::getInstance()->{$this->formModelDataSessionCellName} = $data;
    }

    /**
     * Remove data from session
     */
    public function removeFormModelTmpData()
    {
        $this->saveFormModelTmpData(null);
    }
}
