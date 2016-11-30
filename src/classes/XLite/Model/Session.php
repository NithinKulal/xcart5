<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Session
 *
 * @Entity
 * @Table  (name="sessions",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="sid", columns={"sid"})
 *      },
 *      indexes={
 *          @Index (name="expiry", columns={"expiry"})
 *      }
 * )
 * @HasLifecycleCallbacks
 */
class Session extends \XLite\Model\AEntity
{
    /**
     * Maximum admin session TTL (12 hours)
     */
    const MAX_ADMIN_TTL = 43200;

    /**
     * Maximum customer session TTL (7 days)
     */
    const MAX_CUSTOMER_TTL = 604800;


    /**
     * Session increment id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $id;

    /**
     * Public session id
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=32)
     */
    protected $sid;

    /**
     * Session expiration time
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $expiry;

    /**
     * Session cells
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\SessionCell", mappedBy="session", cascade={"persist","remove"})
     * @JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $cells;

    /**
     * Form IDs
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\FormId", mappedBy="session", cascade={"persist","remove"})
     * @JoinColumn (name="id", referencedColumnName="session_id", onDelete="CASCADE")
     */
    protected $formIds;

    /**
     * Cells cache
     *
     * @var array
     */
    protected $cache;


    /**
     * Get maximum session TTL
     *
     * @return integer
     */
    protected static function getMaxTTL()
    {
        return \XLite::isAdminZone()
            ? self::MAX_ADMIN_TTL
            : self::MAX_CUSTOMER_TTL;
    }

