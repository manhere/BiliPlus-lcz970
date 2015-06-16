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

    $update = 0;
	if (empty($_GET["cid"])||preg_match("/^[1-9][0-9]*$/",$_GET["cid"]))
	    {
		$title = 'CID'.$_GET["cid"].' - 播放';
				$cid = $_GET["cid"];
				if ($_GET["player"]=='bilibili')
				    {
				    $divplay = '<iframe height="650" width="100%" src="/play/do.php?player=bilibili&cid='.$cid.'" scrolling="no" border="0" frameborder="no" framespacing="0"></iframe>';
				    }
				if ($_GET["player"]=='bilibili_bili')
				    {
				    $divplay = '<iframe height="650" width="100%" src="/play/do.php?player=bilibili_bili&cid='.$cid.'" scrolling="no" border="0" frameborder="no" framespacing="0"></iframe>';
				    }
	    }
	else
	    {
	    $error = '<div class="framesubtitle">Bad Request</div><div class="errordescription">参数格式错误，CID号请输入纯数字。</div>';
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
	$pagedata = mysql_real_escape_string($apijson);
	$pagemp4 = mysql_real_escape_string($mp4json);
	$videodata = mysql_real_escape_string($videoxml);
	//mysql_query("INSERT INTO CACHE_PAGE (ID,SUCCESS,DATA,MP3,MP4,LASTUPDATE) VALUES ('{$id}','1','{$pagedata}','{$mp3file}','','{$time}') ON DUPLICATE KEY UPDATE SUCCESS=VALUES(SUCCESS),DATA=VALUES(DATA),LASTUPDATE=VALUES(LASTUPDATE)");
	mysql_query("INSERT INTO CACHE_VIDEO (CID,SUCCESS,DATA,LASTUPDATE,MP4) VALUES ('{$cid}','1','{$videodata}','{$time}','{$pagemp4}') ON DUPLICATE KEY UPDATE SUCCESS=VALUES(SUCCESS),DATA=VALUES(DATA),LASTUPDATE=VALUES(LASTUPDATE),MP4=VALUES(MP4)");
	echo '<br/>
<div class="frametitle">数据获取成功</div><br/><br/>
<div class="boxtitle-1">请稍后</div>
<div class="boxcontent-1">
<div class="errordescription"><b>成功从哔哩哔哩开放平台获取数据</b></div><br/>
正在刷新页面，请稍候...<br/><br/>
<div class="buttonbackground"><input type="button" class="button" value="刷新" onclick="document.location.reload()">　<input type="button" class="button" value="帮助" onclick="window.open(\'/?about\')"></div>
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
	echo '<br/>
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
<div class="videotitle">CID'.$_GET["cid"].'</div>
<div><br/></div><div class="hrdescription">播放</div><hr>';
		echo '<div class="alert"><div class="alertbox1">如果遇到无法播放请尝试使用其他视频源</div><a href="/api/cidplay.php?act=play&cid='.$_GET["cid"].'&player=bilibili"><div class="alertbox2">哔哩哔哩原始播放器</div></a><a href="/api/cidplay.php?act=play&cid='.$_GET["cid"].'&player=bilibili_bili"><div class="alertbox2">爱奇艺版权修复播放器</div></a></div>';
		echo '<div class="player">'.$divplay.'</div><div><br/></div>
<div class="hrdescription">视频弹幕下载</div><hr><div class="downloadlink"><div class="tip">下载源视频、MP4视频、XML/ASS弹幕文件</div><a href="/api/cid.php?cid='.$_GET['cid'].'" title="视频弹幕下载页面"><div class="filelist">下载视频/弹幕文件</div></a></div><div><br/></div><br/></div>
<br/>
';
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

