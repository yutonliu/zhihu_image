<?php
require_once __DIR__."/common.php";
require_once __DIR__.'/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

///**
//* 实例化beanstalk
//* 参数依次为：ip地址 端口号默认11300 连接超时时间 是否长连接
//*/
//$pheanstalk = new Pheanstalk('192.168.13.157', 11300, 3, false);
//
////监听tubeName管道，忽略default管道
//$job = $pheanstalk ->watch('tubeName') ->ignore('default') ->reserve();
////$pheanstalk->delete($job); exit;
//
//$url = $job->getData();
//
//$flag = deal($url);
//
//if($flag)
//{
//	$pheanstalk->delete($job);
//}

consumer();

function consumer()
{
	$channel = connectRabbmitmq();

	$channel->queue_declare('hello', false, false, false, false);

	echo " [*] Waiting for messages. To exit press CTRL+C\n";

	$callback = function ($msg) {
//		echo ' [x] Received ', $msg->body, "\n";
		$url = $msg->body;
		echo '这个下载地址为--- '.$url."\n\n".'---- 开始下载：'."\n\n";
		deal($url);
	};

	$channel->basic_consume('hello', '', false, true, false, false, $callback);

	while (count($channel->callbacks)) {
		$channel->wait();
	}
}

function deal($url)
{
	$data = curlrequest($url);
	//json数据转化为array
	$arrdatas = json_decode($data,true);

	$arrdata = $arrdatas['data'];

	//获取回答内容
	foreach($arrdata as $v)
	{
		$vals = $v['content'];
		$datas[] = $vals;
	}

	//正则匹配出每个回答内容里的图片
	$imgs = [];
	foreach($datas as $str)
	{
		//modified by lyt 20190419
		$picDatas = img_match($str);
		foreach($picDatas as $picData)
		{
			$imgs[] = $picData['src'];
		}
	}

	if(count($imgs) == 0 || empty($imgs))
	{
		echo "当前问题回答列表中无图片，请更换页数或者换其他问题";
		exit;
	}

	echo "共".count($imgs)."张图片"."#### 开始下载: ----"."\n";

	$i = 0;
	foreach($imgs as $img)
	{
		download($img);
		$i++;
		echo "第<$i>张图片下载成功"."\n";
	}

	echo "当前页码图片全部下载成功-------图片文件在当前目录good下"."\n\n";

	return true;
}

/**
 * @desc : 图片下载到本地
 * @author : mark.liu
 * @date : 2018/07/19
 */
function download($imgurl)
{
	$ch = curl_init($imgurl);
	$filename = time().rand(1,100);
	$fp = fopen(__DIR__."/../good/$filename.jpg", 'wb');
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_exec($ch);
	curl_close($ch);
	fclose($fp);
}

/**
 * $str，要进行处理的内容
 * $ext，要匹配的扩展名
 */
function img_match($str,$ext='jpg|jpeg|gif|bmp|png'){
	$list = array();
	//先取出所有img标签文本
	$c1 = preg_match_all('/<img\s.*?>/', $str, $m1);
	//对所有的img标签进行取属性
	for($i=0; $i<$c1; $i++){
		//匹配出所有的属性
		$c2 = preg_match_all('/(\w+)\s*=\s*(?:(?:(["\'])(.*?)(?=\2))|([^\/\s]*))/', $m1[0][$i], $m2);
		//将匹配完的结果进行结构重组
		for($j=0; $j<$c2; $j++) {
			$list[$i][$m2[1][$j]] = !empty($m2[4][$j]) ? $m2[4][$j] : $m2[3][$j];
		}
	}
	return $list;
}