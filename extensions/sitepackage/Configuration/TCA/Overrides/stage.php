<?php

defined('TYPO3_MODE') || exit();

$ll = 'LLL:EXT:sitepackage/Resources/Private/Language/locallang.xlf:tt_content.';

/**
 * Add Content Element
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
        $ll . 'ce_stage',
        'ce_stage',
        'default-icon',
        'custom'
    ]
);

/**
 * Assign Icon
 */
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['ce_stage'] = 'default-icon';

/**
 * Configure element type
 */
$GLOBALS['TCA']['tt_content']['types']['ce_stage'] = [
    'showitem' => '
        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
        --palette--;;headers,
        bodytext;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext_formlabel,
    --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.media,
        assets,
    --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
        --palette--;;frames,
        --palette--;;appearanceLinks,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
        --palette--;;language,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
        --palette--;;hidden,
        --palette--;;access,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
    --div--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_category.tabs.category,categories,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended
    ',
    'columnsOverrides' => [
        'header' => [
            'description' => $ll . 'ce_stage.header.description',
        ],
        'bodytext' => [
            'description' => $ll . 'ce_stage.bodytext.description',
            'config' => [
                'rows' => 4,
            ]
        ],
        'assets' => [
            'config' => [
                'appearance' => [
                    'collapseAll' => 1
                ],
                'filter' => [
                    0 => [
                        'userFunc' => \TYPO3\CMS\Core\Resource\Filter\FileExtensionFilter::class . '->filterInlineChildren',
                        'parameters' => [
                            'allowedFileExtensions' => 'gif,jpg,jpeg,bmp,png',
                        ]
                    ]
                ],
                'overrideChildTca' => [
                    'columns' => [
                        'uid_local' => [
                            'config' => [
                                'appearance' => [
                                    'elementBrowserAllowed' => 'gif,jpg,jpeg,bmp,png',
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
