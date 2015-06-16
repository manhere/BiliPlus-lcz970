<?php
$urls = explode('|',$_POST['urls']);
foreach($urls as $url)
echo urldecode($url).'<br/>';