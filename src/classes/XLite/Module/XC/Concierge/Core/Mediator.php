<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Core;

use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Module\XC\Concierge\Core\Message\Identify;

/**
 * Event mediator
 */
class Mediator extends \XLite\Base\Singleton
{
    use ExecuteCachedTrait;

    protected function __construct()
    {
        parent::__construct();

        if (PHP_SAPI !== 'cli' && !defined('LC_CACHE_BUILDING')) {
            $this->includeLibrary();
            if ($this->isConfigured()) {
                $this->initOptions();
            }
        }
    }

    /**
     * @return string
     */
    public function getWriteKey()
    {
        return \XLite\Core\Config::getInstance()->XC->Concierge->write_key;
    }

    /**
     * @return boolean
     */
    public function isConfigured()
    {
        return $this->executeCachedRuntime(function () {
            return (bool) $this->getWriteKey();
        });
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->executeCachedRuntime(function () {
            return $this->defineOptions();
        });
    }

    public function initOptions()
    {
        $this->setRuntimeCache('getOptions', null);
        $this->getOptions();
    }

    /**
     * @return array
     */
    protected function defineOptions()
    {
        $languageCode = \XLite\Core\Session::getInstance()->getLanguage()->getCode();

        $result = [
            'anonymousId' => \XLite\Core\Session::getInstance()->getID(),
            'context'     => [
                'plugin' => [
                    'name'    => 'X-Cart',
                    'version' => \XLite\Module\XC\Concierge\Main::getVersion(),
                ],
                // @todo: [ISO 639-1]_[ISO 3166-1 alpha-2]
                'locale' => $languageCode . '_' . strtoupper($languageCode),
            ],
            'userId' => \XLite\Core\Config::getInstance()->XC->Concierge->user_id,
        ];

        //if (\XLite\Core\Auth::getInstance()->isLogged()) {
        //    $result['userId'] = \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();
        //}

        //if (!\XLite\Core\Auth::getInstance()->isLogged()) {
        //    $result['Intercom']['hideDefaultLauncher'] = true;
        //}

        return $result;
    }

    // {{{ Message

    /**
     * Add message to session
     *
     * @param AMessage $message
     *
     * @return boolean
     */
    public function addMessage(AMessage $message)
    {
        $result = false;

        if ($this->isConfigured()) {
            $messages = \XLite\Core\Session::getInstance()->concierge_messages;
            if (!is_array($messages)) {
                $messages = [];
            }
            $messages[] = $message->toArray();
            \XLite\Core\Session::getInstance()->concierge_messages = $messages;

            $result = true;
        }

        return $result;
    }

    /**
     * Get stored messages
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = \XLite\Core\Session::getInstance()->concierge_messages;
        unset(\XLite\Core\Session::getInstance()->concierge_messages);

        return is_array($messages) ? $messages : [];
    }

    /**
     * @return array|Identify
     */
    public function getIdentifyMessage()
    {
        $result = [];

        $messages = \XLite\Core\Session::getInstance()->concierge_messages;
        if ($messages) {
            foreach ($messages as $i => $message) {
                if (AMessage::TYPE_IDENTIFY === $message['type']) {
                    $result = $message;
                    unset($messages[$i]);
                }
            }
            \XLite\Core\Session::getInstance()->concierge_messages = $messages;
        }

        if (!$result) {
            $auth = \XLite\Core\Auth::getInstance();
            $profile = $auth->getProfile();
            $config = \XLite\Core\Config::getInstance();
            $result = new Identify($config->XC->Concierge->user_id, $auth->getConciergeCompanyId(), $profile, $config);
        }
        unset(\XLite\Core\Session::getInstance()->sessionImmediateCreated);

        return $result ? [$result] : [];
    }

    /**
     * Throw track request
     *
     * @param string $event      Event name
     * @param array  $properties Event properties
     */
    public function throwTrack($event, array $properties)
    {
        $this->throwMessage(AMessage::TYPE_TRACK, [
            'event'      => $event,
            'properties' => $properties,
        ]);
    }

    /**
     * Throw track request
     *
     * @param string $type
     * @param array  $arguments
     */
    protected function throwMessage($type, $arguments)
    {
        if ($this->isConfigured() && method_exists('\Analytics', $type)) {
            call_user_func(['\Analytics', $type], array_merge($arguments, $this->getOptions()));
            \Analytics::flush();
        }
    }

    // }}}

    // {{{ Errors handling

