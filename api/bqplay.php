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
			$datatime = $apijson['LASTUPDATE'];
			$info = $apijson['DATA'];
			$bqsrc = array('td','sohu','pptv','mletv','youku');
			$from = $info['list'][($page-1)]['type'];
			$vid = $info['list'][($page-1)]['vid'];
			$videotitle = $info['title'];
			$parttitle = $info['list'][($page-1)]['part'];
			$author = $info['author'];
			$authorid = $info['mid'];
		  $videoplay = $info['play'];
			$videodanmu = $info['video_review'];
			foreach($info['list'] as $i=>$useless){$parts['list'][$i]=urlencode($info['list'][$i]['part']);}
			$pagesarrayjson = urldecode(json_encode($parts['list']));
			/*
			$p = 0;
			$pagelist = '';
			while (isset($info['list'][$p]['cid']))
		{
			$pagelist = $pagelist.'<a href="/api/bqplay.php?av='.$_GET["av"].'&page='.$info['list'][$p]['page'].'"><div class="listbox">[P'.($p+1).'] '.$info['list'][$p]['part'].'</div></a>';
			$p++;
		}
		  */
		  $videodescription = $info['description'];
		  $videoscore = $info['credit'];
		  $videocoin = $info['coins'];
		  $videofavorite = $info['favorites'];
		  $videotime = $info['created_at'];
		  $title = $videotitle.' - AV'.$_GET["av"].' - 下载';
			foreach ($bqsrc as $checkfrom) {
				if ($from==$checkfrom) 
				$bq = 1;
			}
			$cid = $info['list'][($page-1)]['cid'];
			$vid = $info['list'][($page-1)]['vid'];
			if (empty($_GET['vtype']))$vtype = 'super'; else $vtype = $_GET['vtype'];
			if ($bq==1){
				switch($from){
				case 'sohu':
					$vid = explode('|',$vid);
					$vid = $vid[1];
				break;
				case 'td':
					$weblink = $info['list'][($page-1)]['weblink'];
					$weblink1 = explode('/',$weblink);
					$tdalbum = $weblink1[4];
					$tdvcode = explode('.',$weblink1[5]);
					$tdvcode = $tdvcode[0];
					$vid = $tdalbum.'/'.$tdvcode;
				break;
				case 'pptv':
					$vid = $info['list'][($page-1)]['vid'];
					$vid = explode("/",$vid);
					$vid = explode(".",$vid[4]);
					$vid = $vid[0];
					if (empty($_GET['vtype']))$vtype = 'high';
				break;
				}
				$divplay = '<iframe height="700" width="100%" src="/play/bqdo.php?av='.$_GET['av'].'&page='.$_GET['page'].'&cid='.$cid.'&src='.$from.'&vid='.$vid.'&vtype='.$vtype.'" scrolling="no" border="0" frameborder="no" framespacing="0"></iframe>';
			}
			else
			$divplay = '<meta http-equiv="refresh" content="1; url=/api/do.php?act=play&av='.$_GET['av'].'&page='.$_GET['page'].'&player=bilibili" />';
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
<div class="videotime">投稿时间：'.$videotime.'</div><div class="videoinfo">播放:'.$videoplay.' | 弹幕:'.$videodanmu.' | 评分:'.$videoscore.' | 硬币:'.$videocoin.' | 收藏:'.$videofavorite.'</div><br/><div><br/></div><div class="hrdescription">播放</div><hr><div class="player">'.$divplay.'</div><div class="hrdescription">视频简介</div><hr><div class="videodescription">'.$videodescription.'</div><br/>
<div class="hrdescription">视频弹幕下载</div><hr><div class="downloadlink"><div class="tip">下载源视频、MP4视频、XML/ASS弹幕文件</div><a href="/api/geturl.php?act=info&av='.$_GET['av'].'&page='.$_GET['page'].'" title="视频弹幕下载页面"><div class="filelist">下载视频/弹幕文件</div></a></div><div class="hrdescription">分P列表</div><hr><div class="pagelist"><script>
av='.$_GET['av'].';
items='.$pagesarrayjson.';

for (var i=0;i<items.length;i++)
{
	page=i+1;
	part=items[i];
	document.write(\'<a href="/api/bqplay.php?act=play&av=\'+av+\'&page=\'+page+\'"><div class="listbox">[P\'+page+\'] \'+part+\'</div></a>\');
}

</script></div><div><br/></div><br/>
<div class="footer">本页面为缓存数据，数据缓存时间：'.$datatime.' <div class="update"><a href="'.$_SERVER['REQUEST_URI'].'&update=1">刷新数据</a></div></div>
<br/>
';
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
