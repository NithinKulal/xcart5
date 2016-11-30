<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Model;

/**
 * MailChimp Store
 *
 * @Entity (repositoryClass="\XLite\Module\XC\MailChimp\Model\Repo\Store")
 * @Table  (name="mailchimp_store")
 */
class Store extends \XLite\Model\AEntity
{
    /**
     * Store ID
     *
     * @var string
     *
     * @Id
     * @Column (type="string", length=32)
     */
    protected $id = '';

    /**
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $name = '';

    /**
     * @var \XLite\Module\XC\MailChimp\Model\MailChimpList
     *
     * @OneToOne   (targetEntity="XLite\Module\XC\MailChimp\Model\MailChimpList", inversedBy="store")
     * @JoinColumn (name="list_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $list;
    
    /**
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $main = false;
    
    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return MailChimpList
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param MailChimpList $list
     */
    public function setList($list)
    {
        $this->list = $list;
    }

    /**
     * @return boolean
     */
    public function getMain()
    {
        return $this->main;
    }

    /**
     * @return boolean
     */
    public function isMain()
    {
        return $this->main;
    }

    /**
     * @param boolean $main
     */
    public function setMain($main)
    {
        $this->main = (bool) $main;
    }
}
