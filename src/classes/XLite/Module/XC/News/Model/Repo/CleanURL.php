<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\News\Model\Repo;

/**
 * Clean URL repository
 */
class CleanURL extends \XLite\Model\Repo\CleanURL implements \XLite\Base\IDecorator
{
    /**
     * Returns available entities types
     *
     * @return array
     */
    public static function getEntityTypes()
    {
        $list = parent::getEntityTypes();
        $list['XLite\Module\XC\News\Model\NewsMessage'] = 'newsMessage';

        return $list;
    }

    /**
     * Returns news message url regexp pattern
     *
     * @return string
     */
    protected function getPatternNewsMessage()
    {
        return $this->getCommonPattern() . '(\.' . static::CLEAN_URL_DEFAULT_EXTENSION . ')?';
    }

    /**
     * Post process clean URL
     *
     * @param string                    $url    URL
     * @param \XLite\Model\Base\Catalog $entity Entity
     *
     * @return string
     */
    protected function postProcessURLNewsMessage($url, $entity)
    {
        return $url . '.' . static::CLEAN_URL_DEFAULT_EXTENSION;
    }

    /**
     * Parse clean URL
     * Return array((string) $target, (array) $params)
     *
     * @param string $url  Main part of a clean URL
     * @param string $last First part before the "url" OPTIONAL
     * @param string $rest Part before the "url" and "last" OPTIONAL
     * @param string $ext  Extension OPTIONAL
     *
     * @return array
     */
    protected function parseURLNewsMessage($url, $last = '', $rest = '', $ext = '')
    {
        $result = null;

        if ($ext) {
            $result = $this->findByURL('newsMessage', $url . $ext);
        }

        return $result;
    }

    /**
     * Hook for modules
     *
     * @param string $url    Main part of a clean URL
     * @param string $last   First part before the "url"
     * @param string $rest   Part before the "url" and "last"
     * @param string $ext    Extension
     * @param string $target Target
     * @param array  $params Additional params
     *
     * @return array
     */
    protected function prepareParseURL($url, $last, $rest, $ext, $target, $params)
    {
        list($target, $params) = parent::prepareParseURL($url, $last, $rest, $ext, $target, $params);

        if ('newsMessage' == $target && !empty($last)) {
            unset($params['id']);
        }

        return array($target, $params);
    }

    /**
     * Build product URL
     *
     * @param array  $params Params
     *
     * @return array
     */
    protected function buildURLNewsMessage($params)
    {
        $urlParts = array();

        if (!empty($params['id'])) {
            /** @var \XLite\Module\XC\News\Model\NewsMessage $newsMessage */
            $newsMessage = \XLite\Core\Database::getRepo('XLite\Module\XC\News\Model\NewsMessage')->find($params['id']);

            if (isset($newsMessage) && $newsMessage->getCleanURL()) {
                $urlParts[] = $newsMessage->getCleanURL();
                unset($params['id']);
            }
        }

        return array($urlParts, $params);
    }

    /**
     * Build fake url with placeholder
     *
     * @param \XLite\Model\AEntity|string $entity Entity
     * @param array                       $params Params
     *
     * @return string
     */
    protected function buildFakeURLNewsMessage($entity, $params)
    {
        $urlParts = array($this->postProcessURL(static::PLACEHOLDER, $entity));

        return array($urlParts, $params);
    }
}
