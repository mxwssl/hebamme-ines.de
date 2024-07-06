<?php

namespace Deployer;

require_once 'recipe/common.php';
require_once 'contrib/rsync.php';

import(__DIR__ . '/hosts.yml');

set('keep_releases', 2);

set('shared_dirs', [
    'private/fileadmin',
    'private/uploads',
    'private/typo3temp/assets/_processed_',
    'private/typo3temp/assets/compressed',
    'private/typo3temp/assets/images',
    'var'
]);

set('shared_files', [
    '.env',
    'private/typo3conf/LocalConfiguration.php'
]);

set('writable_dirs', [
    'public/typo3temp'
]);

set('bin/php', function () {
    return '/usr/bin/php8.3-cli';
});

set('rsync_src', '../../');

set('rsync', [
    'exclude' => [
        '.DS_Store',
        '.editorconfig',
        '.env',
        '.git',
        '.gitignore',
        '.gitlab-ci',
        'extensions/**/utils',
        'extensions/**/node_modules',
        'extensions/**/package-lock.json',
        'extensions/**/package.json',
        'extensions/**/yarn.lock',
        'extensions/**/Resources/Private/Frontend',
        'private/typo3conf/AdditionalConfigurationDevelopment.php',
        'private/typo3conf/AdditionalConfigurationDevelopment.sample-mamp.php',
        'README.md',
    ],
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

// Tasks

desc('Prepare staging');
task('staging', function () {
    run('mv {{release_path}}/config/server/{{alias}}/_.htaccess {{release_path}}/public/.htaccess');
    run('mv {{release_path}}/config/server/{{alias}}/_.htpasswd {{release_path}}/public/.htpasswd');
    run('rm -r {{release_path}}/config/server/');
})->select('stage=staging');
after('rsync', 'staging');

desc('Deploy your project');
task('deploy', [
    'deploy:setup',
    'deploy:lock',
    'deploy:release',
    'rsync:warmup',
    'rsync',
    'deploy:shared',
    'deploy:writable',
    'deploy:clear_paths',
    'deploy:publish'
]);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

desc('Migrate database');
task('database:migrate', function () {
    run('{{bin/php}} {{release_path}}/vendor/bin/typo3cms database:updateschema');
});
before('deploy:symlink', 'database:migrate');

// Clear cache after symlink new release
desc('Clear TYPO3 Cache');
task('typo3:cache:clear', function () {
    run('{{bin/php}} {{release_path}}/vendor/bin/typo3cms cache:flush');
});
after('deploy:symlink', 'typo3:cache:clear');








// require 'recipe/common.php';

// // Load host configuration from YAML file
// inventory('hosts.yml');


// set('binaries', [
//     'staging' => [
//         'php' => ''
//     ],
//     'production' => [
//         'php' => ''
//     ]
// ]);
// set('keep_releases', 2);
// /***************
//  * Set shared directories
//  */
// $sharedDirectories = [
//     'private/fileadmin',
//     'private/uploads',
//     'private/typo3temp/assets/_processed_',
//     'private/typo3temp/assets/compressed',
//     'private/typo3temp/assets/images',
//     'var'
// ];
// set('shared_dirs', $sharedDirectories);
// /***************
//  * Set shared files
//  */
// $sharedFiles = [
//     '.env',
//     'private/typo3conf/LocalConfiguration.php'
// ];
// set('shared_files', $sharedFiles);
// /***************
//  * Set writable directories
//  */
// $writeableDirectories = [
//     'public/typo3temp'
// ];
// set('writable_dirs', $writeableDirectories);
// /***************
//  * Set excluded files and folder for rsync
//  */
// $exclude = [
//     '*.example',
//     '.ddev',
//     '.DS_Store',
//     '.editorconfig',
//     '.env',
//     '.git',
//     '.gitignore',
//     '.gitlab-ci',
//     '.gitlab-ci.yml',
//     '.idea',
//     'extensions/**/bower.json',
//     'extensions/**/bower_components',
//     'extensions/**/gulp',
//     'extensions/**/node_modules',
//     'extensions/**/package-lock.json',
//     'extensions/**/package.json',
//     'extensions/**/Resources/Private/Frontend',
//     'private/typo3conf/AdditionalConfigurationDevelopment.php',
//     'private/typo3conf/AdditionalConfigurationDevelopment.sample-mamp.php',
//     'README.md',
//     'Readme.rst',
//     'Readme.txt',
//     'Upgrading.rst',
//     'Upgrading.txt'
// ];
// set('rsync', [
//     'exclude' => array_merge($sharedDirectories, $sharedFiles, $exclude),
//     'exclude-file' => false,
//     'include' => [],
//     'include-file' => false,
//     'filter' => [],
//     'filter-file' => false,
//     'filter-perdir' => false,
//     'flags' => 'az',
//     'options' => ['delete'],
//     'timeout' => 300
// ]);
// set('rsync_src', './');
// /***************
//  * TYPO3 related task
//  */
// task('typo3', function(){
//     $binaries = get('binaries');
//     $php = $binaries[get('stage')]['php'];
//     run('cd {{release_path}} && ' . $php . ' vendor/bin/typo3cms install:generatepackagestates');
//     run('cd {{release_path}} && ' . $php . ' vendor/bin/typo3cms install:extensionsetupifpossible');
//     run('cd {{release_path}} && ' . $php . ' vendor/bin/typo3cms cache:flush');
// });
// /***************
//  * Set staging task
//  */
// task('staging', function(){
//     run('cd {{release_path}} && mv config/server/Staging/_.htaccess public/.htaccess');
//     run('cd {{release_path}} && mv config/server/Staging/_.htpasswd public/.htpasswd');
//     run('cd {{release_path}} && rm -r config/server/');
// })->onStage('staging');
// /***************
//  * Set production task
//  */
// task('production', function(){
//     run('cd {{release_path}} && mv config/server/Production/_.htaccess public/.htaccess');
//     run('cd {{release_path}} && rm -r config/server/');
// })->onStage('production');
// /***************
//  * Define deploy task
//  */
// task('deploy', [
//     'deploy:prepare',
//     'deploy:release',
//     'rsync:warmup',
//     'rsync',
//     'deploy:shared',
//     'deploy:writable',
//     'typo3',
//     'staging',
//     'production',
//     'deploy:symlink',
//     'cleanup'
// ]);
