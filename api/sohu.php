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
			if (($info['list'][($page-1)]['type'])=='sohu')
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
				$proxy = getproxy();
				$vid = $info['list'][($page-1)]['vid'];
				$vid = explode("|",$vid);
				$vid = $vid[1];
				$api_url = "http://api.tv.sohu.com/v4/video/info/$vid.json?api_key=f351515304020cad28c92f70f002261c";
				$curl = curl_init();
		    curl_setopt($curl, CURLOPT_URL,$api_url);
		    curl_setopt($curl, CURLOPT_PROXY, $proxy);
		    curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; MSIE 11.0; Windows NT 6.1; WOW64; Trident/6.0)');
		    curl_setopt($curl, CURLOPT_ENCODING, "gzip");
		    curl_setopt($curl, CURLOPT_HEADER,0);
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
		    $apijson = curl_exec($curl);
		    curl_close($curl);
				if (!empty($apijson)) {
				$video_datas = json_decode($apijson,true);
				$json = $video_datas["data"];
				$data = array();
				$qualitys = array("original"=>array("n"=>"原画","cx"=>"ori"),"super"=>array("n"=>"超清","cx"=>"super"),"high"=>array("n"=>"高清","cx"=>"high"),"nor"=>array("n"=>"标清","cx"=>"nor"));
				foreach($qualitys as $qt=>$qd){
					if($json["url_".$qt."_mp4"]){
						$files = array();
						$_seconds = explode(",",$json["clips_duration_".$qt]);
						$_urls = explode(",",$json["url_".$qt."_mp4"]);
						for($ix=0;$ix<count($_urls);$ix++){
							$files[] = array("seconds"=>intval($_seconds[$ix]),"url"=>$_urls[$ix]."&pt=1&prod=h5&pg=1&eye=0&cateCode=101");
						}
						$data[$qd["cx"]] = $files;
					}
				}
				if($json["download_url"]){$data['low'] = array(array("seconds"=>intval($json["total_duration"]),"url"=>$json["download_url"]."&pt=1&prod=h5&pg=1&eye=0&cateCode=101"));}
					if (isset($data['low'])) {
						$part = 1;
						foreach ($data['low'] as $partinfo) {
							$partlengthsec = $partinfo['seconds'];
							$partlength = str_pad(floor($partlengthsec/60),2,"0", STR_PAD_LEFT).':'.sprintf("%02d",round(fmod($partlengthsec,60),3));
							$urls['low']['out'] = $urls['low']['out'].'<a href="'.$partinfo['url'].'"><div class="filelist">[分段'.$part.'] '.$partlength.'</div></a>';
							$urls['low']['urls'] = $urls['low']['urls'].urlencode($partinfo['url']).'|';
							$part++;
						}
					}
					if (isset($data['nor'])) {
						$part = 1;
						foreach ($data['nor'] as $partinfo) {
							$partlengthsec = $partinfo['seconds'];
							$partlength = str_pad(floor($partlengthsec/60),2,"0", STR_PAD_LEFT).':'.sprintf("%02d",round(fmod($partlengthsec,60),3));
							$urls['nor']['out'] = $urls['nor']['out'].'<a href="'.$partinfo['url'].'"><div class="filelist">[分段'.$part.'] '.$partlength.'</div></a>';
							$urls['nor']['urls'] = $urls['nor']['urls'].urlencode($partinfo['url']).'|';
							$part++;
						}
					}
					if (isset($data['high'])) {
						$part = 1;
						foreach ($data['high'] as $partinfo) {
							$partlengthsec = $partinfo['seconds'];
							$partlength = str_pad(floor($partlengthsec/60),2,"0", STR_PAD_LEFT).':'.sprintf("%02d",round(fmod($partlengthsec,60),3));
							$urls['high']['out'] = $urls['high']['out'].'<a href="'.$partinfo['url'].'"><div class="filelist">[分段'.$part.'] '.$partlength.'</div></a>';
							$urls['high']['urls'] = $urls['high']['urls'].urlencode($partinfo['url']).'|';
							$part++;
						}
					}
					if (isset($data['super'])) {
						$part = 1;
						foreach ($data['super'] as $partinfo) {
							$partlengthsec = $partinfo['seconds'];
							$partlength = str_pad(floor($partlengthsec/60),2,"0", STR_PAD_LEFT).':'.sprintf("%02d",round(fmod($partlengthsec,60),3));
							$urls['super']['out'] = $urls['super']['out'].'<a href="'.$partinfo['url'].'"><div class="filelist">[分段'.$part.'] '.$partlength.'</div></a>';
							$urls['super']['urls'] = $urls['super']['urls'].urlencode($partinfo['url']).'|';
							$part++;
						}
					}
					if (isset($data['ori'])) {
						$part = 1;
						foreach ($data['ori'] as $partinfo) {
							$partlengthsec = $partinfo['seconds'];
							$partlength = str_pad(floor($partlengthsec/60),2,"0", STR_PAD_LEFT).':'.sprintf("%02d",round(fmod($partlengthsec,60),3));
							$urls['ori']['out'] = $urls['ori']['out'].'<a href="'.$partinfo['url'].'"><div class="filelist">[分段'.$part.'] '.$partlength.'</div></a>';
							$urls['ori']['urls'] = $urls['ori']['urls'].urlencode($partinfo['url']).'|';
							$part++;
						}
					}
					} else {
						$error = 2;
						$e_text = '<div class="framesubtitle">Bad Sohu API JSON</div><div class="errordescription">搜狐视频地址获取出错，请尝试刷新重试。</div>';
					}
				
			}
			else
			{
				$error = 1;
				$e_text = '<div class="framesubtitle">Wrong Video Type</div><div class="errordescription">该分P不是搜狐版权。</div>';
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
<div><br/></div><div class="hrdescription">视频信息</div><hr><div class="videoinfo"><div class="infolist">CID：'.$cid.'</div><div class="infolist">视频源：搜狐视频</div><div class="infolist">搜狐VID：'.$vid.'</div></div>
<div><br/></div><div class="hrdescription">视频源文件</div><hr><div class="videofile"><fieldset><legend>源视频文件</legend>';
if (isset($urls['low'])) {
	echo '<form name="input" action="/api/urloutput.php" method="post" style="margin:0px;display: inline" target="_blank"><input type="hidden" name="urls" value="'.$urls['low']['urls'].'"><div class="tip">[单段-低清] <input type="image" src="/image/button.png" style="color:#EEEEEE;"></div>'.$urls['low']['out'].'</form>';
}
if (isset($urls['nor'])) {
	echo '<form name="input" action="/api/urloutput.php" method="post" style="margin:0px;display: inline" target="_blank"><input type="hidden" name="urls" value="'.$urls['nor']['urls'].'"><div class="tip">[分段-标清] <input type="image" src="/image/button.png" style="color:#EEEEEE;"></div>'.$urls['nor']['out'].'</form>';
}
if (isset($urls['high'])) {
	echo '<form name="input" action="/api/urloutput.php" method="post" style="margin:0px;display: inline" target="_blank"><input type="hidden" name="urls" value="'.$urls['high']['urls'].'"><div class="tip">[分段-高清] <input type="image" src="/image/button.png" style="color:#EEEEEE;"></div>'.$urls['high']['out'].'</form>';
}
if (isset($urls['super'])) {
	echo '<form name="input" action="/api/urloutput.php" method="post" style="margin:0px;display: inline" target="_blank"><input type="hidden" name="urls" value="'.$urls['super']['urls'].'"><div class="tip">[分段-超清] <input type="image" src="/image/button.png" style="color:#EEEEEE;"></div>'.$urls['super']['out'].'</form>';
}
if (isset($urls['ori'])) {
	echo '<form name="input" action="/api/urloutput.php" method="post" style="margin:0px;display: inline" target="_blank"><input type="hidden" name="urls" value="'.$urls['ori']['urls'].'"><div class="tip">[分段-1080P] <input type="image" src="/image/button.png" style="color:#EEEEEE;"></div>'.$urls['ori']['out'].'</form>';
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
