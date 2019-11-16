<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/15
 * Time: 9:13
 */

require __DIR__.'/vendor/autoload.php';
use MdDoc\Builder;

(new \Symfony\Component\Filesystem\Filesystem())->mkdir(__DIR__.'/test/doc');
$b = new Builder("./test", "./test/doc", "README.md");
$b->run();