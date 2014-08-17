#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;

array_splice($argv, 1, 0, "rename");

$application = new Application();
$command = new \Usu\TvShowsRenamer\Command\RenamerCommand();
$application->add($command);
$application->run(new \Symfony\Component\Console\Input\ArgvInput($argv));