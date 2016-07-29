<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Model;

/**
 * The search cache model class
 * 
 * @Entity
 * @Table (name="search_cache",
 *      indexes={
 *          @Index (name="key_index", columns={"keyValue"}),
 *          @Index (name="time_index", columns={"actualTime"})
 *      }
 *  )
 */
class SearchCache extends \XLite\Model\AEntity
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
     * Key value (md4 storage)
     *
     * @var string
     *
     * @Column (type="string", length=32)
     */
    protected $keyValue;

    /**
     * Search result
     *
     * @var array
     *
     * @Column (type="array")
     */
    protected $resultValue = array();

    /**
     * Actual time last moment
     *
     * @var integer
     * 
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $actualTime = 0;


    public function setKeyValue($value)
    {
        $this->keyValue = $value;
    }

    public function setResultValue($value)
    {
        $this->resultValue = $value;
    }

    public function setActualTime($value)
    {
        $this->actualTime = $value;
    }

    public function getKeyValue()
    {
        return $this->keyValue;
    }

    public function getActualTime()
    {
        return $this->actualTime;
    }

    public function getResultValue()
    {
        return $this->resultValue;
    }
}
