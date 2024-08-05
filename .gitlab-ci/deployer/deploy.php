<?php

namespace Deployer;

require_once 'recipe/common.php';
require_once 'contrib/rsync.php';

// Import necessary host configuration
import(__DIR__ . '/hosts.yml');

// Set executing binaries
set('bin/php', function () {
    return '/usr/bin/php8.3-cli';
});

// Number of releases to keep
set('keep_releases', 2);

// Define shared directories
set('shared_dirs', [
    'private/fileadmin',
    'private/uploads',
    'private/typo3temp/assets/_processed_',
    'private/typo3temp/assets/compressed',
    'private/typo3temp/assets/images',
    'var'
]);

// Define shared files
set('shared_files', [
    '.env',
    'private/typo3conf/LocalConfiguration.php'
]);

// Define writable directories
set('writable_dirs', [
    'public/typo3temp'
]);

// Set the source path for rsync
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

// General task to execute after rsync
task('general', function () {
    run('mv {{release_path}}/config/server/{{alias}}/_.htaccess {{release_path}}/public/.htaccess');
    run('rm -r {{release_path}}/config/server/');
})
->desc('Prepare release');

// Add general task to execute after rsync
after('rsync', 'general');

// Optional task to execute before general for staging
task('staging', function () {
    run('mv {{release_path}}/config/server/{{alias}}/_.htpasswd {{release_path}}/public/.htpasswd');
    writeln('Moved .htpasswd file to {{release_path}}/public');
})
->desc('Prepare staging')
->select('stage=staging');

// Add staging task to execute before general
before('general', 'staging');

// Automatically unlock if deploy fails.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
task('database:migrate', function () {
    run('{{bin/php}} {{release_path}}/vendor/bin/typo3cms database:updateschema');
})
->desc('Migrate database');

// Add database migration task before symlink new release
before('deploy:symlink', 'database:migrate');

// Clear cache after symlink new release
task('typo3:cache:clear', function () {
    run('{{bin/php}} {{release_path}}/vendor/bin/typo3cms cache:flush');
})
->desc('Clear TYPO3 Cache');

// Clear cache after symlink new release
after('deploy:symlink', 'typo3:cache:clear');

// Deploy additional configuration manually
task('deploy:config', function () {
    runLocally('rsync -az {{rsync_src}}private/typo3conf/AdditionalConfiguration.php {{remote_user}}@{{hostname}}:{{deploy_path}}/shared/private/typo3conf/AdditionalConfiguration.php');
    writeln('Moved AdditionalConfiguration to {{alias}}/shared/private/typo3conf');
});

// The deploy task
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
])
->desc('Deploy project');
