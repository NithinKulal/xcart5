<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model;

/**
 * Class represents a products return
 *
 * @Entity
 * @Table  (name="capost_returns",
 *      indexes={
 *          @Index (name="date", columns={"date"}),
 *          @Index (name="status", columns={"status"})
 *      }
 * )
 * @HasLifecycleCallbacks
 */
class ProductsReturn extends \XLite\Model\AEntity
{
    /**
     * Return statuses
     */
    const STATUS_INIT     = 'I';
    const STATUS_REJECTED = 'R';
    const STATUS_APPROVED = 'A';

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
     * Referece to the return items model
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\CanadaPost\Model\ProductsReturn\Item", mappedBy="return", cascade={"all"})
     */
    protected $items;

    /**
     * This structure represents a list of links 
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\CanadaPost\Model\ProductsReturn\Link", mappedBy="return", cascade={"all"})
     */
    protected $links;

    /**
     * Referece to the orders model
     *
     * @var \XLite\Model\Order
     *
     * @ManyToOne  (targetEntity="XLite\Model\Order", inversedBy="capostReturns")
     * @JoinColumn (name="orderId", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Status code
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=2)
     */
    protected $status = self::STATUS_INIT;

    /**
     * Previous status code
     *
     * @var string
     */
    protected $oldStatus = self::STATUS_INIT;

    /**
     * Creation timestamp
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $date;

    /**
     * Last renew timestamp
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $lastRenewDate = 0;

    /**
     * Customer notes
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $notes = '';

    /**
     * Admin notes
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $adminNotes = '';

    /**
     * Tracking PIN code
     *
     * @var string
     *
     * @Column (type="string", length=16, nullable=true)
     */
    protected $trackingPin;

    // {{{ Service methods

    /**
     * Constructor
     *
     * @param array $data Entity properties (OPTIONAL)
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
        $this->links = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Set old status (not stored in the DB)
     *
     * @param string $value Status code
     *
     * @return void
     */
    public function setOldStatus($value)
    {
        $this->oldStatus = $value;
    }

    /**
     * Set order
     *
     * @param \XLite\Model\Order $order Order object (OPTIONAL)
     *
     * @return void
     */
    public function setOrder(\XLite\Model\Order $order = null)
    {
        $this->order = $order;
    }

    /**
     * Add an item 
     *
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn\Item $newItem Item object
     *
     * @return void
     */
    public function addItem(\XLite\Module\XC\CanadaPost\Model\ProductsReturn\Item $newItem)
    {
        $newItem->setReturn($this);

        $this->addItems($newItem);
    }

    /**
     * Add a link 
     *
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn\Link $newLink Link model
     *
     * @return void
     */
    public function addLink(\XLite\Module\XC\CanadaPost\Model\ProductsReturn\Link $newLink)
    {
        $newLink->setReturn($this);

        $this->addLinks($newLink);
    }

    // }}}

    /**
     * Return list of all allowed product return statuses
     *
     * @param string $status Status to get OPTIONAL
     *
     * @return array|string
     */
    public static function getAllowedStatuses($status = null)
    {
        $list = array(
            static::STATUS_INIT     => 'Requires authorization',
            static::STATUS_APPROVED => 'Approved',
            static::STATUS_REJECTED => 'Rejected',
        );

        return isset($status)
            ? (isset($list[$status]) ? $list[$status] : null)
            : $list;
    }
    
    /**
     * Get formatted return ID
     *
     * @return string
     */
    public function getNumber()
    {
        return '#' . str_pad($this->getId(), 5, 0, STR_PAD_LEFT);
    }

    // {{{ Change status routine

    /**
     * Status handlers list
     *
     * @var array
     */
    protected static $statusHandlers = array(
        self::STATUS_INIT         => array(
            self::STATUS_REJECTED => 'reject',
            self::STATUS_APPROVED => 'approve',
        ),
        self::STATUS_APPROVED     => array(),
        self::STATUS_REJECTED     => array(),
    );

    /**
     * Set status
     *
     * @param string $value Status code
     *
     * @return boolean
     */
    public function setStatus($value)
    {
        $oldStatus = ($this->status != $value) ? $this->status : null;

        $result = false;

        $statusHandler = $this->getStatusHandler($oldStatus, $value);

        if (
            $oldStatus
            && $this->isPersistent()
            && !empty($statusHandler)
        ) {
            $result = $this->{'handleStatusChange' . ucfirst($statusHandler)}();
            
            if ($result) {
                $this->oldStatus = $oldStatus;
                $this->status = $value;
            }

            \XLite\Core\Database::getEM()->flush();
        }

        return $result;
    }

