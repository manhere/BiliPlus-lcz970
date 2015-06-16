<?php setcookie ("visiturl",$_SERVER['REQUEST_URI'],time()+3600*24*7,"/"); ?><?php
/* Require Config Files */
require dirname(dirname(__FILE__)).'/task/config.php';
require dirname(dirname(__FILE__)).'/task/mysql.php';
require dirname(dirname(__FILE__)).'/include/functions.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>BiliPlus - ( ゜- ゜)つロ 乾杯~</title>
<meta name="author" content="Tundra" />
<meta name="Copyright" content="Copyright Tundra All Rights Reserved." />
<meta name="keywords" content="BiliPlus,哔哩哔哩,Bilibili,下载,播放,弹幕,音乐,黑科技,HTML5" />
<meta name="description" content="哔哩哔哩投稿视频、弹幕、音乐下载，更换弹幕播放器，简明现代黑科技 - BiliPlus - ( ゜- ゜)つロ 乾杯~" />
<link rel="icon" href="/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" href="/style/biliplus.css" type="text/css" />
</head>
<body onload="LoadContent()">
<div id="userbar" class="userbar">
<?php
if (empty($_COOKIE['login']))
    {
    echo 'BiliPlus访客系统</div><script language="javascript" type="text/javascript">window.location.href="/api/login.php?act=reg&url='.urlencode($_SERVER['REQUEST_URI']).'"</script></body></html>';
    }
if ($_COOKIE['login']==1)
    {
    echo '<p class="userbarcontent">欢迎，<b>'.$_COOKIE["uname"].'</b> | <b><a href="'.$loginurl.'">连接哔哩哔哩账户</a></b></p>';
    }
if ($_COOKIE['login']==2)
    {
    echo '<p class="userbarcontent">欢迎，<b>'.$_COOKIE["uname"].'</b> | <b><a href="/api/login.php?act=logout">退出哔哩哔哩账户</a></b></p>';
    }
