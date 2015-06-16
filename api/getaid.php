<?php setcookie ("visiturl",$_SERVER['REQUEST_URI'],time()+3600*24*7,"/"); ?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>BiliPlus - ( ゜- ゜)つロ 乾杯~</title>
<?php  
/* Require Config Files */
require dirname(dirname(__FILE__)).'/task/config.php';
require dirname(dirname(__FILE__)).'/task/mysql.php';
require dirname(dirname(__FILE__)).'/include/functions.php';
/* Core Function For Fetching Data From Bilibili */
function UpdateCache($av,$appkey,$appsecret)
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
		$sign = get_sign(array("type"=>"json","appkey"=>$appkey,"id"=>$av,"batch"=>"1","access_key"=>$_COOKIE["access_key"]),$appsecret);
		$apiurl = 'http://api.bilibili.com/view?type=json&appkey='.$appkey.'&id='.$av.'&batch=1&access_key='.$_COOKIE["access_key"].'&sign='.$sign;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL,$apiurl);
	curl_setopt($curl, CURLOPT_ENCODING, "gzip");
	curl_setopt($curl, CURLOPT_HEADER,0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
	//curl_setopt($curl, CURLOPT_FOLLOWLOCATION,1);
	$apijson = curl_exec($curl);
	curl_close($curl);
	$info = json_decode($apijson,true);
	if (!array_key_exists("code",$info))
	{
		if (empty($info['list'][0]['cid']))
		{
			$error = 3;
			$e_text = '<div class="framesubtitle">视频解析错误：网络错误</div><div class="errordescription">服务器无法获取正确的视频接口数据格式。<br/>可能原因是服务器与哔哩哔哩接口的网络连接不畅或网络不稳定，请尝试刷新数据。</div>';
		}
	}
	else
	    {
	    if ($info["code"]=="-403")
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
	$apijson = '';
	$title = 'AV'.$_GET["av"].' - 数据更新';
	UpdateCache($_GET["av"],'27eb53fc9058f8c3','c2ed53a74eeefe3cf99fbd01d8c9c375');
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
	if (empty($_GET["av"])||preg_match("/^[1-9][0-9]*$/",$_GET["av"]))
	    {
		$title = 'AV'.$_GET["av"].' - 分P列表';
		if (!empty($_GET["av"]))
		    {
		    $id = $_GET["av"];
		    $file = file_exists("../cache/$id.json");
		    if (empty($file))
			{
			UpdateCache($_GET["av"],'27eb53fc9058f8c3','c2ed53a74eeefe3cf99fbd01d8c9c375');
			}
		    else
			{
		  $apijson = json_decode(file_get_contents("../cache/$id.json"),true);
			$datatime = $apijson['LASTUPDATE'];
			$info = $apijson['DATA'];
			if (!array_key_exists("code",$info))
			    {
				$videotitle = $info['title'];
				foreach($info['list'] as $i=>$useless){unset($info['list'][$i]['has_alias']);$info['list'][$i]['part']=urlencode($info['list'][$i]['part']);}
				$pagesarrayjson = urldecode(json_encode($info['list']));
				/*
				$p = 0;
				$pagelist = '';
				while (isset($info['list'][$p]['cid']))
						{
				if (!empty($info['list'][$p]['vid']))
				    $vid = $info['list'][$p]['vid'];
				else
				    $vid = '不可用';
				    $weblink = '';
				    if (isset($info['list'][$p]['weblink']))
				    $weblink = '<div class="infolist">网页链接：'.$info['list'][$p]['weblink'].'</div>';
						  $getmethod = 'geturl';
						  $from_real = $info['list'][$p]['type'];
						  $play = '<a href="/api/do.php?act=play&av='.$_GET["av"].'&page='.($p+1).'&player=bilibili"><div class="filelist">在线播放</div></a>';
						  $bqvideo = array('td','sohu','pptv','mletv','youku');
						  foreach ($bqvideo as $checkfrom) {
						  	if ($from_real==$checkfrom) 
						  	$play = '<a href="/api/bqplay.php?av='.$_GET["av"].'&page='.($p+1).'"><div class="filelist">在线播放</div></a>';;
						  }
					    if($from_real=='sina') $from='新浪视频';
					    if($from_real=='youku') {$from='优酷视频';$getmethod = 'youku';}
					    if($from_real=='tudou') $from='土豆视频';
					    if($from_real=='qq') $from='腾讯视频';
					    if($from_real=='local') $from='哔哩哔哩';
					    if($from_real=='letv') $from='乐视云';
					    if($from_real=='mletv') {$from='乐视版权';$getmethod = 'letv';}
					    if($from_real=='sohu') {$from='搜狐版权';$getmethod = 'sohu';}
					    if($from_real=='pptv') {$from='PPTV版权';$getmethod = 'pptv';}
					    if($from_real=='iqiyi-fix') $from='爱奇艺版权转投';
					    if($from_real=='iqiyi') $from='爱奇艺版权';
					    if($from_real=='td') {$from='优土豆版权';$getmethod = 'tudou';}
					    if($from_real=='vupload') $from='哔哩哔哩';
					    if(empty($from)) $from = '未知';
						$pagelist = $pagelist.'<a href="/api/'.$getmethod.'.php?act=info&av='.$_GET["av"].'&page='.($p+1).'"><div class="filelist">获取视频地址</div></a>'.$play.'<div class="listbox">[P'.($p+1).'] '.$info['list'][$p]['part'].'</div><div class="infolist">CID：'.$info['list'][$p]['cid'].'</div><div class="infolist">视频源：'.$from.'</div><div class="infolist">VID：'.$vid.'</div>'.$weblink.'<br/>';
						$p++;
						}
						*/
						$author = $info['author'];
					  $authorid = $info['mid'];
					  $videoplay = $info['play'];
					  $videodanmu = $info['video_review'];
					  $videoscore = $info['credit'];
					  $videocoin = $info['coins'];
					  $videofavorite = $info['favorites'];
					  $videotime = $info['created_at'];
					  $videoimg = $info['pic'];
					  $tags = explode(',',$info['tag']);
					  $videotag = '';
					  foreach ($tags as $tag)
					  $videotag = $videotag.'<a href="/api/do.php?act=search&word='.$tag.'&o=default&n=20&p=1">'.$tag.'</a> ';
					  $videodesc = nl2br($info['description']);
					  $videotype = $info['typename'];
					  $title = $videotitle.' - AV'.$_GET["av"].' - 下载';
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
	else
	    {
	    $error = '<div class="framesubtitle">Bad Request</div><div class="errordescription">参数格式错误，AV号请输入纯数字。</div>';
	    }
	}
?>
<meta name="author" content="Tundra" />
<meta name="Copyright" content="Copyright Tundra All Rights Reserved." />
<?php
if (($update==1)&&($error==0))
echo '<meta http-equiv="refresh" content="0; url='.str_ireplace('&update=1','',$_SERVER['REQUEST_URI']).'" />';
?>
<meta name="keywords" content="BiliPlus,哔哩哔哩,Bilibili,下载,播放,弹幕,音乐,黑科技,HTML5" />
<meta name="description" content="哔哩哔哩投稿视频、弹幕、音乐下载，更换弹幕播放器，简明现代黑科技 - BiliPlus - ( ゜- ゜)つロ 乾杯~" />
<link rel="icon" href="/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<script language="JavaScript">document.title = "<?php echo $title; ?> - BiliPlus - ( ゜- ゜)つロ 乾杯~";</script>
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
	$id = $_GET["av"];
	$filedata = json_encode(array("ID"=>$id,"SUCCESS"=>"1","DATA"=>json_decode($apijson),LASTUPDATE=>$time));
	$file=fopen("../cache/$id.json","w");
	fwrite($file,$filedata);
	fclose($file);
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

	    echo '<div class="videotitle">AV'.$_GET["av"].' - '.$videotitle.'</div>
<table width="100%" border="0" cellpadding="4px">
    <tr>
	<td width="15%" style="text-align:center;color:#FFFFFF;font-family:SimHei;font-size:16px;font-weight:bold;background-color:#666666">[ '.$videotype.' ]</td>
	<td width="85%" style="text-align:left;color:#FFFFFF;font-family:SimHei;font-size:16px;font-weight:bold;background-color:#666666">UP主：<a href="http://space.bilibili.com/'.$authorid.'" target="_blank">'.$author.'</a>（UID：'.$authorid.'）</td>
    </tr>
</table>
<div class="videotime">投稿时间：'.$videotime.'</div><div class="docinfo">播放:'.$videoplay.' | 弹幕:'.$videodanmu.' | 积分:'.$videoscore.' | 硬币:'.$videocoin.' | 收藏:'.$videofavorite.'</div><br/>
<br/><div class="videodesc">'.$videodesc.'</div>
<div class="infolist">Tag：'.$videotag.'</div><br/>
<a href="http://www.bilibili.com/video/av'.$_GET['av'].'/" title="主站地址跳转" target="_blank"><div class="filelist">主站页面</div></a><a href="bilibili://?av='.$_GET['av'].'" title="手机客户端跳转"><div class="filelist">手机客户端</div></a>
<div><br/></div><div class="hrdescription">封面</div><hr>
<img src='.$videoimg.'>
<div><br/></div><div class="hrdescription">分P列表</div><hr><div class="pagelist"><script>
av='.$_GET['av'].';
items='.$pagesarrayjson.';

for (var i=0;i<items.length;i++)
{
	page=items[i]["page"];
	from=items[i]["type"];
	part=items[i]["part"];
	cid=items[i]["cid"];
	vid=items[i]["vid"];
	if(vid==\'\'){vid=\'不可用\';}
	
	bq=\'geturl\';isbq=0;player=\'\';
	switch(from){
		case \'sina\':from=\'新浪视频\';break;
		case \'youku\':from=\'优酷视频\';isbq=1;bq=\'youku\';break;
		case \'tudou\':from=\'土豆视频\';break;
		case \'qq\':from=\'腾讯视频\';break;
		case \'local\':from=\'哔哩哔哩\';break;
		case \'letv\':from=\'乐视云\';break;
		case \'mletv\':from=\'乐视版权\';isbq=1;bq=\'letv\';break;
		case \'sohu\':from=\'搜狐版权\';isbq=1;bq=\'sohu\';break;
		case \'pptv\':from=\'PPTV版权\';isbq=1;bq=\'pptv\';break;
		case \'iqiyi-fix\':from=\'爱奇艺版权转投\';player=\'_bili\';break;
		case \'iqiyi\':from=\'爱奇艺版权\';break;
		case \'td\':from=\'优土豆版权\';isbq=1;bq=\'tudou\';break;
		case \'vupload\':from=\'哔哩哔哩\';break;
		default:from=\'未知\';
	}
	down=\'<a href="/api/\'+bq+\'.php?act=info&av=\'+av+\'&page=\'+page+\'"><div class="filelist">获取视频地址</div></a>\';
	play=\'<a href="/api/do.php?act=play&av=\'+av+\'&page=\'+page+\'&player=bilibili\'+player+\'"><div class="filelist">在线播放</div></a>\';
	if(isbq==1){play=\'<a href="/api/bqplay.php?av=\'+av+\'&page=\'+page+\'"><div class="filelist">在线播放</div></a>\';}
	document.write(down+play+\'<div class="listbox">[P\'+page+\'] \'+part+\'</div><div class="infolist">来源：\'+from+\'</div><div class="infolist">CID：\'+cid+\'</div><div class="infolist">VID：\'+vid+\'</div><br>\');
}

</script>
</div><br/><div class="footer">本页面为缓存数据，数据缓存时间：'.$datatime.' <div class="update"><a href="'.$_SERVER['REQUEST_URI'].'&update=1">刷新数据</a></div></div>
<br/>';
	    
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

