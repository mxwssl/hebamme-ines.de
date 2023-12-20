<?php

defined('TYPO3_MODE') || exit();

$GLOBALS['TCA']['pages']['columns']['media'] = array_replace_recursive(
    $GLOBALS['TCA']['pages']['columns']['media'],
    [
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
);
