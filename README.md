# zhihu_image

### 执行文件方法 
```
$url = "https://www.zhihu.com/question/31919242"; //知乎问题的url地址
$offset = 0; //从第几个开始 可为空
$limit = 100; //截取几个 可为空
Downloadzhimg::downImg($url,$offset,$limit);
```