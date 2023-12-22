<?php

defined('TYPO3_MODE') || die();

(function () {
    /**
     * Allow tables
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_sitepackage_feedback_item');
})();
