#gamer-to-rss

###關於此程式
巴哈姆特小屋轉換 RSS 格式提供閱讀器讀取用。
小屋其實有很多神人整理很多好東西，但是巴哈又不提供小屋的 RSS，然後我又不想登入…
所以…交給 Google Reader 吧！

###小屋創作RSS
目前提供巴哈小屋的創作提供 RSS 轉換，
必須自行將此程式提供 Google Reader 或其他 RSS 閱讀器使用，
由於只會讀取第一頁，所以過往發表過的創作不會被存取到，有需要可以另外存起來。

###Example
```php
include("GamerHomeCreation.php");

//直接把帳號從 GET 傳入
$owner = isset($_GET['owner']) ? $_GET['owner'] : '';

//讀取帳號的資料
$GamerHomeCreation = new GamerHomeCreation($owner);
$GamerHomeCreation->asXml();
```

###其他說明
日前跟同事提這功能應該要 OpenSource 出來，所以就丟上來了 XD
其實哈拉版的文章有時也會有這樣的需求，日後有空再補吧。