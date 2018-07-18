<?php

    /**
     * @desc : curl post请求
     * @author : zhq
     * @date : 2017/03/01
     */
    function curlrequest($url,$post_data=null,$type="get"){
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

   $offset = 0;
   $limit = 100;
//   $url = "https://www.zhihu.com/api/v4/questions/66313867/answers?include=data%5B%2A%5D.is_normal%2Cadmin_closed_comment%2Creward_info%2Cis_collapsed%2Cannotation_action%2Cannotation_detail%2Ccollapse_reason%2Cis_sticky%2Ccollapsed_by%2Csuggest_edit%2Ccomment_count%2Ccan_comment%2Ccontent%2Ceditable_content%2Cvoteup_count%2Creshipment_settings%2Ccomment_permission%2Ccreated_time%2Cupdated_time%2Creview_info%2Crelevant_info%2Cquestion%2Cexcerpt%2Crelationship.is_authorized%2Cis_author%2Cvoting%2Cis_thanked%2Cis_nothelp%3Bdata%5B%2A%5D.mark_infos%5B%2A%5D.url%3Bdata%5B%2A%5D.author.follower_count%2Cbadge%5B%3F%28type%3Dbest_answerer%29%5D.topics&limit=$limit&offset=$offset&sort_by=default";

//    $url = "https://www.zhihu.com/api/v4/questions/29173647/answers?include=data%5B*%5D.is_normal%2Cadmin_closed_comment%2Creward_info%2Cis_collapsed%2Cannotation_action%2Cannotation_detail%2Ccollapse_reason%2Cis_sticky%2Ccollapsed_by%2Csuggest_edit%2Ccomment_count%2Ccan_comment%2Ccontent%2Ceditable_content%2Cvoteup_count%2Creshipment_settings%2Ccomment_permission%2Ccreated_time%2Cupdated_time%2Creview_info%2Crelevant_info%2Cquestion%2Cexcerpt%2Crelationship.is_authorized%2Cis_author%2Cvoting%2Cis_thanked%2Cis_nothelp%3Bdata%5B*%5D.mark_infos%5B*%5D.url%3Bdata%5B*%5D.author.follower_count%2Cbadge%5B%3F(type%3Dbest_answerer)%5D.topics&offset=&limit=$limit&sort_by=default";

//   $url = "https://www.zhihu.com/api/v4/questions/43551423/answers?include=data%5B%2A%5D.is_normal%2Cadmin_closed_comment%2Creward_info%2Cis_collapsed%2Cannotation_action%2Cannotation_detail%2Ccollapse_reason%2Cis_sticky%2Ccollapsed_by%2Csuggest_edit%2Ccomment_count%2Ccan_comment%2Ccontent%2Ceditable_content%2Cvoteup_count%2Creshipment_settings%2Ccomment_permission%2Ccreated_time%2Cupdated_time%2Creview_info%2Crelevant_info%2Cquestion%2Cexcerpt%2Crelationship.is_authorized%2Cis_author%2Cvoting%2Cis_thanked%2Cis_nothelp%3Bdata%5B%2A%5D.mark_infos%5B%2A%5D.url%3Bdata%5B%2A%5D.author.follower_count%2Cbadge%5B%3F%28type%3Dbest_answerer%29%5D.topics&limit=$limit&offset=$offset&sort_by=default";

   $data = curlrequest($url);
   
//   file_put_contents('./data.json',$data);
 
   #data = file_get_contents('./data.json');
   $arrdatas = json_decode($data,true);
   
   $arrdata = $arrdatas['data'];
   
   $returndata = [];

   foreach($arrdata as $v)
   {
      $vals = $v['content'];
      $datas[] = $vals;
   }

   foreach($datas as $str)
   {
//       $str = $datas[0];

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


    function download($imgurl)
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

    $i = 0;
    echo "共".count($imgs)."张图片"."#### 开始下载: ----"."\n";
    foreach($imgs as $img)
    {
        download($img);
        $i++;
        echo "第<$i>张图片下载成功"."\n";
    }
//    var_dump($imgs);

