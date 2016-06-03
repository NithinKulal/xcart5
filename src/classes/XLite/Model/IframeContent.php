<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Iframe content 
 *
 * @Entity
 * @Table  (name="iframe_contents")
 */
class IframeContent extends \XLite\Model\AEntity
{
    /**
     * Unique id 
     * 
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Form URL
     * 
     * @var string
     *
     * @Column (type="string")
     */
    protected $url;

    /**
     * Form method
     *
     * @var string
     *
     * @Column (type="string", length=16)
     */
    protected $method = 'POST';

    /**
     * Form data 
     * 
     * @var array
     *
     * @Column (type="array")
     */
    protected $data = array();

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return IframeContent
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set method
     *
     * @param string $method
     * @return IframeContent
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Get method
     *
     * @return string 
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return IframeContent
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get data
     *
     * @return array 
     */
    public function getData()
    {
        return $this->data;
    }
}

