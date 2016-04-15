<?php
namespace LuffyZhao\Ebook;

use Exception;

class ebook
{
    /**
     * [$_mapping description]
     * @var array
     */
    private $_mapping = array(
        'www.biquku.com' => 'biquku',
    );
    /**
     * 实例化对象
     * @var string
     */
    private $_instance = '';

    /**
     * 设置解析网站
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T10:27:20+0800
     * @param    [type]                   $url [description]
     */
    public function __construct($url)
    {
        $driverPath = parse_url($url, PHP_URL_HOST);

        if (!isset($this->_mapping[$driverPath])) {
            throw new Exception("还没有[{$driverPath}]网站的配置文件");
        }

        $class = "\\LuffyZhao\\Ebook\\Driver\\" . $this->_mapping[$driverPath];

        if (!class_exists($class)) {
            throw new Exception("没有在[{$class}]网站的配置文件中找到对应的驱动");
        }
        $this->_instance = new $class($url);
    }
    /**
     * 生成epub文件
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-04-15T17:57:07+0800
     * @param    boolean                  $delete [description]
     * @return   [type]                           [description]
     */
    public function bind($delete = false)
    {
        // 获取书籍属性
        $this->_instance->bookProperties();
        // 设置目录
        $this->_instance->createDir();
        // 章节
        $this->_instance->bookChap();
        // 生成epub配置文件
        $this->_instance->bookOther();

    }

    /**
     * 设置临时目录
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-04-15T18:00:49+0800
     * @param    string                   $value [description]
     */
    public function setTmpDir($value = '')
    {
        $this->_instance->setTmpDir($value);
    }

    /**
     * 设置缓存目录
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-04-15T18:00:49+0800
     * @param    string                   $value [description]
     */
    public function setDataDir($value = '')
    {
        $this->_instance->setDataDir($value);
    }
}
