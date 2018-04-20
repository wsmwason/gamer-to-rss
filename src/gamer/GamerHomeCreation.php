<?php
namespace wsmwason\gamer;

use DiDom\Document;

class GamerHomeCreation {

    /**
     * 小屋帳號
     * @var string
     */
    protected $owner;

    /**
     * 頁面 HTML
     * @var DiDom\Document[]
     */
    protected $html = array();

    /**
     * 文章
     * @var array
     */
    protected $entries = array();

    /**
     * 欲抓取的創作列表最大頁數
     * @var int
     */
    protected $maxCrawlPage = 1;

    /**
     * 創作最多有幾頁
     * @var int
     */
    protected $maxPage = 1;

    /**
     * @param string $owner
     */
    public function __construct($owner = '')
    {
        $this->setOwner($owner);
    }

    /**
     * 取得小屋帳號
     *
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * 設定小屋帳號
     *
     * @param string $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * 取得最大頁數
     *
     * @return int
     */
    public function getMaxPage()
    {
        return $this->maxPage;
    }

    /**
     * 設定最大頁數
     *
     * @param int $maxPage
     */
    public function setMaxPage($maxPage)
    {
        $this->maxPage = $maxPage;
    }

    /**
     * 取得最大頁數
     *
     * @return int
     */
    public function getMaxCrawlPage()
    {
        return $this->maxCrawlPage;
    }

    /**
     * 設定最大抓取頁數
     *
     * @param int $maxCrawlPage
     */
    public function setMaxCrawlPage($maxCrawlPage)
    {
        $this->maxCrawlPage = $maxCrawlPage;
    }

    /**
     * 抓資料
     */
    public function parseUrl()
    {
        for ($i=1; $i<=$this->maxCrawlPage; $i++) {
            $url = 'http://home.gamer.com.tw/creation.php?owner=' . $this->owner . '&page=' . $i;
            $document = new Document($url, true);
            $this->html[$i] = $document;

            // 找看總共有幾頁
            if ($i==1) {
                if (count($pages = $document->find('#BH-pagebtn > p > a')) != 0) {
                    $this->maxPage = (int) $pages[count($pages)-1]->text();
                }
            }

            // 抓取頁碼超過總頁碼則不再抓取
            if ($i > $this->maxPage) {
                break;
            }
        }
    }

    /**
     * 取得文章內容
     */
    private function parseEntries()
    {
        foreach ($this->html as $htmlDoc) {
            if (count($entriesDom = $htmlDoc->find('div.HOME-mainbox1')) != 0) {
                foreach ($entriesDom as $entryDom) {
                    $entry = array();

                    // title, link
                    if (count($title = $entryDom->find('h1')) != 0) {
                        $entry['title'] = trim(html_entity_decode($title[0]->text()));
                        $entry['link'] = 'http://home.gamer.com.tw/' . $title[0]->find('a')[0]->attr('href');
                    }

                    // image
                    if (count($image = $entryDom->find('img')) != 0) {
                        $entry['image'] = $image[0]->attr('src');
                    }

                    // description
                    if (count($description = $entryDom->find('p')) != 0) {
                        $entry['description'] = trim(html_entity_decode($description[1]->text()));
                    }

                    // author, pubDate
                    if (count($author = $entryDom->find('span.ST1')) != 0) {
                        $entry['author'] = trim(html_entity_decode($author[0]->find('a')[0]->text()));
                        $entry['pubDate'] = explode('│', $author[0]->text())[1];
                    }

                    if (!empty($entry)) {
                        $this->entries[] = $entry;
                    }
                }
            }
        }
    }

    /**
     * 產生 RSS
     */
    protected function asRss()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><rss version="2.0" />');
        $channel = $xml->addChild('channel');
            $channel->addChild('title', htmlspecialchars($this->owner) . ' 的小屋創作 RSS');
            $channel->addChild('link', 'http://home.gamer.com.tw/creation.php?owner='.$this->owner);
            $channel->addChild('language', 'zh-tw');
            $channel->addChild('lastBuildDate', date("D, j M Y H:i:s +0800", time()));
            $channel->addChild('ttl', '20');

        foreach ($this->entries as $id => $entry) {
            $item = $channel->addChild('item');
                $item->addChild('title', $entry['title']);
                $item->addChild('link', $entry['link']);
                $item->addChild('author', $entry['author']);
                $description = $entry['description'];
                if (!empty($entry['image'])) {
                    $description = '<img src="' . $entry['image'] . '"><br>' . $description;
                }
                $item->addChild('description', $description);
                $item->addChild('pubDate', date("D, j M Y H:i:s +0800", strtotime($entry['pubDate'])));
        }

        header('Content-type: text/xml');
        echo $xml->asXML();
    }

    /**
     * 輸出 RSS
     */
    public function asXml()
    {
        $this->parseUrl();
        $this->parseEntries();
        $this->asRss();
    }

}
