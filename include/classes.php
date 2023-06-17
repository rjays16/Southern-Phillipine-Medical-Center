<?php

/**
 * classes.php
 *
 * Class-path specification loaded by the Autoloader class. Subnamespaces are
 * designated with the '@' prefixand mapped to their respective  base paths.
 * More specific namespace mapping can be done directly via namespace => path
 * spec.
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014. Segworks Technologies Corporation
 */

$basePath = dirname(dirname(__FILE__));

return array(
    '@Base' => $basePath.'/include/base/',
    '@Helpers' => $basePath.'/include/helpers/',
    '@Components' => $basePath.'/include/components/',
    '@Widgets' => $basePath.'/include/widgets/',

    // HIS Modules Sub-namespaces
    '@Inventory' => $basePath.'/modules/inventory/',
    '@SystemAdmin' => $basePath.'/modules/system_admin/',
    '@Codeception' => $basePath.'/codeception/',

    // Specific namespace/path combination
    // Example:
    // 'Segworks/HIS/Sample/Subnamespace/Class' => $basePath . '/somedir/File.php',
);