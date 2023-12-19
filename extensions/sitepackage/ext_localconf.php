<?php

defined('TYPO3_MODE') || exit();

use TYPO3\CMS\Core;

(function () {
    // Register Icons
    $iconRegistry = Core\Utility\GeneralUtility::makeInstance(Core\Imaging\IconRegistry::class);

    $iconRegistry->registerIcon(
        'default-icon',
        Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:sitepackage/Resources/Public/Icons/Extension.svg']
    );

    $versionInfo = Core\Utility\GeneralUtility::makeInstance(Core\Information\Typo3Version::class);
    // Only include page.tsconfig if TYPO3 version is below 12 so that it is not imported twice.
    if ($versionInfo->getMajorVersion() < 12) {
        Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            '@import "EXT:sitepackage/Configuration/page.tsconfig"'
        );
    }

    // Force feature toggle for own PreviewRenderer
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['fluidBasedPageModule'] = true;

    // RTE Configuration for ckeditor
    $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['default'] = 'EXT:sitepackage/Configuration/RTE/Default.yaml';
    $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['minimal'] = 'EXT:sitepackage/Configuration/RTE/Minimal.yaml';
})();
