<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Layout manager
 */
class Layout extends \XLite\Base\Singleton
{
    /**
     * Repository paths
     */
    const PATH_SKIN     = 'skins';
    const PATH_CUSTOMER = 'customer';
    const PATH_COMMON   = 'common';
    const PATH_ADMIN    = 'admin';
    const PATH_CONSOLE  = 'console';
    const PATH_MAIL     = 'mail';
    const PATH_PDF      = 'pdf';

    /**
     * Web URL output types
     */
    const WEB_PATH_OUTPUT_SHORT = 'sort';
    const WEB_PATH_OUTPUT_FULL  = 'full';
    const WEB_PATH_OUTPUT_URL   = 'url';

    /**
     * Layout style
     */
    const LAYOUT_TWO_COLUMNS_LEFT  = 'left';
    const LAYOUT_TWO_COLUMNS_RIGHT = 'right';
    const LAYOUT_THREE_COLUMNS     = 'three';
    const LAYOUT_ONE_COLUMN        = 'one';

    /**
     * Layout groups
     */
    const LAYOUT_GROUP_DEFAULT = 'default';
    const LAYOUT_GROUP_HOME = 'home';

    /**
     * Widgets resources collector
     *
     * @var array
     */
    protected $resources = array();

    /**
     * Prepare resources flag
     *
     * @var boolean
     */
    protected $prepareResourcesFlag = false;

    /**
     * Current skin
     *
     * @var string
     */
    protected $skin;

    /**
     * Current locale
     *
     * @var string
     */
    protected $locale;

    /**
     * Current skin path
     *
     * @var string
     */
    protected $path;

    /**
     * Current interface
     *
     * @var string
     */
    protected $currentInterface = \XLite::CUSTOMER_INTERFACE;

    /**
     * Main interface of mail.
     * For example body.twig of mail is inside MAIL interface
     * but the inner widgets and templates in this template are inside CUSTOMER or ADMIN interfaces
     *
     * @var string
     */
    protected $innerInterface = \XLite::CUSTOMER_INTERFACE;

    /**
     * Current resources group (on moment of registerResources())
     *
     * @var string
     */
    protected $currentGroup = null;

    /**
     * Skins list
     *
     * @var array
     */
    protected $skins = array();

    /**
     * Skin paths
     *
     * @var array
     */
    protected $skinPaths = array();

    /**
     * Resources cache
     *
     * @var array
     */
    protected $resourcesCache = array();

    /**
     * Skins cache flag
     *
     * @var boolean
     */
    protected $skinsCache = false;

    /**
     * Registered meta tags
     *
     * @var array
     */
    protected $metaTags = [];

    /**
     * Registered id strings
     *
     * @var array
     */
    protected $idStrings = [];

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        $color = $this->getLayoutColor();

        $image = 'images/logo' . ($color ? ('_' . $color) : '') . '.png';
        $url = $this->getResourceWebPath($image, static::WEB_PATH_OUTPUT_URL, \XLite::CUSTOMER_INTERFACE);