    /**
     * Check if product return can be proposed
     *
     * @retrun boolean
     */
    public function canBeApproved()
    {
        return (
            static::STATUS_INIT == $this->getStatus()
        );
    }
    
    /**
     * Check if product return can be transmitted
     *
     * @return boolean
     */
    public function canBeRejected()
    {
        return (
            static::STATUS_INIT == $this->getStatus()
        );
    }

    /**
     * Return base part of the certain "change status" handler name
     *
     * @param string $old Old status code
     * @param string $new New status code
     *
     * @return string
     */
    protected function getStatusHandler($old, $new)
    {
        return (isset(static::$statusHandlers[$old][$new])) ? static::$statusHandlers[$old][$new] : '';
    }

    /**
     * Status change handler: "Requires Authorization" to "Approved"
     *
     * @return boolean
     */
    protected function handleStatusChangeApprove()
    {
        $result = false;

        if ($this->canBeApproved()) {

            $result = $this->callApiCreateAuthorizedReturn();

            if ($result) {
                
                // Send email notification
                \XLite\Core\Mailer::sendProductsReturnApproved($this);
            }

            $result = true;
        }

        return $result;
    }

    /**
     * Status change handler: "Requires Authorization" to "Rejected"
     *
     * @return boolean
     */
    protected function handleStatusChangeReject()
    {
        $result = false;

        if ($this->canBeRejected()) {
            
            // Send email notifications
            \XLite\Core\Mailer::sendProductsReturnRejected($this);

            $result = true;
        }

        return $result;
    }

    // }}}
    
    // {{{ Lifecycle callbacks
        
    /**
     * Prepare before saving 
     *
     * @PrePersist
     * @PreUpdate
     *
     * @return void
     */
    public function prepareBeforeSave()
    {
        if (!is_numeric($this->date) || !is_int($this->date)) {
            $this->setDate(\XLite\Core\Converter::time());
        }

        $this->setLastRenewDate(\XLite\Core\Converter::time());
    }

    // }}}

    // {{{ Helper methods
    
    /**
     * Get total return items amount
     *
     * @return integer
     */
    public function getItemsTotalAmount()
    {
        $totalAmount = 0;
        
        foreach ($this->getItems() as $item) {
            $totalAmount += $item->getAmount();
        }
        
        return $totalAmount;
    }
    
    /**
     * Get total cost of all returned items 
     *
     * @return float
     */
    public function getItemsTotalCost()
    {
        $totalCost = 0;

        foreach ($this->getItems() as $item) {
            $totalCost += $item->getOrderItem()->getPrice() * $item->getAmount();
        }
        
        return $totalCost;
    }

    /**
     * Get total weight of all returned items
     *
     * @return float
     */
    public function getItemsTotalWeight()
    {
        $totalWeight = 0;

        foreach ($this->getItems() as $item) {
            $totalWeight += $item->getOrderItem()->getObject()->getWeight() * $item->getAmount();
        }

        return $totalWeight;
    }
    
    /**
     * Check - return has links or not
     *
     * @return boolean
     */
    public function hasLinks()
    {
        return 0 < $this->getLinks()->count();
    }
    
    /**
     * Get "return label" link model
     *
     * @return \XLite\Module\XC\CanadaPost\Model\Repo\ProductsReturn|null
     */
    public function getReturnLabelLink()
    {
        return $this->getLinkByRel('returnLabel');
    }

    /**
     * Get link by rel field
     *
     * @param string $rel Link's rel field value
     *
     * @return \XLite\Module\XC\CanadaPost\Model\Repo\ProductsReturn|null
     */
    public function getLinkByRel($rel)
    {
        $link = null;

        foreach ($this->getLinks() as $_link) {
            if ($_link->getRel() == $rel) {
                $link = $_link;
                break;
            }
        }

        return $link;
    }

    // }}}

    // {{{ Canda Post API calls

    /**
     * Canada Post API calls errors
     *
     * @var null|array
     */
    protected $apiCallErrors = null;

    /**
     * Get Canada Post API call errors
     *
     * @return null|array
     */
    public function getApiCallErrors()
    {
        return $this->apiCallErrors;
    }
    
