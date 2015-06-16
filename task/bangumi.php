<?php
/* Get API Request Sign */
function get_sign($params, $key)
    {
    $_data = array();
    ksort($params);
    reset($params);
    foreach ($params as $k => $v)
        {
        $_data[] = $k . '=' . rawurlencode($v);
        }
    $_sign = implode('&', $_data);
    return strtolower(md5($_sign.$key));
    }
/* Get API Request Sign */
include_once dirname(__FILE__).'/mysql.php';
echo 'EXECUTING...<br/>';
echo '--------------------<br/>';
mysql_query("DELETE FROM BANGUMI");
echo 'CLEARED BANGUMI CACHE<br/>';
echo '--------------------<br/>';
$sign = get_sign(array("appkey"=>"21087a09e533a072","type"=>"json","tid"=>"13","page"=>"1","pagesize"=>"16","days"=>"7","ver"=>"2","order"=>"promote","access_key"=>"237ecfe4b4795a715ea320acda31015a"),'e5b8ba95cab6104100be35739304c23a');
$json2 = file_get_contents('http://api.bilibili.com/list?appkey=21087a09e533a072&type=json&tid=13&page=1&pagesize=16&days=7&ver=2&order=promote&access_key=237ecfe4b4795a715ea320acda31015a&sign='.$sign);
$return2 = json_decode($json2,true);
$number2 = 0;
while (isset($return2["list"][$number2]["aid"]))
    {
    $id2 = $number2+1;
    mysql_query("UPDATE LIST_BANGUMI SET TITLE ='{$return2["list"][$number2]["title"]}' WHERE ID='{$id2}'");
    mysql_query("UPDATE LIST_BANGUMI SET AID ='{$return2["list"][$number2]["aid"]}' WHERE ID='{$id2}'");
    mysql_query("UPDATE LIST_BANGUMI SET PIC ='{$return2["list"][$number2]["pic"]}' WHERE ID='{$id2}'");
    mysql_query("UPDATE LIST_BANGUMI SET INFO ='{$return2["list"][$number2]["description"]}' WHERE ID='{$id2}'");
    mysql_query("UPDATE LIST_BANGUMI SET PLAY ='{$return2["list"][$number2]["play"]}' WHERE ID='{$id2}'");
    mysql_query("UPDATE LIST_BANGUMI SET DANMU ='{$return2["list"][$number2]["video_review"]}' WHERE ID='{$id2}'");
    mysql_query("UPDATE LIST_BANGUMI SET FAVOURITE ='{$return2["list"][$number2]["favorites"]}' WHERE ID='{$id2}'");
    $number2 = $number2+1;
    }
echo 'CACHED LIST_BANGUMI,TOTAL:'.$id2.'<br/>';
echo '--------------------<br/>';
$sign = get_sign(array("type"=>"json","btype"=>"2","appkey"=>"21087a09e533a072","access_key"=>"237ecfe4b4795a715ea320acda31015a"),'e5b8ba95cab6104100be35739304c23a');
$return9 = json_decode(file_get_contents('http://api.bilibili.com/bangumi?type=json&btype=2&appkey=21087a09e533a072&access_key=237ecfe4b4795a715ea320acda31015a&sign='.$sign),true);
$number9 = 0;
while (isset($return9['list'][$number9]['spid']))
    {
    mysql_query("INSERT INTO BANGUMI (TITLE,TYPE,SPID,COVER,WEEKDAY,COUNT,CLICK,ATTENTION,LASTUPDATE) VALUES ('{$return9["list"][$number9]["title"]}','2','{$return9["list"][$number9]["spid"]}','{$return9["list"][$number9]["cover"]}','{$return9["list"][$number9]["weekday"]}','{$return9["list"][$number9]["bgmcount"]}','{$return9["list"][$number9]["click"]}','{$return9["list"][$number9]["attention"]}','{$return9["list"][$number9]["lastupdate_at"]}')");
    $number9 = $number9+1;
    }
echo 'CACHED BANGUMI_2,TOTAL:'.($number9+1).'<br/>';
echo '--------------------<br/>';
$sign = get_sign(array("type"=>"json","btype"=>"3","appkey"=>"21087a09e533a072","access_key"=>"237ecfe4b4795a715ea320acda31015a"),'e5b8ba95cab6104100be35739304c23a');
$return9 = json_decode(file_get_contents('http://api.bilibili.com/bangumi?type=json&btype=3&appkey=21087a09e533a072&access_key=237ecfe4b4795a715ea320acda31015a&sign='.$sign),true);
$number9 = 0;
while (isset($return9['list'][$number9]['spid']))
    {
    mysql_query("INSERT INTO BANGUMI (TITLE,TYPE,SPID,COVER,WEEKDAY,COUNT,CLICK,ATTENTION,LASTUPDATE) VALUES ('{$return9["list"][$number9]["title"]}','3','{$return9["list"][$number9]["spid"]}','{$return9["list"][$number9]["cover"]}','{$return9["list"][$number9]["weekday"]}','{$return9["list"][$number9]["bgmcount"]}','{$return9["list"][$number9]["click"]}','{$return9["list"][$number9]["attention"]}','{$return9["list"][$number9]["lastupdate_at"]}')");
    $number9 = $number9+1;
    }
echo 'CACHED BANGUMI_3,TOTAL:'.($number9+1).'<br/>';
echo '--------------------<br/>';

echo 'SUCCESS!';
echo '<meta http-equiv="refresh" content="1; url=/?bangumi" />';