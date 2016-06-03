<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model\Order\Parcel;

/**
 * Class represents a Canada Post Shipment Manifests links (returned by the "Transmit Shipments" request)
 *
 * @Entity
 * @Table  (name="order_capost_parcel_manifests")
 */
class Manifest extends \XLite\Module\XC\CanadaPost\Model\Base\Link
{
    /**
     * PO number for the manifest
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $poNumber;

    /**
     * Shipments
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ManyToMany (targetEntity="XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment", inversedBy="manifests")
     * @JoinTable (
     *      name="order_capost_parcel_shipments_manifests",
     *      joinColumns={@JoinColumn (name="manifest_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn (name="shipment_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $shipments;

    /**
     * This structure represents a list of links to information relating to the manifest (referece to the manifest's links model)
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\CanadaPost\Model\Order\Parcel\Manifest\Link", mappedBy="manifest", cascade={"all"})
     */
    protected $links;

    /**
     * Constructor
     *
     * @param array $data Entity properties (OPTIONAL)
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        $this->shipments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->links = new \Doctrine\Common\Collections\ArrayCollection();
        
        parent::__construct($data);
    }

    /**
     * Associate a shipment
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment $shipment Shipment object
     *
     * @return void
     */
    public function addShipment(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment $shipment)
    {
        $this->shipments[] = $shipment;
    }

    /**
     * Add a link to manifest
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Manifest\Link $newLink Link object
     *
     * @return void
     */
    public function addLink(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Manifest\Link $newLink)
    {
        $newLink->setManifest($this);

        $this->addLinks($newLink);
    }
    
    /**
     * Get Canada Post manifest ID
     *
     * @return string|null
     */
    public function getManifestId()
    {
        $manifestId = null;
        
        if ($this->getHref()) {

            preg_match('/manifest\/(\d+)$/', $this->getHref(), $matches);
            
            $manifestId = $matches[1];
        }
        
        return $manifestId;
    }

    // {{{ Canda Post API calls
    
    /**
     * Call "Get Maifest" request
     *
     * @return boolean
     */
    public function callApiGetManifest()
    {
        $result = false;
        
        $data = \XLite\Module\XC\CanadaPost\Core\API::getInstance()->callGetManifestRequest($this);

        if (isset($data->errors)) {
            
            // Save errors
            $this->apiCallErrors = $data->errors;

        } else {

            sleep(2); // to get Canada Post server time to generate PDF documents

            $this->setPoNumber($data->poNumber);

            foreach ($data->links as $link) {

                $manifestLink = new \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Manifest\Link();
                $manifestLink->setManifest($this);

                $this->addLink($manifestLink);

                foreach (array('rel', 'href', 'mediaType', 'idx') as $linkField) {

                    if (isset($link->{$linkField})) {
                        $manifestLink->{'set' . \XLite\Core\Converter::convertToCamelCase($linkField)}($link->{$linkField});
                    }
                }

                if (
                    !$manifestLink->callApiGetArtifact()
                    && $manifestLink->getApiCallErrors()
                ) {
                    // Error is occurred while downloading PDF documents
                    if (!isset($this->apiCallErrors)) {
                        $this->apiCallErrors = array();
                    }

                    $this->apiCallErrors += $manifestLink->getApiCallErrors();
                }
            }

            \XLite\Core\Database::getEM()->flush();
 
            $result = true;
        }

        return $result;
    }

    // }}}

    /**
     * Set poNumber
     *
     * @param string $poNumber
     * @return Manifest
     */
    public function setPoNumber($poNumber)
    {
        $this->poNumber = $poNumber;
        return $this;
    }

    /**
     * Get poNumber
     *
     * @return string 
     */
    public function getPoNumber()
    {
        return $this->poNumber;
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
     * Set rel
     *
     * @param string $rel
     * @return Manifest
     */
    public function setRel($rel)
    {
        $this->rel = $rel;
        return $this;
    }

    /**
     * Get rel
     *
     * @return string 
     */
    public function getRel()
    {
        return $this->rel;
    }

    /**
     * Set href
     *
     * @param string $href
     * @return Manifest
     */
    public function setHref($href)
    {
        $this->href = $href;
        return $this;
    }

    /**
     * Get href
     *
     * @return string 
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * Set idx
     *
     * @param integer $idx
     * @return Manifest
     */
    public function setIdx($idx)
    {
        $this->idx = $idx;
        return $this;
    }

    /**
     * Get idx
     *
     * @return integer 
     */
    public function getIdx()
    {
        return $this->idx;
    }

    /**
     * Set mediaType
     *
     * @param string $mediaType
     * @return Manifest
     */
    public function setMediaType($mediaType)
    {
        $this->mediaType = $mediaType;
        return $this;
    }

    /**
     * Get mediaType
     *
     * @return string 
     */
    public function getMediaType()
    {
        return $this->mediaType;
    }

    /**
     * Add shipments
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment $shipments
     * @return Manifest
     */
    public function addShipments(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment $shipments)
    {
        $this->shipments[] = $shipments;
        return $this;
    }

    /**
     * Get shipments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getShipments()
    {
        return $this->shipments;
    }

    /**
     * Add links
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Manifest\Link $links
     * @return Manifest
     */
    public function addLinks(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Manifest\Link $links)
    {
        $this->links[] = $links;
        return $this;
    }

    /**
     * Get links
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLinks()
    {
        return $this->links;
    }
}
