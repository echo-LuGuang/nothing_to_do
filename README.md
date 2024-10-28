## nothing to do，没事干，来摸鱼

### 简介

这是一个基于[webman](https://www.workerman.net/webman)的爬虫项目，非常适合php框架的初学者学习，亦可用作毕业设计。

爬取内容：今日头条、微博、虎扑、知乎、B站、天涯等第三方网站的热点事件等，只用作学习。

````
php8.x
mysql
redis
Crontab定时任务
Event事件
GuzzleHttp网络请求
QueryList识别html
api接口
nginx反向代理
GuzzleHttp网络代理请求（翻墙需求）
````

### 安装

```
$ git clone https://github.com/echo-LuGuang/nothing_to_do.git
```

```
$ composer install
```

```
$ copy .env.example .env

修改.env配置

修改 config/redis.php
```

Linux：

```
$ php start.php start
```

Windows：

```
$ windows.bat
```

### 最后

`如果您喜欢这个项目，还请您为它点一个star⭐`