?>
</div>
<?php
include_once (dirname(dirname(__FILE__)).'/html/sidebar.html');
include_once (dirname(dirname(__FILE__)).'/html/announce.html');
/* Core Function For Fetching Data From Bilibili */
function UpdateCache($av,$page,$appkey,$appsecret)
    {
    global $update;
    global $title;
    global $apijson;
    global $mp4json;
    global $mp3file;
    global $cid;
    global $videoxml;
    global $error;
    global $e_text;
    $update = 1;
    if (empty($_COOKIE["access_key"]))
	{
	$error = 1;
	$e_text = '<div class="framesubtitle">无哔哩哔哩账户信息</div><div class="errordescription"><b>服务器需要连接哔哩哔哩账户后获取信息。</b><br/>请点击页面右上方的“连接哔哩哔哩账户”登录哔哩哔哩后访问此页面。</div>Error: [-403] No Access_Key, please login.';
	}
    else
	{
		$id = $_GET["av"];
		    $file = file_exists("../cache/$id.json");
		    if (empty($file))
			{
		$error = 1;
		$e_text = '<div class="framesubtitle">No Cache Found</div><div class="errordescription">未找到分P数据，请先获取分P数据。</div><meta http-equiv="refresh" content="1; url=/api/getaid.php?act=info&av='.$_GET["av"].'" />';
			}
		else
			{
			$apijson = json_decode(file_get_contents("../cache/$id.json"),true);
			$info = $apijson['DATA'];
			$datatime = $apijson['LASTUPDATE'];
	if (!array_key_exists("code",$info))
	    {
	    if (isset($info['list'][($page-1)]['cid']))
		{
			$from = $info['list'][($page-1)]['type'];
			$notsupport = array('td','sohu','pptv','mletv');
			foreach ($notsupport as $checkfrom) {
				if ($from==$checkfrom) 
				$notsupport = 1;
			}
			if ($notsupport==1) {
				$error = 1;
				$e_text = '<div class="framesubtitle">Not Supported Source</div><div class="errordescription">不支持的视频源。</div>';
			} else {
		$videotitle = $info['title'];
		$cid = $info['list'][($page-1)]['cid'];
		//MP4 VIDEO FETCH//
		echo '<script language="JavaScript">document.title = "AV'.$_GET["av"].' - 获取MP4视频地址 - BiliPlus - ( ゜- ゜)つロ 乾杯~";</script>';
		flush();
		$proxy = getproxy();
		/*
		$mp4url = 'http://www.bilibili.com/m/html5?aid='.$av.'&page='.$page;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL,$mp4url);
		curl_setopt($curl, CURLOPT_HEADER,0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION,1);
		$mp4json = curl_exec($curl);
		
		if (stristr($mp4json['src'],'g3.letv.cn'))
		    {
		    $mp4json = json_encode(array('from'=>'html5_high','quality'=>'Original','src'=>$mp4json['src']));
		    }
		else
		    {
		    */
		    //$sign = get_sign(array("otype"=>"xml","type"=>"mp4","ts"=>$timestamp,"platform"=>$platform,"quality"=>"2","appkey"=>$appkey,"cid"=>$cid,"access_key"=>$_COOKIE["access_key"]),$appsecret);
		    $mp4interfaceurl = 'http://interface.bilibili.com/playurl?otype=json&type=mp4&platform=ios&quality=2&appkey='.$appkey.'&cid='.$cid;
		    $curl = curl_init();
		    curl_setopt($curl, CURLOPT_URL,$mp4interfaceurl);
		    curl_setopt($curl, CURLOPT_PROXY, $proxy);
		    curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; MSIE 11.0; Windows NT 6.1; WOW64; Trident/6.0)');
		    curl_setopt($curl, CURLOPT_REFERER,"http://www.bilibili.com/");
		    curl_setopt($curl, CURLOPT_ENCODING, "gzip");
		    curl_setopt($curl, CURLOPT_HEADER,0);
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
		    $mp4interface = curl_exec($curl);
		    curl_close($curl);
		    $mp4interface = json_decode($mp4interface,true);
		    //$mp4interface = json_decode(file_get_contents($mp4interfaceurl),true);
		    if (isset($mp4interface['format']))
			{
			if (stristr($mp4interface['format'],'hd'))
			    {
			    $mp4json = json_encode(array('from'=>'interface_high','quality'=>'High','src'=>$mp4interface['durl']{0}['url']));
			    }
			else
			    {
			    $mp4json = json_encode(array('from'=>'interface_low','quality'=>'Low','src'=>$mp4interface['durl']{0}['url']));
			    }
			}
		    else
			{
			if (isset($mp4interface['from']))
			    {
			    $mp4json = json_encode(array('from'=>$mp4interface['from'],'quality'=>'Auto','src'=>$mp4interface['durl']{0}['url']));
			    }
			else
			    {
			    $mp4json = json_encode(array('from'=>'sina_low','quality'=>'Low','src'=>$mp4interface['durl']{0}['url']));
			    }
			}
		    //}
		//MP4 VIDEO FETCH//
		//SOURCE VIDEO FETCH//
		echo '<script language="JavaScript">document.title = "AV'.$_GET["av"].' - 获取FLV视频地址 - BiliPlus - ( ゜- ゜)つロ 乾杯~";</script>';
		flush();
		//$sign = get_sign(array("otype"=>"xml","type"=>"flv","ts"=>$timestamp,"platform"=>$platform,"quality"=>"3","appkey"=>$appkey,"cid"=>$cid,"access_key"=>$_COOKIE["access_key"]),$appsecret);
		$quality = '4';
		if ($from=='youku')
		{
			$quality = '2';
		}
		$interfaceurl = 'http://interface.bilibili.com/playurl?otype=xml&type=flv&platform=ios&quality='.$quality.'&appkey='.$appkey.'&cid='.$cid;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL,$interfaceurl);
		curl_setopt($curl, CURLOPT_PROXY, $proxy);
		curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; MSIE 11.0; Windows NT 6.1; WOW64; Trident/6.0)');
		curl_setopt($curl, CURLOPT_HEADER,0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
		$videoxml = curl_exec($curl);
		curl_close($curl);
		if (!empty($videoxml)){
		//$videoxml = file_get_contents($interfaceurl);
		//SOURCE VIDEO FETCH//
		$errorcheck = json_decode($videoxml,true);
		if (empty($errorcheck["error_code"]))
		    {
		    $play = simplexml_load_string($videoxml,'SimpleXMLElement',LIBXML_NOCDATA);
		    if (($play->result)=='succ'||($play->result)=='suee')
			{
			$error = 0;
			}
		    else
			{
			if ((string)$play->message=='video is encoding.')
			    {
			    $error = 1;
			    $e_text = '<div class="framesubtitle">视频解析错误：INTERFACE_ERROR</div><div class="errordescription">无法获取视频文件URL。<br/>“视频正在转码中”，哔哩哔哩API服务器如是说道。<br/>此错误与BiliPlus无关，不要问我“为什么主站现在就能看？”，因为我也不知道为什么。<br/>请尝试过一段时间后刷新数据，给您造成的不便请谅解。</div>Error: ['.$play->type.'] '.$play->message;
			    }
			else
			    {
			    $error = 3;
			    $e_text = '<div class="framesubtitle">视频解析错误：BAD_INTERFACE_XML</div><div class="errordescription">无法获取视频文件URL。<br/>可能原因是视频已被删除(视频内容不和谐/UP主自行删除)或API无法解析该投稿(版权番剧可能出现该情况)，具体原因请查看下方错误代码。</div>Error: ['.$play->type.'] '.$play->message;
			    }
			}
		    }
		else
		    {
		    $error = 3;
		    $e_text = '<div class="framesubtitle">视频解析错误：INTERFACE_ERROR</div><div class="errordescription">无法获取视频文件URL。<br/>可能原因是视频已被删除(视频内容不和谐/UP主自行删除)或API无法解析该投稿(版权番剧可能出现该情况)，具体原因请查看下方错误代码。</div>Error: ['.$errorcheck["error_code"].'] '.$errorcheck["error_text"];
		    }
		  } else {
			$error = 3;
			  $e_text = '<div class="framesubtitle">视频解析错误：EMPTY_INTERFACE_XML</div><div class="errordescription">无法获取视频文件URL。<br/>获取视频地址失败，请刷新重试。</div>';
			}
		  
		  }
		}
	    else
		{
		$error = 1;
		$e_text = '<div class="framesubtitle">页面不存在</div><div class="errordescription">AV'.$av.' 没有第 '.$page.' 页！<br/>请确认您输入的AV号及分P页码无误。</div>';
		}
	    }
	else
	    {
	    if ($info["error"]==-403)
		{
		$error = 2;
		$e_text = '<div class="framesubtitle">权限不足</div><div class="errordescription">服务器不允许您访问此投稿。<br/>可能该视频已被删除或该投稿已被屏蔽。<br/>[B站已加强管理力度，请不要尝试解析已被删除的不和谐视频]</div>Error: ['.$info["code"].'] '.$info["error"];
		}
	    else
		{
		$error = 2;
		$e_text = '<div class="framesubtitle">CID解析错误：NO_SUCH_VIDEO</div><div class="errordescription">无法获取视频CID。<br/>可能原因是您输入的AV号不存在或该投稿仅允许会员浏览，具体原因请查看下方错误代码。</div>Error: ['.$info["code"].'] '.$apijson;
		}
	    }
	  }
	}
    }
