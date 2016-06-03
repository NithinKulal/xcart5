<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Form id repository
 */
class FormId extends \XLite\Model\Repo\ARepo
{
    /**
     * Form id length
     */
    const FORM_ID_LENGTH = 32;


    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SERVICE;

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = array(
        'date' => false,
        'id'   => false,
    );

    /**
     * Form id characters list
     *
     * @var array
     */
    protected $chars = array(
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
        'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
        'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
        'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
        'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
        'Y', 'Z',
    );

    /**
     * Frontier length
     *
     * @var integer
     */
    protected $frontierLength = 100;

    /**
     * Count session by public session id
     *
     * @param string               $formId  Form id
     * @param \XLite\Model\Session $session Session OPTIONAL
     *
     * @return integer
     */
    public function countByFormIdAndSession($formId, $session = null)
    {
        if (!isset($session)) {
            $session = \XLite\Core\Session::getInstance()->getModel();
        }

        return intval($this->defineByFormIdAndSessionQuery($formId, $session)->getSingleScalarResult());
    }

    /**
     * Generate public session id
     *
     * @param \XLite\Model\Session $session Session OPTIONAL
     *
     * @return string
     */
    public function generateFormId($session = null)
    {
        if (!isset($session)) {
            $session = \XLite\Core\Session::getInstance()->getModel();
        }
        $iterationLimit = 30;
        $limit = count($this->chars) - 1;

        do {
            $id = '';
            for ($i = 0; self::FORM_ID_LENGTH > $i; $i++) {
                $id .= $this->chars[mt_rand(0, $limit)];
            }
            $iterationLimit--;
        } while (0 < $this->countByFormIdAndSession($id, $session) && 0 < $iterationLimit);

        if (0 == $iterationLimit) {
            // TODO - add throw exception
            \XLite\Logger::logCustom(
                'FORM_ID_GENERATOR',
                'Iteration limit has been reached during the Form ID generation procedure. sessionID=' . $session->getId() . ', formId=' . $id
            );
        }

        return $id;
    }

    /**
     * Remove expired form IDs
     *
     * @param \XLite\Model\Session $session Session OPTIONAL OPTIONAL
     *
     * @return void
     */
    public function removeExpired($session = null)
    {
        if (!isset($session)) {
            $session = \XLite\Core\Session::getInstance()->getModel();
        }

        if ($session && !\XLite\Core\Session::getInstance()->isDump()) {
            $id = $this->getFrontierId($session);
            if ($id) {
                $this->defineRemoveExpiredQuery($id, $session)->execute();
            }
        }
    }

    /**
     * Define query for countByFormIdAndSession) method
     *
     * @param string               $formId  Form id
     * @param \XLite\Model\Session $session Session
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineByFormIdAndSessionQuery($formId, $session)
    {
        return $this->createQueryBuilder('f')
            ->select('COUNT(f)')
            ->andWhere('f.session = :session AND f.form_id = :fid')
            ->setParameter('session', $session)
            ->setParameter('fid', $formId);
    }

    /**
     * Get frontier date
     *
     * @param \XLite\Model\Session $session Session
     *
     * @return integer|void
     */
    protected function getFrontierId($session)
    {
        return $this->defineGetFrontierQuery($this->frontierLength, $session)->getSingleScalarResult() ?: null;
    }

    /**
     * Define query for getFrontierId() method
     *
     * @param integer              $frontier Frontier length
     * @param \XLite\Model\Session $session  Session
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineGetFrontierQuery($frontier, $session)
    {
        return $this->createQueryBuilder('f')
            ->select('f.id')
            ->andWhere('f.session = :session')
            ->setFirstResult($frontier)
            ->setMaxResults(1)
            ->setParameter('session', $session);
    }

    /**
     * Define query for removeExpired() method
     *
     * @param integer              $id      Frontier id
     * @param \XLite\Model\Session $session Session id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineRemoveExpiredQuery($id, $session)
    {
        return $this->getQueryBuilder()
            ->delete($this->_entityName, 'f')
            ->andWhere('f.date < :time AND f.session = :session')
            ->setParameter('time', \XLite\Core\Converter::time())
            ->setParameter('session', $session);
    }
}
