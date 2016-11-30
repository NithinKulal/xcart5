<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Access Control Actions Controller
 */
class AccessControl extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Time of lock access (sec)
     */
    const TIME_OF_LOCK_ACCESS = 120;

    /**
     * Max count of login attempt
     */
    const MAX_COUNT_OF_ACCESS_ATTEMPTS = 5;

    /**
     * Prefix for tmp vars storage access attempts
     */
    const TMP_VAR_LOCK_IP_PREFIX = 'access_control_ip_';

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->isAccessLocked()) {
            return null;
        } elseif ($this->getAccessControlCell() && $this->getAccessControlCell()->isExpired()) {
            return static::t('Access denied');
        }

        return parent::getTitle();
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        if (!$this->isAccessLocked()) {
            $this->doKeyAction(null, function (\XLite\Model\AccessControlCell $acc) {
                $this->headerStatus(403);
            });
        }
    }

    /**
     * Resend link action
     */
    protected function doActionResendLink()
    {
        if (!$this->isAccessLocked()) {
            $this->doKeyAction(null, function (\XLite\Model\AccessControlCell $acc) {
                if ($this->isResendMethodExist($acc)) {
                    $this->{$acc->getResendMethod()}($acc);
                }
            });
        } else {
            \XLite\Core\TopMessage::addError('Access is locked out');
            $this->setReturnURL($this->buildURL('access_control'));
            $this->doRedirect();
        }
    }

    /**
     * Is access control cell method exist
     *
     * @param \XLite\Model\AccessControlCell $acc
     *
     * @return bool
     */
    public function isResendMethodExist(\XLite\Model\AccessControlCell $acc = null)
    {
        if (null === $acc) {
            $acc = $this->getAccessControlCell();
        }

        $method = $acc ? $acc->getResendMethod() : null;
        return $method && method_exists($this, $method);
    }

    /**
     * Return Access control cell
     *
     * @return null|\XLite\Model\AccessControlCell
     */
    public function getAccessControlCell()
    {
        return $this->getAccessControlCellByKey();
    }

    /**
     * Return Access control cell by cell hash
     *
     * @param null $key
     *
     * @return null|\XLite\Model\AccessControlCell
     */
    protected function getAccessControlCellByKey($key = null)
    {
        if (null === $key) {
            $key = \XLite\Core\Request::getInstance()->key;
        }

        if ($key && $acc = \XLite\Core\Database::getRepo('XLite\Model\AccessControlCell')->findByHash($key)) {
            return $acc;
        }

        return null;
    }

    /**
     * Check key and perform callback if it expired
     *
     * @param null $key
     * @param null $expiredCallback
     */
    protected function doKeyAction($key = null, $expiredCallback = null)
    {
        if ($acc = $this->getAccessControlCellByKey($key)) {
            if ($acc->isExpired()) {
                if (is_callable($expiredCallback)) {
                    $expiredCallback($acc);
                }
            } else {
                \XLite\Core\Session::getInstance()->addAccessControlCellHash($acc->getHash());
                $this->setReturnURL($acc->buildReturnURL());
                $this->doRedirect();
            }
        } else {
            if (\XLite\Core\Request::getInstance()->key) {
                $this->registerAccessFailedAttempt();
            }
            $this->markAsAccessDenied();
        }
    }

    /**
     * Resend order invoice
     *
     * @param \XLite\Model\AccessControlCell $acc
     */
    protected function resendAccessLink(\XLite\Model\AccessControlCell $acc)
    {
        if ($profile = $this->getProfileFromACC($acc)) {
            $newAcc = \XLite\Core\Database::getRepo('XLite\Model\AccessControlCell')->generateAccessControlCell(
                $acc->getAccessControlEntities(),
                $acc->getAccessControlZones(),
                'resendAccessLink'
            );

            $newAcc->setReturnData($acc->getReturnData());

            \XLite\Core\Mailer::sendAccessLinkCustomer($profile, $newAcc);

            \XLite\Core\TopMessage::addInfo('Access link has been successfully sent');
            $this->setReturnURL($this->buildURL());
        } else {
            \XLite\Core\TopMessage::addError('Error sending link');
            $this->setReturnURL($this->buildURL('access_control', '', ['key' => $acc->getHash()]));
        }

        $this->doRedirect();
    }

    /**
     * Get profile from Access control cell entities
     *
     * @param \XLite\Model\AccessControlCell $acc
     *
     * @return null | \XLite\Model\Profile
     */
    protected function getProfileFromACC(\XLite\Model\AccessControlCell $acc)
    {
        $ace = $acc->getAccessControlEntityByType('\XLite\Model\Order');

        if ($ace && $order = $ace->getEntity()) {
            return $order->getProfile();
        }

        return null;
    }

    // {{{ Brute force protection

    /**
     * Register failed attempt to access
     */
    protected function registerAccessFailedAttempt()
    {
        $ip = \XLite\Core\Request::getInstance()->getClientIp();

        $tmpVarCell = static::TMP_VAR_LOCK_IP_PREFIX . $ip;

        $attemptsData = \XLite\Core\TmpVars::getInstance()->{$tmpVarCell};

        if (!$attemptsData) {
            $attemptsData = [
                'count' => 1,
                'time' => \XLite\Core\Converter::time()
            ];
        } else {
            $attemptsData['count'] = \XLite\Core\Converter::time() < ($attemptsData['time'] + static::TIME_OF_LOCK_ACCESS)
                ? $attemptsData['count'] + 1
                : 1;
            $attemptsData['time'] = \XLite\Core\Converter::time();
        }

        \XLite\Core\TmpVars::getInstance()->{$tmpVarCell} = $attemptsData;

        if ($this->isAccessLocked()) {
            \XLite\Core\TopMessage::addError('Access is locked out');
            $this->setReturnURL($this->buildURL('access_control'));
            $this->doRedirect();
        }
    }

    /**
     * Check if access locked
     *
     * @return bool
     */
    public function isAccessLocked()
    {
        $ip = \XLite\Core\Request::getInstance()->getClientIp();

        $tmpVarCell = static::TMP_VAR_LOCK_IP_PREFIX . $ip;

        $attemptsData = \XLite\Core\TmpVars::getInstance()->{$tmpVarCell};

        if (
            $attemptsData
            && $attemptsData['count'] > static::MAX_COUNT_OF_ACCESS_ATTEMPTS
            && \XLite\Core\Converter::time() < ($attemptsData['time'] + static::TIME_OF_LOCK_ACCESS)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getTimeLeftToUnlock()
    {
        $ip = \XLite\Core\Request::getInstance()->getClientIp();

        $tmpVarCell = static::TMP_VAR_LOCK_IP_PREFIX . $ip;

        $attemptsData = \XLite\Core\TmpVars::getInstance()->{$tmpVarCell};

        if ($attemptsData) {
            return $attemptsData['time'] + static::TIME_OF_LOCK_ACCESS - \XLite\Core\Converter::time();
        }

        return 0;
    }

    // }}}
}