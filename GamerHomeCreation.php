<?php
/**
 * 巴哈小屋轉換 RSS
 */
class GamerHomeCreation {

	protected $_creation_detail_url = 'http://home.gamer.com.tw/creationDetail.php?sn=';
	protected $_owner;
	protected $_html;
	protected $_entries;

	function __construct($owner = '')
	{
		if(empty($owner)) die('要提供帳號.');
		$this->_owner = $owner;
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
		return $html;
	}

	/**
	 * 讀取創作文章
	 * @return array
	 */
	private function parseEntries()
	{
		$entries = array();
		if(preg_match_all('#<a class="TS1" href="creationDetail\.php\?sn=(\d+)">([^<]+)</a>.*?作者：<a href="[^"]+">([^<]+)</a>.*?│(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})│#isu', $this->_html, $matches)){
			foreach($matches[1] as $i => $id){
				$entries[$id] = array(
					'id' => $id,
					'title' => $matches[2][$i],
					'url' => $this->_creation_detail_url.$id,
					'author' => $matches[3][$i],
					'pubdate' => $matches[4][$i],
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
				$item->addChild('description', $this->_owner.' 的小屋創作: '.$id);
				$item->addChild('pubDate', date("D, j M Y H:i:s +0800", strtotime($entry['pubdate'])));
		}

		header('Content-type: text/xml');
    echo $XML->asXML();
	}

}
