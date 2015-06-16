<?php setcookie ("visiturl",$_SERVER['REQUEST_URI'],time()+3600*24*7,"/"); ?><html>
<head>
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
/* Require Config Files */
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
if (preg_match("/^[1-9][0-9]*$/",$_GET["av"]))
	{
	if (!empty($_GET["page"]))
	    {
	    if (preg_match("/^[1-9][0-9]*$/",$_GET["page"]))
		{
		$page = $_GET["page"];
		}
	    else
		{
		$error = 1;
		$e_text = '<div class="framesubtitle">Bad Request</div><div class="errordescription">参数格式错误，页码请输入纯数字。</div>';
		}
	    }
	$apijson = '';
	$title = 'AV'.$_GET["av"].' - 下载';
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
			if (($info['list'][($page-1)]['type'])=='mletv')
			{
				$videotitle = $info['title'];
				$cid = $info['list'][($page-1)]['cid'];
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
				//$proxy = getproxy();
				$vid = $info['list'][($page-1)]['vid'];
				$tkey = letu_((time()));
				$api_url = 'http://api.letv.com/mms/out/video/playJson?id='.$vid.'&platid=1&splatid=101&format=0&tkey='.$tkey.'&domain=www.letv.com';
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL,$api_url);
				//curl_setopt($curl, CURLOPT_PROXY, $proxy);
				curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; MSIE 11.0; Windows NT 6.1; WOW64; Trident/6.0)');
				curl_setopt($curl, CURLOPT_ENCODING, "gzip");
				curl_setopt($curl, CURLOPT_HEADER,0);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
				$apijson = curl_exec($curl);
				curl_close($curl);
				if (!empty($apijson)) {
					$apijson = str_replace('tss=ios', 'tss=no', $apijson);
					$apijson = str_replace('platid=1&splatid=101', 'platid=14&splatid=1401', $apijson);
					$video_datas = json_decode($apijson,true);
					if (!empty($video_datas["playurl"]['dispatch'])) {
					$json = $video_datas["playurl"]['dispatch'];
					/*
					if (isset($_SERVER)){
						if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
							$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
						} else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
							$realip = $_SERVER["HTTP_CLIENT_IP"];
						} else {
							$realip = $_SERVER["REMOTE_ADDR"];
						}
					} else {
						if (getenv("HTTP_X_FORWARDED_FOR")){
							$realip = getenv("HTTP_X_FORWARDED_FOR");
						} else if (getenv("HTTP_CLIENT_IP")) {
							$realip = getenv("HTTP_CLIENT_IP");
						} else {
							$realip = getenv("REMOTE_ADDR");
						}
					}
					*/
					$realip = false;
					if (isset($json['350'])) {
						$urllow = 'http://g3.letv.cn'.$json['350'][0].'&retry=1&tag=gug&sign=webdisk_19722818&termid=1&pay=0&ostype=windows&hwtype=un';
					}
					if (isset($json['1000'])) {
						$urlnor = 'http://g3.letv.cn'.$json['1000'][0].'&retry=1&tag=gug&sign=webdisk_19722818&termid=1&pay=0&ostype=windows&hwtype=un';
					}
					if (isset($json['1300'])) {
						$urlhigh = 'http://g3.letv.cn'.$json['1300'][0].'&retry=1&tag=gug&sign=webdisk_19722818&termid=1&pay=0&ostype=windows&hwtype=un';
					}
					if (isset($json['720p'])) {
						$urlsuper = 'http://g3.letv.cn'.$json['720p'][0].'&retry=1&tag=gug&sign=webdisk_19722818&termid=1&pay=0&ostype=windows&hwtype=un';
					}
					if (isset($json['1080p'])) {
						$urlori = 'http://g3.letv.cn'.$json['1080p'][0].'&retry=1&tag=gug&sign=webdisk_19722818&termid=1&pay=0&ostype=windows&hwtype=un';
					}
					} else {
						$error = 2;
						$e_text = '<div class="framesubtitle">Bad Letv API JSON</div><div class="errordescription">乐视视频地址获取出错，请尝试刷新重试。</div>';
					}
					} else {
						$error = 2;
						$e_text = '<div class="framesubtitle">Bad Letv API JSON</div><div class="errordescription">乐视视频地址获取出错，请尝试刷新重试。</div>';
					}
				
			}
			else
			{
				$error = 1;
				$e_text = '<div class="framesubtitle">Wrong Video Type</div><div class="errordescription">该分P不是乐视版权。</div>';
			}
		}
	}
	else
	{
	$error = 1;
	$e_text = '<div class="framesubtitle">Bad Request</div><div class="errordescription">参数格式错误，AV号请输入纯数字。</div>';
	}

echo '<script language="JavaScript">document.title = "'.$title.';  - BiliPlus - ( ゜- ゜)つロ 乾杯~";</script>
<div id="content" class="content">';
if (isset($error))
{
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
	echo '<br/>
<div class="frametitle">糟糕，出错啦！</div><br/><br/>
<div class="boxtitle-1">详细错误信息</div>
<div class="boxcontent-1">'.$e_text.'</div><br/>
<div class="buttonbackground"><input type="button" class="button" value="返回" onclick="history.go(-1)">　<input type="button" class="button" value="刷新" onclick="document.location.reload()">　<input type="button" class="button" value="帮助" onclick="window.open(\'/?about\')"></div>
';
	}
}
else
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
<div><br/></div><div class="hrdescription">视频信息</div><hr><div class="videoinfo"><div class="infolist">CID：'.$cid.'</div><div class="infolist">视频源：乐视视频</div><div class="infolist">乐视VID：'.$vid.'</div></div>
<div><br/></div><div class="hrdescription">视频源文件</div><hr><div class="videofile"><fieldset><legend>源视频文件</legend>';
if (isset($urllow)) {
	echo '<a href="'.$urllow.'" target="_blank"><div class="filelist">[低清]</div></a>';
}
if (isset($urlnor)) {
	echo '<a href="'.$urlnor.'" target="_blank"><div class="filelist">[标清]</div></a>';
}
if (isset($urlhigh)) {
	echo '<a href="'.$urlhigh.'" target="_blank"><div class="filelist">[高清]</div></a>';
}
if (isset($urlsuper)) {
	echo '<a href="'.$urlsuper.'" target="_blank"><div class="filelist">[720P]</div></a>';
}
if (isset($urlori)) {
	echo '<a href="'.$urlori.'" target="_blank"><div class="filelist">[1080P]</div></a>';
}
echo '</fieldset><div><br/></div>
<div class="hrdescription">弹幕下载</div><hr><div class="danmufile"><div class="tip">如果浏览器直接打开了文件，请右键链接→选择“另存为”</div><a href="'.$danmakuxml.'" target="_blank" title="下载XML格式弹幕文件（哔哩哔哩原始弹幕文件）"><div class="filelist">XML格式弹幕</div></a><a href="'.$danmakuass.'" target="_blank" title="下载ASS格式弹幕文件（适用于本地播放器）"><div class="filelist">ASS格式弹幕</div></a></div><br/>
<div class="hrdescription">在线播放</div><hr><div class="playlink"><div class="tip">请选择播放器在线播放弹幕视频</div><a href="http://www.bilibili.com/video/av'.$_GET['av'].'/index_'.$page.'.html" title="主站地址跳转" target="_blank"><div class="filelist">B站</div></a><a href="bilibili://?av='.$_GET['av'].'" title="手机客户端跳转"><div class="filelist">手机客户端</div></a></div><br/>
<br/>';
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
