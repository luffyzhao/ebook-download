<?php
require '../vendor/autoload.php';

use LuffyZhao\Ebook\ebook;
try {

    $a = new ebook('http://www.biquku.com/1/1766/');
    //设置临时文件目录
    // $a->setTmpDir();
    //设置缓存目录
    // $a->setDataDir();
    $a->bind();

} catch (\Exception $e) {
    echo 'Caught exception: ', $e->getMessage(), "\n";
    exit;
}
