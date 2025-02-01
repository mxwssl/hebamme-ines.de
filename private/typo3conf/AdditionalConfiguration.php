<?php

defined('TYPO3_MODE') || die();

use TYPO3\CMS\Core\Core\Environment;

$context = Environment::getContext()->__toString();

$customChanges = array(
    'DB' => [
        'Connections' => [
            'Default' => [
                'charset' => 'utf8mb4',
                'dbname' => getenv('TYPO3_INSTALL_DB_DBNAME'),
                'driver' => getenv('TYPO3_INSTALL_DB_DRIVER'),
                'host' => getenv('TYPO3_INSTALL_DB_HOST'),
                'password' => getenv('TYPO3_INSTALL_DB_PASSWORD'),
                'tableoptions' => [
                    'charset' => 'utf8mb4',
                    'collate' => 'utf8mb4_unicode_ci',
                ],
                'unix_socket' => getenv("TYPO3_INSTALL_DB_UNIX_SOCKET"),
                'user' => getenv('TYPO3_INSTALL_DB_USER'),
            ],
        ],
    ],
    'SYS' => array(
        'sitename' => getenv('SITE_NAME') . ' [' . $context . ']',
        'enableDeprecationLog' => 0,
    ),
    'EXTENSIONS' => [
        'backend' => [
            'backendFavicon' => 'EXT:sitepackage/Resources/Public/Img/Icons/extension.svg',
            'backendLogo' => 'EXT:sitepackage/Resources/Public/Img/Icons/extension.svg',
            'loginBackgroundImage' => 'EXT:sitepackage/Resources/Public/Img/background.svg',
            'loginFootnote' => '‹/› with ♥ in Berlin.',
            'loginHighlightColor' => '#A0CD8E',
            'loginLogo' => 'EXT:sitepackage/Resources/Public/Img/hebamme-ines.svg',
        ],
        'staticfilecache' => [
            'overrideCacheDirectory' => 'typo3temp/assets/tx_staticfilecache/'
        ]
    ]
);

$GLOBALS['TYPO3_CONF_VARS'] = array_replace_recursive($GLOBALS['TYPO3_CONF_VARS'], (array)$customChanges);

/**
 * Include additional configuration based on environment
 */
$file = realpath(__DIR__) . '/AdditionalConfiguration' . str_replace('/', '', $context) . '.php';
if (is_file($file)) {
    include_once($file);
    $GLOBALS['TYPO3_CONF_VARS'] = array_replace_recursive($GLOBALS['TYPO3_CONF_VARS'], (array)$customChanges);
}
