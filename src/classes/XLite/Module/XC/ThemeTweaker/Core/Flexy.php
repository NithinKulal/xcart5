<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core;

/**
 * Flexy-templates operations
 */
class Flexy extends \XLite\Base\Singleton
{
    /**
     * Cache of flexy-templates list
     *
     * @var array
     */
    protected static $flexyCache;

    /**
     * Get list of flexy templates
     *
     * @return array
     */
    public static function getTemplatesList()
    {
        if (!isset(static::$flexyCache)) {

            if (is_null(\XLite\Core\Session::getInstance()->flexyCache)) {
                static::detectFlexyTemplates();
            }

            static::$flexyCache = \XLite\Core\Session::getInstance()->flexyCache;
        }

        return static::$flexyCache;
    }

    /**
     * Search flexy templates
     *
     * @return void
     */
    public static function detectFlexyTemplates()
    {
        $list = array();

        $dirs = static::getSearchDirs();

        foreach ($dirs as $d) {

            $dir = LC_DIR_SKINS . $d;

            if (\Includes\Utils\FileManager::isDir($dir)) {
                $filter = new \Includes\Utils\FileFilter($dir, '/\.tpl$/');

                foreach ($filter->getIterator() as $file) {
                    if (!$file->isDir()) {
                        $list[] = $file->getRealPath();
                    }
                }
            }
        }

        sort($list);

        \XLite\Core\Session::getInstance()->flexyCache = $list;
    }

    /**
     * Get list of templates entities (for items list)
     *
     * @return array
     */
    public static function getTemplateObjects()
    {
        $result = array();

        $list = static::getTemplatesList();

        foreach ($list as $id => $path) {
            $tplFile = str_replace(LC_DIR_SKINS, '', $path);
            $twigPath = static::getNewPath($tplFile);
            $origTwigFile = static::getNewPath($tplFile, true);
            $result[] = new \XLite\Module\XC\ThemeTweaker\Model\FlexyTemplate(
                $id,
                $tplFile,
                str_replace(LC_DIR_SKINS, '', $twigPath),
                \Includes\Utils\FileManager::isExists($twigPath),
                \Includes\Utils\FileManager::isExists($origTwigFile)
            );
        }

        return $result;
    }

