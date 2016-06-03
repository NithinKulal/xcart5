<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Measures repository
 */
class Measure extends \XLite\Model\Repo\ARepo
{
    /**
     * Get total score
     *
     * @return float
     */
    public function getScore()
    {
        $result = $this->createQueryBuilder('m')
            ->select('AVG(m.fsTime) fsTime')
            ->addSelect('AVG(m.dbTime) dbTime')
            ->addSelect('AVG(m.cpuTime) cpuTime')
            ->getArrayResult();
        $result = reset($result);

        return $result
            ? round($result['fsTime'] + $result['dbTime'] + $result['cpuTime'], 0)
            : 0;
    }

    /**
     * Get file system score
     *
     * @return float
     */
    public function getFilesystemScore()
    {
        $result = $this->createQueryBuilder('m')
            ->select('AVG(m.fsTime) time')
            ->getArrayResult();
        $result = reset($result);

        return $result ? round($result['time'], 0) : 0;
    }

    /**
     * Get database score
     *
     * @return float
     */
    public function getDatabaseScore()
    {
        $result = $this->createQueryBuilder('m')
            ->select('AVG(m.dbTime) time')
            ->getArrayResult();
        $result = reset($result);

        return $result ? round($result['time'], 0) : 0;
    }

    /**
     * Get computation score
     *
     * @return float
     */
    public function getComputationScore()
    {
        $result = $this->createQueryBuilder('m')
            ->select('AVG(m.cpuTime) time')
            ->getArrayResult();
        $result = reset($result);

        return $result ? round($result['time'], 0) : 0;
    }

    /**
     * Get last date
     *
     * @return integer
     */
    public function getLastDate()
    {
        $result = $this->createQueryBuilder('m')
            ->orderBy('m.date', 'desc')
            ->setMaxResults(1)
            ->getSingleResult();

        return $result ? $result->getDate() : null;
    }
}