if (!empty($_GET["update"])&&($_GET["update"]==1))
    {
    if (preg_match("/^[1-9][0-9]*$/",$_GET["av"]))
	{
	if (!empty($_GET["page"]))
	    {
	    if (preg_match("/^[1-9][0-9]*$/",$_GET["page"]))
		{
		$update = 1;
		$page = $_GET["page"];
		}
	    else
		{
		$error = 1;
		$e_text = '<div class="framesubtitle">Bad Request</div><div class="errordescription">参数格式错误，页码请输入纯数字。</div>';
		}
	    }
	$apijson = '';
	$title = 'AV'.$_GET["av"].' - 数据更新';
	UpdateCache($_GET["av"],$page,$appkey,$appsecret);
	}
	else
	{
	$error = 1;
	$e_text = '<div class="framesubtitle">Bad Request</div><div class="errordescription">参数格式错误，AV号请输入纯数字。</div>';
	}
    }
else
    {
    $update = 0;
    if (!empty($_GET["act"]))
	{
	if (empty($_GET["av"])||preg_match("/^[1-9][0-9]*$/",$_GET["av"]))
	    {
	    if (!empty($_GET["page"]))
		{
		if (preg_match("/^[1-9][0-9]*$/",$_GET["page"]))
		    {
		    $page = $_GET["page"];
		    }
		else
		    {
		    $error = '<div class="framesubtitle">Bad Request</div><div class="errordescription">参数格式错误，页码请输入纯数字。</div>';
		    }
		}
	    if ($_GET["act"]=='info')
		{
		$title = 'AV'.$_GET["av"].' - 下载';
		if (!empty($_GET["av"]))
		    {
		    if (!empty($_GET["page"]))
			$page = $_GET["page"];
		    else
			$page = 1;
		    $id = $_GET["av"];
		    $file = file_exists("../cache/$id.json");
		    if (empty($file))
			{
				$update = 1;
			$error = 1;
			$e_text = '<div class="framesubtitle">No Cache Found</div><div class="errordescription">未找到分P数据，请先获取分P数据。</div><meta http-equiv="refresh" content="1; url=/api/getaid.php?act=info&av='.$_GET["av"].'" />';
			}
		    else
			{
			$apijson = json_decode(file_get_contents("../cache/$id.json"),true);
			$info = $apijson['DATA'];
			if (!array_key_exists("code",$info))
			    {
			    if (isset($info['list'][($page-1)]['cid']))
				{
				$videotitle = $info['title'];
				$cid = $info['list'][($page-1)]['cid'];
				if (!empty($info['list'][($page-1)]['vid']))
				    {
				    $vid = $info['list'][($page-1)]['vid'];
				    }
				else
				    {
				    $vid = '不可用';
				    }
				$videoxml = mysql_query("SELECT * FROM CACHE_VIDEO WHERE CID='{$cid}'");
				$videoxml = mysql_fetch_array($videoxml);
				$data = $videoxml['DATA'];
				$errorcheck = json_decode($data,true);
				if (empty($errorcheck["error_code"]))
				    {
				    if (!!simplexml_load_string($data))
					{
				$validts = $videoxml['VALIDTS'];
				if ($validts>time()){
					$play = XML2Array::createArray($data);
					$play = $play['video'];
					if (($play['result'])=='succ'||($play['result'])=='suee')
					    {
					    if (!isset($play['durl'][0]['url']))
						{
						$durldata = $play['durl'];
						$play['durl'] = '';
						$play['durl'][0] = $durldata;
						}
					    $videolengthms = ($play['timelength'])*1.0;
					    $videolength = str_pad(floor($videolengthms/60000),3,"0", STR_PAD_LEFT).':'.sprintf("%02d",round(fmod(($videolengthms/1000),60),3));
					    $part = 0;
					    $video = '';
					    while(!empty($play['durl'][$part]['url']))
						{
						$partlengthms = $play['durl'][$part]['length'];
						$partlength = str_pad(floor($partlengthms/60000),2,"0", STR_PAD_LEFT).':'.sprintf("%02d",round(fmod(($partlengthms/1000),60),3));
						$parturl = $play['durl'][$part]['url'];
						$parturl=str_replace('tss=ios', 'tss=no', $parturl);
						$urls = $urls.urlencode($parturl).'|';
						$video = $video.'<a href="'.$parturl.'" target="_blank" title="[分段'.($part+1).'] 时长：'.$partlength.'"><div class="filelist">[分段'.($part+1).'] '.$partlength.'</div></a>';
						$part++;
						}
					    $urlencode2 = urlencode($apijson['MP3']);
					    $audiomp3 = 'https://'.$_SERVER["HTTP_HOST"].'/url/go.php?url='.$urlencode2;
					    $getmp4 = json_decode($videoxml['MP4'],true);
					    $videomp4 = $getmp4['src'];
					    $videomp4=str_replace('tss=ios', 'tss=no', $videomp4);
					    $pages = $info['pages'];
					    $from_real = $play['from'];
					    $from_src = $info['list'][($page-1)]['type'];
					    if($from_real=='sina') $from_real='新浪视频';
					    if($from_real=='youku') $from_real='优酷视频';
					    if($from_real=='tudou') $from_real='土豆视频';
					    if($from_real=='qq') $from_real='腾讯视频';
					    if($from_real=='local') $from_real='哔哩哔哩';
					    if($from_real=='letv') $from_real='乐视云';
					    if($from_real=='mletv') $from_real='乐视版权';
					    if($from_real=='sohu') $from_real='搜狐版权';
					    if($from_real=='pptv') $from_real='PPTV版权';
					    if($from_real=='iqiyi') $from_real='爱奇艺版权';
					    if($from_real=='vupload') $from_real='哔哩哔哩';
					    if(empty($from_real)) $from_real = '未知';
					    $src = $play['src'];
					    if($src>=400) $from_real='新浪视频';
					    if($from_src=='sina') $from_src='新浪视频';
					    if($from_src=='youku') $from_src='优酷视频';
					    if($from_src=='tudou') $from_src='土豆视频';
					    if($from_src=='qq') $from_src='腾讯视频';
					    if($from_src=='local') $from_src='哔哩哔哩云视频';
					    if($from_src=='letv') $from_src='乐视云视频';
					    if($from_src=='mletv') $from_src='乐视版权';
					    if($from_src=='sohu') $from_src='搜狐版权';
					    if($from_src=='pptv') $from_src='PPTV版权';
					    if($from_src=='iqiyi-fix') $from_src='爱奇艺版权转投';
					    if($from_src=='iqiyi') $from_src='爱奇艺版权';
					    if($from_src=='vupload') $from_src='哔哩哔哩版权';
					    if(empty($from_src)) $from_src = '未知';
					    $datatime = $videoxml['LASTUPDATE'];
					    if (!empty($info['list'][($page-1)]['part']))
					    $parttitle = $info['list'][($page-1)]['part'];
					    else
					    $parttitle = $videotitle;
					    $author = $info['author'];
					    $authorid = $info['mid'];
					    $videoplay = $info['play'];
					    $videodanmu = $info['video_review'];
					    $videoscore = $info['credit'];
					    $videocoin = $info['coins'];
					    $videofavorite = $info['favorites'];
					    $videotime = $info['created_at'];
					    $danmakuxml = 'http://www.bilibilijj.com/ashx/Barrage.ashx?f=true&s=xml&av=&p=&cid='.$cid.'&n='.$parttitle;
					    $danmakuass = 'http://www.bilibilijj.com/ashx/Barrage.ashx?f=true&s=ass&av=&p=&cid='.$cid.'&n='.$parttitle;
					    $title = $videotitle.' - AV'.$_GET["av"].' - 下载';
					    }
					else
					    {
					    if ((string)$play->message=='video is encoding.')
						{
						$error = '<div class="framesubtitle">视频解析错误：API错误</div><div class="errordescription">无法获取视频文件URL。<br/>“视频正在转码中”，哔哩哔哩API服务器如是说道。<br/>此错误与BiliPlus无关，不要问我“为什么主站现在就能看？”，因为我也不知道为什么。<br/>请尝试过一段时间后刷新数据，给您造成的不便请谅解。</div>Error: ['.$play->type.'] '.$play->message;
						}
					    else
						{
						$error = '<div class="framesubtitle">视频解析错误：无效API文档</div><div class="errordescription">无法获取视频文件URL。<br/>可能原因是视频已被删除(视频内容不和谐/UP主自行删除)或API无法解析该投稿(版权番剧可能出现该情况)，具体原因请查看下方错误代码。</div>Error: ['.$play->type.'] '.$play->message;
						}
					    }
					}
				    else
					{
						if (preg_match("/^[1-9][0-9]*$/",$_GET["av"]))
	{
	if (!empty($_GET["page"]))
	    {
	    if (preg_match("/^[1-9][0-9]*$/",$_GET["page"]))
		{
		$update = 1;
		$page = $_GET["page"];
		}
	    else
		{
		$error = 1;
		$e_text = '<div class="framesubtitle">Bad Request</div><div class="errordescription">参数格式错误，页码请输入纯数字。</div>';
		}
	    }
	$apijson = '';
	$title = 'AV'.$_GET["av"].' - 数据更新';
	UpdateCache($_GET["av"],$page,$appkey,$appsecret);
	}
	else
	{
	$error = 1;
	$e_text = '<div class="framesubtitle">Bad Request</div><div class="errordescription">参数格式错误，AV号请输入纯数字。</div>';
	}
					}
					}
				    else
					{
						if (preg_match("/^[1-9][0-9]*$/",$_GET["av"]))
	{
	if (!empty($_GET["page"]))
	    {
	    if (preg_match("/^[1-9][0-9]*$/",$_GET["page"]))
		{
		$update = 1;
		$page = $_GET["page"];
		}
	    else
		{
		$error = 1;
		$e_text = '<div class="framesubtitle">Bad Request</div><div class="errordescription">参数格式错误，页码请输入纯数字。</div>';
		}
	    }
	$apijson = '';
	$title = 'AV'.$_GET["av"].' - 数据更新';
	UpdateCache($_GET["av"],$page,$appkey,$appsecret);
	}
	else
	{
	$error = 1;
	$e_text = '<div class="framesubtitle">Bad Request</div><div class="errordescription">参数格式错误，AV号请输入纯数字。</div>';
	}
					}
				    }
				else
				    {
				    $error = '<div class="framesubtitle">视频解析错误：API错误</div><div class="errordescription">无法获取视频文件URL。<br/>可能原因是视频已被删除(视频内容不和谐/UP主自行删除)或API无法解析该投稿(版权番剧可能出现该情况)，具体原因请查看下方错误代码。</div>Error: ['.$errorcheck["error_code"].'] '.$errorcheck["error_text"];
				    }
				  
				}
			    else
				{
				$error = 'AV'.$_GET["av"].' 没有第 '.$_GET["page"].' 页！<br/>AV'.$_GET["av"].' does not have page '.$_GET["page"].' !';
				}
			    }
			else
			    {
			    if ($info["code"]==-403)
				$error = '<div class="framesubtitle">权限不足</div><div class="errordescription">服务器不允许您访问此投稿。<br/>可能该视频已被删除或该投稿已被屏蔽。<br/>[B站已加强管理力度，请不要尝试解析已被删除的不和谐视频]</div>Error: ['.$info["code"].'] '.$info["error"];
			    else
				$error = '<div class="framesubtitle">CID解析错误：视频不存在</div><div class="errordescription">无法获取视频CID。<br/>可能原因是您输入的AV号不存在或该投稿仅允许会员浏览，具体原因请查看下方错误代码。</div>Error: ['.$info["code"].'] '.$info["error"];
			    }
			}
		    }
		else
		    {
		    $error = '<div class="framesubtitle">Bad Request</div><div class="errordescription">服务器无法识别你的请求，请检查你是否填写了所有必须参数…</div>';
		    }
		}
	    }
	else
	    {
	    $error = '<div class="framesubtitle">Bad Request</div><div class="errordescription">参数格式错误，AV号请输入纯数字。</div>';
	    }
	}
    else
	{
	$error = '<div class="framesubtitle">Bad Request</div><div class="errordescription">参数不足，服务器无法识别你的请求。</div>';
	}
    }