    /**
     * Call "Create Authorized Return" request
     * To get error message you need to call "getApiCallErrors" method (if return is false)
     *
     * @return boolean
     */
    protected function callApiCreateAuthorizedReturn()
    {
        $result = false;

        if ($this->hasLinks()) {

            // Return already has links (documents)
            // TODO: probably here should be the procedure that will check all links and download it's files

            $result = true;

        } else { 

            $data = \XLite\Module\XC\CanadaPost\Core\Service\Returns::getInstance()->callCreateAuthorizedReturnByProductsReturn($this);
        
            $result = $this->handleCreateAuthorizedReturnResult($data);

            if ($result) {

                // Dowload documents (aka artifacts)
                sleep(2); // lets give to Canada Post server 2 seconds to generate PDF documents
            
                $this->downloadArtifacts();
            }
        }
        
        return $result;
    }

    /**
     * Handle "Create Authorized Return" request return
     *
     * @param \XLite\Core\CommonCell $data Returned value
     *
     * @return boolean
     */
    protected function handleCreateAuthorizedReturnResult(\XLite\Core\CommonCell $data)
    {
        $result = false;

        if (isset($data->errors)) {

            // Parse errors
            $this->apiCallErrors = $data->errors;
        
        } else if (isset($data->authorizedReturnInfo)) {
            
            $this->trackingPin = $data->authorizedReturnInfo->trackingPin;

            foreach ($data->authorizedReturnInfo->links as $_link) {

                $link = new \XLite\Module\XC\CanadaPost\Model\ProductsReturn\Link();
                $link->setReturn($this);

                $this->addLink($link);
                
                \XLite\Core\Database::getEM()->persist($link);

                foreach (array('rel', 'href', 'mediaType', 'idx') as $_field) {
                    $link->{'set' . \XLite\Core\Converter::convertToCamelCase($_field)}($_link->{$_field});
                }
            }

            \XLite\Core\Database::getEM()->flush();

            $result = true;
        }

        return $result;
    }
    
    /**
     * Download related artifacts (documents)
     *
     * @return void
     */
    protected function downloadArtifacts()
    {
        $links = $this->getLinks();
        
        if (isset($links)) {

            foreach ($links as $k => $link) {

                $link->callApiGetArtifact();

                if ($link->getApiCallErrors()) {
                    // Save errors
                    // $this->apiCallErrors = array_merge((array) $this->apiCallErrors, $link->getApiCallErrors());
                    // TODO: change errors API
                }
            }
        }

        \XLite\Core\Database::getEM()->flush();
    }

    // }}}

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
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return ProductsReturn
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
     * Set lastRenewDate
     *
     * @param integer $lastRenewDate
     * @return ProductsReturn
     */
    public function setLastRenewDate($lastRenewDate)
    {
        $this->lastRenewDate = $lastRenewDate;
        return $this;
    }

    /**
     * Get lastRenewDate
     *
     * @return integer 
     */
    public function getLastRenewDate()
    {
        return $this->lastRenewDate;
    }

    /**
     * Set notes
     *
     * @param text $notes
     * @return ProductsReturn
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * Get notes
     *
     * @return text 
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set adminNotes
     *
     * @param text $adminNotes
     * @return ProductsReturn
     */
    public function setAdminNotes($adminNotes)
    {
        $this->adminNotes = $adminNotes;
        return $this;
    }

    /**
     * Get adminNotes
     *
     * @return text 
     */
    public function getAdminNotes()
    {
        return $this->adminNotes;
    }

    /**
     * Set trackingPin
     *
     * @param string $trackingPin
     * @return ProductsReturn
     */
    public function setTrackingPin($trackingPin)
    {
        $this->trackingPin = $trackingPin;
        return $this;
    }

    /**
     * Get trackingPin
     *
     * @return string 
     */
    public function getTrackingPin()
    {
        return $this->trackingPin;
    }

    /**
     * Add items
     *
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn\Item $items
     * @return ProductsReturn
     */
    public function addItems(\XLite\Module\XC\CanadaPost\Model\ProductsReturn\Item $items)
    {
        $this->items[] = $items;
        return $this;
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Add links
     *
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn\Link $links
     * @return ProductsReturn
     */
    public function addLinks(\XLite\Module\XC\CanadaPost\Model\ProductsReturn\Link $links)
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

    /**
     * Get order
     *
     * @return \XLite\Model\Order 
     */
    public function getOrder()
    {
        return $this->order;
    }
}
