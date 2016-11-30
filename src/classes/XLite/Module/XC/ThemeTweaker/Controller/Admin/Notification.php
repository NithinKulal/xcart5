<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin;

/**
 * Notification
 */
class Notification extends \XLite\Controller\Admin\Notification implements \XLite\Base\IDecorator
{
    protected function doActionSendTestEmail()
    {
        $request = \XLite\Core\Request::getInstance();
        $templatesDirectory = $request->templatesDirectory;
        $order = \XLite\Module\XC\ThemeTweaker\Main::getDumpOrder();

        if (\XLite\Module\XC\ThemeTweaker\Main::isOrderNotification($templatesDirectory) && $order) {
            $to = \XLite\Core\Auth::getInstance()->getProfile()->getLogin();

            $result = \XLite\Core\Mailer::getInstance()->sendOrderRelatedPreview(
                $templatesDirectory,
                $to,
                $request->page === \XLite::ADMIN_INTERFACE ? \XLite::ADMIN_INTERFACE : \XLite::CUSTOMER_INTERFACE,
                $order
            );

            if ($result) {
                \XLite\Core\TopMessage::addInfo('The test email notification has been sent to X', ['email' => $to]);

            } else {
                \XLite\Core\TopMessage::addWarning('Failure sending test email to X', ['email' => $to]);
            }
        }

        $this->setReturnURL($this->getURL());
    }

    /**
     * Process request
     *
     * @return void
     */
    public function processRequest()
    {
        $request = \XLite\Core\Request::getInstance();
        $order = \XLite\Module\XC\ThemeTweaker\Main::getDumpOrder();

        if ($request->preview && $order) {
            $innerInterface = \XLite\Core\Request::getInstance()->page === \XLite::ADMIN_INTERFACE
                ? \XLite::ADMIN_INTERFACE
                : \XLite::CUSTOMER_INTERFACE;

            $data = [
                'order' => $order
            ];

            $mailer = new \XLite\View\Mailer();

            echo $mailer->getNotificationPreviewContent(
                $request->templatesDirectory,
                $data,
                $innerInterface
            );

        } else {
            parent::processRequest();
        }
    }
}
