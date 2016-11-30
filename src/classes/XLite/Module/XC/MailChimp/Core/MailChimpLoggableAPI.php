<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core;
use XLite\Module\XC\MailChimp\Main;

/**
 * MailChimp core class
 */
class MailChimpLoggableAPI extends \DrewM\MailChimp\MailChimp
{
    protected $actionMessageToLog = null;

    /**
     * @return null
     */
    public function getActionMessageToLog()
    {
        return $this->actionMessageToLog;
    }

    /**
     * @inheritDoc
     */
    public function delete($method, $args = array(), $timeout = 10)
    {
        $result = parent::delete($method, $args, $timeout);
        
        $this->tryToLog($method, $result);
        
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function get($method, $args = array(), $timeout = 10)
    {
        $result = parent::get($method, $args, $timeout);

        $this->tryToLog($method, $result);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function patch($method, $args = array(), $timeout = 10)
    {
        $result = parent::patch($method, $args, $timeout);

        $this->tryToLog($method, $result);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function post($method, $args = array(), $timeout = 10)
    {
        $result = parent::post($method, $args, $timeout);

        $this->tryToLog($method, $result);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function put($method, $args = array(), $timeout = 10)
    {
        $result = parent::put($method, $args, $timeout);

        $this->tryToLog($method, $result);

        return $result;
    }
    /**
     * @param null $actionMessageToLog
     */
    public function setActionMessageToLog($actionMessageToLog)
    {
        $this->actionMessageToLog = $actionMessageToLog;
    }

    /**
     * Try to log if errors
     *
     * @param       $endpoint
     * @param array $result
     */
    protected function tryToLog($endpoint, $result = [])
    {
        $request = $this->getLastRequest();
        $request['body'] = json_decode($request['body'], true);

        $response = $this->getLastRequest();
        $response['body'] = json_decode($response['body'], true);

        if ($this->success()) {
            $message = ($this->getActionMessageToLog() ?: $endpoint) . ': Success';

            Main::logInfo($message, [
                'request'       => $request,
                'response'      => $response,
                'result'        => $result,
            ]);

        } elseif ($result && isset($result['status']) && $result['status'] !== 404) {
            $message = ($this->getActionMessageToLog() ?: $endpoint) . ': Error';

            Main::logError($message, [
                'request'       => $request,
                'response'      => $response,
                'result'        => $result,
                'error'         => $this->getLastError(),
                'errors'        => isset($result['errors']) ? $result['errors'] : [] 
            ]);
        }
        
        $this->setActionMessageToLog(null);
    }
}
