<?php

defined('TYPO3_MODE') || exit();

(function ($extensionKey) {
    /*
     * Add default TypoScript (constants and setup)
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        $extensionKey,
        'Configuration/TypoScript',
        'Sitepackage'
    );
})('sitepackage');
