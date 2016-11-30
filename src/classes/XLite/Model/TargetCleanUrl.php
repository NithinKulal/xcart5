<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;


class TargetCleanUrl extends \XLite\Model\AEntity
{
    /**
     * Clean URL
     *
     * @var string
     */
    protected $cleanURL = '';

    /**
     * Clean URLs
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $cleanURLs;

    /**
     * @return string
     */
    public function getCleanURL()
    {
        return $this->cleanURL;
    }

    /**
     * @param string $cleanURL
     */
    public function setCleanURL($cleanURL)
    {
        $this->cleanURL = $cleanURL;
        $this->cleanURLs->add($cleanURL);
    }

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        $this->cleanURLs = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get entity unique identifier name
     *
     * @return string
     */
    public function getUniqueIdentifierName()
    {
        return 'id';
    }

    /**
     * Get entity unique identifier value
     *
     * @return integer
     */
    public function getUniqueIdentifier()
    {
        return null;
    }
}