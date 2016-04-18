<?php
namespace LuffyZhao\Ebook\Driver;

use \LuffyZhao\Ebook\Epub;

class biquku extends Epub
{
    /**
     * 内容章节拼接地址
     * @var string
     */
    private $_host = '';

    /**
     * 书籍介绍页
     * @var string
     */
    private $_url = '';

    /**
     * 书籍介绍页ob
     * @var string
     */
    private $_dom = '';

    /**
     * 获取属性的正则
     * @var array
     */
    private $propertiesPECL = array(
        "setAuthor"      => 'og:novel:author',
        "setTitle"       => 'og:novel:book_name',
        "setSubject"     => 'og:novel:category',
        "setDescription" => 'og:description',
    );

    public function __construct($url)
    {
        parent::__construct();
        $this->_url = $this->_host = $url;
    }

    /**
     * 书籍属性
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T11:04:18+0800
     * @return   [type]                   [description]
     */
    public function bookProperties()
    {
        $this->_dom = $dom = $this->load($this->_url);
        foreach ($dom->find('meta') as $value) {
            if (($function = array_search($value->getAttr('property'), $this->propertiesPECL)) !== false) {
                $content = $value->getAttr('content');
                $this->$function($content);
            }
        }
    }

    /**
     * 章节生成
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-03-09T11:38:31+0800
     * @return   [type]                   [description]
     */
    public function bookChap()
    {
        $list = $this->_dom->find('div#list', 0);
        if ($list) {
            foreach ($list->find('a') as $value) {
                $chapter            = array();
                $chapter['content'] = $this->getContent($this->_host . $value->href);
                $chapter['title']   = $value->innerHtml();
                $this->addChapter($chapter);
            }
        } else {
            throw new Exception("在打扫房间", 1);
        }
    }

    protected function getContent($url)
    {
        $dom = $this->load($url);

        $content = $dom->find('#content', 0)->innerHtml();

        $content = str_replace('&nbsp;', '', $content);
        $content = str_replace('<br />', '</p><p>', $content);
        $content = str_replace('<br/>', '</p><p>', $content);
        $content = str_replace('<br>', '</p><p>', $content);
        $content = str_replace('<p></p>', '', $content);
        $content = "<p>{$content}</p>";
        return $content;
    }
}
