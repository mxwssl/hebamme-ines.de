<?php

defined('TYPO3_MODE') || exit();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

(function ($llSP, $llFE, $llC) {

    foreach (['header', 'subheader'] as $field) {
        $GLOBALS['TCA']['tt_content']['columns'][$field]['config']['type'] = 'text';
        $GLOBALS['TCA']['tt_content']['columns'][$field]['config']['rows'] = 2;
    }

    /*
     * Add content element to selector list
     */
    ExtensionManagementUtility::addTcaSelectItemGroup(
        'tt_content',
        'CType',
        'custom',
        $llSP . 'tt_content.custom',
        'before:default'
    );

    /*
     * Extend existing palettes
     */
    $GLOBALS['TCA']['tt_content']['palettes'] = array_replace_recursive(
        $GLOBALS['TCA']['tt_content']['palettes'],
        [
            'headers' => [
                'label' => $llFE . 'palette.headers',
                'showitem' => '
                    header;' . $llFE . 'header_formlabel,
                    subheader;' . $llFE . 'subheader_formlabel,
                    --linebreak--,
                    header_layout;' . $llFE . 'header_layout_formlabel,
                    header_position;' . $llSP . 'tt_content.header_position
                ',
            ],
        ]
    );

    foreach ([['header_class', 'after:header_layout'], ['subheader_position', 'after:header_position']] as $field) {
        /*
         * Add fields to palette
         */
        ExtensionManagementUtility::addFieldsToPalette(
            'tt_content',
            'headers',
            $field[0],
            $field[1]
        );

        $paletteHeaderColumns[$field[0]] = [
            'label' => $llSP . 'tt_content.' . $field[0],
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
            ],
        ];
    }

    /*
     * Register new input fields
     */
    ExtensionManagementUtility::addTCAcolumns(
        'tt_content',
        $paletteHeaderColumns
    );

})(
    'LLL:EXT:sitepackage/Resources/Private/Language/locallang.xlf:',
    'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:',
    'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:'
);
