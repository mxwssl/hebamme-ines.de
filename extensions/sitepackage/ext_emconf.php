<?php

/**
 * Extension Manager/Repository config file
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Sitepackage',
    'description' => 'Composer-based TYPO3 Extension',
    'category' => 'templates',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
            'fluid_styled_content' => '11.5.0-11.5.99',
            'rte_ckeditor' => '11.5.0-11.5.99',
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Mxw\\Sitepackage\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Max Wessel',
    'author_email' => 'mx.wssl@gmail.com',
    'version' => '1.0.0',
];
