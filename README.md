# BiliPlus - lcz970

这是一个二次元爱好者搭建的BiliPlus服务器，并对网站源文件进行了一些编辑，原始BiliPlus地址：[https://github.com/TundraWork/BiliPlus](https://github.com/TundraWork/BiliPlus)

## 与原始相比所做更改
=下载链接改为直链，而非https跳转页面
+下载页面的在线观看选项添加了“主站”和“手机客户端”的链接

## BiliPlus

*BiliPlus* is a Bilibili API Helper site based on PHP.

It can provide you with Bilibili video play&amp;download, hot list&amp;bangumi list, video&amp;special subject search, special subject view and other functions based on Open API and some other interfaces of Bilibili.

## License

Copyright (c) 2014 TundraWork, under the 
[GNU AFFERO GENERAL PUBLIC LICENSE, Version 3 (AGPL-3.0)](http://opensource.org/licenses/AGPL-3.0).

PLEASE NOTE that the license requires you to public your version of the source code if you do any modifications on it. You can simply create a fork and commit your versions.

## Support

We are now working on a new project and, our update on this project can be very slow.

However, please feel free to submit any BUGs.

## System Require

1. PHP 5.3 or higher
2. MySQL Server of any version
3. A Domain with SSL support(HTTPS protocol)

## Preparing For Install

0. PLEASE NOTE : Do not use AppKey, AppSecret and etc. in old versions of our code, they can only use on our domain "bilicloud.com".
1. Register your API Key on Bilibili Open Api Platform, just visit this link(you need to login first): [http://www.bilibili.com/account/getapi](http://www.bilibili.com/account/getapi)
2. Enter your site name after "网站名称", and enter "http://(Your Site's Domain)/api/login.php" after "网站地址".
3. The page will automatically generate a link of a html file, download&move it into the root directory of your site.
4. Click the button below, and the page will give you your PublicKey, AppKey, AppSecret & 3rdLoginURL("第3方登录请求地址"), save them in a file for further using.
5. Edit "/task/config.php", fill AppKey, AppSecret, 3rdLoginURL using those you saved just now.

## Install And Config

0. PLEASE NOTE : Duo to the data distribution problem of bilibili CDN server, servers in many areas CAN NOT get the correct API/Interface data, so you should choose servers in a good network environment, good luck!
1. We recommend you to use an empty MySQL database and run BiliPlus in a new web server.
2. Edit "/task/mysql.php" and add your MySQL server info so that we can connent to your database.
3. Copy all files to your web server's root document directory.
4. Run "/task/install.php" to set up database.
5. Run "/task/createlist.php" to create data cache for some video lists.
6. Run "/task/getlist.php" to update cache data of the video lists.
7. Create a Cron task for "/task/getlist.php", we recommend you to run it every one hour.
8. (optional) For security reasons, disable the visitor's access permission of "/task" directory.

## Enjoy It

That's all. If you find any BUGs in BiliPlus or have any suggestions, just Submit an issue or Contact us.

Then you can have a good time! Or star BiliPlus if you like :)


