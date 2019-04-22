<?php
require_once __DIR__.'/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
/*
 * @desc 公共方法
 */
/**
 * @desc : curl 请求
 * @author : mark.liu
 * @date : 2018/07/19
 */
function curlrequest($url,$post_data='',$type="get"){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if($type == "post")
	{
		// post数据
		curl_setopt($ch, CURLOPT_POST, 1);
		// post的变量
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	}
	$output = curl_exec($ch);
	curl_close($ch);
	//打印获得的数据
	//print_r($output);
	return $output;
}

function connectRabbmitmq()
{
	//echo 12345;
	$connection = new AMQPStreamConnection('192.168.13.157', 5672, 'guest', 'guest');
	//print_r($connection);
	$channel = $connection->channel();
	return $channel;
}