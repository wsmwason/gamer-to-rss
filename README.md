# gamer-to-rss

巴哈姆特小屋轉換 RSS

### 關於此程式
巴哈姆特小屋轉換 RSS 格式提供閱讀器讀取用。
小屋其實有很多神人整理很多好東西，但是巴哈又不提供小屋的 RSS，然後我又不想登入…
所以…交給 Feedly 吧！

### 小屋創作 RSS
目前提供巴哈小屋的創作提供 RSS 轉換，
必須自行將此程式提供 Feedly  或其他 RSS 閱讀器使用。
或是可以方便定期追蹤特定的小屋自己設定通知提醒用。

### 透過 Composer 安裝

	$ composer require wsmwason/gamer-to-rss

### Example
```php
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
```

### License

The MIT License (MIT)