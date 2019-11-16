#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/14
 * Time: 15:29
 */

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use MdDoc\App;

try{
    $application = new Application();
    $application->add(new App());
    $application->run();
}catch (Exception $e) {
    $message = sprintf('%s %d: %s%s', $e->getFile(), $e->getLine(), $e->getMessage(), PHP_EOL);
    echo $message;
    exit(1);
}
