<?php
namespace LuffyZhao\Ebook;

use Exception;
use ZipArchive;
use \HtmlParser\ParserDom;

/**
 * EPUB 生成类
 */
abstract class Epub
{
    /*
     * Book's Properties
     */
    protected $bookProperties = array(
        'title'       => '',
        'author'      => '',
        'language'    => 'zh-CN',
        'id'          => '',
        'css'         => 'body.chapter h1{text-align:center;}h3{text-align:center;}body.chapter p{text-align:justify;}',
        'timestamp'   => '',
        'subject'     => '',
        'description' => '',
        'image'       => '',
    );

    /**
     * 当前生成章节
     * @var integer
     */
    protected $chapterKey = 0;

    /**
     * 章节目录
     * @var array
     */
    protected $chapterList = array();

    /*
     * Folder Structure
     */
    protected $templatesDir = '';
    protected $tmpDir       = '';
    protected $dataDir      = '';

    protected $filename = null; // generated automatically later on.

    public function __construct()
    {
        $this->bookProperties['timestamp'] = explode("+", date("c"))[0] . "Z";
        $this->bookProperties['id']        = mt_rand(1000000000, 9999999999);
    }

    /**
     * 设置 bookProperties 里的数据
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T16:52:50+0800
     * @return   [type]                   [description]
     */
    abstract public function bookProperties();

    /**
     * 章节生成
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T16:52:37+0800
     * @return   [type]                   [description]
     */
    abstract public function bookChap();

