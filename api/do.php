<?php setcookie ("visiturl",$_SERVER['REQUEST_URI'],time()+3600*24*7,"/"); ?>
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
<style type="text/css">
a{text-decoration:none}
#userbar a:link{color:#FFFFFF}
#userbar a:visited{color:#FFFFFF}
#userbar a:hover{color:#CDCDCD}
#userbar a:active{color:#3388FF}
html,body{margin:0px;width:100%;height:100%;font-size:16px;cursor:default}
div.frametitle{font-family:"Microsoft YaHei";font-size:24px;font-weight:bold}
div.framesubtitle{font-family:"Verdana";font-size:16px;font-weight:bold}
div.framedescription{font-family:"Microsoft YaHei";font-size:16px}
div.sidebar{margin:0px;width:150px;height:100%;background-color:#3388FF;box-shadow:5px 0px 0px #858585;position:fixed;top:0px;left:0px}
div.sidebar-title{margin:0px 0px 6px 0px;width:150px;height:75px;background-color:#3388FF;text-align:center;font-family:"Verdana";font-size:50px;font-weight:bold;color:white}
div.sidebar-list{margin:6px 0px 0px 0px;padding:10px 0px 10px 0px;width:150px;text-align:center;font-family:"Microsoft YaHei",SimHei;font-size:20px;font-weight:bold;color:white;box-shadow:0px -6px 0px #FFFFFF}
div.sidebar-about{margin:6px 0px 0px 0px;padding:10px 0px 10px 0px;width:150px;background-color:#3388FF;text-align:center;font-family:"Verdana";font-size:12px;color:white;box-shadow:0px -6px 0px #FFFFFF}
div.sidebar-list-block{margin:0px;padding:10px 0px 10px 0px;width:100%}
div.sidebar div:hover{background:#858585}
div.userbar{margin:0 0 6px 0;padding:0px;width:100%;color:#FFFFFF;text-align:right;font-size:14px;font-family:"Microsoft YaHei";background-color:#D04D74;float:right}
p.userbarcontent{margin:8px}
div.loading{margin:10px 0px 10px 0px;padding:0px;width:100%;height:100%;text-align:center;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:18px;font-weight:bold;background-color:#999999;float:left;display:block;z-index:9}
div.content{margin:0px;padding:0px 0px 0px 160px;width:100%;box-sizing:border-box;float:left;display:block;z-index:1}
input.button{margin:6px 0 6px 0;width:100px;font-family:"Microsoft YaHei";font-size:14px;font-weight:bold}
</style>
</head>
<body onload="LoadContent()">
<div id="userbar" class="userbar">
<?php
require dirname(dirname(__FILE__)).'/task/config.php';
require dirname(dirname(__FILE__)).'/task/mysql.php';
require dirname(dirname(__FILE__)).'/include/functions.php';
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
		echo '<script language="JavaScript">document.title = "AV'.$_GET["av"].' - 获取API信息 - BiliPlus - ( ゜- ゜)つロ 乾杯~";</script>';
		flush();
		//$timestamp = time();
		//$headers['CLIENT-IP'] = '58.32.100.0';
		//$headers['X-FORWARDED-FOR'] = '58.32.100.0';
		$headers['Accept-Encoding'] = 'gzip,deflate';
		$headers['User-Agent'] = 'BiliPlus/2.0.0 (tundrawork@gmail.com)';
		$headers['Referer'] = 'http://www.bilibili.com';
		$headerArr = array();
		foreach($headers as $n=>$v){$headerArr[] = $n.':'.$v;}
		$sign = get_sign(array("type"=>"json","appkey"=>$appkey,"id"=>$av,"page"=>$page,"batch"=>"1","platform"=>$platform,"access_key"=>$_COOKIE["access_key"]),$appsecret);
		$apiurl = 'http://api.bilibili.com/view?type=json&appkey='.$appkey.'&id='.$av.'&page='.$page.'&batch=1&platform='.$platform.'&access_key='.$_COOKIE["access_key"].'&sign='.$sign;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL,$apiurl);
	curl_setopt($curl, CURLOPT_HEADER,0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
	//curl_setopt($curl, CURLOPT_FOLLOWLOCATION,1);
	$apijson = curl_exec($curl);
	curl_close($curl);
	$info = json_decode($apijson,true);
	if (!array_key_exists("code",$info))
	    {
	    if (isset($info['list'][($page-1)]['cid']))
		{
		$videotitle = $info['title'];
		$cid = $info['list'][($page-1)]['cid'];
		$from = $info['list'][($page-1)]['type'];
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
		    $mp4interfaceurl = 'http://interface.bilibili.com/playurl?otype=json&type=mp4&platform='.$platform.'&quality=2&appkey='.$appkey.'&cid='.$cid;
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
		$interfaceurl = 'http://interface.bilibili.com/playurl?otype=xml&type=flv&platform='.$platform.'&quality='.$quality.'&appkey='.$appkey.'&cid='.$cid;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL,$interfaceurl);
		curl_setopt($curl, CURLOPT_PROXY, $proxy);
		curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; MSIE 11.0; Windows NT 6.1; WOW64; Trident/6.0)');
		curl_setopt($curl, CURLOPT_ENCODING, "gzip");
		curl_setopt($curl, CURLOPT_HEADER,0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
		$videoxml = curl_exec($curl);
		curl_close($curl);
		//$videoxml = file_get_contents($interfaceurl);
		//SOURCE VIDEO FETCH//
		$errorcheck = json_decode($videoxml,true);
		if (empty($errorcheck["error_code"]))
		    {
		    $play = simplexml_load_string($videoxml,'SimpleXMLElement',LIBXML_NOCDATA);
		    //$play = json_decode($videoxml);
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
			UpdateCache($_GET["av"],$page,$appkey,$appsecret);
			}
		    else
			{
			$apijson = json_decode(file_get_contents("../cache/$id.json"),true);
			$datatime = $apijson['LASTUPDATE'];
			$info = $apijson['DATA'];
			if (!array_key_exists("code",$info))
			    {
			    if (isset($info['list'][($page-1)]['cid']))
				{
				$videotitle = $info['title'];
				$cid = $info['list'][($page-1)]['cid'];
				if (isset($info['list'][($page-1)]['vid']))
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
					$play = XML2Array::createArray($data);
					$play = $play['video'];
					if (($play['result'])=='succ'||($play['result'])=='suee')
					    {
					    $p = 0;
					    $pagelist = '';
					    while (isset($info['list'][$p]['cid']))
						{
						$pagelist = $pagelist.'<a href="/api/do.php?act=info&av='.$_GET["av"].'&page='.$info['list'][$p]['page'].'"><div class="listbox">[P'.($p+1).'] '.$info['list'][$p]['part'].'</div></a>';
						$p++;
						}
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
						$video = $video.'<a href="'.$parturl.'" target="_blank" title="[分段'.($part+1).'] 时长：'.$partlength.'"><div class="filelist">[分段'.($part+1).'] '.$partlength.'</div></a>';
						$part++;
						}
					    $urlencode2 = urlencode($apijson['MP3']);
					    $audiomp3 = 'https://'.$_SERVER["HTTP_HOST"].'/url/go.php?url='.$urlencode2;
					    $getmp4 = json_decode($apijson['MP4'],true);
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
					    if($from_real=='iqiyi-fix') $from_real='爱奇艺版权转投';
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
					    if($from_src=='iqiyi') $from_src='爱奇艺版权';
					    if($from_src=='vupload') $from_src='哔哩哔哩版权';
					    if(empty($from_src)) $from_src = '未知';
					    $parttitle = $info['list'][($page-1)]['part'];
					    $author = $info['author'];
					    $authorid = $info['mid'];
					    $videoplay = $info['play'];
					    $videodanmu = $info['video_review'];
					    $videoscore = $info['credit'];
					    $videocoin = $info['coins'];
					    $videofavorite = $info['favorites'];
					    $videotime = $info['created_at'];
					    $danmakuxml = 'http://www.bilibilijj.com/ashx/Barrage.ashx?f=true&s=xml&av=&p=&cid='.$cid.'&n='.$videotitle;
					    $danmakuass = 'http://www.bilibilijj.com/ashx/Barrage.ashx?f=true&s=ass&av=&p=&cid='.$cid.'&n='.$videotitle;
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
					$error = '<div class="framesubtitle">视频解析错误：网络错误</div><div class="errordescription">服务器无法获取正确的视频接口数据格式。<br/>可能原因是服务器与哔哩哔哩接口的网络连接不畅或网络不稳定，请尝试刷新数据，具体原因请查看下方错误代码。</div>Error: [-400] bad xml document';
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
	    if ($_GET["act"]=='play')
		{
		$title = 'AV'.$_GET["av"].' - 播放';
		if ((!empty($_GET["av"]))&&(!empty($_GET["player"])))
		    {
		    if (!empty($_GET["page"]))
			$page = $_GET["page"];
		    else
			$page = 1;
		    $id = $_GET["av"];
		    $file = file_exists("../cache/$id.json");
		    if (empty($file))
			{
			UpdateCache($_GET["av"],$page,$appkey,$appsecret);
			}
		    else
			{
			$apijson = json_decode(file_get_contents("../cache/$id.json"),true);
			$datatime = $apijson['LASTUPDATE'];
			$return = $apijson['DATA'];
			if (!array_key_exists("error",$return))
			    {
			    $videotitle = $return['title'];
			    if (isset($return['list'][($page-1)]['cid']))
				{
				$cid = $return['list'][($page-1)]['cid'];
				foreach($return['list'] as $i=>$useless){$info['list'][$i]=urlencode($return['list'][$i]['part']);}
				$pagesarrayjson = urldecode(json_encode($info['list']));
				/*
				$p = 0;
				$pagelist = '';
				while (isset($return['list'][$p]['cid']))
				{
				$pagelist = $pagelist.'<a href="/api/do.php?act=play&av='.$_GET["av"].'&page='.($p+1).'&player='.$_GET["player"].'"><div class="listbox">[P'.($p+1).'] '.$return['list'][$p]['part'].'</div></a>';
				$p++;
				}
				*/
				if ($_GET["player"]=='mukio')
				    {
				    $mp4 = json_decode($apijson['MP4'],true);
				    $mp4video = urlencode($mp4['src']);
				    /* MukioPlayer has been canceled in my plan, you can use another player here instead. */
				    $divplay = '<b>由于近期各大视频网站升级视频解析算法，我们暂时无法获取视频文件地址，因此暂停提供MukioPlayer播放服务，请等待恢复，抱歉</b><br/>PS：下载功能和其他播放器不受影响';
				    //$divplay = '<iframe height="720" width="1280" src="/play/do.php?player=mukio&cid='.$cid.'" scrolling="no" border="0" frameborder="no" framespacing="0"></iframe>';
				    }
				if ($_GET["player"]=='bilibili')
				    {
				    $divplay = '<iframe height="650" width="100%" src="/play/do.php?player=bilibili&aid='.$_GET["av"].'&cid='.$cid.'" scrolling="no" border="0" frameborder="no" framespacing="0"></iframe>';
				    }
				if ($_GET["player"]=='bilibili_bili')
				    {
				    $divplay = '<iframe height="650" width="100%" src="/play/do.php?player=bilibili_bili&aid='.$_GET["av"].'&cid='.$cid.'" scrolling="no" border="0" frameborder="no" framespacing="0"></iframe>';
				    }
				if ($_GET["player"]=='bilibili_iqiyi720')
				    {
				    $divplay = '<iframe height="650" width="100%" src="/play/do.php?player=bilibili_iqiyi720&aid='.$_GET["av"].'&cid='.$cid.'" scrolling="no" border="0" frameborder="no" framespacing="0"></iframe>';
				    }
				if ($_GET["player"]=='bilibili_iqiyi1080')
				    {
				    $divplay = '<iframe height="650" width="100%" src="/play/do.php?player=bilibili_iqiyi1080&aid='.$_GET["av"].'&cid='.$cid.'" scrolling="no" border="0" frameborder="no" framespacing="0"></iframe>';
				    }
				if ($_GET["player"]=='bilibili_tucao')
				    {
				    $divplay = '<iframe height="650" width="100%" src="/play/do.php?player=bilibili_tucao&aid='.$_GET["av"].'&cid='.$cid.'" scrolling="no" border="0" frameborder="no" framespacing="0"></iframe>';
				    }
				if ($_GET["player"]=='html5')
				    {
				    $api = json_decode(file_get_contents('http://interface.bilibili.com/playurl?otype=json&appkey=5a88bc9210cda8e6&cid='.$cid.'&type=mp4'),true);
				    $video = $api['durl']{0}['url'];
				    $comment = 'http://comment.bilibili.com/'.$cid.'.xml';
				    }
				if (empty($_GET["player"]))
				    {
				    $error = '<div class="framesubtitle">Bad Request</div><div class="errordescription">服务器无法识别你的请求，请检查你是否填写了所有必须参数…</div>';
				    }
				$parttitle = $return['list'][$page-1]['part'];
				$author = $return['author'];
				$authorid = $return['mid'];
				$videoplay = $return['play'];
				$videodanmu = $return['video_review'];
				$videoscore = $return['credit'];
				$videocoin = $return['coins'];
				$videofavorite = $return['favorites'];
				$videodescription = $return['description'];
				$videopic = $return['pic'];
				$videotime = $return['created_at'];
				$typeid = $return['tid'];
				$title = $videotitle.' - AV'.$_GET["av"].' - 播放';
				}
			    else
				{
				$error = 'AV'.$_GET["av"].' 没有第 '.$_GET["page"].' 页！<br/>AV'.$_GET["av"].' does not have page '.$_GET["page"].' !';
				}
			    }
			else
			    {
			    if ($return["code"]==-403)
				$error = '<div class="framesubtitle">权限不足</div><div class="errordescription">此页面仅限正式会员浏览。<br/>请点击页面右上方的“连接哔哩哔哩账户”登录哔哩哔哩后访问此页面。</div>Error: ['.$return["code"].'] '.$return["error"];
			    else
				$error = '<div class="framesubtitle">CID解析错误：NO_SUCH_VIDEO</div><div class="errordescription">无法获取视频CID。<br/>可能原因是您输入的AV号不存在或该投稿仅允许会员浏览，具体原因请查看下方错误代码。</div>Error: ['.$return["code"].'] '.$return["error"];
			    }
			}
		    }
		else
		    {
		    $error = '<div class="framesubtitle">Bad Request</div><div class="errordescription">服务器无法识别你的请求，请检查你是否填写了所有必须参数…</div>';
		    }
		}
	    if ($_GET["act"]=='search')
		{
		if (empty($_COOKIE["access_key"]))
		    {
		    $title = $_GET["word"].' - 搜索';
		    if (!empty($_GET["word"]))
			{
			$url = 'http://api.bilibili.com/search?type=json&appkey=5a88bc9210cda8e6&keyword='.$_GET["word"].'&page='.$_GET["p"].'&order='.$_GET["o"].'&pagesize='.$_GET["n"];
			$json = file_get_contents($url);
			$return = json_decode($json,true);
			$property = $return["property"];
			$result = $property["result"];
			$keyword = $property["QueryKeywords"];
			$page = $return["page"];
			if ($return["error"]!='overspeed')
			    {
			    if ($page!=0)
				{
				if (!empty($_GET["p"]))
				    {
				    $thispage = $_GET["p"];
				    }
				else
				    {
				    $thispage = 1;
				    }
				if ($thispage!=1)
				    {
				    $prevpage = $thispage-1;
				    $prevpagelink = '/api/do.php?act=search&word='.$_GET["word"].'&p='.$prevpage.'&o='.$_GET["o"].'&n='.$_GET["n"];
				    }
				else
				    {
				    $prevpage = '';
				    $prevpagelink = '';
				    }
				if ($thispage!=$page)
				    {
				    $nextpage = $thispage+1;
				    $nextpagelink = '/api/do.php?act=search&word='.$_GET["word"].'&p='.$nextpage.'&o='.$_GET["o"].'&n='.$_GET["n"];
				    }
				else
				    {
				    $nextpage = '';
				    $nextpagelink = '';
				    }
				$firstpagelink = '/api/do.php?act=search&word='.$_GET["word"].'&p=1'.'&o='.$_GET["o"].'&n='.$_GET["n"];
				$lastpagelink = '/api/do.php?act=search&word='.$_GET["word"].'&p='.$page.'&o='.$_GET["o"].'&n='.$_GET["n"];
				$nr = 0;
				}
			    else
				{
				$nr = 1;
				}
			    }
			else
			    {
			    $error = '<div class="framesubtitle">API错误：OverSpeed</div><div class="errordescription">请求频率过高，服务器拒绝服务。<br/>请稍后重试。</div>';
			    }
			}
		    else
			{
			$error = '<div class="framesubtitle">Bad Request</div><div class="errordescription">服务器无法识别你的请求，请检查你是否填写了所有必须参数…</div>';
			}
		    }
		else
		    {
		    $title = $_GET["word"].' - 搜索';
		    if (!empty($_GET["word"]))
			{
			$sign = get_sign(array("type"=>"json","appkey"=>$appkey,"keyword"=>$_GET["word"],"page"=>$_GET["p"],"order"=>$_GET["o"],"pagesize"=>$_GET["n"],"access_key"=>$_COOKIE["access_key"]),$appsecret);
			$json = file_get_contents('http://api.bilibili.com/search?type=json&appkey='.$appkey.'&keyword='.$_GET["word"].'&page='.$_GET["p"].'&order='.$_GET["o"].'&pagesize='.$_GET["n"].'&access_key='.$_COOKIE["access_key"].'&sign='.$sign);
			$return = json_decode($json,true);
			$property = $return["property"];
			$result = $property["result"];
			$keyword = $property["QueryKeywords"];
			$page = $return["page"];
			if ($return["error"]!='overspeed')
			    {
			    if ($page!=0)
				{
				if (!empty($_GET["p"]))
				    {
				    $thispage = $_GET["p"];
				    }
				else
				    {
				    $thispage = 1;
				    }
				if ($thispage!=1)
				    {
				    $prevpage = $thispage-1;
				    $prevpagelink = '/api/do.php?act=search&word='.$_GET["word"].'&p='.$prevpage.'&o='.$_GET["o"].'&n='.$_GET["n"];
				    }
				else
				    {
				    $prevpage = '';
				    $prevpagelink = '';
				    }
				if ($thispage!=$page)
				    {
				    $nextpage = $thispage+1;
				    $nextpagelink = '/api/do.php?act=search&word='.$_GET["word"].'&p='.$nextpage.'&o='.$_GET["o"].'&n='.$_GET["n"];
				    }
				else
				    {
				    $nextpage = '';
				    $nextpagelink = '';
				    }
				$firstpagelink = '/api/do.php?act=search&word='.$_GET["word"].'&p=1'.'&o='.$_GET["o"].'&n='.$_GET["n"];
				$lastpagelink = '/api/do.php?act=search&word='.$_GET["word"].'&p='.$page.'&o='.$_GET["o"].'&n='.$_GET["n"];
				$nr = 0;
				}
			    else
				{
				$nr = 1;
				}
			    }
			else
			    {
			    $error = '<div class="framesubtitle">API错误：OverSpeed</div><div class="errordescription">请求频率过高，服务器拒绝服务。<br/>请稍后重试。</div>';
			    }
			}
		    else
			{
			$error = '<div class="framesubtitle">Bad Request</div><div class="errordescription">服务器无法识别你的请求，请检查你是否填写了所有必须参数…</div>';
			}
		    }
		}
	    if ($_GET["act"]=='viewsp')
		{
		if (empty($_COOKIE["access_key"]))
		    {
		    $title = $_GET["title"].' - 专题';
		    if (!empty($_GET["id"]) && !empty($_GET["title"]))
			{
			if (!empty($_GET["bangumi"]))
			    $bangumi = $_GET["bangumi"];
			else
			    $bangumi = 0;
			$sign = get_sign(array("type"=>"json","appkey"=>$appkey,"spid"=>$_GET["id"],"bangumi"=>$bangumi),$appsecret);
			$listurl = file_get_contents('http://api.bilibili.com/spview?type=json&appkey='.$appkey.'&spid='.$_GET["id"].'&bangumi='.$bangumi.'&sign='.$sign);
			$list = json_decode($listurl,true);
			$result = $list["results"];
			$sign = get_sign(array("type"=>"json","appkey"=>$appkey,"spid"=>$_GET["id"]),$appsecret);
			$infourl = file_get_contents('http://api.bilibili.com/sp?type=json&appkey='.$appkey.'&spid='.$_GET["id"].'&sign='.$sign);
			$info = json_decode($infourl,$true);
			if ($info["code"]==0)
			    {
			    $title = $info["title"].' - 专题';
			    }
			else
			    {
			    $error = '<div class="framesubtitle">专题解析错误</div><div class="errordescription">无法获取专题信息。<br/>可能原因是该SPID不存在或该专题仅允许会员浏览，具体原因请查看下方错误代码。</div>Error: ['.$info["code"].'] '.$info["error"];
			    }
			}
		    else
			{
			$error = '<div class="framesubtitle">Bad Request</div><div class="errordescription">服务器无法识别你的请求，请检查你是否填写了所有必须参数…</div>';
			}
		    }
		else
		    {
		    $title = $_GET["title"].' - 专题';
		    if (!empty($_GET["id"]) && !empty($_GET["title"]))
			{
			if (!empty($_GET["bangumi"]))
			    $bangumi = $_GET["bangumi"];
			else
			    $bangumi = 0;
			$sign1 = get_sign(array("type"=>"json","appkey"=>$appkey,"spid"=>$_GET["id"],"bangumi"=>$bangumi,"access_key"=>$_COOKIE["access_key"]),$appsecret);
			$list = json_decode(file_get_contents('http://api.bilibili.com/spview?type=json&appkey='.$appkey.'&spid='.$_GET["id"].'&bangumi='.$bangumi.'&access_key='.$_COOKIE["access_key"].'&sign='.$sign1),true);
			$result = $list["results"];
			$sign2 = get_sign(array("type"=>"json","appkey"=>$appkey,"spid"=>$_GET["id"],"access_key"=>$_COOKIE["access_key"]),$appsecret);
			$info = json_decode(file_get_contents('http://api.bilibili.com/sp?type=json&appkey='.$appkey.'&spid='.$_GET["id"].'&access_key='.$_COOKIE["access_key"].'&sign='.$sign2),true);
			if ($info["code"]==0)
			    {
			    $title = $info["title"].' - 专题';
			    }
			else
			    {
			    $error = '<div class="framesubtitle">专题解析错误</div><div class="errordescription">无法获取专题信息。<br/>可能原因是该SPID不存在或该专题仅允许正式会员浏览，具体原因请查看下方错误代码。</div>Error: ['.$info["code"].'] '.$info["error"];
			    }
			}
		    else
			{
			$error = '<div class="framesubtitle">Bad Request</div><div class="errordescription">服务器无法识别你的请求，请检查你是否填写了所有必须参数…</div>';
			}
		    }
		}
	    if ($_GET["act"]=='logout')
		{
		$title = '退出登录';
		setcookie ("access_key",'',time()-3600,"/");
		setcookie ("mid",'',time()-3600,"/");
		setcookie ("uname",'',time()-3600,"/");
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
	echo '<style type="text/css">
div.title{font-family:"Microsoft YaHei";font-size:24px;font-weight:bold}
div.subtitle{font-family:"Verdana";font-size:18px;font-weight:bold}
div.boxtitle-1{margin:0px;padding:6px;width:200px;height:28px;color:#FFFFFF;text-align:center;font-family:"Microsoft YaHei";font-size:18px;font-weight:bold;background-color:#006EDC}
div.boxcontent-1{margin:0px;padding:4px 0px 0px 0px;height:150px;border-top:6px solid #1E90FF;clear:both}
div.errordescription{padding:8px;font-family:"Microsoft YaHei";font-size:15px;background-color:#DDDDDD}
div.buttonbackground{padding:12px;text-align:center;background-color:#999999}
</style>
<br/>
<div class="frametitle">糟糕，出错啦！</div><br/><br/>
<div class="boxtitle-1">详细错误信息</div>
<div class="boxcontent-1">'.$e_text.'</div><br/>
<div class="buttonbackground"><input type="button" class="button" value="返回" onclick="history.go(-1)">　<input type="button" class="button" value="帮助" onclick="window.open(\'/?about\')"></div>
';
	}
    if ($error==2)
	{
	$id = $_GET["av"];
	$pagedata = mysql_real_escape_string($apijson);
	//mysql_query("INSERT INTO CACHE_PAGE (ID,SUCCESS,DATA,LASTUPDATE) VALUES ('{$id}','2','{$pagedata}','{$time}') ON DUPLICATE KEY UPDATE SUCCESS=VALUES(SUCCESS),DATA=VALUES(DATA),LASTUPDATE=VALUES(LASTUPDATE)");
	echo '<style type="text/css">
div.title{font-family:"Microsoft YaHei";font-size:24px;font-weight:bold}
div.subtitle{font-family:"Verdana";font-size:18px;font-weight:bold}
div.boxtitle-1{margin:0px;padding:6px;width:200px;height:28px;color:#FFFFFF;text-align:center;font-family:"Microsoft YaHei";font-size:18px;font-weight:bold;background-color:#006EDC}
div.boxcontent-1{margin:0px;padding:4px 0px 0px 0px;height:150px;border-top:6px solid #1E90FF;clear:both}
div.errordescription{padding:8px;font-family:"Microsoft YaHei";font-size:15px;background-color:#DDDDDD}
div.buttonbackground{padding:12px;text-align:center;background-color:#999999}
</style>
<br/>
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
	$id = $_GET["av"];
	$pagedata = mysql_real_escape_string($apijson);
	$pagemp4 = mysql_real_escape_string($mp4json);
	$videodata = mysql_real_escape_string($videoxml);
	//mysql_query("INSERT INTO CACHE_PAGE (ID,SUCCESS,DATA,MP3,MP4,LASTUPDATE) VALUES ('{$id}','1','{$pagedata}','{$mp3file}','{$pagemp4}','{$time}') ON DUPLICATE KEY UPDATE SUCCESS=VALUES(SUCCESS),DATA=VALUES(DATA),LASTUPDATE=VALUES(LASTUPDATE)");
	//mysql_query("INSERT INTO CACHE_VIDEO (CID,SUCCESS,DATA,LASTUPDATE) VALUES ('{$cid}','0','{$videodata}','{$time}') ON DUPLICATE KEY UPDATE SUCCESS=VALUES(SUCCESS),DATA=VALUES(DATA),LASTUPDATE=VALUES(LASTUPDATE)");
	echo '<style type="text/css">
div.title{font-family:"Microsoft YaHei";font-size:24px;font-weight:bold}
div.subtitle{font-family:"Verdana";font-size:18px;font-weight:bold}
div.boxtitle-1{margin:0px;padding:6px;width:200px;height:28px;color:#FFFFFF;text-align:center;font-family:"Microsoft YaHei";font-size:18px;font-weight:bold;background-color:#006EDC}
div.boxcontent-1{margin:0px;padding:4px 0px 0px 0px;height:150px;border-top:6px solid #1E90FF;clear:both}
div.errordescription{padding:8px;font-family:"Microsoft YaHei";font-size:15px;background-color:#DDDDDD}
div.buttonbackground{padding:12px;text-align:center;background-color:#999999}
</style>
<br/>
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
	$id = $_GET["av"];
	$filedata = json_encode(array("ID"=>$id,"SUCCESS"=>"1","DATA"=>json_decode($apijson),LASTUPDATE=>$time));
	$file=fopen("../cache/$id.json","w");
	fwrite($file,$filedata);
	fclose($file);
	mysql_query("INSERT INTO CACHE_VIDEO (CID,SUCCESS,DATA,LASTUPDATE,MP4,VALIDTS) VALUES ('{$cid}','1','{$videodata}','{$time}','{$pagemp4}','{$validts}') ON DUPLICATE KEY UPDATE SUCCESS=VALUES(SUCCESS),DATA=VALUES(DATA),LASTUPDATE=VALUES(LASTUPDATE),MP4=VALUES(MP4),VALIDTS=VALUES(VALIDTS)");
	echo '<style type="text/css">
div.title{font-family:"Microsoft YaHei";font-size:24px;font-weight:bold}
div.subtitle{font-family:"Verdana";font-size:18px;font-weight:bold}
div.boxtitle-1{margin:0px;padding:6px;width:200px;height:28px;color:#FFFFFF;text-align:center;font-family:"Microsoft YaHei";font-size:18px;font-weight:bold;background-color:#006EDC}
div.boxcontent-1{margin:0px;padding:4px 0px 0px 0px;height:150px;border-top:6px solid #1E90FF;clear:both}
div.errordescription{padding:8px;font-family:"Microsoft YaHei";font-size:15px;background-color:#DDDDDD}
div.buttonbackground{padding:12px;text-align:center;background-color:#999999}
</style>
<br/>
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
	    echo '<style type="text/css">
    a{text-decoration:none}
    a:link{color:#FFFFFF}
    a:visited{color:#FFFFFF}
    a:hover{color:#CDCDCD}
    a:active{color:#3388FF}
    div.videotitle{margin:2px;padding:4px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:20px;font-weight:bold;background-color:#1E90FF}
    div.parttitle{margin:2px;padding:4px;color:#FFFFFF;font-family:SimHei;font-size:16px;font-weight:bold;background-color:#999999}
    div.pagelist{margin:0px;padding:4px}
    div.docinfo{margin:2px;padding:4px;color:#FFFFFF;font-family:"SimSun";font-size:16px;font-weight:bold;background-color:#888888;float:left}
    div.videotime{margin:2px;padding:4px;color:#FFFFFF;font-family:"SimSun";font-size:16px;font-weight:bold;background-color:#888888;float:right}
    div.videoinfo{margin:0px;padding:4px}
    div.infolist{margin:4px;padding:4px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:14px;font-weight:bold;background-color:#1E90FF;display:inline-block}
    div.videofile{margin:0px;padding:4px}
    div.danmufile{margin:0px;padding:4px}
    div.playlink{margin:0px;padding:4px}
    div.tip{margin:2px;padding:2px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:13px;font-weight:bold;background-color:green}
    div.filelist{margin:4px;padding:4px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:15px;font-weight:bold;background-color:#006EDC;border-style:solid;border-width:2px;border-color:#999;display:inline-block}
    div.filelist_empty{margin:4px;padding:4px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:15px;font-weight:bold;background-color:#666666;display:inline-block}
    div.mp3convert{margin:4px;padding:4px;color:#E9006D;font-family:"Microsoft YaHei";font-size:15px;font-weight:bold;background-color:#FFFFFF;border-style:solid;border-width:2px;border-color:#E9006D;display:inline-block}
    div.listbox{margin:2px;padding:4px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:14px;font-weight:bold;background-color:#FF40AD;display:inline-block}
    div.hrdescription{color:#222222;font-family:"SimSun";font-size:16px;font-weight:bold;float:left;display:inline-block;clear:both}
    div.footer{margin:2px;padding:4px;font-family:"Microsoft YaHei";font-size:14px;font-color:#FFFFFF;text-align:center;background-color:#999999}
    div.update{margin:0px;padding:2px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:14px;font-weight:bold;background-color:#1E90FF;display:inline-block}
</style>
<div class="videotitle">AV'.$_GET["av"].' - '.$videotitle.'</div>
<table width="100%" border="0" cellpadding="4px">
    <tr>
	<td width="80%" style="text-align:left;color:#FFFFFF;font-family:SimHei;font-size:16px;font-weight:bold;background-color:#666666">[P'.$page.'] '.$parttitle.'</td>
	<td width="20%" style="text-align:center;color:#FFFFFF;font-family:SimHei;font-size:16px;font-weight:bold;background-color:#666666">UP主：<a href="http://space.bilibili.com/'.$authorid.'" target="_blank">'.$author.'</a></td>
    </tr>
</table>
<div class="videotime">投稿时间：'.$videotime.'</div><div class="docinfo">播放:'.$videoplay.' | 弹幕:'.$videodanmu.' | 评分:'.$videoscore.' | 硬币:'.$videocoin.' | 收藏:'.$videofavorite.'</div><br/>
<div><br/></div><div class="infolist"><a href=/api/getaid.php?act=info&av='.$_GET["av"].'>返回详细分P列表</a></div>
<div><br/></div><div class="hrdescription">视频信息</div><hr><div class="videoinfo"><div class="infolist">CID：'.$cid.'</div><div class="infolist">投稿视频源：'.$from_src.'</div><div class="infolist">VID：'.$vid.'</div><div class="infolist">当前视频源：'.$from_real.'</div><div class="infolist">视频分段：共'.$part.'段</div></div>
<div><br/></div><div class="hrdescription">视频源文件</div><hr><div class="videofile"><div class="infolist">视频总长：'.$videolength.'</div><br/><fieldset><legend>源视频文件</legend><div class="tip">请按顺序下载所有分段获得完整视频</div>'.$video.'</fieldset><fieldset><legend>合并+转码MP4</legend><div class="tip">视频质量可能有轻微下降，部分投稿无法提供MP4下载</div>';
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
	if ($_GET["act"]=='play')
	    {
		if ($_GET["player"]=='custom')
		    {
		    echo '<style type="text/css">
    a{text-decoration:none}
    a:link{color:#FFFFFF}
    a:visited{color:#FFFFFF}
    a:hover{color:#CDCDCD}
    a:active{color:#3388FF}
    div.content{background-color:#FFFFFF;color:#000000}
    div.videotitle{margin:2px;padding:4px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:20px;font-weight:bold;background-color:#1E90FF}
    div.videoinfo{margin:2px;padding:4px;color:#FFFFFF;font-family:"SimSun";font-size:16px;font-weight:bold;background-color:#888888;float:left}
    div.videotime{margin:2px;padding:4px;color:#FFFFFF;font-family:"SimSun";font-size:16px;font-weight:bold;background-color:#888888;float:right}
    div.pagelist{padding:2px;font-family:"Microsoft YaHei";font-size:15px}
    div.videodescription{padding:8px;font-family:"Microsoft YaHei";font-size:14px;background-color:#DDDDDD}
    div.downloadlink{margin:0px;padding:4px}
    div.tip{margin:2px;padding:2px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:13px;font-weight:bold;background-color:green}
    div.filelist{margin:4px;padding:4px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:15px;font-weight:bold;background-color:#006EDC;border-style:solid;border-width:2px;border-color:#999;display:inline-block}
    div.listbox{margin:2px;padding:4px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:14px;font-weight:bold;background-color:#FF40AD;display:inline-block}
    div.hrdescription{color:#666666;font-family:"SimSun";font-size:14px;font-weight:bold;float:left;display:inline-block}
    div.selectplayer{margin:0px;padding:0px;width:100%;height:90px;color:#FFFFFF;text-align:center;font-family:Verdana;font-size:18px;font-weight:bold}
    div.footer{margin:2px;padding:4px;font-family:"Microsoft YaHei";font-size:14px;font-color:#FFFFFF;text-align:center;background-color:#999999}
    div.update{margin:0px;padding:2px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:14px;font-weight:bold;background-color:#1E90FF;display:inline-block}
</style>
<div class="videotitle">AV'.$_GET["av"].$videotitle.'</div><table width="100%" border="0" cellpadding="4px">
    <tr>
	<td width="80%" style="text-align:left;color:#FFFFFF;font-family:SimHei;font-size:16px;font-weight:bold;background-color:#666666">[P'.$page.'] '.$parttitle.'</td>
	<td width="20%" style="text-align:center;color:#FFFFFF;font-family:SimHei;font-size:16px;font-weight:bold;background-color:#666666">UP主：<a href="http://space.bilibili.com/'.$authorid.'" target="_blank">'.$author.'</a></td>
    </tr>
</table>
<div class="videotime">投稿时间：'.$videotime.'</div><div class="videoinfo">播放:'.$videoplay.' | 弹幕:'.$videodanmu.' | 评分:'.$videoscore.' | 硬币:'.$videocoin.' | 收藏:'.$videofavorite.'</div><br/><div><br/></div><div class="hrdescription">分P列表</div><hr><div class="pagelist">'.$pagelist.'</div><div><br/></div><div class="hrdescription">选择播放器</div><hr>
<div class="selectplayer">
<div style="margin:4px;width:24%;height:90px;line-height:90px;float:left"><img height=100% src="'.$videopic.'"/></div>
<a href="/api/do.php?act=play&av='.$_GET["av"].'&page='.$page.'&player=bilibili"><div style="margin:4px;width:24%;height:90px;line-height:90px;background-color:#1E90FF;float:left">BiliPlayer</div></a>
<a href="/api/do.php?act=play&av='.$_GET["av"].'&page='.$page.'&player=html5"><div style="margin:4px;width:24%;height:90px;line-height:90px;background-color:#1E90FF;float:left">HTML5</div></a>
<a href="/api/do.php?act=play&av='.$_GET["av"].'&page='.$page.'&player=mukio"><div style="margin:4px;width:24%;height:90px;line-height:90px;background-color:#1E90FF;float:left">MukioPlayer</div></a>
</div>
<div style="clear:left"><br/></div><div class="hrdescription">视频简介</div><hr><div class="videodescription">'.$videodescription.'</div><br/>
<div class="hrdescription">视频弹幕下载</div><hr><div class="downloadlink"><div class="tip">下载源视频、MP4视频、XML/ASS弹幕文件</div><a href="/api/getaid.php?act=info&av='.$_GET['av'].'" title="视频弹幕下载页面"><div class="filelist">下载视频/弹幕文件</div></a></div><br/>
<div class="footer">本页面为缓存数据，数据缓存时间：'.$datatime.' <div class="update"><a href="'.$_SERVER['REQUEST_URI'].'&update=1">刷新数据</a></div></div>
<br/>
';
		    }
		if ($_GET["player"]=='html5')
		    {
		    echo '<link rel="stylesheet" href="/style/abplayer/base.css" />
<style type="text/css">
    a{text-decoration:none}
    a:link{color:#FFFFFF}
    a:visited{color:#FFFFFF}
    a:hover{color:#CDCDCD}
    a:active{color:#3388FF}
    body{min-width:1200px;background-color:#FFFFFF;color:#000000}
    div.videotitle{margin:2px;padding:4px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:20px;font-weight:bold;background-color:#1E90FF}
    div.videoinfo{margin:2px;padding:4px;color:#FFFFFF;font-family:"SimSun";font-size:16px;font-weight:bold;background-color:#888888;float:left}
    div.videotime{margin:2px;padding:4px;color:#FFFFFF;font-family:"SimSun";font-size:16px;font-weight:bold;background-color:#888888;float:right}
    div.pagelist{padding:2px;font-family:"Microsoft YaHei";font-size:15px}
    div.video{text-align:center}
    div.videodescription{padding:8px;font-family:"Microsoft YaHei";font-size:14px;background-color:#DDDDDD}
    div.downloadlink{margin:0px;padding:4px}
    div.tip{margin:2px;padding:2px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:13px;font-weight:bold;background-color:green}
    div.filelist{margin:4px;padding:4px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:15px;font-weight:bold;background-color:#006EDC;border-style:solid;border-width:2px;border-color:#999;display:inline-block}
    div.listbox{margin:2px;padding:4px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:14px;font-weight:bold;background-color:#FF40AD;display:inline-block}
    div.hrdescription{color:#666666;font-family:"SimSun";font-size:14px;font-weight:bold;float:left;display:inline-block}
    div.player{text-align:conter}
    div.ABP-Unit{margin:0 auto}
    div.footer{margin:2px;padding:4px;font-family:"Microsoft YaHei";font-size:14px;font-color:#FFFFFF;text-align:center;background-color:#999999}
    div.update{margin:0px;padding:2px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:14px;font-weight:bold;background-color:#1E90FF;display:inline-block}
</style>
<script src="/js/CommentCoreLibrary.js"></script>
<script src="/js/libxml.js"></script>
<script src="/js/mobile.js"></script>
<script src="/js/player.js"></script>
		<script type="text/javascript">
			window.addEventListener("load",function(){
				var inst = ABP.create(document.getElementById("player"), {
					src: {
						playlist: [{
							video: document.getElementById("video"),
							comments: "'.$comment.'"
						}]
					},
					width: 1024,
					height: 576,
					mobile: isMobile()
				});
				window.abpinst = inst;
			});
		</script>
<div class="videotitle">'.$videotitle.'</div>
<table width="100%" border="0" cellpadding="4px">
    <tr>
	<td width="80%" style="text-align:left;color:#FFFFFF;font-family:SimHei;font-size:16px;font-weight:bold;background-color:#666666">[P'.$page.'] '.$parttitle.'</td>
	<td width="20%" style="text-align:center;color:#FFFFFF;font-family:SimHei;font-size:16px;font-weight:bold;background-color:#666666">UP主：<a href="http://space.bilibili.com/'.$authorid.'" target="_blank">'.$author.'</a></td>
    </tr>
</table>
<div class="videotime">投稿时间：'.$videotime.'</div><div class="videoinfo">播放:'.$videoplay.' | 弹幕:'.$videodanmu.' | 评分:'.$videoscore.' | 硬币:'.$videocoin.' | 收藏:'.$videofavorite.'</div><br/><div><br/></div><div class="hrdescription">播放</div><hr>
<div class="tip">推荐使用最新Chrome浏览器播放HTML5视频，其他浏览器可能无法正常使用播放器全部功能。</div>
<div id="player" class="player"></div>
<div class="video">
	<video id="video" poster="'.$videopic.'" preload="auto" autobuffer="true" data-setup="{}">
		<source src="'.$video.'">
		<div><b>【错误】您使用的浏览器不支持HTML5视频...</b> <a href="/html/html5player.html" target="_blank" style="color:#3388FF">[详细浏览器支持情况]</a></div>
	</video>
</div>
<div><br/></div><div class="hrdescription">视频简介</div><hr><div class="videodescription">'.$videodescription.'</div><br/>
<div class="hrdescription">视频弹幕下载</div><hr><div class="downloadlink"><div class="tip">下载源视频、MP4视频、XML/ASS弹幕文件</div><a href="/api/getaid.php?act=info&av='.$_GET['av'].'" title="视频弹幕下载页面"><div class="filelist">下载视频/弹幕文件</div></a></div><div><br/></div><div class="hrdescription">分P列表</div><hr><div class="pagelist"><script>
av='.$_GET['av'].';
items='.$pagesarrayjson.';

for (var i=0;i<items.length;i++)
{
	page=i+1;
	part=items[i];
	
	player=\''.$_GET['player'].'\';
	document.write(\'<a href="/api/do.php?act=play&av=\'+av+\'&page=\'+page+\'&player=\'+player+\'"><div class="listbox">[P\'+page+\'] \'+part+\'</div></a>\');
}

</script>
</div><br/>
<div class="footer">本页面为缓存数据，数据缓存时间：'.$datatime.' <div class="update"><a href="'.$_SERVER['REQUEST_URI'].'&update=1">刷新数据</a></div></div>
<br/>
';
		    }
		if ($_GET["player"]=='mukio'||$_GET["player"]=='bilibili'||$_GET["player"]=='bilibili_bili'||$_GET["player"]=='bilibili_iqiyi720'||$_GET["player"]=='bilibili_iqiyi1080'||$_GET["player"]=='bilibili_tucao')
		    {
		echo '<style type="text/css">
    a{text-decoration:none}
    a:link{color:#FFFFFF}
    a:visited{color:#FFFFFF}
    a:hover{color:#CDCDCD}
    a:active{color:#3388FF}
    div.videotitle{margin:2px;padding:4px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:20px;font-weight:bold;background-color:#1E90FF}
    div.videoinfo{margin:2px;padding:4px;color:#FFFFFF;font-family:"SimSun";font-size:16px;font-weight:bold;background-color:#888888;float:left}
    div.videotime{margin:2px;padding:4px;color:#FFFFFF;font-family:"SimSun";font-size:16px;font-weight:bold;background-color:#888888;float:right}
    div.pagelist{padding:2px;font-family:"Microsoft YaHei";font-size:15px}
    div.videodescription{padding:8px;font-family:"Microsoft YaHei";font-size:14px;background-color:#DDDDDD}
    div.downloadlink{margin:0px;padding:4px}
    div.tip{margin:2px;padding:2px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:13px;font-weight:bold;background-color:green}
    div.filelist{margin:4px;padding:4px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:15px;font-weight:bold;background-color:#006EDC;border-style:solid;border-width:2px;border-color:#999;display:inline-block}
    div.listbox{margin:2px;padding:4px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:14px;font-weight:bold;background-color:#FF40AD;display:inline-block}
    div.hrdescription{color:#666666;font-family:"SimSun";font-size:14px;font-weight:bold;float:left;display:inline-block}
    div.alert{margin:2px;padding:4px;height:30px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:14px;font-weight:bold;background-color:#228B22}
    div.alertbox1{margin:2px;padding:4px;width:500px;background-color:#228B22;float:left}
    div.alertbox2{margin:4px;padding:2px 4px 2px 4px;text-align:center;background-color:#55B424;float:left}
    div.footer{margin:2px;padding:4px;font-family:"Microsoft YaHei";font-size:14px;font-color:#FFFFFF;text-align:center;background-color:#999999}
    div.update{margin:0px;padding:2px;color:#FFFFFF;font-family:"Microsoft YaHei";font-size:14px;font-weight:bold;background-color:#1E90FF;display:inline-block}
</style>
<div class="videotitle">'.$videotitle.'</div>
<table width="100%" border="0" cellpadding="4px">
    <tr>
	<td width="80%" style="text-align:left;color:#FFFFFF;font-family:SimHei;font-size:16px;font-weight:bold;background-color:#666666">[P'.$page.'] '.$parttitle.'</td>
	<td width="20%" style="text-align:center;color:#FFFFFF;font-family:SimHei;font-size:16px;font-weight:bold;background-color:#666666">UP主：<a href="http://space.bilibili.com/'.$authorid.'" target="_blank">'.$author.'</a></td>
    </tr>
</table>
<div class="videotime">投稿时间：'.$videotime.'</div><div class="videoinfo">播放:'.$videoplay.' | 弹幕:'.$videodanmu.' | 评分:'.$videoscore.' | 硬币:'.$videocoin.' | 收藏:'.$videofavorite.'</div><br/><div><br/></div><div class="hrdescription">播放</div><hr>';
		if ($typeid==33)
		    echo '<div class="alert"><div class="alertbox1">您正在观看连载动画区视频，如遇到版权番无法播放请尝试使用其他播放器！</div><a href="/api/do.php?act=play&av='.$_GET["av"].'&page='.$page.'&player=bilibili"><div class="alertbox2">哔哩哔哩原始播放器</div></a><a href="/api/do.php?act=play&av='.$_GET["av"].'&page='.$page.'&player=bilibili_bili"><div class="alertbox2">爱奇艺版权修复播放器</div></a></div>';
		echo '<div class="player">'.$divplay.'</div><div><br/></div><div class="hrdescription">视频简介</div><hr><div class="videodescription">'.$videodescription.'</div><br/>
<div class="hrdescription">视频弹幕下载</div><hr><div class="downloadlink"><div class="tip">下载源视频、MP4视频、XML/ASS弹幕文件</div><a href="/api/geturl.php?act=info&av='.$_GET['av'].'&page='.$_GET['page'].'" title="视频弹幕下载页面"><div class="filelist">下载视频/弹幕文件</div></a></div><div class="hrdescription">分P列表</div><hr><div class="pagelist"><script>
av='.$_GET['av'].';
items='.$pagesarrayjson.';

for (var i=0;i<items.length;i++)
{
	page=i+1;
	part=items[i];
	
	player=\''.$_GET['player'].'\';
	document.write(\'<a href="/api/do.php?act=play&av=\'+av+\'&page=\'+page+\'&player=\'+player+\'"><div class="listbox">[P\'+page+\'] \'+part+\'</div></a>\');
}

</script>
</div><div><br/></div><br/>
<div class="footer">本页面为缓存数据，数据缓存时间：'.$datatime.' <div class="update"><a href="'.$_SERVER['REQUEST_URI'].'&update=1">刷新数据</a></div></div>
<br/>
';
		    }
	    }
	if ($_GET["act"]=='search')
	    {
	    echo '<style type="text/css">
    a{text-decoration:none}
    a:link{color:#FFFFFF}
    a:visited{color:#FFFFFF}
    a:hover{color:#CDCDCD}
    a:active{color:#3388FF}
</style>
<br/>
<div style="width:100%">
    <form name="input" action="/api/do.php?act=search" method="get">
    <input type="hidden" name="act" value="search">
	<fieldset>
	    <legend>搜索</legend>
		<input type="text" name="word" value="'.$_GET['word'].'" style="width:700px;"><select name="o">
		<option value="default">综合排序</option>
		<option value="stow">收藏数</option>
		<option value="scores">推荐数</option>
		<option value="damku">弹幕数</option>
		<option value="click">点击量</option>
		<option value="pubdate">发布日期</option>
		<option value="senddate">修改日期</option>
		<option value="ranklevel">相关度</option>
		<option value="id">投稿序号</option>
		</select><select name="n">
		<option value="5"> 5个/页</option>
		<option value="10">10个/页</option>
		<option value="20" selected="selected">20个/页</option>
		<option value="30">30个/页</option>
		<option value="50">50个/页</option>
		</select><input type="hidden" name="p" value="1">　　<input type="submit" value="　搜索　">
	</fieldset><br/>
    </form>';
		$number = 0;
		while (isset($return['result'][$number]['id']))
			{
			if ($return['result'][$number]['type']=='video')
				{
				echo '<hr>
<table width="100%" border="0">
    <tr>
	<td width="15%" align="center" style="color:#FFFFFF;background-color:#1E90FF"><a href="'.$return['result'][$number]['typeurl'].'">'.$return['result'][$number]['typename'].'</a></td>
	<td width="65%" align="center" style="color:#FFFFFF;background-color:#1E90FF"><b>'.$return['result'][$number]['title'].'</b></td>
	<td width="20%" align="center" style="color:#FFFFFF;background-color:#1E90FF">UP主：<b><a href="http://space.bilibili.com/'.$return['result'][$number]['mid'].'">'.$return['result'][$number]['author'].'</a></b></td>
    </tr>
</table>
<table width="100%" border="0">
    <tr>
	<td rowspan="5" style="vertical-align:top"><img width="120" src="'.$return['result'][$number]['pic'].'"/></td>
    </tr>
    <tr>
	<td colspan="3">'.$return['result'][$number]['description'].'</td>
    </tr>
    <tr>
	<td colspan="3"><b>播放:'.$return['result'][$number]['play'].' </b>|<b> 收藏:'.$return['result'][$number]['favorites'].' </b>|<b> 评论:'.$return['result'][$number]['review'].' </b>|<b> 弹幕:'.$return['result'][$number]['video_review'].'</b></td>
    </tr>
    <tr>
	<td width="33%" align="center" style="color:#FFFFFF;background-color:#1FAAFF"><a href="/api/getaid.php?act=info&av='.$return['result'][$number]['id'].'"><b>获取视频文件</b></a></td>
	<td width="33%" align="center" style="color:#FFFFFF;background-color:#1FAAFF"><a href="/api/do.php?act=play&av='.$return['result'][$number]['id'].'&page=1&player=bilibili"><b>播放弹幕视频</b></a></td>
	<td width="33%" align="center" style="color:#FFFFFF;background-color:#1FAAFF"><a href="'.$return['result'][$number]['arcurl'].'" target="_blank"><b>前往主站播放</b></a></td>
    </tr>
</table>';
				}
			if ($return['result'][$number]['type']=='special')
				{
				echo '<hr>
<table width="100%" border="0">
    <tr>
	<td width="15%" align="center" style="color:#FFFFFF;background-color:#FF40AD"><a href="'.$return['result'][$number]['typeurl'].'">'.$return['result'][$number]['typename'].'</a></td>
	<td width="65%" align="center" style="color:#FFFFFF;background-color:#FF40AD"><b>'.$return['result'][$number]['title'].'</b></td>
	<td width="20%" align="center" style="color:#FFFFFF;background-color:#FF40AD">管理：<b><a href="http://space.bilibili.com/'.$return['result'][$number]['mid'].'">'.$return['result'][$number]['author'].'</a></b></td>
    </tr>
</table>
<table width="100%" border="0">
    <tr>
	<td rowspan="5" style="vertical-align:top"><img width="120" src="'.$return['result'][$number]['pic'].'"/></td>
    </tr>
    <tr>
	<td colspan="3">'.$return['result'][$number]['description'].'</td>
    </tr>
    <tr>
	<td colspan="3"><b>点击:'.$return['result'][$number]['click'].' </b>|<b> 订阅:'.$return['result'][$number]['favourite'].' </b>|<b> 关注:'.$return['result'][$number]['attention'].' </b>|<b> 投稿:'.$return['result'][$number]['count'].'</b></td>
    </tr>
    <tr>
	<td width="50%" align="center" style="color:#FFFFFF;background-color:#FF80D5"><b><a href=/api/do.php?act=viewsp&id='.$return['result'][$number]['spid'].'&title='.urlencode($return['result'][$number]['title']).'>打开专题列表</a></b></td>
	<td width="50%" align="center" style="color:#FFFFFF;background-color:#FF80D5"><a href="http://www.bilibili.com'.$return['result'][$number]['arcurl'].'" target="_blank">前往主站查看</a></td>
    </tr>
</table>';
				}
			$number = $number+1;
			}
		echo '<hr><br/>
<table width="100%" border="0">
    <tr>
	<td align="center" style="color:#FFFFFF;background-color:#1E90FF"><a href="'.$firstpagelink.'">首页</a>　<b>第</b> <a href="'.$prevpagelink.'">'.$prevpage.'</a>　<b>'.$thispage.'</b>　<a href="'.$nextpagelink.'">'.$nextpage.'</a> <b>页</b>　<a href="'.$lastpagelink.'">末页</a></td>
    </tr>
</table>
</div>
<br/>';
	    }
	if ($_GET["act"]=='viewsp')
	    {
	    echo '<style type="text/css">
    div.header{height:35px;border-bottom: 6px solid #FF4DA0}
    div.sptype{line-height:35px;color:#E9006D;font-family:"Microsoft YaHei";font-size:16px;font-weight:bold;float:left}
    div.title{line-height:35px;font-family:"Microsoft YaHei";font-size:24px;font-weight:bold;float:left}
    div.subtitle{line-height:35px;color:#666666;font-family:"Verdana";font-size:18px;font-weight:bold;float:left}
    div.info{padding:10px 0px 0px 0px;width:200px;font-family:"Microsoft YaHei";font-size:15px;font-weight:bold;float:left}
    div.description{margin:0px 0px 0px 200px;padding:10px 0px 0px 0px;font-family:"Microsoft YaHei";font-size:16px}
    div.list{margin:0px 0px 0px 200px}
    div.boxtitle-2{margin:0px;padding:6px;width:200px;height:28px;color:#FFFFFF;text-align:center;font-family:"Microsoft YaHei";font-size:18px;font-weight:bold;background-color:#E9006D}
    div.boxcontent-2{margin:0px;padding:4px 0px 0px 0px;width:1050px;height:auto;border-top:6px solid #FF4DA0}
    div.listbox{margin:8px 8px 8px 8px;padding:2px;width:240px;height:125px;font-family:"Microsoft YaHei";box-shadow:0px 0px 3px 3px #888888}
    div.listboxtitle{margin:0px;padding:0px 0px 4px 0px;font-family:"Microsoft YaHei";font-size:15px;font-weight:bold;box-shadow:0px -4px 0px #FF4DA0 inset;white-space:nowrap;text-overflow:ellipsis;overflow:hidden}
    div.listboxtitle:hover{text-overflow:inherit;overflow:visible}
    div.listboxbutton{margin:0px 0px 4px 2px;padding:10px 0px 0px 0px;width:108px;height:28px;color:#FFFFFF;text-align:center;font-family:"Microsoft YaHei";font-size:14px;font-weight:bold;background-color:#1FAAFF}
    div.listboxcontent{height:95px;font-family:"Microsoft YaHei";font-size:12px;background-color:#DDDDDD;overflow:auto}
</style>
    ';
	    if ($info['isbangumi']==1)
		{
		$sptype = '专题';
		}
	    if ($info['isbangumi']==1)
		{
		if ($info['isbangumi_end']==0)
		    $sptype = '二次元新番';
		if ($info['isbangumi_end']==1)
		    $sptype = '二次元完结';
		}
	    if ($info['isbangumi']==2)
		{
		if ($info['isbangumi_end']==0)
		    $sptype = '三次元新番';
		if ($info['isbangumi_end']==1)
		    $sptype = '三次元完结';
		}
	    echo '<div class="header"><div class="sptype">【'.$sptype.'】</div> <div class="title">'.$_GET['title'].'</div><div class="subtitle">&nbsp; - 专题列表</div></div><div class="info"><img style="width:128px" src="'.$info['cover'].'"><br/><br/>点击：'.$info['view'].'<br/>订阅：'.$info['favourite'].'<br/>关注度：'.$info['attention'].'<br/>投稿数：'.$info['count'].'<br/>更新：'.$info['lastupdate_at'].'<br/><br/>显示类型：<select onchange="location.href = this.options[this.selectedIndex].value;"><option value="/api/do.php?act=viewsp&id='.$_GET['id'].'&title='.$_GET['title'].'&bangumi=0"';
	    if ($_GET['bangumi']=='1'){
	    	echo '>相关视频</option><option value="/api/do.php?act=viewsp&id='.$_GET['id'].'&title='.$_GET['title'].'&bangumi=1" selected="selected';
	    }else{
	    	echo ' selected="selected">相关视频</option><option value="/api/do.php?act=viewsp&id='.$_GET['id'].'&title='.$_GET['title'].'&bangumi=1';
	    }
	    echo '">番剧</option></select></div><div class="description">　　'.$info['description'].'</div><hr><div class="list">';
	    $number = 0;
	    while (isset($list['list'][$number]['aid']))
		{
		echo '<div class="listbox" style="float:left"><div class="listboxtitle">'.$list['list'][$number]['title'].'</div><table style="width:100%;border:0px"><tr><td style="width:120px;text-align:center"><img style="max-width:120px;max-height:90px" src="'.$list['list'][$number]['cover'].'"></td><td><a href="/api/getaid.php?act=info&av='.$list['list'][$number]['aid'].'"><div class="listboxbutton">下载视频文件</div></a><a href="/api/do.php?act=play&av='.$list['list'][$number]['aid'].'&page=1&player=bilibili"><div class="listboxbutton">播放弹幕视频</div></a></td></tr></table></div>';
		$number = $number+1;
		}
	    echo '</div>';
	    }
	if ($_GET["act"]=='logout')
	    {
	    echo '<script language="javascript" type="text/javascript">window.location.href="/"</script>';
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

