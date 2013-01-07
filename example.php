<?php

include("GamerHomeCreation.php");

//直接把帳號從 GET 傳入
$owner = isset($_GET['owner']) ? $_GET['owner'] : '';

//讀取帳號的資料
$GamerHomeCreation = new GamerHomeCreation($owner);
$GamerHomeCreation->asXml();