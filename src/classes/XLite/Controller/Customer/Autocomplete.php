<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Autocomplete controller 
 */
class Autocomplete extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Data 
     * 
     * @var array
     */
    protected $data = array();

    /**
     * Process request
     *
     * @return void
     */
    public function processRequest()
    {
        $content = json_encode($this->data);

        header('Content-Type: application/json; charset=UTF-8');
        header('Content-Length: ' . strlen($content));
        header('ETag: ' . md5($content));

        print ($content);
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $dictionary = \XLite\Core\Request::getInstance()->dictionary;

        if ($dictionary) {
            $method = 'assembleDictionary' . \XLite\Core\Converter::convertToCamelCase($dictionary);
            if (method_exists($this, $method)) {

                // Method name assembled from 'assembleDictionary' + dictionary request argument
                $data = $this->$method(strval(\XLite\Core\Request::getInstance()->erm));
                $this->data = $this->processData($data);
            }
        }

        $this->silent = true;
    }

    /**
     * Process data 
     * 
     * @param array $data Key-value data
     *  
     * @return array
     */
    protected function processData(array $data)
    {
        $list = array();

        foreach ($data as $k => $v) {
            $list[] = array(
                'label' => $v,
                'value' => $k,
            );
        }

        return $list;
    }

}