    /**
     * Return true if orchan templates found
     *
     * @return boolean
     */
    public static function hasOrphans()
    {
        $result = false;

        $templates = static::getTemplateObjects();

        foreach ($templates as $template) {
            if (!$template->isOrigExists()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Get list of directories to search flexy-templates
     *
     * @return array
     */
    public static function getSearchDirs()
    {
        return array('custom_skin', 'theme_tweaker');
    }

    /**
     * Get new path
     *
     * @param string $path Old path (e.g. default/en/main.tpl)
     *
     * @return string
     */
    public static function getNewPath($path, $getOrigFile = false)
    {
        $path = static::getMovedPath($path);

        $data = explode(LC_DS, $path);

        // Change default => customer
        if ('default' === $data[1]) {
            $data[1] = 'customer';
        }

        // Detect lng code
        if (isset($data[2]) && 2 === strlen($data[2])) {
            if (!$getOrigFile && 'en' !== $data[2]) {
                $data[1] .= '_' . $data[2];
            }
            unset($data[2]);
        }

        if ('mail' === $data[1]) {
            // Move mail/en => mail/common
            $data[1] = 'mail' . LC_DS . 'common';

        } elseif (isset($data[3]) && 'mail' === $data[3]) {
            // Move admin/[en]/mail => mail/admin and default/[en]/mail => customer/mail
            $data[1] = 'mail' . LC_DS . $data[1];
            unset($data[3]);
        }

        if ($getOrigFile) {
            unset($data[0]);
        }

        return LC_DIR_SKINS . preg_replace('/\.tpl$/', '.twig', implode(LC_DS, $data));
    }

    /**
     * Get moved path
     *
     * @param string $path Original path
     *
     * @return string
     */
    public static function getMovedPath($path)
    {
        $moveList = static::getMovedList();

        $parts = explode(LC_DS, $path);

        $skin = $parts[1];

        if (isset($moveList[$skin])) {
            $prefix = implode(LC_DS, array_slice($parts, 0, 3)) . LC_DS;
            $shortPath = implode(LC_DS, array_slice($parts, 3));

            foreach ($moveList[$skin] as $search => $replace) {

                $search = str_replace(
                    array('/', '.tpl', '.', '*'),
                    array('\\' . LC_DS, '.tpl$', '\.', '(.*)'),
                    $search
                );

                if (preg_match('/^' . $search . '/', $shortPath, $match)) {
                    $path = $prefix . (isset($match[1]) ? preg_replace('/\*/', $match[1], $replace) : $replace);
                    break;
                }
            }
        }

        return $path;
    }

    /**
     * Return list of moved templates
     *
     * @return array
     */
    public static function getMovedList()
    {
        return array(
            'default' => array(
                'authentication/*'          => 'authorization/*',
                'authentication.tpl'        => 'authorization/authorization.twig',
                'authentication_popup.tpl'  => 'authorization/authorization_popup.twig',
                'layout/header.*.tpl'       => 'layout/header/header.*.twig',
                'layout/main.header.tpl'    => 'layout/header/main.header.twig',
                'layout/mobile.header.tpl'  => 'layout/header/mobile.header.twig',
                'top_menu.tpl'              => 'layout/header/top_menu.twig',
                'layout/main.center.*.tpl'  => 'layout/content/main.center.*.twig',
                'layout/main.location.tpl'  => 'layout/content/main.location.twig',
                'center.tpl'                => 'layout/content/center.twig',
                'center_top.tpl'            => 'layout/content/center_top.twig',
                'category_description.tpl'  => 'layout/content/category_description.twig',
                'welcome.tpl'               => 'layout/content/welcome.twig',
                'main.footer.*.tpl'         => 'layout/footer/main.footer.*.twig',
                'powered_by.tpl'            => 'layout/footer/powered_by.twig',
                'footer_menu.tpl'           => 'layout/footer/footer_menu.twig',
                'recover_password/*'        => 'recover_password/parts/*',
                'form_field.tpl'            => 'form_field/form_field.twig',
                'shipping_list.tpl'         => 'form_field/shipping_list.twig',
                'location.tpl'              => 'location/location.twig',
                'tooltip.tpl'               => 'common/tooltip.twig',
                'top_continue_shopping.tpl' => 'shopping_cart/top_continue_shopping.twig',
            )
        );
    }

    /**
     * Get flexy template content
     *
     * @param string $fileName Flexy-template file name (short path)
     *
     * @return string
     */
    public static function getFlexyContent($fileName)
    {
        $result = null;

        if ($fileName) {
            $fileName = LC_DIR_SKINS . $fileName;

            $list = static::getTemplatesList();

            if (in_array($fileName, $list)) {
                $result = \Includes\Utils\FileManager::read($fileName);
            }
        }

        return $result;
    }

    /**
     * Save content as a twig template
     *
     * @param string $fileName Flexy-template file name (short path)
     * @param string $content  Content to save
     *
     * @return boolean
     */
    public static function saveTwigContent($fileName, $content)
    {
        $result = false;

        if ($fileName) {
            $flexyFile = LC_DIR_SKINS . $fileName;

            $list = static::getTemplatesList();

            if (in_array($flexyFile, $list)) {
                $twigFile = static::getNewPath($fileName);

                $content = preg_replace("/\r\n|\n\r|\r|\n/", PHP_EOL, $content);
                $result = \Includes\Utils\FileManager::write($twigFile, $content);

                if ($result && 'theme_tweaker' === substr($fileName, 0, strpos($fileName, LC_DS))) {

                    $template = str_replace(LC_DIR_SKINS, '', $twigFile);

                    $dbTemplate = \XLite\Core\Database::getRepo('XLite\Module\XC\ThemeTweaker\Model\Template')
                        ->findOneBy(array('template' => $template));

                    if (!$dbTemplate) {
                        $dbTemplate = new \XLite\Module\XC\ThemeTweaker\Model\Template;
                    }

                    $dbTemplate->setTemplate($template);
                    $dbTemplate->setDate(time());

                    \XLite\Core\Database::getEM()->persist($dbTemplate);
                    \XLite\Core\Database::getEM()->flush();
                }
            }
        }

        return $result;
    }

    /**
     * Remove flexy templates.
     * Return list of files which cannot be removed or empty array
     *
     * @return array
     */
    public static function removeFlexyTemplates()
    {
        $result = array();
        $deleted = array();

        $list = static::getTemplatesList();

        $repo = \XLite\Core\Database::getRepo('XLite\Module\XC\ThemeTweaker\Model\Template');

        // Remove files
        foreach ($list as $path) {

            $dbTemplate = $repo->findOneBy(array('template' => str_replace(LC_DIR_SKINS, '', $path)));

            if ($dbTemplate) {
                $repo->delete($dbTemplate);
            }

            if (\Includes\Utils\FileManager::deleteFile($path)) {
                $deleted[] = $path;

            } else {
                $result[] = $path;
            }
        }

        if (empty($result)) {

            // Remove empty directories
            $dirs = static::detectEmptyDirectories();

            foreach ($dirs as $dir) {
                if (\Includes\Utils\FileManager::isExists($dir)) {
                    \Includes\Utils\FileManager::unlinkRecursive($dir);
                    $deleted[] = $dir;
                }
            }
        }

        if ($deleted) {
            // Log action
            \XLite\Logger::getInstance()->log(
                'Flexy-to-twig converter: The following flexy-templates and empty directories have been removed:'
                . PHP_EOL
                . var_export($deleted, true),
                LOG_INFO
            );
        }

        return $result;
    }

    /**
     * Search empty directories
     *
     * @return array
     */
    public static function detectEmptyDirectories()
    {
        $list = array();

        $dirs = static::getSearchDirs();

        foreach ($dirs as $d) {

            $dir = LC_DIR_SKINS . $d;

            if (\Includes\Utils\FileManager::isDir($dir)) {

                $filter = new \Includes\Utils\FileFilter($dir, null, \RecursiveIteratorIterator::SELF_FIRST);

                foreach ($filter->getIterator() as $file) {

                    $realPath = $file->getRealPath();

                    if ($file->isDir()) {
                        $list[$realPath] = true;

                    } else {
                        $subdir = dirname($realPath);
                        while (isset($list[$subdir])) {
                            $list[$subdir] = false;
                            $subdir = dirname($subdir);
                        }
                    }
                }
            }
        }

        return array_keys(array_filter($list));
    }
}
