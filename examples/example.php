<?php
include('../vendor/autoload.php');

// 直接把帳號從 GET 傳入
$owner = isset($_GET['owner']) ? $_GET['owner'] : '';

// 從 GET 傳入欲抓取最大頁數
$maxPage = isset($_GET['maxCrawlPage']) ? $_GET['maxCrawlPage'] : 1;

// 讀取帳號的資料
$GamerHomeCreation = new wsmwason\gamer\GamerHomeCreation($owner);
$GamerHomeCreation->setOwner($owner);
$GamerHomeCreation->setMaxCrawlPage($maxPage);
$GamerHomeCreation->asXml();
