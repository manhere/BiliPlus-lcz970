<?php
/* XML To Array Function For Parsing Bilibili Interface XML Doc */
class XML2Array {
    private static $xml = null;
	private static $encoding = 'UTF-8';
    /**
     * Initialize the root XML node [optional]
     * @param $version
     * @param $encoding
     * @param $format_output
     */
    public static function init($version = '1.0', $encoding = 'UTF-8', $format_output = true) {
	self::$xml = new DOMDocument($version, $encoding);
	self::$xml->formatOutput = $format_output;
		self::$encoding = $encoding;
    }
    /**
     * Convert an XML to Array
     * @param string $node_name - name of the root node to be converted
     * @param array $arr - aray to be converterd
     * @return DOMDocument
     */
    public static function &createArray($input_xml) {
	$xml = self::getXMLRoot();
		if(is_string($input_xml)) {
			$parsed = $xml->loadXML($input_xml);
			if(!$parsed) {
throw new Exception('[XML2Array] Error parsing the XML string.');
			}
		} else {
			if(get_class($input_xml) != 'DOMDocument') {
throw new Exception('[XML2Array] The input XML object should be of type: DOMDocument.');
			}
			$xml = self::$xml = $input_xml;
		}
		$array[$xml->documentElement->tagName] = self::convert($xml->documentElement);
	self::$xml = null;    // clear the xml node in the class for 2nd time use.
	return $array;
    }
    /**
     * Convert an Array to XML
     * @param mixed $node - XML as a string or as an object of DOMDocument
     * @return mixed
     */
    private static function &convert($node) {
		$output = array();
		switch ($node->nodeType) {
			case XML_CDATA_SECTION_NODE:
// we do not need cdata nodes, so we disabled it
//$output['@cdata'] = trim($node->textContent);
$output = trim($node->textContent);
break;
			case XML_TEXT_NODE:
$output = trim($node->textContent);
break;
			case XML_ELEMENT_NODE:
// for each child node, call the covert function recursively
for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
	$child = $node->childNodes->item($i);
	$v = self::convert($child);
	if(isset($child->tagName)) {
		$t = $child->tagName;
		// assume more nodes of same kind are coming
		if(!isset($output[$t])) {
			$output[$t] = array();
		}
		$output[$t][] = $v;
	} else {
		//check if it is not an empty text node
		if($v !== '') {
			$output = $v;
		}
	}
}
if(is_array($output)) {
	// if only one node of its kind, assign it directly instead if array($value);
	foreach ($output as $t => $v) {
		if(is_array($v) && count($v)==1) {
			$output[$t] = $v[0];
		}
	}
	if(empty($output)) {
		//for empty nodes
		$output = '';
	}
}
// loop through the attributes and collect them
if($node->attributes->length) {
	$a = array();
	foreach($node->attributes as $attrName => $attrNode) {
		$a[$attrName] = (string) $attrNode->value;
	}
	// if its an leaf node, store the value in @value instead of directly storing it.
	if(!is_array($output)) {
		$output = array('@value' => $output);
	}
	$output['@attributes'] = $a;
}
break;
		}
		return $output;
    }
    /*
     * Get the root XML node, if there isn't one, create it.
     */
    private static function getXMLRoot(){
	if(empty(self::$xml)) {
	    self::init();
	}
	return self::$xml;
    }
}
/* Sign Generate Function For Bilibili API Interface */
function get_sign($params, $key)
    {
    $_data = array();
    ksort($params);
    reset($params);
    foreach ($params as $k => $v)
	{
	$_data[] = $k . '=' . rawurlencode($v);
	}
    $_sign = implode('&', $_data);
    return strtolower(md5($_sign.$key));
    }
/* 获取代理服务器 */
function getproxy()
    {
    /*
    $curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "http://cn-proxy.com/");
	  curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; MSIE 11.0; Windows NT 6.1; WOW64; Trident/6.0)');
	  curl_setopt($curl, CURLOPT_ENCODING, "gzip");
	  curl_setopt($curl, CURLOPT_HEADER,0);
	  curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
	  $html = curl_exec($curl);
	  curl_close($curl);
    preg_match_all('#<td>([^<]+)</td>[\r\n]+<td>80</td>[\r\n]#ms', $html, $proxys);
    $rand = mt_rand(0, (count($proxys[1]) - 1));
    return $proxys[1][$rand] . ":80";
    */
    return false;
    }
/*获取优酷地址*/
function getFileid($fileId,$seed){
	$mixed = getMixString($seed);
	$ids = explode("*",rtrim($fileId,'*')); //去掉末尾的*号分割为数组
	$realId = "";
	for ($i=0;$i<count($ids);$i++){
	$idx = $ids[$i];
	$realId .= substr($mixed,$idx,1);
	}  
	return $realId;
}

function getMixString($seed){
	$mixed = "";
	$source = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ/\\:._-1234567890";
	$len = strlen($source);
	for($i=0;$i<$len;$i++){
	$seed = ($seed * 211 + 30031)%65536;
	$index = ($seed / 65536 * strlen($source));
	$c = substr($source,$index,1);
	$mixed .= $c;
	$source = str_replace($c,"",$source);
	}
	return $mixed;
}