    /**
     * [setDataDir description]
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-04-15T18:01:48+0800
     * @param    [type]                   $dataDir [description]
     */
    public function setDataDir($dataDir)
    {
        $this->dataDir = $dataDir;
    }
    /**
     * [setTmpDir description]
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-04-15T18:02:06+0800
     * @param    [type]                   $tmpDir [description]
     */
    public function setTmpDir($tmpDir)
    {
        $this->dataDir = $tmpDir;
    }
    /**
     * 设置标题
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T11:08:30+0800
     */
    public function setTitle($title)
    {
        $this->bookProperties['title'] = $title;

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $title = iconv('UTF-8', 'GB18030', $title);
        }
    }

    /**
     * 设置 主题词或关键词
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T11:19:16+0800
     * @param    [type]                   $subject [description]
     */
    public function setSubject($subject)
    {
        $this->bookProperties['subject'] = $subject;
    }

    /**
     * 设置内容描述
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T11:20:23+0800
     * @param    [type]                   $description [description]
     */
    public function setDescription($description)
    {
        $this->bookProperties['description'] = $description;
    }

    /**
     * 设置css
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T16:05:45+0800
     * @param    [type]                   $filename [description]
     */
    public function setCss($filename)
    {
        if (file_exists($filename)) {
            $this->bookProperties['css'] = file_get_contents($filename);
        }

    }

    /**
     * [setImage description]
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T16:46:40+0800
     * @param    [type]                   $filename [description]
     */
    public function setImage($filename)
    {
        if (file_exists($filename)) {
            $this->bookProperties['image'] = $filename;
        }
    }
    /**
     * 生成目录
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T14:06:42+0800
     * @param    string                   $value [description]
     * @return   [type]                          [description]
     */
    public function createDir()
    {
        if ($this->templatesDir == '') {
            $this->templatesDir = $this->dirPath(dirname(__DIR__) . '/templates_new/');
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $filename = iconv('UTF-8', 'GB18030', $this->bookProperties['title']);
        } else {
            $filename = $this->bookProperties['title'];
        }

        if ($this->tmpDir == '') {
            $this->tmpDir = $this->dirPath(dirname(__DIR__) . '/tmp/' . $filename . '/');
        }

        $this->filename = $filename . '.epub';

        if (!file_exists($this->tmpDir . 'OEBPS/')) {
            mkdir($this->tmpDir . 'OEBPS/', 0777, true);
            mkdir($this->tmpDir . 'OEBPS/Styles/', 0777, true);
            mkdir($this->tmpDir . 'OEBPS/Images/', 0777, true);
        }

        if (!file_exists($this->tmpDir . 'META-INF/')) {
            mkdir($this->tmpDir . 'META-INF/', 0777, true);
        }

        file_put_contents($this->tmpDir . 'mimetype', 'application/epub+zip');
        file_put_contents($this->tmpDir . 'OEBPS/Styles/stylesheet.css', $this->bookProperties['css']);

        if ($this->bookProperties['image'] != '' && file_exists($this->bookProperties['image'])) {
            if (!copy($this->bookProperties['image'], $this->tmpDir . 'OEBPS/Images/cover.jpg')) {
                throw new Exception("添加封面失败！");
            } else {
                $this->bookProperties['image'] = '';
            }
        }
    }

    /**
     * 生成epub配置文件
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T14:24:29+0800
     * @return   [type]                   [description]
     */
    public function bookOther()
    {
        $this->createFile('content.opf.php', 'OEBPS/content.opf');
        $this->createFile('toc.ncx.php', 'OEBPS/toc.ncx');
        $this->createFile('toc.xhtml.php', 'OEBPS/toc.xhtml');
        $this->createFile('title_page.xhtml.php', 'OEBPS/title_page.xhtml');
        $this->createFile('container.xml.php', 'META-INF/container.xml');

        $fileLists = $this->dirList($this->tmpDir);

        $this->createZip($fileLists, true);

    }

    /**
     * 生成Chapter
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T16:47:06+0800
     * @param    [type]                   $params array(
     *                                                 'title' => ''
     *                                                 'content' => ''
     *                                                   )
     */
    public function addChapter($params)
    {
        $name = 'OEBPS/chap' . $this->chapterKey . '.xhtml';

        $centont = $this->createFile('chapter.xhtml.php', $name, $params);

        $this->chapterList[$this->chapterKey] = $params['title'];
        $this->chapterKey++;
    }

    /**
     * 创建Epub
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T14:47:55+0800
     * @param    array                    $files     [description]
     * @param    boolean                  $overwrite [description]
     * @return   [type]                              [description]
     */
    protected function createZip($files = array(), $overwrite = false)
    {

        $destination = $this->tmpDir . '../' . $this->filename;

        if (file_exists($destination) && !$overwrite) {
            return false;
        }

        $valid_files = array();

        if (is_array($files)) {
            foreach ($files as $file) {
                if (file_exists($this->tmpDir . $file)) {
                    $valid_files[] = $file;
                }
            }
        }

        if (count($valid_files)) {
            $zip = new ZipArchive();
            if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                return false;
            }

            foreach ($valid_files as $file) {
                $zip->addFile($this->tmpDir . $file, $file);
            }
            $zip->close();

            return file_exists($destination);
        } else {
            return false;
        }
    }
    /**
     * 设置作者
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T11:08:34+0800
     */
    protected function setAuthor($author)
    {
        $this->bookProperties['author'] = $author;
    }

    /**
     * [gbk_to_utf8 description]
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T11:24:27+0800
     * @param    [type]                   $str [description]
     * @return   [type]                        [description]
     */
    protected function convert($str, $to = 'utf-8')
    {
        if ($to == 'utf-8') {
            return $str;
        }
        return mb_convert_encoding($str, 'utf-8', $to);
    }

    /**
     * 生成文件
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T13:50:29+0800
     * @param    [type]                   $params   模板所需参数
     * @param    [type]                   $template 模板文件地址
     * @return   [type]                             [description]
     */
    protected function createFile($template, $filename, $params = array())
    {
        if (is_array($params) && !empty($params)) {
            extract($params);
        }

        $filename = $this->tmpDir . $filename;
        ob_start();
        require $this->templatesDir . $template;
        $output = ob_get_contents();
        ob_end_clean();
        file_put_contents($filename, $output);
    }

    /**
     * 列出目录下所有文件
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T14:40:30+0800
     * @param    [type]                   $path 路径
     * @param    string                   $exts 扩展名
     * @param    array                    $list 增加的文件列表
     * @return   [type]                         [description]
     */
    protected function dirList($path, $list = array())
    {
        $path  = $this->dirPath($path);
        $files = glob($path . '*');
        foreach ($files as $v) {
            if (is_dir($v)) {
                $list = $this->dirList($v, $list);
            } else {
                $list[] = str_replace($this->tmpDir, '', $v);
            }
        }
        return $list;
    }

    /**
    转化 \ 为 /
     *
    @param  string  $path   路径
    @return string  路径
     */
    protected function dirPath($path)
    {
        $path = str_replace('\\', '/', $path);
        if (substr($path, -1) != '/') {
            $path = $path . '/';
        }

        return $path;
    }

    /**
     * Url内容是否缓存
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T17:00:11+0800
     * @param    [type]                   $url [description]
     * @return   boolean                       [description]
     */
    protected function isCache($key, $value = false)
    {
        $cacheKey = md5($key);
        if ($this->dataDir == '') {
            $cachePath = dirname(__DIR__) . '/data/' . substr($cacheKey, 0, 3) . '/';
        } else {
            $cachePath = $this->dataDir;
        }

        if (!file_exists($cachePath)) {
            mkdir($cachePath, 0777, true);
        }
        $filename = $cachePath . $cacheKey . '.cache';
        if (!file_exists($filename) && $value === false) {
            return false;
        } elseif (!file_exists($filename) && $value === '') {
            return true;
        } elseif (!file_exists($filename) && $value) {
            file_put_contents($filename, $value);
        } elseif (file_exists($filename) && $value === '') {
            unlink($filename);
            return true;
        } else {
            return file_get_contents($filename);
        }

    }

    /**
     * 获取远程html文件
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T17:09:59+0800
     * @param    string                   $value [description]
     * @return   [type]                          [description]
     */
    protected function load($url = '')
    {
        $html = $this->isCache($url);

        if ($html == false) {
            $html = $this->load_file($url);
            $this->isCache($url, $html);
        }

        return new ParserDom($html);
    }

    /**
     * 获取文件 本地&远程
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-04-15T15:21:35+0800
     * @param    [type]                   $url [description]
     * @return   [type]                        [description]
     */
    protected function load_file($url)
    {
        $html = file_get_contents($url);
        if (isset($this->charset)) {
            $html = $this->convert($html, $this->charset);
        }

        return $html;
    }

}
