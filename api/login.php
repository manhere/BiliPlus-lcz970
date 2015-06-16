<?php
header("Content-Type: text/html; charset=utf-8");
function GetUID()
	{
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$string = '';
	for ($i=0;$i<10;$i++)
		{
		$string .= $chars[mt_rand(0,strlen($chars)-1)];
		}
	return $string;
	}
if ($_GET['act']=='reg')
    {
    $login = 1;
    $uid = GetUID();
    $mid = '0';
    $uname = '游客'.$uid;
    $access_key = '45e1c24ae50d12c1d3f74a9b19d701ab';
    setcookie ("login",$login,time()+3600*24,"/");
    setcookie ("uid",$uid,time()+3600*24,"/");
    setcookie ("mid",$mid,time()+3600*24,"/");
    setcookie ("uname",$uname,time()+3600*24,"/");
    setcookie ("access_key",$access_key,time()+3600*24,"/");
    setcookie ("visiturl",$_GET['url'],time()+3600*24,"/");
    echo '<!DOCTYPE html><html><head><meta name=viewport content=width=device-width,initial-scale=1.0><meta http-equiv="Content-Type" content="text/html;charset=UTF-8"><title>访客控制系统 - BiliPlus</title></head><body>欢迎来到BiliPlus，正在生成随机登录信息…<script language="javascript" type="text/javascript">window.location.href="'.$_GET['url'].'"</script></body></html>';
    exit();
    }
if ($_GET['act']=='visit')
    {
    setcookie ("visiturl",$_GET['url'],time()+3600*24,"/");
    exit();
    }
if (empty($_GET['act']))
    {
    if (empty($_GET['access_key'])||empty($_GET['mid'])||empty($_GET['uname']))
        {
        echo 'Bad Request';
        exit();
        }
    else
        {
        $login = 2;
        setcookie ("login",$login,time()+3600*24*28,"/");
        setcookie ("mid",$_GET['mid'],time()+3600*24*28,"/");
        setcookie ("uname",$_GET['uname'],time()+3600*24*28,"/");
        setcookie ("access_key",$_GET['access_key'],time()+3600*24*28,"/");
        echo '<!DOCTYPE html><html><head><meta name=viewport content=width=device-width,initial-scale=1.0><meta http-equiv="Content-Type" content="text/html;charset=UTF-8"><title>访客控制系统 - BiliPlus</title></head><body>欢迎回来，正在为您设置登录cookie，cookie有效期28天<script language="javascript" type="text/javascript">window.location.href="'.$_COOKIE['visiturl'].'"</script></body></html>';
        }
    }
if ($_GET["act"]=='logout')
    {
    $login = 1;
    $uname = '游客'.$_COOKIE['uid'];
    setcookie ("login",$login,time()-3600,"/");
    setcookie ("access_key",'',time()-3600,"/");
    setcookie ("mid",'',time()-3600,"/");
    setcookie ("uname",$uname,time()+3600*24,"/");
    echo '<!DOCTYPE html><html><head><meta name=viewport content=width=device-width,initial-scale=1.0><meta http-equiv="Content-Type" content="text/html;charset=UTF-8"><title>访客控制系统 - BiliPlus</title></head><body>感谢使用，正在为您清除登录cookie<script language="javascript" type="text/javascript">window.location.href="'.$_COOKIE['visiturl'].'"</script></body></html>';
    }
?>