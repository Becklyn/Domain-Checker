<?php

use Becklyn\DomainChecker\CheckCommand;
use Symfony\Component\Console\Application;

if (is_file($autoloader = __DIR__ . "/vendor/autoload.php"))
{
    require_once $autoloader;
}
else if (is_file($autoloader = dirname(__DIR__, 2) . "/autoload.php"))
{
    require_once $autoloader;
}


$checkCommand = new CheckCommand();
$application = new Application();

$application->add($checkCommand);
$application->setDefaultCommand($checkCommand->getName());
$application->run();
