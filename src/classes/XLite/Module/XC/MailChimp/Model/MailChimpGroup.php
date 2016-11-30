<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Model;

/**
 * MailChimp mail group
 *
 * @Entity
 * @Table  (name="mailchimp_list_group")
 */
class MailChimpGroup extends \XLite\Model\AEntity
{
    /**
     * Group ID
     *
     * @var string
     *
     * @Id
     * @Column (type="string", length=32)
     */
    protected $id = '';

    /**
     * Group name
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $title = '';

    /**
     * MailChimp parent list
     *
     * @var \XLite\Module\XC\MailChimp\Model\MailChimpList
     *
     * @ManyToOne (targetEntity="XLite\Module\XC\MailChimp\Model\MailChimpList", inversedBy="groups")
     */
    protected $list;

    /**
     * Group names
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\MailChimp\Model\MailChimpGroupName", mappedBy="group", cascade={"all"})
     */
    protected $names;

    /**
     * Group type
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $type = '';

    /**
     * Enabled
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    function __construct(array $data = array())
    {
        $this->names     = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Set id
     *
     * @param string $id
     * @return MailChimpGroup
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return MailChimpGroup
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set list
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $list
     * @return MailChimpGroup
     */
    public function setList(\XLite\Module\XC\MailChimp\Model\MailChimpList $list = null)
    {
        $this->list = $list;
        return $this;
    }

    /**
     * Get list
     *
     * @return \XLite\Module\XC\MailChimp\Model\MailChimpList 
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * Get enabled names
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEnabledNames()
    {
        return $this->getNames()->filter(function($name){
            return $name->getEnabled();
        });
    }

    /**
     * Add group names
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpGroupName $names
     * @return MailChimpGroup
     */
    public function addNames(\XLite\Module\XC\MailChimp\Model\MailChimpGroupName $names)
    {
        $this->names[] = $names;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }
}
