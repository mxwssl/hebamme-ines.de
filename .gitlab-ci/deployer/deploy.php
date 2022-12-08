<?php
namespace Deployer;
require_once dirname(__DIR__, 2) . '/vendor/deployer/deployer/recipe/common.php';
require_once dirname(__DIR__, 2) . '/vendor/deployer/recipes/recipe/rsync.php';
inventory(__DIR__ . '/hosts.yml');
set('binaries', [
    'staging' => [
        'php' => ''
    ],
    'production' => [
        'php' => ''
    ]
]);
set('keep_releases', 2);
/***************
 * Set shared directories
 */
$sharedDirectories = [
    'private/fileadmin',
    'private/uploads',
    'private/typo3temp/assets/_processed_',
    'private/typo3temp/assets/compressed',
    'private/typo3temp/assets/images',
    'var'
];
set('shared_dirs', $sharedDirectories);
/***************
 * Set shared files
 */
$sharedFiles = [
    '.env',
    'private/typo3conf/LocalConfiguration.php'
];
set('shared_files', $sharedFiles);
/***************
 * Set writable directories
 */
$writeableDirectories = [
    'public/typo3temp'
];
set('writable_dirs', $writeableDirectories);
/***************
 * Set excluded files and folder for rsync
 */
$exclude = [
    '*.example',
    '.ddev',
    '.DS_Store',
    '.editorconfig',
    '.env',
    '.git',
    '.gitignore',
    '.gitlab-ci',
    '.gitlab-ci.yml',
    '.idea',
    'extensions/**/bower.json',
    'extensions/**/bower_components',
    'extensions/**/gulp',
    'extensions/**/node_modules',
    'extensions/**/package-lock.json',
    'extensions/**/package.json',
    'extensions/**/Resources/Private/Frontend',
    'private/typo3conf/AdditionalConfigurationDevelopment.php',
    'private/typo3conf/AdditionalConfigurationDevelopment.sample-mamp.php',
    'README.md',
    'Readme.rst',
    'Readme.txt',
    'Upgrading.rst',
    'Upgrading.txt'
];
set('rsync', [
    'exclude' => array_merge($sharedDirectories, $sharedFiles, $exclude),
    'exclude-file' => false,
    'include' => [],
    'include-file' => false,
    'filter' => [],
    'filter-file' => false,
    'filter-perdir' => false,
    'flags' => 'az',
    'options' => ['delete'],
    'timeout' => 300
]);
set('rsync_src', './');
/***************
 * TYPO3 related task
 */
task('typo3', function(){
    $binaries = get('binaries');
    $php = $binaries[get('stage')]['php'];
    run('cd {{release_path}} && ' . $php . ' vendor/bin/typo3cms install:generatepackagestates');
    run('cd {{release_path}} && ' . $php . ' vendor/bin/typo3cms install:extensionsetupifpossible');
    run('cd {{release_path}} && ' . $php . ' vendor/bin/typo3cms cache:flush');
});
/***************
 * Set staging task
 */
task('staging', function(){
    run('cd {{release_path}} && mv config/server/Staging/_.htaccess public/.htaccess');
    run('cd {{release_path}} && mv config/server/Staging/_.htpasswd public/.htpasswd');
    run('cd {{release_path}} && rm -r config/server/');
})->onStage('staging');
/***************
 * Set production task
 */
task('production', function(){
    run('cd {{release_path}} && mv config/server/Production/_.htaccess public/.htaccess');
    run('cd {{release_path}} && rm -r config/server/');
})->onStage('production');
/***************
 * Define deploy task
 */
task('deploy', [
    'deploy:prepare',
    'deploy:release',
    'rsync:warmup',
    'rsync',
    'deploy:shared',
    'deploy:writable',
    'typo3',
    'staging',
    'production',
    'deploy:symlink',
    'cleanup'
]);
