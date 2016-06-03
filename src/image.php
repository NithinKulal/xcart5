<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// To avoid superflous checks
define('XLITE_INSTALL_MODE', true);
define('LC_DO_NOT_REBUILD_CACHE', true);

require_once (__DIR__ . DIRECTORY_SEPARATOR . 'top.inc.php');

if (isset($_REQUEST['target'])) {
    $path = null;
    switch ($_REQUEST['target']) {

        case 'module':
            if (!empty($_REQUEST['author']) && !empty($_REQUEST['name'])) {
                $image = isset($_REQUEST['image']) ? $_REQUEST['image'] : null;
                switch ($image) {
                    case 'icon.png':
                    default:
                        $path = \Includes\Utils\ModulesManager
                            ::getModuleIconFile($_REQUEST['author'], $_REQUEST['name']);
                        break;
                }
            }
            break;

        default:
            // ...
    }

    if (!empty($path)) {

        $type   = 'png';
        $data   = \Includes\Utils\FileManager::read($path);
        $length = strlen($data);

        header('Content-Type: image/' . $type);
        header('Content-Length: ' . $length);

        echo ($data);
    }
}