function yk_d($a){
	if (!$a) {
	return '';
	}
	$f = strlen($a);
	$b = 0;
	$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
	for ($c = ''; $b < $f;) {
	$e = charCodeAt($a, $b++) & 255;
	if ($b == $f) {
		$c .= charAt($str, $e >> 2);
		$c .= charAt($str, ($e & 3) << 4);
		$c .= '==';
		break;
	}
	$g = charCodeAt($a, $b++);
	if ($b == $f) {
		$c .= charAt($str, $e >> 2);
		$c .= charAt($str, ($e & 3) << 4 | ($g & 240) >> 4);
		$c .= charAt($str, ($g & 15) << 2);
		$c .= '=';
		break;
	}
	$h = charCodeAt($a, $b++);
	$c .= charAt($str, $e >> 2);
	$c .= charAt($str, ($e & 3) << 4 | ($g & 240) >> 4);
	$c .= charAt($str, ($g & 15) << 2 | ($h & 192) >> 6);
	$c .= charAt($str, $h & 63);
	}
	return $c;
}

function yk_na($a){
	if (!$a) {
	return '';
	}
	$sz = '-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,62,-1,-1,-1,63,52,53,54,55,56,57,58,59,60,61,-1,-1,-1,-1,-1,-1,-1,0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,-1,-1,-1,-1,-1,-1,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,-1,-1,-1,-1,-1';
	$h = explode(',', $sz);
	$i = strlen($a);
	$f = 0;
	for ($e = ''; $f < $i;) {
	do {
		$c = $h[charCodeAt($a, $f++) & 255];
	} while ($f < $i && -1 == $c);
	if (-1 == $c) {
		break;
	}
	do {
		$b = $h[charCodeAt($a, $f++) & 255];
	} while ($f < $i && -1 == $b);
	if (-1 == $b) {
		break;
	}
	$e .= fromCharCode($c << 2 | ($b & 48) >> 4);
	do {
		$c = charCodeAt($a, $f++) & 255;
		if (61 == $c) {
		return $e;
		}
		$c = $h[$c];
	} while ($f < $i && -1 == $c);
	if (-1 == $c) {
		break;
	}
	$e .= fromCharCode(($b & 15) << 4 | ($c & 60) >> 2);
	do {
		$b = charCodeAt($a, $f++) & 255;
		if (61 == $b) {
		return $e;
		}
		$b = $h[$b];
	} while ($f < $i && -1 == $b);
	if (-1 == $b) {
		break;
	}
	$e .= fromCharCode(($c & 3) << 6 | $b);
	}
	return $e;
}

function yk_e($a, $c){
	for ($f = 0, $i, $e = '', $h = 0; 256 > $h; $h++) {
	$b[$h] = $h;
	}
	for ($h = 0; 256 > $h; $h++) {
	$f = (($f + $b[$h]) + charCodeAt($a, $h % strlen($a))) % 256;
	   $i = $b[$h];
	$b[$h] = $b[$f];
	$b[$f] = $i;
	}
	for ($q = ($f = ($h = 0)); $q < strlen($c); $q++) {
	$h = ($h + 1) % 256;
	$f = ($f + $b[$h]) % 256;
	$i = $b[$h];
	$b[$h] = $b[$f];
	$b[$f] = $i;
	$e .= fromCharCode(charCodeAt($c, $q) ^ $b[($b[$h] + $b[$f]) % 256]);
	}
	return $e;
}
	
function fromCharCode($codes){
	if (is_scalar($codes)) {
	$codes = func_get_args();
	}
	$str = '';
	foreach ($codes as $code) {
	$str .= chr($code);
	}
	return $str;
}

function charCodeAt($str, $index){
	$charCode = array();
	$key = md5($str);
	$index = $index + 1;
	if (isset($charCode[$key])) {
	return $charCode[$key][$index];
	}
	$charCode[$key] = unpack('C*', $str);
	return $charCode[$key][$index];
}

function charAt($str, $index = 0){
	return substr($str, $index, 1);
}
function let_($value, $key){
	$i = 0;
	while ($i < $key) {
		$value = 2147483647 & $value >> 1 | ($value & 1) << 31;
		++$i;
	}
	return $value;
}
function letu_($time)
{
	$gettimeurl = 'http://api.letv.com/mms/out/video/playJson?id=20219175&platid=1&splatid=101&format=1&tkey=-1447324515&domain=www.letv.com';
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL,$gettimeurl);
	curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; MSIE 11.0; Windows NT 6.1; WOW64; Trident/6.0)');
	curl_setopt($curl, CURLOPT_ENCODING, "gzip");
	curl_setopt($curl, CURLOPT_HEADER,0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
	$gettime = curl_exec($curl);
	curl_close($curl);
	$gettime = json_decode($gettime,true);
	$stime=$gettime['playstatus']['stime'];
	$key = 773625421;
	$value = let_($stime, $key % 13);
	$value = $value ^ $key;
	$value = let_($value, $key % 17);
	return $value;
}
function letvget($url,$ip){
	//$header = array('X-FORWARDED-FOR:'.$ip); 
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL,$url);
	//curl_setopt($curl, CURLOPT_HTTPHEADER, $header); 
	curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; MSIE 11.0; Windows NT 6.1; WOW64; Trident/6.0)');
	curl_setopt($curl, CURLOPT_ENCODING, "gzip");
	curl_setopt($curl, CURLOPT_HEADER,0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
	$fetch = curl_exec($curl);
	curl_close($curl);
	return $fetch;
}