    /**
     * Handle shutdown
     */
    public function handleShutdown()
    {
        $error = error_get_last();

        if (isset($error['type'])
            && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)
        ) {
            $message = sprintf('Error (code: %s): $s', $error['type'], $error['message']);

            if (!empty($error['file'])) {
                $message .= sprintf(' in %s:%s', $error['file'], $error['line']);
            }

            $this->throwTrack('Error', ['error' => $message]);
        }
    }

    /**
     * Handle exception
     *
     * @param \Exception $exception Exception
     */
    public function handleException(\Exception $exception)
    {
        $message = 'Exception: ' . $exception->getMessage();
        $backTrace = $this->getBackTrace(2, $exception->getTrace());

        $this->throwTrack('Error', ['error' => $message, 'backTrace' => $backTrace]);
    }

    // }}}

    // {{{ Assemblers

    /**
     * Assemble message for 'identify' request
     * @todo: remove
     *
     * @param \XLite\Model\Profile $profile Profile
     * @param \XLite\Model\Address $address Address OPTIONAL
     *
     * @return array
     */
    protected function assembleIdentifyMessage(\XLite\Model\Profile $profile, \XLite\Model\Address $address = null)
    {
        $message = [
            $profile->getProfileId(),
            [
                'createdAt' => date('c', $profile->getAdded()),
                'email'     => $profile->getLogin(),
                'id'        => $profile->getProfileId(),
                'username'  => $profile->getLogin(),
            ],
        ];

        if (!$address) {
            $address = $profile->getBillingAddress() ?: $profile->getShippingAddress();
        }
        if ($address) {
            $message[1]['address'] = [
                'city'       => $address->getCity(),
                'country'    => $address->getCountry()->getCode(),
                'postalCode' => $address->getZipcode(),
                'state'      => $address->getState()->getState(),
                'street'     => $address->getStreet(),
            ];
            $message[1]['firstName'] = $address->getFirstname();
            $message[1]['lastName'] = $address->getLastname();
            $message[1]['name'] = $address->getName();
            $message[1]['phone'] = $address->getPhone();
            $message[1]['title'] = $address->getTitle();

            // Mixpanel
            $message[1]['$country_code'] = $address->getCountry()->getCode();
            $message[1]['$city'] = $address->getCity();
            $message[1]['$region'] = $address->getState()->getState();
        }

        return $message;
    }

    // }}}

    // {{{ Service

    /**
     * Include PHP SDK
     */
    protected function includeLibrary()
    {
        if ($this->isConfigured() && !\XLite\Core\Operator::isClassExists('Analytics')) {
            require_once(LC_DIR_MODULES . 'XC/Concierge/lib/Segment.php');
            class_alias('Segment', 'Analytics');
            \Analytics::init($this->getWriteKey());
        }
    }

    // }}}

    // {{{ Back trace

    /**
     * Get back trace
     *
     * @param integer $slice     Slice OPTIONAL
     * @param array   $backtrace Trace OPTIONAL
     *
     * @return array
     */
    protected function getBackTrace($slice = 2, array $backtrace = [])
    {
        $backtrace = $backtrace ?: debug_backtrace(false);
        $backtrace = array_slice($backtrace, $slice);

        $result = [];
        foreach ($backtrace as $line) {
            $parts = [];

            if (isset($line['file'])) {
                $parts[] = sprintf('file %s', $line['file']);

            } elseif (isset($line['class'], $line['function'])) {
                $args = isset($line['args']) ? $line['args'] : [];
                $parts[] = sprintf('method %s::%s%s', $line['class'], $line['function'], $this->getBackTraceArgs($args));

            } elseif (isset($line['function'])) {
                $args = isset($line['args']) ? $line['args'] : [];
                $parts[] = sprintf('function %s%s', $line['function'], $this->getBackTraceArgs($args));
            }

            if (isset($line['line'])) {
                $parts[] = $line['line'];
            }

            if ($parts) {
                $result[] = implode(' : ', $parts);
            }
        }

        return $result;
    }

    /**
     * Get back trace row arguments
     *
     * @param array $args Back trace args
     *
     * @return string
     */
    protected function getBackTraceArgs(array $args)
    {
        $result = [];
        foreach ($args as $arg) {
            if (is_bool($arg)) {
                $result[] = $arg ? 'true' : 'false';

            } elseif (is_int($arg) || is_float($arg)) {
                $result[] = $arg;

            } elseif (is_string($arg)) {
                if (is_callable($arg)) {
                    $result[] = 'lambda function';

                } else {
                    $result[] = '\'' . $arg . '\'';
                }
            } elseif (is_resource($result)) {
                $result[] = (string) $arg;

            } elseif (is_array($arg)) {
                if (is_callable($arg)) {
                    $result[] = 'callback ' . get_class($arg[0]) . '::' . $arg[1];

                } else {
                    $result[] = 'array(' . count($arg) . ')';
                }
            } elseif (is_object($arg)) {
                if (is_callable($arg)
                    && class_exists('Closure')
                    && $arg instanceof \Closure
                ) {
                    $result[] = 'anonymous function';

                } else {
                    $result[] = 'object of ' . get_class($arg);
                }
            } elseif (null === $arg) {
                $result[] = 'null';

            } else {
                $result[] = 'variable of ' . gettype($arg);
            }
        }

        return '(' . implode(', ', $result) . ')';
    }

    // }}}
}
