<?php
require_once __DIR__."/common.php";
require_once __DIR__.'/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * @desc : 获取url地址并插入队列
 * @author : mark.liu
 * @date : 2018/07/19
 */
function urlData($url_str)
{
	$url = getUrl($url_str);
	//get方式请求数据
	$data = curlrequest($url);

	//json数据转化为array
	$arrdatas = json_decode($data,true);

	//add by lyt 20190419
	$totals = $arrdatas['paging']['totals'];


	$channel = connectRabbmitmq();

	for($i=0;$i<$totals;$i++)
	{
		$request_url = getUrl($url_str,$i);

//		$pheanstalk = new Pheanstalk('192.168.13.157', 11300, 3, false);
//
//		$jobData = $request_url;
//
//		$pheanstalk ->useTube('tubeName') ->put($jobData,1024,5);
		$channel->queue_declare('hello', false, false, false, false);
		$msg = new AMQPMessage($request_url);
		$channel->basic_publish($msg, '', 'hello');
	}

}
/**
 * @desc : 获取url地址
 * @author : mark.liu
 * @date : 2018/07/19
 */
function getUrl($url_str,$offset='')
{
	$markid = explode('/',$url_str)[4];

	$urldata['include'] = "data[*].is_normal,admin_closed_comment,reward_info,is_collapsed,annotation_action,annotation_detail,collapse_reason,is_sticky,collapsed_by,suggest_edit,comment_count,can_comment,content,editable_content,voteup_count,reshipment_settings,comment_permission,created_time,updated_time,review_info,relevant_info,question,excerpt,relationship.is_authorized,is_author,voting,is_thanked,is_nothelp,is_labeled,is_recognized,paid_info;data[*].mark_infos[*].url;data[*].author.follower_count,badge[*].topics";

	$urldata['offset'] = empty($offset) || !isset($offset) ? 0 : $offset; //从第几个回答开始  必填参数

//	$urldata['limit'] = empty($limit) || !isset($limit) ? 5 : $limit; //截取几个 必填参数

//	$urldata['limit'] = $limit;

	$urlparam = http_build_query($urldata);

	$url = "https://www.zhihu.com/api/v4/questions/$markid/answers?".$urlparam;

	return $url;
}

$url = "https://www.zhihu.com/question/31919242";
urlData($url);