?>
<script language="JavaScript">document.title = "<?php echo $title; ?> - BiliPlus - ( ゜- ゜)つロ 乾杯~";</script>
<?php
if (($update==1)&&($error==0))
echo '<meta http-equiv="refresh" content="1; url='.str_ireplace('&update=1','',$_SERVER['REQUEST_URI']).'" />';
?>
<div id="content" class="content">
<?php
if ($update==1)
    {
    $time = date("Y/m/d H:i:s");
    if ($error==1)
	{
	echo '<br/>
<div class="frametitle">糟糕，出错啦！</div><br/><br/>
<div class="boxtitle-1">详细错误信息</div>
<div class="boxcontent-1">'.$e_text.'</div><br/>
<div class="buttonbackground"><input type="button" class="button" value="返回" onclick="history.go(-1)">　<input type="button" class="button" value="帮助" onclick="window.open(\'/?about\')"></div>
';
	}
    if ($error==2)
	{
	$id = $_GET["av"].'_'.$page;
	$pagedata = mysql_real_escape_string($apijson);
	//mysql_query("INSERT INTO CACHE_PAGE (ID,SUCCESS,DATA,LASTUPDATE) VALUES ('{$id}','2','{$pagedata}','{$time}') ON DUPLICATE KEY UPDATE SUCCESS=VALUES(SUCCESS),DATA=VALUES(DATA),LASTUPDATE=VALUES(LASTUPDATE)");
	echo '<br/>
<div class="frametitle">糟糕，出错啦！</div><br/><br/>
<div class="boxtitle-1">详细错误信息</div>
<div class="boxcontent-1">
<div class="errordescription"><b>无法从哔哩哔哩开放平台获取正确数据</b></div><br/>
'.$e_text.'
</div><br/><br/>
<div class="buttonbackground"><input type="button" class="button" value="返回" onclick="history.go(-1)">　<input type="button" class="button" value="帮助" onclick="window.open(\'/?about\')"></div>
';
	}
    if ($error==3)
	{
	$id = $_GET["av"].'_'.$page;
	$pagedata = mysql_real_escape_string($apijson);
	$pagemp4 = mysql_real_escape_string($mp4json);
	$videodata = mysql_real_escape_string($videoxml);
	//mysql_query("INSERT INTO CACHE_PAGE (ID,SUCCESS,DATA,MP3,MP4,LASTUPDATE) VALUES ('{$id}','1','{$pagedata}','{$mp3file}','{$pagemp4}','{$time}') ON DUPLICATE KEY UPDATE SUCCESS=VALUES(SUCCESS),DATA=VALUES(DATA),LASTUPDATE=VALUES(LASTUPDATE)");
	//mysql_query("INSERT INTO CACHE_VIDEO (CID,SUCCESS,DATA,LASTUPDATE) VALUES ('{$cid}','0','{$videodata}','{$time}') ON DUPLICATE KEY UPDATE SUCCESS=VALUES(SUCCESS),DATA=VALUES(DATA),LASTUPDATE=VALUES(LASTUPDATE)");
	echo '<br/>
<div class="frametitle">糟糕，出错啦！</div><br/><br/>
<div class="boxtitle-1">详细错误信息</div>
<div class="boxcontent-1">
<div class="errordescription"><b>无法从哔哩哔哩开放平台获取正确数据</b></div><br/>
'.$e_text.'
</div><br/><br/>
<div class="buttonbackground"><input type="button" class="button" value="返回" onclick="history.go(-1)">　<input type="button" class="button" value="刷新" onclick="document.location.reload()">　<input type="button" class="button" value="帮助" onclick="window.open(\'/?about\')"></div>
';
	}
    if ($error==0)
	{
	$id = $_GET["av"].'_'.$page;
	$pagedata = mysql_real_escape_string($apijson);
	$pagemp4 = mysql_real_escape_string($mp4json);
	$videodata = mysql_real_escape_string($videoxml);
	$validts = (time()+43200);
	//mysql_query("INSERT INTO CACHE_PAGE (ID,SUCCESS,DATA,MP3,MP4,LASTUPDATE) VALUES ('{$id}','1','{$pagedata}','{$mp3file}','','{$time}') ON DUPLICATE KEY UPDATE SUCCESS=VALUES(SUCCESS),DATA=VALUES(DATA),LASTUPDATE=VALUES(LASTUPDATE)");
	mysql_query("INSERT INTO CACHE_VIDEO (CID,SUCCESS,DATA,LASTUPDATE,MP4,VALIDTS) VALUES ('{$cid}','1','{$videodata}','{$time}','{$pagemp4}','{$validts}') ON DUPLICATE KEY UPDATE SUCCESS=VALUES(SUCCESS),DATA=VALUES(DATA),LASTUPDATE=VALUES(LASTUPDATE),MP4=VALUES(MP4),VALIDTS=VALUES(VALIDTS)");
	echo '<br/>
<div class="frametitle">数据获取成功</div><br/><br/>
<div class="boxtitle-1">请稍后</div>
<div class="boxcontent-1">
<div class="errordescription"><b>成功从哔哩哔哩开放平台获取数据</b></div><br/>
正在刷新页面，请稍候...<br/><br/>
<div class="buttonbackground"><input type="button" class="button" value="刷新" onclick="document.location.reload()">　<input type="button" class="button" value="帮助" onclick="window.open(\'/?about\')"></div><script language="javascript" type="text/javascript">function Refresh(){window.location.href="/api/do.php?act=info&av='.$_GET["av"].'&page='.$page.'"}</script>
';
	}
    /*
    if ($refresh==1)
	{
	echo '<script language="javascript" type="text/javascript">function Refresh(){window.location.href="/api/do.php?act=info&av='.$_GET["av"].'&page='.$page.'"}</script>';
	}
	*/
    }
