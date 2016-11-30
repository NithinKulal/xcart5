<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Access control cell repo
 */
class AccessControlCell extends \XLite\Model\Repo\ARepo
{
    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('hash'),
    );

    /**
     * Generate AccessControlCell by Entity and Zone(optional)
     *
     * @param \XLite\Model\AEntity[] $entities
     * @param array                  $zones
     * @param string                 $resendMethod resend method name in \XLite\Controller\Customer\AccessControl
     *
     * @return \XLite\Model\AccessControlCell
     * @throws \Exception
     */
    public function generateAccessControlCell($entities = [], $zones = [], $resendMethod = null)
    {
        $typeRepo = \XLite\Core\Database::getRepo('\XLite\Model\AccessControlEntityType');
        $zoneRepo = \XLite\Core\Database::getRepo('\XLite\Model\AccessControlZoneType');

        $acc = $this->generateBaseAccessControlCell();

        try {
            foreach ($entities as $entity) {
                $ace = new \XLite\Model\AccessControlEntity();
                $ace->setEntity($entity);
                $ace->setCell($acc);
                $acc->addAccessControlEntity($ace);
                $typeRepo->insert($ace, false);
            }

            foreach ($zones as $zone) {
                $acz = new \XLite\Model\AccessControlZone();
                if (is_object($zone) && $zone instanceof \XLite\Model\AccessControlZone) {
                    $acz->setTypeByZone($zone);
                } elseif (is_object($zone) && $zone instanceof \XLite\Model\AccessControlZoneType) {
                    $acz->setType($zone);
                } else {
                    $acz->setTypeByName($zone);
                }
                $acz->setCell($acc);
                $acc->addAccessControlZone($acz);
                $zoneRepo->insert($acz, false);
            }

            $acc->setResendMethod($resendMethod);

            \XLite\Core\Database::getEM()->flush();
        } catch (\XLite\Core\Exception\AccessControlEntityTypeNotFoundException $e) {
            $this->delete($acc);
            throw $e;
        } catch (\XLite\Core\Exception\AccessControlZoneTypeNotFoundException $e) {
            $this->delete($acc);
            throw $e;
        }

        return $acc;
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndHash(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere($this->getMainAlias($queryBuilder) . '.hash = :hash')
                ->setParameter('hash', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndHashes(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (is_array($value) && !empty($value)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in($this->getMainAlias($queryBuilder) . '.hash', $value)
            );
        }
    }

    /**
     * Generate new Access Control Cell
     *
     * @return \XLite\Model\AccessControlCell
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function generateBaseAccessControlCell()
    {
        $acc = new \XLite\Model\AccessControlCell();
        $acc->setHash(null);
        $acc->setReturnData([]);
        $this->insert($acc);

        $em = \XLite\Core\Database::getEM();
        $conn = $em->getConnection();

        $conn->beginTransaction();

        try {
            $unique = false;

            do {
                $hash = $this->generateHash();

                try {
                    $em->createQueryBuilder()
                        ->select('acc.id')
                        ->from('XLite\Model\AccessControlCell', 'acc')
                        ->where('acc.hash = :hash')
                        ->setParameter('hash', $hash)
                        ->getQuery()
                        ->getSingleResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                    $unique = true;
                }

            } while (!$unique);

            $qb = $em->createQueryBuilder();
            $qb->update('XLite\Model\AccessControlCell', 'acc')
                ->set('acc.hash', $qb->expr()->literal($hash))
                ->where('acc.id = :acc_id')
                ->setParameter('acc_id', $acc->getUniqueIdentifier())
                ->getQuery()
                ->execute();

            $acc->setHash($hash);

            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
            $this->delete($acc);

            throw $e;
        }

        return $acc;
    }

    /**
     * Generate random hash
     *
     * @return mixed
     */
    public function generateHash()
    {
        return function_exists('openssl_random_pseudo_bytes')
            ? bin2hex(openssl_random_pseudo_bytes(32))
            : (md5(microtime(true) + mt_rand(0, 1000000)) . md5(microtime(true) + mt_rand(0, 1000000)));
    }

    /**
     * Find Access Control Cell by hash
     *
     * @param $hash
     *
     * @return null|object
     */
    public function findByHash($hash)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->hash = $hash;
        $result = $this->search($cnd);

        return count($result) ? $result[0] : null;
    }

    /**
     * Find Access Control Cell by hash
     *
     * @param $hashes
     *
     * @return array
     */
    public function findByHashes(array $hashes)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->hashes = $hashes;
        return $this->search($cnd);
    }
}