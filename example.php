<?php

include("GamerHomeCreation.php");

//直接把帳號從 GET 傳入
$owner = isset($_GET['owner']) ? $_GET['owner'] : '';

//從GET傳入欲抓取最大頁數
$MaxPageNum = isset($_GET['MaxPageNum']) ? $_GET['MaxPageNum'] : '';

//讀取帳號的資料
$GamerHomeCreation = new GamerHomeCreation($owner);
$GamerHomeCreation->setMaxPageNum($MaxPageNum);
$GamerHomeCreation->asXml();
