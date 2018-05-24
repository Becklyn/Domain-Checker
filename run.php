<?php

use Becklyn\DomainChecker\CheckCommand;
use Symfony\Component\Console\Application;


require_once __DIR__ . '/vendor/autoload.php';

$checkCommand = new CheckCommand();
$application = new Application();

$application->add($checkCommand);
$application->setDefaultCommand($checkCommand->getName());
$application->run();
