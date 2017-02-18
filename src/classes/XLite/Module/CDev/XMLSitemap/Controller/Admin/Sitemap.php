<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XMLSitemap\Controller\Admin;
use XLite\Core\Database;
use XLite\Core\EventTask;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use XLite\Module\CDev\XMLSitemap\Logic\Sitemap\Generator;

/**
 * Sitemap
 */
class Sitemap extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('XML sitemap');
    }

    /**
     * Get engines
     *
     * @return array
     */
    public function getEngines()
    {
        return array(
            'Google'  => array(
                'title' => 'Google',
                'url'   => 'http://google.com/webmasters/tools/ping?sitemap=%url%',
            ),
            'Bing / Yahoo'    => array(
                'title' => 'Bing / Yahoo',
                'url'   => 'http://www.bing.com/webmaster/ping.aspx?siteMap=%url%',
            ),
        );
    }

    /**
     * Check - generation process is not-finished or not
     *
     * @return boolean
     */
    public function isSitemapGenerationNotFinished()
    {
        $eventName = Generator::getEventName();
        $state = Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);

        return $state
        && in_array(
            $state['state'],
            array(EventTask::STATE_STANDBY, EventTask::STATE_IN_PROGRESS)
        )
        && !Database::getRepo('XLite\Model\TmpVar')->getVar($this->getSitemapGenerationCancelFlagVarName());
    }

    /**
     * Check - generation process is finished or not
     *
     * @return boolean
     */
    public function isSitemapGenerationFinished()
    {
        return !$this->isSitemapGenerationNotFinished();
    }

    /**
     * Get export cancel flag name
     *
     * @return string
     */
    protected function getSitemapGenerationCancelFlagVarName()
    {
        return Generator::getSitemapGenerationCancelFlagVarName();
    }

    /**
     * Manually generate sitemap
     *
     * @return void
     */
    protected function doActionGenerate()
    {
        if ($this->isSitemapGenerationFinished()) {
            Generator::run([]);
        }

        $this->setReturnURL(
            $this->buildURL('sitemap')
        );
    }

    /**
     * Cancel
     *
     * @return void
     */
    protected function doActionSitemapGenerationCancel()
    {
        Generator::cancel();

        $this->setReturnURL(
            $this->buildURL('sitemap')
        );
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $request = Request::getInstance();

        if ($request->sitemap_generation_completed) {
            TopMessage::addInfo('Sitemap generation has been completed successfully.');

            $this->setReturnURL(
                $this->buildURL('sitemap')
            );

        } elseif ($request->sitemap_generation_failed) {
            TopMessage::addError('Sitemap generation has been stopped.');

            $this->setReturnURL(
                $this->buildURL('sitemap')
            );
        }
    }

    /**
     * Place URL into engine's endpoints
     *
     * @return void
     */
    protected function doActionLocate()
    {
        $engines = Request::getInstance()->engines;

        if ($engines) {
            foreach ($this->getEngines() as $key => $engine) {
                if (in_array($key, $engines)) {
                    $url = urlencode(
                        \XLite::getInstance()->getShopURL(
                            \XLite\Core\Converter::buildURL('sitemap', '', array(), \XLite::getCustomerScript())
                        )
                    );
                    $url = str_replace('%url%', $url, $engine['url']);
                    if (\XLite\Core\Operator::checkURLAvailability($url)) {
                        TopMessage::addInfo(
                            'Site map successfully registred on X',
                            array('engine' => $key)
                        );

                    } else {
                        TopMessage::addWarning(
                            'Site map has not been registred in X',
                            array('engine' => $key)
                        );
                    }
                }
            }
        }

        $postedData = Request::getInstance()->getData();
        $options    = Database::getRepo('\XLite\Model\Config')
            ->findBy(array('category' => $this->getOptionsCategory()));
        $isUpdated  = false;

        foreach ($options as $key => $option) {
            $name = $option->getName();
            $type = $option->getType();

            if (isset($postedData[$name]) || 'checkbox' == $type) {
                if ('checkbox' == $type) {
                    $option->setValue(isset($postedData[$name]) ? 'Y' : 'N');

                } else {
                    $option->setValue($postedData[$name]);
                }

                $isUpdated = true;
                Database::getEM()->persist($option);
            }
        }

        if ($isUpdated) {
            Database::getEM()->flush();
        }
    }

    /**
     * Returns shipping options
     *
     * @return array
     */
    public function getOptions()
    {
        return Database::getRepo('\XLite\Model\Config')
            ->findByCategoryAndVisible($this->getOptionsCategory());
    }


    /**
     * Get options category
     *
     * @return string
     */
    protected function getOptionsCategory()
    {
        return 'CDev\XMLSitemap';
    }
}