    /**
     * Return instance of the session cell repository
     *
     * @return \XLite\Model\Repo\SessionCell
     */
    protected static function getSessionCellRepo()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\SessionCell');
    }

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        $this->cells   = new \Doctrine\Common\Collections\ArrayCollection();
        $this->formIds = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Set session id
     *
     * @param string $value Session id
     *
     * @return void
     */
    public function setSid($value)
    {
        $this->sid = $value;
    }

    /**
     * Update expiration time
     *
     * @return void
     */
    public function updateExpiry()
    {
        $ttl = \XLite\Core\Session::getTTL();
        $this->setExpiry(0 < $ttl ? $ttl : \XLite\Core\Converter::time() + self::getMaxTTL());
    }

    /**
     * Session cell getter
     *
     * @param string $name Cell name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $cell = $this->getCellByName($name);

        return $cell ? $cell->getValue() : null;
    }

    /**
     * Session cell setter
     *
     * @param string $name  Cell name
     * @param mixed  $value Value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->setCellValue($name, $value);
    }

    /**
     * Check - set session cell with specified name or not
     *
     * @param string $name Cell name
     *
     * @return boolean
     */
    public function __isset($name)
    {
        return !is_null($this->getCellByName($name));
    }

    /**
     * Remove session cell
     *
     * @param string $name Cell name
     *
     * @return void
     */
    public function __unset($name)
    {
        $this->setCellValue($name, null);
    }

    /**
     * Unset in batch mode
     *
     * @param string $name Cell name
     *
     * @return void
     */
    public function unsetBatch($name)
    {
        $count = 0;
        foreach (func_get_args() as $name) {
            $cell = $this->getCellByName($name);
            if ($cell && !$cell->isDetached()) {
                static::getSessionCellRepo()->delete($cell, false);
                $count++;
            }
        }

        if (0 < $count) {
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Return cached session cells
     *
     * @return array
     */
    public function getCellsCache()
    {
        if (!isset($this->cache)) {
            // Initialize cache
            $this->cache = array();

            foreach ($this->getCells() as $cell) {
                $this->cache[$cell->getName()] = $cell;
            }
        }

        return $this->cache;
    }

    /**
     * Get session cell by name
     *
     * @param string  $name        Cell name
     *
     * @return \XLite\Model\SessionCell|void
     */
    protected function getCellByName($name)
    {
        return \Includes\Utils\ArrayManager::getIndex($this->getCellsCache(), $name, true);
    }

    /**
     * Set session cell value
     *
     * @param string $name  Cell name
     * @param mixed  $value Value to set
     *
     * @return void
     */
    protected function setCellValue($name, $value)
    {
        // Check if cell exists (need to perform update or delete)
        $cell = $this->getCellByName($name);

        if (!$cell) {

            // Cell not found - create new
            if (isset($value)) {
                $cell = $this->insertCell($name, $value);
                $this->cache[$name] = $cell;
            }

        } elseif (isset($value)) {

            // Only perform SQL query if cell value is changed
            if ($cell->getValue() !== $value) {
                if ($cell->isDetached()) {
                    $cell = \XLite\Core\Database::getEM()->merge($cell);
                }
                $cell->map($this->prepareDataForExistingCell($value, $cell));
                \XLite\Core\Database::getEM()->persist($cell);
                \XLite\Core\Database::getEM()->flush($cell);

                $this->cache[$name] = $cell;
            }

        } else {

            // Set the "null" value to delete current cell
            if (!$cell->isDetached()) {
                $this->getCells()->removeElement($cell);
                \XLite\Core\Database::getEM()->remove($cell);
                \XLite\Core\Database::getEM()->flush($cell);
            }

            unset($this->cache[$name]);
        }
    }

    /**
     * Insert session cell.
     * This is a specific method to insert cell by using 'REPLACE INTO' query to avoid errors like 'Duplicate key...'
     *
     * @param string $name  Cell name
     * @param mixed  $value Cell value
     *
     * @return \XLite\Model\SessionCell
     */
    protected function insertCell($name, $value)
    {
        $connection = \XLite\Core\Database::getEM()->getConnection();

        $id = $this->getId();

        $data = $this->prepareDataForNewCell($id, $name, $value);

        $connection->connect();

        $cols = array();
        $placeholders = array();

        foreach ($data as $columnName => $value) {
            $cols[] = $columnName;
            $placeholders[] = '?';
        }

        $query = 'REPLACE INTO ' . static::getSessionCellRepo()->getTableName()
            . ' (' . implode(', ', $cols) . ')'
            . ' VALUES (' . implode(', ', $placeholders) . ')';

        $connection->executeUpdate($query, array_values($data));

        return static::getSessionCellRepo()->findOneBy(
            array(
                'session' => $this,
                'name'    => $name,
            )
        );
    }

    /**
     * Prepare data for cell insert
     *
     * @param integer $id    Session ID
     * @param string  $name  Cell name
     * @param mixed   $value Data to store
     *
     * @return array
     */
    protected function prepareDataForNewCell($id, $name, $value)
    {
        return array(
            'id'    => $id,
            'name'  => $name,
        ) + $this->prepareDataForExistingCell($value);
    }

    /**
     * Prepare data for cell update
     *
     * @param mixed                    $value Data to store
     * @param \XLite\Model\SessionCell $cell  Cell to update OPTIONAL
     *
     * @return array
     */
    protected function prepareDataForExistingCell($value, \XLite\Model\SessionCell $cell = null)
    {
        return array(
            'value' => \XLite\Model\SessionCell::prepareValueForSet($value),
            'type'  => \XLite\Model\SessionCell::getTypeByValue($value),
        );
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
     * Get sid
     *
     * @return string 
     */
    public function getSid()
    {
        return $this->sid;
    }

    /**
     * Set expiry
     *
     * @param integer $expiry
     * @return Session
     */
    public function setExpiry($expiry)
    {
        $this->expiry = $expiry;
        return $this;
    }

    /**
     * Get expiry
     *
     * @return integer 
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * Add cells
     *
     * @param \XLite\Model\SessionCell $cells
     * @return Session
     */
    public function addCells(\XLite\Model\SessionCell $cells)
    {
        $this->cells[] = $cells;
        return $this;
    }

    /**
     * Get cells
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCells()
    {
        return $this->cells;
    }

    /**
     * Add formIds
     *
     * @param \XLite\Model\FormId $formIds
     * @return Session
     */
    public function addFormIds(\XLite\Model\FormId $formIds)
    {
        $this->formIds[] = $formIds;
        return $this;
    }

    /**
     * Get formIds
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFormIds()
    {
        return $this->formIds;
    }

    /**
     * Do some actions to logoff
     */
    public function logoff()
    {
        $this->last_profile_id = $this->profile_id;

        foreach (\XLite\Core\Auth::getInstance()->getSessionVarsToClear() as $var) {
            unset($this->$var);
        }

        foreach (\XLite\Model\Cart::getInstance()->getSessionVarsToClearOnLogoff() as $var) {
            unset($this->$var);
        }
    }
}