        return $url
            ?: $this->getResourceWebPath('images/logo.png', static::WEB_PATH_OUTPUT_URL, \XLite::CUSTOMER_INTERFACE);
    }

    /**
     * Get apple icon
     *
     * @return string
     */
    public function getAppleIcon()
    {
        $color = $this->getLayoutColor();

        $image = 'images/icon192x192' . ($color ? ('_' . $color) : '') . '.png';
        $url = $this->getResourceWebPath($image, static::WEB_PATH_OUTPUT_URL, \XLite::COMMON_INTERFACE);

        return $url
            ?: $this->getResourceWebPath('images/icon192x192.png', static::WEB_PATH_OUTPUT_URL, \XLite::COMMON_INTERFACE);
    }

    // {{{ Layout changers methods

    /**
     * The modules can use the method in the last step of classes rebuilding.
     * The module removes the viewer class list location via this method.
     *
     * For example:
     *
     * \XLite\Core\Layout::getInstance()->removeClassFromList(
     *    'XLite\Module\CDev\Bestsellers\View\Bestsellers'
     * );
     *
     * After the classes rebuilding the bestsellers block is removed
     * from any list in the store
     *
     * @param string $class Name of class to remove
     *
     * @return void
     *
     * @see \XLite\Module\AModule::runBuildCacheHandler()
     */
    public function removeClassFromLists($class)
    {
        $data = array(
            'child' => $class,
        );

        $this->removeFromList($data);
    }

    /**
     * The modules can use the method in the last step of classes rebuilding.
     * The module removes the viewer class list location via this method.
     *
     * For example:
     *
     * \XLite\Core\Layout::getInstance()->removeClassFromList(
     *    'XLite\Module\CDev\Bestsellers\View\Bestsellers',
     *    'sidebar.first',
     *    \XLite\Model\ViewList::INTERFACE_CUSTOMER
     * );
     *
     * After the classes rebuilding the bestsellers block is removed
     * from 'sidebar.first' list in customer interface
     *
     * @param string $class    Name of class to remove
     * @param string $listName List name where the class was located
     * @param string $zone     Interface where the list is located OPTIONAL
     *
     * @return void
     *
     * @see \XLite\Module\AModule::runBuildCacheHandler()
     */
    public function removeClassFromList($class, $listName, $zone = null)
    {
        $data = array(
            'child' => $class,
            'list'  => $listName,
        );

        if (null !== $zone) {
            $data['zone'] = $zone;
        }

        $this->removeFromList($data);
    }

    /**
     * The modules can use the method in the last step of classes rebuilding.
     * The module adds the viewer class list location via this method.
     *
     * Options array contains other info that must be added to the viewList entry.
     * \XLite\Model\ViewList entry contains `weight` and `zone` parameters
     *
     * For example:
     *
     * \XLite\Core\Layout::getInstance()->addClassToList(
     *    'XLite\Module\CDev\Bestsellers\View\Bestsellers',
     *    'sidebar.second',
     *    array(
     *        'zone'   => \XLite\Model\ViewList::INTERFACE_CUSTOMER,
     *        'weight' => 100,
     *    )
     * );
     *
     * If any module decorates \XLite\Model\ViewList class and adds any other info
     * you can insert additional information via $options parameter
     *
     * @param string $class    Class name WITHOUT leading `\`
     * @param string $listName Name of the list where the class must be located
     * @param array  $options  Additional info to add to the viewList entry OPTIONAL
     *
     * @return \XLite\Model\ViewList New entry of the viewList
     *
     * @see \XLite\Model\ViewList
     * @see \XLite\Module\AModule::runBuildCacheHandler()
     */
    public function addClassToList($class, $listName, $options = array())
    {
        return $this->addToList(
            array_merge(
                array(
                    'child' => $class,
                    'list' => $listName,
                ),
                $options
            )
        );
    }

    /**
     * The modules can use the method in the last step of classes rebuilding.
     * The module removes the template from any lists via this method.
     *
     * For example:
     *
     * \XLite\Core\Layout::getInstance()->removeTemplateFromList(
     *    'product\details\parts\common.button-add2cart.twig'
     * );
     *
     * After the classes rebuilding the add to cart block is removed
     * from any list in any interface
     *
     * @param string $tpl Name of template to remove
     *
     * @return void
     *
     * @see \XLite\Module\AModule::runBuildCacheHandler()
     */
    public function removeTemplateFromLists($tpl)
    {
        $data = array(
            'tpl'   => $this->prepareTemplateToList($tpl),
        );

        $this->removeFromList($data);
    }

    /**
     * The modules can use the method in the last step of classes rebuilding.
     * The module removes the template list location via this method.
     *
     * For example:
     *
     * \XLite\Core\Layout::getInstance()->removeTemplateFromList(
     *    'product\details\parts\common.button-add2cart.twig',
     *    'product.details.page.info.buttons.cart-buttons',
     *    \XLite\Model\ViewList::INTERFACE_CUSTOMER
     * );
     *
     * After the classes rebuilding the add to cart block is removed
     * from 'product.details.page.info.buttons.cart-buttons' list in customer interface
     *
     * @param string $tpl      Name of template to remove
     * @param string $listName List name where the class was located
     * @param string $zone     Interface where the list is located   OPTIONAL
     *
     * @return void
     *
     * @see \XLite\Module\AModule::runBuildCacheHandler()
     */
    public function removeTemplateFromList($tpl, $listName, $zone = null)
    {
        $data = array(
            'tpl'   => $this->prepareTemplateToList($tpl),
            'list'  => $listName,
        );

        if (null !== $zone) {
            $data['zone'] = $zone;
        }

        $this->removeFromList($data);
    }

    /**
     * The modules can use the method in the last step of classes rebuilding.
     * The module adds the viewer class list location via this method.
     *
     * Options array contains other info that must be added to the viewList entry.
     * \XLite\Model\ViewList entry contains `weight` and `zone` parameters
     *
     * For example:
     *
     * \XLite\Core\Layout::getInstance()->addClassToList(
     *    'modules/CDev/XMLSitemap/menu.twig',
     *    'sidebar.second',
     *    array(
     *        'zone'   => \XLite\Model\ViewList::INTERFACE_CUSTOMER,
     *        'weight' => 100,
     *    )
     * );
     *
     * If any module decorates \XLite\Model\ViewList class and adds any other info
     * you can insert additional information via $options parameter
     *
     * @param string $tpl      Template relative path
     * @param string $listName Name of the list where the template must be located
     * @param array  $options  Additional info to add to the viewList entry OPTIONAL
     *
     * @return \XLite\Model\ViewList
     *
     * @see \XLite\Model\ViewList
     * @see \XLite\Module\AModule::runBuildCacheHandler()
     */
    public function addTemplateToList($tpl, $listName, $options = array())
    {
        return $this->addToList(
            array_merge(
                array(
                    'tpl' => $this->prepareTemplateToList($tpl),
                    'list' => $listName,
                ),
                $options
            )
        );
    }

    /**
     * Method is used as a wrapper to remove viewlist entry directly from DB
     * The remove<Template|Class>FromList() methods use the method
     *
     * @param array $data Viewlist entry data to remove
     *
     * @return void
     *
     * @see \XLite\Core\Layout::removeTemplateFromList()
     * @see \XLite\Core\Layout::removeClassFromList()
     */
    protected function removeFromList($data)
    {
        $repo = \XLite\Core\Database::getRepo('\XLite\Model\ViewList');
        $repo->deleteInBatch($repo->findBy($data), false);
    }

    /**
     * Method is used as a wrapper to insert viewList entry directly into DB
     * The add<Template|Class>ToList() methods use the method
     *
     * @param array $data ViewList entry data to insert
     *
     * @return \XLite\Model\AEntity
     */
    protected function addToList($data)
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\ViewList')->insert(new \XLite\Model\ViewList($data));
    }

    /**
     * The viewlist templates are stored in DB with the system based directory
     * separator. When using addTemplateToList() and removeTemplateFromList() methods
     * the template string must be changed to the directory separator based file path
     *
     * @param string $list List
     *
     * @return string
     *
     * @see \XLite\Core\Layout::addTemplateToList()
     * @see \XLite\Core\Layout::removeTemplateFromList()
     */
    protected function prepareTemplateToList($list)
    {
        return $list;
    }

    // }}}

    // {{{ Common getters

    /**
     * Defines the LESS files to be part of the main LESS queue
     *
     * @param string $interface Interface to use: admin or customer values
     *
     * @return array
     */
    public function getLESSResources($interface)
    {
        $result = array(
            'css/style.less',
        );

        if (\XLite::CUSTOMER_INTERFACE === $interface) {
            $result[] = 'top_message/style.less';
        }

        return $result;
    }

    /**
     * Return unique-guaranteed string to be used as id attr.
     * Returns given string in case of the first call with such argument.
     * Any subsequent calls return as <string>_<prefix>
     *
     * @param string $id Given id string
     *
     * @return object
     */
    public function getUniqueIdFor($id)
    {
        $result = $id;
        $iterator = 1;

        while (in_array($result, $this->idStrings, true)) {
            $result = $id . '_' . $iterator;
            $iterator++;
        }

        $this->idStrings[] = $result;

        return $result;
    }

    /**
     * Return skin name
     *
     * @return string
     */
    public function getSkin()
    {
        return $this->skin;
    }

    /**
     * Return current interface
     *
     * @return string
     */
    public function getInterface()
    {
        return $this->currentInterface;
    }

    /**
     * Return inner widget interface
     *
     * @return string
     */
    public function getInnerInterface()
    {
        return $this->innerInterface;
    }

    /**
     * Return inner widget interface (compatibility alias)
     *
     * @deprecated
     * @return string
     */
    public function getMailInterface()
    {
        return $this->getInnerInterface();
    }

    /**
     * Returns the layout path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Return list of all skins
     *
     * @return array
     */
    public function getSkinsAll()
    {
        return array(
            \XLite::CUSTOMER_INTERFACE => self::PATH_CUSTOMER,
            \XLite::COMMON_INTERFACE   => self::PATH_COMMON,
            \XLite::ADMIN_INTERFACE    => self::PATH_ADMIN,
            \XLite::CONSOLE_INTERFACE  => self::PATH_CONSOLE,
            \XLite::MAIL_INTERFACE     => self::PATH_MAIL,
            \XLite::PDF_INTERFACE      => self::PATH_PDF,
        );
    }

    /**
     * getSkinPathRelative
     *
     * @param string $skin Interface
     *
     * @return string
     */
    public function getSkinPathRelative($skin)
    {
        return $skin . (($this->locale && $this->locale != 'en') ? '_' . $this->locale : '');
    }

    /**
     * Returns layout types
     *
     * @return array
     */
    public function getLayoutTypes()
    {
        return array(
            static::LAYOUT_TWO_COLUMNS_LEFT,
            static::LAYOUT_TWO_COLUMNS_RIGHT,
            static::LAYOUT_THREE_COLUMNS,
            static::LAYOUT_ONE_COLUMN,
        );
    }

    /**
     * Returns layout groups and their targets,
     *
     * Default layout group is omitted because it is applicable to any target 
     * and will be considered as fallback.
     *
     * @return array
     */
    public function getLayoutGroups()
    {
        return array(
            static::LAYOUT_GROUP_HOME => array(
                'main'
            )
        );
    }

    /**
     * Returns available layout types
     *
     * @return array
     */
    public function getAvailableLayoutTypes()
    {
        $module = \XLite\Core\Database::getRepo('XLite\Model\Module')->getCurrentSkinModule();

        return $module
            ? $this->getModuleLayoutTypes($module)
            : array(
                static::LAYOUT_GROUP_DEFAULT => $this->getLayoutTypes(),
                static::LAYOUT_GROUP_HOME => $this->getLayoutTypes(),
            );
    }

    /**
     * Returns layout types, defined in module
     * @param  \XLite\Module\AModule $module 
     * @return array
     */
    public function getModuleLayoutTypes($module)
    {
        $validTypes = $this->getLayoutTypes();
        $types = $module->callModuleMethod('getLayoutTypes', array());

        if (count($types) > 0 && is_array(array_values($types)[0])) {
            array_walk($types, function(&$group) use ($validTypes) {
                $group = array_intersect($group, $validTypes);
            });

            return $types;
        } else {
            return array(static::LAYOUT_GROUP_DEFAULT => array_intersect($types, $validTypes));
        }
    }

    /**
     * Returns current layout type
     * @param string $group Layout group name (by default - current displayed group)
     * @return string
     */
    public function getLayoutType($group = null)
    {
        $group = $group ?: $this->getCurrentLayoutGroup();
        $layoutType = $this->getLayoutTypeByGroup($group);
        $availableTypes = $this->getAvailableLayoutTypes();
        $groupAvailableTypes = isset($availableTypes[$group]) ? $availableTypes[$group] : array();

        return in_array($layoutType, $groupAvailableTypes, true)
            ? $layoutType
            : \XLite\Core\Config::getInstance()->Layout->layout_type;
    }

    /**
     * Returns configured layout type value 
     * @param  string $group Layout group name
     * @return string
     */
    public function getLayoutTypeByGroup($group)
    {
        $group = ($group == static::LAYOUT_GROUP_DEFAULT ? '' : '_' . $group);

        return \XLite\Core\Config::getInstance()->Layout->{'layout_type' . $group};
    }

    /**
     * Returns layout group type option name 
     * @param  string $group Layout group name
     * @return string
     */
    public function getLayoutTypeLabelByGroup($group)
    {
        $group = ($group == static::LAYOUT_GROUP_DEFAULT ? '' : '_' . $group);

        $option = \XLite\Core\Database::getRepo('XLite\Model\Config')
            ->findOneBy(array('name' => 'layout_type' . $group, 'category' => 'Layout'));

        return $option ? $option->getOptionName() : '';
    }

    /**
     * Returns available layout colors
     *
     * @return array
     */
    public function getAvailableLayoutColors()
    {
        $module = \XLite\Core\Database::getRepo('XLite\Model\Module')->getCurrentSkinModule();

        return $module
            ? $module->callModuleMethod('getLayoutColors', array())
            : array();
    }

    /**
     * Returns current layout type
     *
     * @return string
     */
    public function getLayoutColor()
    {
        $layoutColor = \XLite\Core\Config::getInstance()->Layout->color;
        $availableColors = $this->getAvailableLayoutColors();

        return isset($availableColors[$layoutColor])
            ? $layoutColor
            : '';
    }

    /**
     * Returns current layout type
     *
     * @return string
     */
    public function getLayoutColorName()
    {
        $layoutColor = \XLite\Core\Config::getInstance()->Layout->color;
        $availableColors = $this->getAvailableLayoutColors();

        return isset($availableColors[$layoutColor])
            ? $availableColors[$layoutColor]
            : '';
    }

    /**
     * Returns skin preview image URL
     *
     * @param \XLite\Model\Module $module Skin module
     * @param string              $color  Color
     * @param string              $type   Layout type
     *
     * @return string
     */
    public function getLayoutPreview($module, $color, $type)
    {
        $result = null;
        $path = $module && $module instanceof \XLite\Model\Module
            ? 'modules/' . $module->getAuthor() . '/' . $module->getName() . '/'
            : 'images/layout/';

        $image = 'preview' . ($color ? ('_' . $color) : '') . ($type ? ('_' . $type) : '') . '.jpg';
        $result = $this->getResourceWebPath($path . $image);

        if (null === $result) {
            $image = 'preview' . ($color ? ('_' . $color) : '') . '.jpg';
            $result = $this->getResourceWebPath($path . $image);
        }

        if (null === $result) {
            $image = 'preview' . ($type ? ('_' . $type) : '') . '.jpg';
            $result = $this->getResourceWebPath($path . $image);
        }

        if (null === $result) {
            $image = 'preview.jpg';
            $result = $this->getResourceWebPath($path . $image);
        }

        return $result ?: $this->getResourceWebPath('images/layout/preview_placeholder.jpg');
    }

    /**
     * Returns current layout preview image URL
     *
     * @return string
     */
    public function getCurrentLayoutPreview()
    {
        return $this->getLayoutPreview(
            \XLite\Core\Database::getRepo('XLite\Model\Module')->getCurrentSkinModule(),
            $this->getLayoutColor(),
            $this->getLayoutType()
        );
    }

    /**
     * Returns current layout images settings (sizes)
     *
     * @return array
     */
    public function getCurrentImagesSettings()
    {
        $currentSkinModule = \XLite\Core\Database::getRepo('XLite\Model\Module')
            ->getCurrentSkinModule();

        $skinModuleName = $currentSkinModule && $currentSkinModule->getActualName() !== "XC\ColorSchemes"
            ? $currentSkinModule->getActualName()
            : 'default';

        return \XLite\Core\Database::getRepo('XLite\Model\ImageSettings')
            ->findByModuleName($skinModuleName);
    }

    // }}}

    // {{{ Substitutional skins routines

    /**
     * Add skin
     *
     * @param string $name      Skin name
     * @param string $interface Interface code OPTIONAL
     *
     * @return void
     */
    public function addSkin($name, $interface = \XLite::CUSTOMER_INTERFACE)
    {
        if (!isset($this->skins[$interface])) {
            $this->skins[$interface] = array();
        }
        array_unshift($this->skins[$interface], $name);
    }

    /**
     * Remove skin
     *
     * @param string $name      Skin name
     * @param string $interface Interface code OPTIONAL
     *
     * @return void
     */
    public function removeSkin($name, $interface = null)
    {
        if (null !== $interface) {
            if (isset($this->skins[$interface])) {
                $key = array_search($name, $this->skins[$interface]);
                if (false !== $key) {
                    unset($this->skins[$interface][$key]);
                }
            }
        } else {
            foreach ($this->skins as $interface => $list) {
                $key = array_search($name, $list);
                if (false !== $key) {
                    unset($this->skins[$interface][$key]);
                }
            }
        }
    }

    /**
     * Get skins list
     *
     * @param string $interface Interface code OPTIONAL
     *
     * @return array
     */
    public function getSkins($interface = null)
    {
        $interface = $interface ?: $this->currentInterface;
        $list = isset($this->skins[$interface]) ? $this->skins[$interface] : array();
        $list = array_merge($list, $this->getBaseSkinByInterface($interface));

        return $list;
    }

    // {{{ Resource path

    /**
     * Returns the resource full path
     *
     * @param string $shortPath Short path
     * @param string $interface Interface code OPTIONAL
     *
     * @return string
     */
    public function getResourceFullPath($shortPath, $interface = null)
    {
        $interface = $interface ?: $this->currentInterface;

        $key = $this->prepareResourceKey($shortPath, $interface);
        if (!isset($this->resourcesCache[$key])) {
            foreach ($this->getSkinPaths($interface) as $path) {
                $fullPath = $path['fs'] . LC_DS . $shortPath;
                if (file_exists($fullPath)) {
                    $this->resourcesCache[$key] = $path;
                    break;
                }
            }
        }

        return isset($this->resourcesCache[$key])
            ? $this->resourcesCache[$key]['fs'] . LC_DS . $shortPath
            : null;
    }

    /**
     * Returns the resource web path
     *
     * @param string $shortPath  Short path
     * @param string $outputType Output type OPTIONAL
     * @param string $interface  Interface code OPTIONAL
     *
     * @return string
     */
    public function getResourceWebPath($shortPath, $outputType = self::WEB_PATH_OUTPUT_SHORT, $interface = null)
    {
        $interface = $interface ?: $this->currentInterface;
        $key = $interface . '.' . $shortPath;

        if (!isset($this->resourcesCache[$key])) {
            foreach ($this->getSkinPaths($interface) as $path) {
                $fullPath = $path['fs'] . LC_DS . $shortPath;
                if (file_exists($fullPath)) {
                    $this->resourcesCache[$key] = $path;
                    break;
                }
            }
        }

        return isset($this->resourcesCache[$key])
            ? $this->prepareResourceURL($this->resourcesCache[$key]['web'] . '/' . $shortPath, $outputType)
            : null;
    }

    /**
     * Defines the resource cache unique identifier of the given resource
     *
     * @param string $shortPath Short path for resource
     * @param string $interface Interface of the resource
     *
     * @return string Unique key identifier for the resource to be stored in the resource cache
     */
    protected function prepareResourceKey($shortPath, $interface)
    {
        return $this->currentInterface . '.' . $this->innerInterface . '.' . $interface . '.' . $shortPath;
    }

    // }}}

    /**
     * Prepare skin URL
     *
     * @param string $shortPath  Short path
     * @param string $outputType Output type OPTIONAL
     *
     * @return string
     */
    public function prepareSkinURL($shortPath, $outputType = self::WEB_PATH_OUTPUT_SHORT)
    {
        $skins = $this->getSkinPaths($this->currentInterface);
        $path = array_pop($skins);

        return $this->prepareResourceURL($path['web'] . '/' . $shortPath, $outputType);

    }

    /**
     * Save substitutonal skins data into cache
     *
     * @return void
     */
    public function saveSkins()
    {
        \XLite\Core\Database::getCacheDriver()->save(
            get_called_class() . '.SubstitutonalSkins',
            $this->resourcesCache
        );
    }

    /**
     * Get skin paths (file system and web)
     *
     * @param string  $interface          Interface code OPTIONAL
     * @param boolean $reset              Local cache reset flag OPTIONAL
     * @param boolean $baseSkins          Use base skins only flag OPTIONAL
     * @param boolean $allInnerInterfaces
     *
     * @return array
     *
     * TODO: refactor
     */
    public function getSkinPaths($interface = null, $reset = false, $baseSkins = false, $allInnerInterfaces = false)
    {
        $interface = $interface ?: $this->currentInterface;

        if (\XLite::MAIL_INTERFACE === $interface || \XLite::PDF_INTERFACE === $interface) {
            $innerInterface = $this->getInnerInterface();
            $key = $interface . '-' . $innerInterface;
            $innerInterfaces = $allInnerInterfaces
                ? [\XLite::CUSTOMER_INTERFACE, \XLite::COMMON_INTERFACE, \XLite::ADMIN_INTERFACE]
                : [$innerInterface, \XLite::COMMON_INTERFACE];

        } else {
            $innerInterface = null;
            $key = $interface;
            $innerInterfaces = [$innerInterface];
        }

        if ($reset || !isset($this->skinPaths[$key])) {
            $this->skinPaths[$key] = [];
            $locales = $this->getLocalesQuery($interface);

            $skins = $baseSkins ? $this->getBaseSkinByInterface($interface) : $this->getSkins($interface);

            foreach ($innerInterfaces as $workInnerInterface) {
                foreach ($skins as $skin) {
                    $webSkin = str_replace(LC_DS, '/', $skin);

                    foreach ($locales as $locale) {
                        $localeFsPath = $locale && $locale !== 'en' ? '_' . $locale : '';
                        $localeWebPath = $localeFsPath;

                        $skinPath = [
                            'name'   => $skin,
                            'fs'     => LC_DIR_SKINS . $skin . $localeFsPath,
                            'web'    => static::PATH_SKIN . '/' . $webSkin . $localeWebPath,
                            'locale' => $locale,
                        ];

                        if ($workInnerInterface) {
                            $webCommonSkin = str_replace(LC_DS, '/', $workInnerInterface);
                            $this->skinPaths[$key][] = [
                                'name'   => $skin . '/' . $workInnerInterface,
                                'fs'     => LC_DIR_SKINS . $skin . LC_DS . $workInnerInterface . $localeFsPath,
                                'web'    => static::PATH_SKIN . '/' . $webSkin . '/' . $webCommonSkin . $localeWebPath,
                                'locale' => $locale,
                            ];

                        } else {
                            $this->skinPaths[$key][] = $skinPath;
                        }
                    }
                }
            }
        }

        return $this->skinPaths[$key];
    }

    /**
     * Returns resource module path
     *
     * @param string $shortPath Short path to resource
     *
     * @return string
     */
    public function getResourceModulePath($shortPath)
    {
        $result = '';
        if (0 === strpos($shortPath, 'modules')) {
            $result = implode('/', array_slice(explode('/', $shortPath), 0, 3));
        }

        return $result;
    }

    /**
     * Get locales query
     *
     * @param string $interface Interface code
     *
     * @return array
     */
    protected function getLocalesQuery($interface)
    {
        if (\XLite::COMMON_INTERFACE === $interface) {
            $result = array(false);

        } else {
            $result = array(
                \XLite\Core\Session::getInstance()->getLanguage()->getCode(),
                $this->locale,
            );

            $result = array_unique($result);
        }

        return $result;
    }

    /**
     * Get base skin by interface code
     *
     * @param string $interface Interface code OPTIONAL
     *
     * @return string[]
     */
    protected function getBaseSkinByInterface($interface = null)
    {
        switch ($interface) {
            case \XLite::ADMIN_INTERFACE:
                $skin = static::PATH_ADMIN;
                break;

            case \XLite::CONSOLE_INTERFACE:
                $skin = static::PATH_CONSOLE;
                break;

            case \XLite::MAIL_INTERFACE:
                $skin = static::PATH_MAIL;
                break;

            case \XLite::COMMON_INTERFACE:
                $skin = static::PATH_COMMON;
                break;

            case \XLite::PDF_INTERFACE:
                $skin = static::PATH_PDF;
                break;

            default:
                $options = \XLite::getInstance()->getOptions('skin_details');
                $skin = $options['skin'] === 'default' ? 'customer' : $options['skin'];
        }

        $result = array($skin);

        // foreach ($this->getLocalesQuery($interface) as $locale) {
        //     if (is_dir(LC_DIR_SKINS . $skin . '_' . $locale)) {
        //         $result[] = $skin . '_' . $locale;
        //     }
        // }

        return $result;
    }

    /**
     * Prepare resource URL
     *
     * @param string $url        URL
     * @param string $outputType Output type
     *
     * @return string
     */
    protected function prepareResourceURL($url, $outputType)
    {
        $url = trim($url);

        switch ($outputType) {
            case static::WEB_PATH_OUTPUT_FULL:
                if (preg_match('/^\w+\//S', $url)) {
                    $url = \XLite::getInstance()->getShopURL($url);
                }
                break;

            default:
        }


        return $url;
    }

    /**
     * Restore substitutonal skins data from cache
     *
     * @return void
     */
    protected function restoreSkins()
    {
        $driver = \XLite\Core\Database::getCacheDriver();

        $data = $driver
            ? $driver->fetch(get_called_class() . '.SubstitutonalSkins')
            : null;

        if ($data && is_array($data)) {
            $this->resourcesCache = $data;
        }
    }

    // }}}

    // {{{ Initialization routines

    /**
     * Set current skin as the admin one
     *
     * @return void
     */
    public function setAdminSkin()
    {
        $this->currentInterface = \XLite::ADMIN_INTERFACE;
        $this->setSkin(static::PATH_ADMIN);
    }

    /**
     * Set current skin as the admin one
     *
     * @return void
     */
    public function setConsoleSkin()
    {
        $this->currentInterface = \XLite::CONSOLE_INTERFACE;
        $this->setSkin(static::PATH_CONSOLE);
    }

    /**
     * Set current skin as the mail one
     *
     * @param string $interface Interface to use after MAIL one OPTIONAL
     *
     * @return void
     */
    public function setMailSkin($interface = \XLite::CUSTOMER_INTERFACE)
    {
        $this->currentInterface = \XLite::MAIL_INTERFACE;

        $this->innerInterface = $interface;

        $this->setSkin(static::PATH_MAIL);
    }

    /**
     * Set current skin as the pdf one
     *
     * @param string $interface Interface to use after MAIL one OPTIONAL
     *
     * @return void
     */
    public function setPdfSkin($interface = \XLite::CUSTOMER_INTERFACE)
    {
        $this->currentInterface = \XLite::PDF_INTERFACE;

        $this->innerInterface = $interface;

        $this->setSkin(static::PATH_PDF);
    }

    /**
     * Set current skin as the customer one
     *
     * @return void
     */
    public function setCustomerSkin()
    {
        $this->skin = null;
        $this->locale = null;
        $this->currentInterface = \XLite::CUSTOMER_INTERFACE;

        $this->setOptions();
    }


    /**
     * Set current skin
     *
     * @param string $skin New skin
     *
     * @return void
     */
    public function setSkin($skin)
    {
        $this->skin = $skin;
        $this->setPath();
    }

    /**
     * Set some class properties
     *
     * @return void
     */
    protected function setOptions()
    {
        $options = \XLite::getInstance()->getOptions('skin_details');
        
        if (isset($options['skin']) && $options['skin'] === 'default') {
            $options['skin'] = 'customer';
        }

        foreach (array('skin', 'locale') as $name) {
            if (!isset($this->$name)) {
                $this->$name = $options[$name];
            }
        }

        $this->setPath();
    }

    /**
     * Set current skin path
     *
     * @return void
     */
    protected function setPath()
    {
        $this->path = self::PATH_SKIN
            . LC_DS . $this->skin
            . ($this->locale && $this->locale != 'en' ? '_' . $this->locale : '')
            . LC_DS;
    }

    /**
     * Constructor
     */
    protected function __construct()
    {
        parent::__construct();

        $this->setOptions();

        $this->skinsCache = (bool)\XLite::getInstance()
            ->getOptions(array('performance', 'skins_cache'));

        if ($this->skinsCache) {
            $this->restoreSkins();
            register_shutdown_function(array($this, 'saveSkins'));
        }
    }

    // }}}

    // {{{ Resources

    /**
     * Register resources
     *
     * @param array              $resources Resources
     * @param integer            $index     Index (weight)
     * @param string             $interface Interface OPTIONAL
     * @param string             $group    Group OPTIONAL
     *
     * @return void
     */
    public function registerResources(array $resources, $index, $interface = null, $group = null)
    {
        $this->currentGroup = $group;

        foreach ($resources as $type => $files) {
            $method = 'register' . strtoupper($type) . 'Resources';

            if (method_exists($this, $method)) {
                $this->{$method}($files, $index, $interface);
            }
        }

        $this->prepareResourcesFlag = false;
        $this->currentGroup = null;
    }

    /**
     * Return list of all registered resources
     *
     * @return array
     */
    public function getRegisteredResources()
    {
        $result = array();
        foreach ($this->getResourcesTypes() as $type) {
            $result[$type] = $this->getRegisteredResourcesByType($type);
        }

        return $result;
    }

    /**
     * Get registered resources by type
     *
     * @param string $type Resource type
     *
     * @return array
     */
    public function getRegisteredResourcesByType($type)
    {
        $result = array();
        foreach ($this->getPreparedResources() as $subresources) {
            if (!empty($subresources[$type])) {
                foreach ($subresources[$type] as $path => $file) {
                    if (isset($result[$path])) {
                        unset($result[$path]);
                    }
                    $result[$path] = $file;
                }
            }
        }

        return $result;
    }

    /**
     * Return list of all registered and prepared resources
     *
     * @param string $group Filter by group OPTIONAL
     *
     * @return array
     */
    public function getRegisteredPreparedResources($group = null)
    {
        $result = array();
        foreach ($this->getResourcesTypes() as $type) {
            $resources = $this->getPreparedResourcesByType($type);

            if ($group) {
                $resources = array_filter(
                    $resources,
                    function ($item) use ($group) {
                        return isset($item['group']) && $item['group'] == $group;
                    }
                );
            }

            $result[$type] = $resources;
        }

        return $result;
    }

    /**
     * Get registered and prepared resources by type
     *
     * @param string $type Resource type
     *
     * @return array
     */
    public function getPreparedResourcesByType($type)
    {
        $resources = array_filter(
            \XLite\Core\Layout::getInstance()->getRegisteredResourcesByType($type),
            array($this, 'isValid' . strtoupper($type) . 'Resource')
        );

        $method = 'prepare' . strtoupper($type) . 'Resources';

        return $this->$method($resources);
    }

    /**
     * Get resources types
     *
     * @return array
     */
    public function getResourcesTypes()
    {
        return array(
            \XLite\View\AView::RESOURCE_JS,
            \XLite\View\AView::RESOURCE_CSS,
        );
    }

    /**
     * Get prepared resources
     *
     * @return array
     */
    protected function getPreparedResources()
    {
        if (!$this->prepareResourcesFlag) {
            $this->resources = $this->prepareResources($this->resources);
            $this->prepareResourcesFlag = true;
        }

        return $this->resources;
    }

    /**
     * Prepare resources
     *
     * @param array $resources Resources
     *
     * @return array
     */
    protected function prepareResources(array $resources)
    {
        ksort($resources, SORT_NUMERIC);

        foreach ($resources as $index => $subresources) {
            foreach ($subresources as $type => $files) {
                foreach ($files as $name => $file) {
                    $file = $this->prepareResource($file, $type);
                    if ($file) {
                        $files[$name] = $file;

                    } else {
                        unset($files[$name]);
                    }
                }

                if ($files) {
                    $subresources[$type] = $files;

                } else {
                    unset($subresources[$type]);
                }
            }

            if ($subresources) {
                $resources[$index] = $subresources;

            } else {
                unset($resources[$index]);
            }
        }

        return $resources;
    }

    /**
     * Prepare resource
     *
     * @param array  $data Resource data
     * @param string $type Resource type
     *
     * @return array
     */
    protected function prepareResource(array $data, $type)
    {
        $data = $this->prepareResourceFullURL($data, $type);
        if ($data) {
            $method = 'prepareResource' . strtoupper($type);
            if (method_exists($this, $method)) {
                $data = $this->$method($data, $type);
            }
        }

        return $data;
    }

    /**
     * Prepare resource full URL
     *
     * @param array  $data Resource data
     * @param string $type Resource type
     *
     * @return array
     */
    protected function prepareResourceFullURL(array $data, $type)
    {
        if (empty($data['url'])) {
            foreach ($data['filelist'] as $file) {
                $shortURL = str_replace(LC_DS, '/', $file);

                $fullURL = $this->getResourceWebPath(
                    $shortURL,
                    \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
                    $data['interface']
                );

                if (null !== $fullURL) {
                    $data['original'] = $data['file'];
                    $data['file'] = $this->getResourceFullPath($shortURL, $data['interface']);
                    $data += array(
                        'media' => 'all',
                        'url'   => $fullURL,
                    );

                    break;
                }
            }
        }

        return empty($data['url']) ? null : $data;
    }

    /**
     * Prepare resource as CSS
     *
     * @param array  $data Resource data
     * @param string $type Resource type
     *
     * @return array
     */
    protected function prepareResourceCSS(array $data, $type)
    {
        if ($this->isLESSResource($data)) {
            $data = $this->prepareResourceLESS($data);
        }

        return $data;
    }

    /**
     * Check if the resource is a LESS one
     *
     * @param array $data Resource data
     *
     * @return boolean
     */
    protected function isLESSResource(array $data)
    {
        return !empty($data['file']) && preg_match('/\.less$/S', $data['file']);
    }

    /**
     * Prepare resource as LESS
     *
     * @param array $data Resource data
     *
     * @return array
     */
    protected function prepareResourceLESS($data)
    {
        $data['less'] = true;

        return $data;
    }

    /**
     * Prepare resource as JS
     *
     * @param array  $data Resource data
     * @param string $type Resource type
     *
     * @return array
     */
    protected function prepareResourceJS(array $data, $type)
    {
        return $data;
    }

    /**
     * Prepare CSS resources
     *
     * @param array $resources Resources
     *
     * @return boolean
     */
    protected function isValidCSSResource(array $resources)
    {
        return isset($resources['url']);
    }

    /**
     * Prepare JS resources
     *
     * @param array $resources Resources
     *
     * @return boolean
     */
    protected function isValidJSResource(array $resources)
    {
        return isset($resources['url']);
    }

    /**
     * Prepare CSS resources
     *
     * @param array $resources Resources
     *
     * @return array
     */
    protected function prepareCSSResources(array $resources)
    {
        $lessResources = array();

        // Detect the merged resources grouping
        foreach ($resources as $index => $resource) {
            if (isset($resource['less'])) {
                if ($resource['media'] === 'force_all') {
                    $resources[$index]['media'] = 'all';
                } elseif (!isset($resource['merge']) && 'common' !== $resource['interface']) {
                    $resource['merge'] = 'bootstrap/css/initialize.less';
                }

                if (isset($resource['merge'])) {
                    $lessResources[$resource['merge']][] = $resource;
                    unset($resources[$index]);
                }
            }
        }

        foreach ($resources as $index => $resource) {
            if (isset($resource['less'])) {
                if (!isset($lessResources[$resource['original']])) {
                    // one resource group is registered
                    $lessGroup = array($resource);

                } else {
                    // The resource is placed into the head of the less resources list
                    $lessGroup = array_merge(array($resource), $lessResources[$resource['original']]);
                }

                $resources[$index] = \XLite\Core\LessParser::getInstance()->makeCSS($lessGroup);

                // Media type is derived from the parent resource
                $resources[$index]['media'] = $resource['media'];
            }
        }

        return $resources;
    }

    /**
     * Prepare JS resources
     *
     * @param array $resources Resources
     *
     * @return array
     */
    protected function prepareJSResources(array $resources)
    {
        return $resources;
    }

    /**
     * Main JS resources registrator. see self::registerResources() for more info
     *
     * @param array   $files     List of file relative pathes to the resources
     * @param integer $index     Position in the ordered resources queue
     * @param string  $interface Interface where the files are located
     *
     * @see \XLite\View\AView::registerResources()
     */
    protected function registerJSResources(array $files, $index, $interface)
    {
        $this->registerResourcesByType($files, $index, $interface, \XLite\View\AView::RESOURCE_JS);
    }

    /**
     * Main CSS resources registrator. see self::registerResources() for more info
     *
     * @param array   $files     List of file relative pathes to the resources
     * @param integer $index     Position in the ordered resources queue
     * @param string  $interface Interface where the files are located
     *
     * @see \XLite\View\AView::registerResources()
     */
    protected function registerCSSResources(array $files, $index, $interface)
    {
        $this->registerResourcesByType($files, $index, $interface, \XLite\View\AView::RESOURCE_CSS);
    }

    /**
     * Main common registrator of resources. see self::registerResources() for more info
     * This method takes the files list and registers them as the resources of the provided $type
     *
     * @param array   $files     List of file relative pathes to the resources
     * @param integer $index     Position in the ordered resources queue
     * @param string  $interface Interface where the files are located
     * @param string  $type      Type of the resources ('js', 'css')
     *
     */
    protected function registerResourcesByType(array $files, $index, $interface, $type)
    {
        foreach ($files as $resource) {
            $resource = $this->prepareResourceByType($resource, $index, $interface, $type);
            $hash = md5(serialize($resource));

            if ($resource && $this->currentGroup && !isset($resource['group'])) {
                $resource['group'] = $this->currentGroup;
            }

            if ($resource && !isset($this->resources[$index][$type][$hash])) {
                $this->resources[$index][$type][$hash] = $resource;
            }
        }
    }

    /**
     * The resource must be prepared before the registration in the resources storage:
     * - the file must be correctly located and full file path must be found
     * - the web location of the resource must be found
     *
     * Then this method actually stores the resource into the static resources storage
     *
     * @param string|array|null $resource  Resource file path or array of resources
     * @param integer           $index
     * @param string            $interface
     * @param string            $type
     *
     * @return array
     */
    protected function prepareResourceByType($resource, $index, $interface, $type)
    {
        if (empty($resource)) {
            $resource = null;

        } elseif (is_string($resource)) {
            $resource = array(
                'file'     => $resource,
                'filelist' => array($resource),
            );
        }

        if ($resource && !isset($resource['url'])) {
            if (!isset($resource['filelist'])) {
                $resource['filelist'] = array($resource['file']);
            }

            if (!isset($resource['interface'])) {
                $resource['interface'] = $interface;
            }
        }

        return $resource;
    }

    // }}}


    // {{{ Meta tags

    /**
     * Register meta tags to include in page content
     *
     * @param array $metaTags
     */
    public function registerMetaTags(array $metaTags)
    {
        if (!empty($metaTags)) {
            $this->metaTags = array_unique(array_merge($this->metaTags, $metaTags));
        }
    }

    /**
     * Return list of all registered meta tags
     *
     * @return array
     */
    public function getRegisteredMetaTags()
    {
        return $this->metaTags;
    }

    // }}}


    // {{{ Sidebars

    /**
     * Returns current layout group based on best-first target
     * @return string
     */
    public function getCurrentLayoutGroup()
    {
        $target = \XLite\Core\Request::getInstance()->target;
        $groups = $this->getLayoutGroups();

        $current = static::LAYOUT_GROUP_DEFAULT;

        foreach ($groups as $name => $targets) {
            if (in_array($target, $targets, true)) {
                $current = $name;
                break;
            }
        }

        return $current;
    }

    /**
     * Is Sidebar Single
     *
     * @return boolean
     */
    public function isSidebarSingle()
    {
        return in_array(
            $this->getLayoutType(),
            array(
                static::LAYOUT_TWO_COLUMNS_LEFT,
                static::LAYOUT_TWO_COLUMNS_RIGHT
            ),
            true
        );
    }

    /**
     * Check - first sidebar is visible or not
     *
     * @return boolean
     */
    public function isSidebarFirstVisible()
    {
        return \XLite::isAdminZone()
            ? $this->isAdminSidebarFirstVisible()
            : $this->isCustomerSidebarFirstVisible();
    }

    /**
     * Check - second sidebar is visible or not
     *
     * @return boolean
     */
    public function isSidebarSecondVisible()
    {
        return \XLite::isAdminZone()
            ? $this->isAdminSidebarSecondVisible()
            : $this->isCustomerSidebarSecondVisible();
    }

    /**
     * Check - first sidebar is visible or not (in admin interface)
     *
     * @return boolean
     */
    protected function isAdminSidebarFirstVisible()
    {
        $widget = new \Xlite\View\Controller;

        return $widget->isViewListVisible('admin.main.page.content.left')
            && !\Xlite::getController()->isForceChangePassword();
    }

    /**
     * Check - second sidebar is visible or not (in admin interface)
     *
     * @return boolean
     */
    protected function isAdminSidebarSecondVisible()
    {
        return false;
    }

    /**
     * Check - first sidebar is visible or not (in customer interface)
     *
     * @return boolean
     */
    protected function isCustomerSidebarFirstVisible()
    {
        return in_array(
            $this->getLayoutType(),
            array(
                static::LAYOUT_TWO_COLUMNS_LEFT,
                static::LAYOUT_THREE_COLUMNS
            ),
            true
        )
            && !in_array(
                \XLite\Core\Request::getInstance()->target,
                $this->getSidebarFirstHiddenTargets(),
                true
            );
    }

    /**
     * Check - second sidebar is visible or not (in customer interface)
     *
     * @return boolean
     */
    protected function isCustomerSidebarSecondVisible()
    {
        return in_array(
            $this->getLayoutType(),
            array(
                \XLite\Core\Layout::LAYOUT_TWO_COLUMNS_RIGHT,
                \XLite\Core\Layout::LAYOUT_THREE_COLUMNS
            ),
            true
        )
        && !in_array(
            \XLite\Core\Request::getInstance()->target,
            $this->getSidebarSecondHiddenTargets(),
            true
        );
    }

    /**
     * Define the pages where first sidebar will be hidden.
     * By default we hide it on:
     *      product page,
     *      cart page,
     *      checkout page
     *      checkout success (invoice) page
     *      payment page
     *
     * @return array
     */
    protected function getSidebarFirstHiddenTargets()
    {
        return array(
            'cart',
            'product',
            'checkout',
            'checkoutPayment',
            'checkoutSuccess',
        );
    }

    /**
     * Define the pages where second sidebar will be hidden.
     * By default we hide it on:
     *      product page,
     *      cart page,
     *      checkout page
     *      checkout success (invoice) page
     *      payment page
     *
     * @return array
     */
    protected function getSidebarSecondHiddenTargets()
    {
        return array(
            'cart',
            'product',
            'checkout',
            'checkoutPayment',
            'checkoutSuccess',
        );
    }

    // }}}
}
