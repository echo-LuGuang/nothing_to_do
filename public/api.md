# nothing_to_do API接口

````
免费提供api接口供前端初学者调用。

接口只适用于学习、娱乐，请勿线上环境使用，不保证随时可以调用成功。
````

### 接口host

`https://hot.nnbp.cc/api/v1`

### code状态码

`200`响应正常

#### 列表接口

接口地址：`/index`

请求方式：`get`

响应内容：

````text
{
  "code": 200,
  "data": [
    {
      "type": "zhihu",   <--详情接口需要用到type参数
      "name": "知乎热榜",
      "color": "#0177D7",
      "time": "5分钟前",
      "list": [
        {
          "title":"美国俄亥俄州氯乙烯泄露，居民紧急撤离，记者直播报道时被捕，具体情况如何？氯乙烯泄露有多严重？",
          "url":"https:\/\/www.zhihu.com\/question\/583774398",
          "subtitle":13088673,
          "type":"zhihu"
        },
        ...
      ]
    },
    ...
  ]
}
````

#### 详情接口

接口地址：`/detail/:type`

请求方式：`get`

响应内容：

````text
{
    "code":200,
    "data":{
        "list":[
            {
                "title":"爸爸因姜姓难取名给孩子叫去寒",
                "url":"https:\/\/s.weibo.com\/weibo?q=%23%E7%88%B8%E7%88%B8%E5%9B%A0%E5%A7%9C%E5%A7%93%E9%9A%BE%E5%8F%96%E5%90%8D%E7%BB%99%E5%AD%A9%E5%AD%90%E5%8F%AB%E5%8E%BB%E5%AF%92%23&t=31&band_rank=1&Refer=top",
                "subtitle":"2712111",
                "type":"weibo"
            },
            ...
        ],
        "time": "5分钟前"
    }
}
````

示例:

````
请求百度热搜：GET `https://hot.nnbp.cc/api/v1/detail/baidu`
````