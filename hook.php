<?php
require_once('class.GitHubHook.php');

// Initiate the GitHub Deployment Hook
$hook = new GitHubHook;

// Enable the debug log (sent to error_log)
$hook->enableDebug();

// Adding `stage` branch to deploy for `staging` to path `/var/www/testhook/stage`
$hook->addBranch('stage', 'staging', '/var/www/stage');

// Adding `prod` branch to deploy for `production` to path `/var/www/testhook/prod` limiting to only `user@gmail.com`
$hook->addBranch('prod', 'production', '/var/www/prod', array('user@gmail.com'));

// Deploy the commits
$hook->deploy();
