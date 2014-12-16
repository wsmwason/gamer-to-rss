<?php
/**
 * 巴哈小屋轉換 RSS
 */
class GamerHomeCreation {

	protected $_creation_detail_url = 'http://home.gamer.com.tw/creationDetail.php?sn=';
	protected $_owner;
	protected $_html;
	protected $_entries;
    	protected $_MaxPageNum;	

	function __construct($owner = '')
	{
		if(empty($owner)) die('要提供帳號.');
		$this->_owner = $owner;
	}

	/**
     	* 設定欲抓取的創作列表最大頁數
     	*/
	public function setMaxPageNum($InputPageNum)
	{
        	if(empty($InputPageNum)):
            		$this->_MaxPageNum = 10;
		else:
            		$this->_MaxPageNum = $InputPageNum;
        	endif;
	}
	
	/**
	 * 轉出 XML
	 */
	public function asXml()
	{
		$this->_html = $this->getCreation();
		$this->_entries = $this->parseEntries();
		$this->asRss();
	}

	/**
	 * 取得小屋首頁
	 * @return string
	 */
	public function getCreation()
	{
		$html = file_get_contents('http://home.gamer.com.tw/creation.php?owner='.$this->_owner);
		if(strpos($html, '的創作')===false) die('查無此小屋.');
        	if(preg_match('#<a class="pagenow">.*?(\d+)</a></p>#isu', $html, $matches))
        	for($pageNum = 2; $pageNum <= $matches[1] && $pageNum <= $this->_MaxPageNum; $pageNum++) {
            		$html .= file_get_contents('http://home.gamer.com.tw/creation.php?owner='.$this->_owner.'&page='.$pageNum);
        	}
		return $html;
	}

	/**
	 * 讀取創作文章
	 * @return array
	 */
	private function parseEntries()
	{
		$entries = array();
		if(preg_match_all('#<div class="HOME-mainbox1">.*?<img src="([^<]+)" />.*?<img src="http://i2.bahamut.com.tw/spacer.gif" class="[^"]+" />\n<a class="TS1" href="creationDetail\.php\?sn=(\d+)">([^<]+)</a>.*?作者：<a href="[^"]+">([^<]+)</a>.*?│(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})│贊助：[^<]+│人氣：[^<]+</span><p>(.*?)\(<a href="[^"]+" class="[^"]+">繼續閱讀#isu', $this->_html, $matches)){
			foreach($matches[2] as $i => $id){
				$entries[$id] = array(
					'id' => $id,
					'title' => $matches[3][$i],
					'url' => $this->_creation_detail_url.$id,
					'author' => $matches[4][$i],
					'pubdate' => $matches[5][$i],
                    			'image-url' => $matches[1][$i],
                    			'description' => $matches[6][$i],
				);
			}
		}
		return $entries;
	}

	/**
	 * 輸出 RSS
	 */
	private function asRss()
	{
		$XML = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><rss version="2.0" />');
		$channel = $XML->addChild('channel');
      		$channel->addChild('title', $this->_owner.' 的小屋創作 RSS');
      		$channel->addChild('link', 'http://home.gamer.com.tw/creation.php?owner='.$this->_owner);
      		$channel->addChild('language', 'zh-tw');
      		$channel->addChild('lastBuildDate', date("D, j M Y H:i:s +0800", time()));
      		$channel->addChild('ttl', '20');

        	foreach($this->_entries as $id => $entry){
        		$item = $channel->addChild('item');
        		$item->addChild('title', $entry['title']);
        		$item->addChild('link', $entry['url']);
        		$item->addChild('author', $entry['author']);
        		$item->addChild('description', str_replace('　','',str_replace('<br />','',$entry['description'])));
        		$item->addChild('pubDate', date("D, j M Y H:i:s +0800", strtotime($entry['pubdate'])));
        		$thumbnail = $item->addChild('media:thumbnail','','http://search.yahoo.com/mrss/');
        		$thumbnail->addAttribute('url',$entry['image-url']);
        		$thumbnail->addAttribute('height',180);
        		$thumbnail->addAttribute('width',180);
        		$content = $item->addChild('media:content','','http://search.yahoo.com/mrss/');
        		$content->addAttribute('url',$entry['image-url']);
        		$content->addAttribute('height',180);
        		$content->addAttribute('width',180);
		}

		header('Content-type: text/xml');
    		echo $XML->asXML();
	}

}
