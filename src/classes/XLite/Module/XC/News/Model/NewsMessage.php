<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\News\Model;

/**
 * NewsMessage
 *
 * @Entity
 * @Table  (name="news",
 *      indexes={
 *          @Index (name="enabled", columns={"enabled"}),
 *      }
 * )
 * @HasLifecycleCallbacks
 */
class NewsMessage extends \XLite\Model\Base\Catalog
{
    /**
     * Unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Is menu enabled or not
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Date add news message
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $date;

    /**
     * Clean URLs
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\CleanURL", mappedBy="newsMessage", cascade={"all"})
     * @OrderBy   ({"id" = "ASC"})
     */
    protected $cleanURLs;

    /**
     * Meta description type
     *
     * @var string
     *
     * @Column (type="string", length=1)
     */
    protected $metaDescType = 'A';

    /**
     * Prepare order before save data operation
     *
     * @return void
     *
     * @PrePersist
     * @PreUpdate
     */
    public function prepareBeforeSave()
    {
        parent::prepareBeforeSave();

        if (!is_numeric($this->date) || !is_int($this->date)) {
            $this->setDate(\XLite\Core\Converter::time());
        }
    }

    /**
     * Check - news is enabled or not
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->getEnabled()
            && $this->getDate() < \XLite\Core\Converter::time();
    }

    /**
     * Get front URL
     *
     * @return string
     */
    public function getFrontURL()
    {
        $url = null;
        if ($this->getId()) {
            $url = \XLite\Core\Converter::makeURLValid(
                \XLite::getInstance()->getShopURL(
                    \XLite\Core\Converter::buildURL(
                        'newsMessage',
                        '',
                        array('id' => $this->getId()),
                        \XLite::getCustomerScript(),
                        true
                    )
                )
            );
        }

        return $url;
    }

    /**
     * Returns meta description
     *
     * @return string
     */
    public function getMetaDesc()
    {
        return 'A' === $this->getMetaDescType() || !$this->getSoftTranslation()->getMetaDesc()
            ? strip_tags($this->getBody())
            : $this->getSoftTranslation()->getMetaDesc();
    }

    /**
     * Returns meta description type
     *
     * @return string
     */
    public function getMetaDescType()
    {
        $result = $this->metaDescType;

        if (!$result) {
            $metaDescPresent = array_reduce($this->getTranslations()->toArray(), function ($carry, $item) {
                return $carry ?: (bool) $item->getMetaDesc();
            }, false);

            $result = $metaDescPresent ? 'C' : 'A';
        }

        return $result;
    }

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
     * Set enabled
     *
     * @param boolean $enabled
     * @return NewsMessage
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return NewsMessage
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return integer 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set metaDescType
     *
     * @param string $metaDescType
     * @return NewsMessage
     */
    public function setMetaDescType($metaDescType)
    {
        $this->metaDescType = $metaDescType;
        return $this;
    }

    /**
     * Add cleanURLs
     *
     * @param \XLite\Model\CleanURL $cleanURLs
     * @return NewsMessage
     */
    public function addCleanURLs(\XLite\Model\CleanURL $cleanURLs)
    {
        $this->cleanURLs[] = $cleanURLs;
        return $this;
    }

    /**
     * Get cleanURLs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCleanURLs()
    {
        return $this->cleanURLs;
    }
}