if ($update==0)
    {
    if (!empty($error))
	{
	echo '<style type="text/css">
    a{text-decoration:none}
    a:link{color:#FFFFFF}
    a:visited{color:#FFFFFF}
    a:hover{color:#CDCDCD}
    a:active{color:#3388FF}
    div.title{font-family:"Microsoft YaHei";font-size:24px;font-weight:bold}
    div.subtitle{font-family:"Verdana";font-size:18px;font-weight:bold}
    div.boxtitle-1{margin:0px;padding:6px;width:200px;height:28px;color:#FFFFFF;text-align:center;font-family:"Microsoft YaHei";font-size:18px;font-weight:bold;background-color:#006EDC}
    div.boxcontent-1{margin:0px;padding:4px 0px 0px 0px;height:150px;border-top:6px solid #1E90FF;clear:both}
    div.boxtitle-2{margin:0px;padding:6px;width:200px;height:28px;color:#FFFFFF;text-align:center;font-family:"Microsoft YaHei";font-size:18px;font-weight:bold;background-color:#E9006D}
    div.boxcontent-2{margin:0px;padding:4px 0px 0px 0px;height:150px;border-top:6px solid #FF4DA0;clear:both}
    div.listbox{margin:8px 8px 8px 8px;padding:2px;width:240px;height:125px;font-family:"Microsoft YaHei";box-shadow:0px 0px 3px 3px #888888;float:left}
    div.listboxtitle{margin:0px;padding:0px 0px 4px 0px;font-family:"Microsoft YaHei";font-size:15px;font-weight:bold;box-shadow:0px -4px 0px #FF4DA0 inset;white-space:nowrap;text-overflow:ellipsis;overflow:hidden}
    div.listboxtitle:hover{text-overflow:inherit;overflow:visible}
    div.listboxbutton{margin:0px 0px 4px 2px;padding:10px 0px 0px 0px;width:108px;height:28px;color:#FFFFFF;text-align:center;font-family:"Microsoft YaHei";font-size:14px;font-weight:bold;background-color:#1FAAFF}
    div.listboxcontent{height:95px;font-family:"Microsoft YaHei";font-size:12px;background-color:#DDDDDD;overflow:auto}
    div.errordescription{padding:8px;font-family:"Microsoft YaHei";font-size:14px;background-color:#DDDDDD}
    div.footer{margin:2px;padding:4px;font-family:"Microsoft YaHei";font-size:14px;font-color:#FFFFFF;text-align:center;background-color:#999999}
    div.update{margin:0px;padding:2px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:14px;font-weight:bold;background-color:#1E90FF;display:inline-block}
</style>
<br/>
<div class="frametitle">糟糕，出错啦！</div><br/><br/>
<div class="boxtitle-1">详细错误信息</div>
<div class="boxcontent-1">
'.$error.'<br/>
</div>
<div class="boxtitle-2">推荐解决方法</div>
<div class="boxcontent-2">
<div class="errordescription"><b>如果您是在首次打开本页面时遇到错误，请尝试点击右侧按钮 <div class="update"><a href="'.$_SERVER['REQUEST_URI'].'&update=1">刷新数据</a></div><br/>如果刷新数据后仍然遇到错误，请查看上方的详细错误信息，如有任何问题欢迎向我们反馈，或前往<div class="update"><a href="http://www.bilibili.com/video/av'.$_GET['av'].'/index_'.$page.'.html" target="_blank">bilibili主站</a></div>观看</b></div>
';
	echo '</div><br/>
<div class="footer">数据更新时间：'.$datatime.' <div class="update"><a href="'.$_SERVER['REQUEST_URI'].'&update=1">刷新数据</a></div></div>
<br/>';
	}
    else
	{
	if ($_GET["act"]=='info')
	    {
	    echo '<div class="videotitle">AV'.$_GET["av"].' - '.$videotitle.'</div>
<table width="100%" border="0" cellpadding="4px">
    <tr>
	<td width="80%" style="text-align:left;color:#FFFFFF;font-family:SimHei;font-size:16px;font-weight:bold;background-color:#666666">[P'.$page.'] '.$parttitle.'</td>
	<td width="20%" style="text-align:center;color:#FFFFFF;font-family:SimHei;font-size:16px;font-weight:bold;background-color:#666666">UP主：<a href="http://space.bilibili.com/'.$authorid.'" target="_blank">'.$author.'</a></td>
    </tr>
</table>
<div class="videotime">投稿时间：'.$videotime.'</div><div class="docinfo">播放:'.$videoplay.' | 弹幕:'.$videodanmu.' | 评分:'.$videoscore.' | 硬币:'.$videocoin.' | 收藏:'.$videofavorite.'</div><br/>
<div><br/></div><div class="infolist"><a href=/api/getaid.php?act=info&av='.$_GET["av"].'>返回详细分P列表</a></div>
<div><br/></div><div class="hrdescription">视频信息</div><hr><div class="videoinfo"><div class="infolist">CID：'.$cid.'</div><div class="infolist">投稿视频源：'.$from_src.'</div><div class="infolist">VID：'.$vid.'</div><div class="infolist">当前视频源：'.$from_real.'</div><div class="infolist">视频分段：共'.$part.'段</div></div>
<div><br/></div><div class="hrdescription">视频源文件（如链接失效请刷新缓存数据）</div><hr><div class="videofile"><div class="infolist">视频总长：'.$videolength.'</div><br/><fieldset><legend>源视频文件</legend><form name="input" action="/api/urloutput.php" method="post" style="margin:0px;display: inline" target="_blank"><input type="hidden" name="urls" value="'.$urls.'"><div class="tip">请按顺序下载所有分段获得完整视频 <input type="image" src="/image/button.png" style="color:#EEEEEE;"></div>'.$video.'</fieldset></form><fieldset><legend>合并+转码MP4</legend><div class="tip">视频质量可能有轻微下降，部分投稿无法提供MP4下载</div>';
	    if (empty($getmp4['src']))
		echo '<div class="filelist_empty">未找到MP4视频文件...</div>';
	    else
		{
		if (stristr($getmp4['from'],'letv_high'))
			echo '<a href="'.$videomp4.'" target="_blank" title="下载合并分段原画质MP4格式视频文件"><div class="filelist">MP4视频下载 [高清]</div></a><div class="infolist">来源:乐视云转码</div>';
		if (stristr($getmp4['from'],'letv'))
			echo '<a href="'.$videomp4.'" target="_blank" title="下载合并分段原画质MP4格式视频文件"><div class="filelist">MP4视频下载 [高清]</div></a><div class="infolist">来源:乐视云转码</div>';
		if (stristr($getmp4['from'],'interface_high'))
			echo '<a href="'.$videomp4.'" target="_blank" title="下载合并分段高清MP4格式视频文件"><div class="filelist">MP4视频下载 [高清]</div></a><div class="infolist">来源:哔哩哔哩云转码</div>';
		if (stristr($getmp4['from'],'interface_auto'))
			echo '<a href="'.$videomp4.'" target="_blank" title="下载合并分段标清MP4格式视频文件"><div class="filelist">MP4视频下载 [低清]</div></a><div class="infolist">来源:哔哩哔哩云转码</div>';
		if (stristr($getmp4['from'],'interface_low'))
			echo '<a href="'.$videomp4.'" target="_blank" title="下载合并分段低清MP4格式视频文件"><div class="filelist">MP4视频下载 [低清]</div></a><div class="infolist">来源:哔哩哔哩云转码</div>';
		if (stristr($getmp4['from'],'sina_low'))
			echo '<a href="'.$videomp4.'" target="_blank" title="下载合并分段低清MP4格式视频文件"><div class="filelist">MP4视频下载 [低清]</div></a><div class="infolist">来源:新浪视频</div>';
		if (stristr($getmp4['from'],'sina'))
			echo '<a href="'.$videomp4.'" target="_blank" title="下载合并分段低清MP4格式视频文件"><div class="filelist">MP4视频下载 [低清]</div></a><div class="infolist">来源:新浪视频</div>';
		if (stristr($getmp4['from'],'youku'))
			echo '<div class="filelist_empty">未找到MP4视频文件...</div><a href="'.$videomp4.'" target="_blank" title="优酷高清m3u8播放列表，可用工具合成MP4"><div class="filelist">M3U8播放列表(手机可直接播放)</div></a><div class="infolist">来源:优酷视频</div>';
		if (stristr($getmp4['from'],'qq'))
			echo '<a href="'.$videomp4.'" target="_blank" title="下载合并分段高清MP4格式视频文件"><div class="filelist">MP4视频下载 [标清]</div></a><div class="infolist">来源:腾讯视频</div>';
		}
	    echo '</fieldset></div><div><br/></div>
<div class="hrdescription">弹幕下载</div><hr><div class="danmufile"><div class="tip">如果浏览器直接打开了文件，请右键链接→选择“另存为”</div><a href="'.$danmakuxml.'" target="_blank" title="下载XML格式弹幕文件（哔哩哔哩原始弹幕文件）"><div class="filelist">XML格式弹幕</div></a><a href="'.$danmakuass.'" target="_blank" title="下载ASS格式弹幕文件（适用于本地播放器）"><div class="filelist">ASS格式弹幕</div></a></div><br/>
<div class="hrdescription">在线播放</div><hr><div class="playlink"><div class="tip">请选择播放器在线播放弹幕视频</div><a href="/api/do.php?act=play&av='.$_GET['av'].'&page='.$page.'&player=bilibili" title="使用哔哩哔哩弹幕播放器播放此视频"><div class="filelist">BiliPlayer</div></a><a href="/api/do.php?act=play&av='.$_GET['av'].'&page='.$page.'&player=html5" title="使用HTML5播放器播放此视频"><div class="filelist">HTML5</div></a><a href="http://www.bilibili.com/video/av'.$_GET['av'].'/index_'.$page.'.html" title="主站地址跳转" target="_blank"><div class="filelist">B站</div></a><a href="bilibili://?av='.$_GET['av'].'" title="手机客户端跳转"><div class="filelist">手机客户端</div></a></div><br/>
<div class="footer">本页面为缓存数据，数据缓存时间：'.$datatime.' <div class="update"><a href="'.$_SERVER['REQUEST_URI'].'&update=1">刷新数据</a></div></div>
<br/>';
	    }
	}
    }
?>
</div>
<script type="text/javascript">
function LoadContent(){document.getElementById("loading").style.display="none";document.getElementById("content").style.display="block";}
function OpenURL(){document.getElementById("loading").style.display="block";document.getElementById("content").style.display="none";}
document.oncontextmenu=new Function("event.returnValue=false;");
document.onselectstart=new Function("event.returnValue=false;");
</script>
</body>
</html>

