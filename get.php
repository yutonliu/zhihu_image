<?php

/**
 * @desc : 下载知乎问题的图片到本地
 * @author : mark.liu
 * @date : 2018/07/19
 */

class Downloadzhimg
{

    /**
     * @desc : 下载知乎问题的图片到本地
     * @author : mark.liu
     * @date : 2018/07/19
     */
    public  static function downImg($url_str,$offset=null,$limit=null)
    {

        $url = self::getUrl($url_str,$offset,$limit);

        //get方式请求数据
        $data = self::curlrequest($url);

        //json数据转化为array
        $arrdatas = json_decode($data,true);

        $arrdata = $arrdatas['data'];

        $returndata = [];

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
            preg_match_all('/\< *[img][^\>]*[src] *= *[\"\']{0,1}([^\"\']*)/i',$str,$match);

            $_datas  = $match[1];

            foreach($_datas as $_data)
            {
                if($_data != "origin_image zh-lightbox-thumb" && $_data != "content_image")
                {
                    $imgs[] = $_data;
                }
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
            self::download($img);
            $i++;
            echo "第<$i>张图片下载成功"."\n";
        }


        echo "图片全部下载成功-------图片文件在当前目录good下";

        exit;

    }

    /**
     * @desc : 获取问题回答的地址
     * @author : mark.liu
     * @date : 2018/07/19
     */

    public  static  function getUrl($url_str,$offset=null,$limit=null)
    {
//        //知乎问题地址
//        $url_str = "https://www.zhihu.com/question/31919242";

        $markid = explode('/',$url_str)[4];

        $urldata['include'] = "data[*].is_normal,admin_closed_comment,reward_info,is_collapsed,annotation_action,annotation_detail,collapse_reason,is_sticky,collapsed_by,suggest_edit,comment_count,can_comment,content,editable_content,voteup_count,reshipment_settings,comment_permission,created_time,updated_time,review_info,relevant_info,question,excerpt,relationship.is_authorized,is_author,voting,is_thanked,is_nothelp;data[*].mark_infos[*].url;data[*].author.follower_count,badge[?(type=best_answerer)].topics";

        $urldata['offset'] = empty($offset) || !isset($offset) ? 0 : $offset; //从第几个回答开始  必填参数

        $urldata['limit'] = empty($limit) || !isset($limit) ? 5 : $limit; //截取几个 必填参数

        $utlparam = http_build_query($urldata);

        $url = "https://www.zhihu.com/api/v4/questions/$markid/answers?".$utlparam;

        return $url;
    }


    /**
     * @desc : curl 请求
     * @author : mark.liu
     * @date : 2018/07/19
     */
    public static function curlrequest($url,$post_data=null,$type="get"){
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

    /**
     * @desc : 图片下载到本地
     * @author : mark.liu
     * @date : 2018/07/19
     */
    public static  function download($imgurl)
    {
        $ch = curl_init($imgurl);
        $filename = time().rand(1,100);
        $fp = fopen("good/$filename.jpg", 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }


}

$url = "https://www.zhihu.com/question/31919242";
$offset = 0; //从第几个开始 可为空
$limit = 100; //截取几个 可为空
Downloadzhimg::downImg($url,$offset,$limit);
