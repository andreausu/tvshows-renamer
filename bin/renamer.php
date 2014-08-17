#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;

$argv[1] = 'renamer';

$application = new Application();
$application->add(new \Usu\TvShowsRenamer\Command\RenamerCommand());
$application->run();