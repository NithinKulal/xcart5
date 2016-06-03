<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Form unique id
 *
 * @Entity
 * @Table  (name="form_ids",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="fs", columns={"form_id","session_id"})
 *      },
 *      indexes={
 *          @Index (name="session_id", columns={"session_id"})
 *      }
 * )
 * @HasLifecycleCallbacks
 */
class FormId extends \XLite\Model\AEntity
{
    /**
     * Maximum TTL of form id (1 hour)
     */
    const MAX_FORM_ID_TTL = 3600;

    
    /**
     * Unique id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", nullable=false)
     */
    protected $id;

    /**
     * Form unique id
     *
     * @var string
     *
     * @Column (type="string", length=32)
     */
    protected $form_id;

    /**
     * Date
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $date;

    /**
     * @var \XLite\Model\Session
     *
     * @ManyToOne  (targetEntity="XLite\Model\Session", inversedBy="formIds")
     * @JoinColumn (name="session_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $session;

    /**
     * Set date (readonly)
     *
     * @param integer $value Date
     *
     * @return void
     */
    public function setDate($value)
    {
    }

    /**
     * Get session ID
     *
     * @return integer
     */
    public function getSessionId()
    {
        return $this->getSession()->getId();
    }

    /**
     * Prepare form id
     *
     * @return void
     * @PrePersist
     */
    public function prepareFormId()
    {
        if (!$this->getFormId()) {
            $this->form_id = $this->getRepository()->generateFormId($this->getSessionId());
        }

        if (!$this->getDate()) {
            $this->date = \XLite\Core\Converter::time() + static::MAX_FORM_ID_TTL;
        }
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
     * Set form_id
     *
     * @param string $formId
     * @return FormId
     */
    public function setFormId($formId)
    {
        $this->form_id = $formId;
        return $this;
    }

    /**
     * Get form_id
     *
     * @return string 
     */
    public function getFormId()
    {
        return $this->form_id;
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
     * Set session
     *
     * @param \XLite\Model\Session $session
     * @return FormId
     */
    public function setSession(\XLite\Model\Session $session = null)
    {
        $this->session = $session;
        return $this;
    }

    /**
     * Get session
     *
     * @return \XLite\Model\Session 
     */
    public function getSession()
    {
        return $this->session;
    }
}
