<?php
require dirname(dirname(__FILE__)).'/include/functions.php';
$id = $_GET["id"];
$sign = get_sign(array("type"=>"json","appkey"=>'27eb53fc9058f8c3',"id"=>$id,"batch"=>"1","access_key"=>'45e1c24ae50d12c1d3f74a9b19d701ab'),'c2ed53a74eeefe3cf99fbd01d8c9c375');
$apiurl = 'http://api.bilibili.com/view?type=json&appkey=27eb53fc9058f8c3&id='.$id.'&batch=1&access_key=45e1c24ae50d12c1d3f74a9b19d701ab&sign='.$sign;
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL,$apiurl);
curl_setopt($curl, CURLOPT_ENCODING, "gzip");
curl_setopt($curl, CURLOPT_HEADER,0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
$apijson = curl_exec($curl);
curl_close($curl);
$info = json_decode($apijson,true);
if (!array_key_exists("code",$info))
{
	if (!empty($info['list'][0]['cid']))
	{
		$filedata = json_encode(array("ID"=>$id,"SUCCESS"=>"1","DATA"=>json_decode($apijson,true),LASTUPDATE=>date("Y/m/d H:i:s")));
		$file=fopen("../cache/$id.json","w");
		fwrite($file,$filedata);
		fclose($file);
	}
} else {
	$error=1;
	$file = file_exists("../cache/$id.json");
	if (!empty($file)) {
		$error=0;
		$apijson = json_decode(file_get_contents("../cache/$id.json"),true);
		$apijson = json_encode($apijson['DATA']);
	}
}

if (!empty($_GET['page'])){
	$info = json_decode($apijson,true);
	$page = ($_GET['page']-1);
	if (!empty($info['list'][$page]['cid'])){
		$info['cid'] = $info['list'][$page]['cid'];
		$info['partname'] = $info['list'][$page]['part'];
		unset($info['list']);
		$apijson=json_encode($info);
	} else {
		$error=1;
		$apijson = '{"code":-404,"error":"no such page"}';
	}
}
if ($_GET['type']!='xml'){
	$info=json_decode($apijson,true);
	if($error!=1){
	$switch=array('typename','title','description','tag','author');
	foreach($switch as $val)
	$info[$val]=urlencode($info[$val]);
	if(isset($info['list'])){
	foreach($info['list'] as $no=>$arr)
	$info['list'][$no]['part']=urlencode($info['list'][$no]['part']);}}
	header("Content-Type: text/plain; charset=utf-8");
	echo urldecode(json_encode($info));
}
else {
	$apijson=str_replace('&','&amp;',$apijson);
	$apijson=str_replace('<','&lt;',$apijson);
	$apijson=str_replace('>','&gt;',$apijson);
	$info=json_decode($apijson,true);
	$xml='<?xml version="1.0" encoding="UTF-8"?>
<info>
';
	foreach($info as $key=>$val){
		if ($key!='list')
		$xml=$xml.'  <'.$key.'>'.$val.'</'.$key.'>
';
		else {
			$xml=$xml.'  <list>
';
			foreach ($val as $page){
				$xml=$xml.'    <data>
      <page>'.$page['page'].'</page>
      <type>'.$page['type'].'</type>
      <part>'.$page['part'].'</part>
      <cid>'.$page['cid'].'</cid>
      <vid>'.$page['vid'].'</vid>
    </data>
';
			}
			$xml=$xml.'  </list>
';
		}
	}
	$xml=$xml.'</info>';
	header("Content-Type: text/xml; charset=utf-8");
	echo $xml;
}