<?php
require dirname(dirname(__FILE__)).'/task/mysql.php';
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
		self::$xml = null;	// clear the xml node in the class for 2nd time use.
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
if (preg_match("/^[1-9][0-9]*$/",$_GET["av"]))
	{
	$av = $_GET["av"];
	if (!empty($_GET["page"]))
		{
		if (preg_match("/^[1-9][0-9]*$/",$_GET["page"]))
			{
			$page = $_GET["page"];
			}
		else
			{
			echo json_encode(array('code'=>400,'error'=>'Bad Request'));
			}
		}
	else
		$page = 1;
	$id = $av.'_'.$page;
	$apijson = mysql_query("SELECT * FROM CACHE_PAGE WHERE ID='{$id}'");
	if (!mysql_num_rows($apijson))
		{
		echo json_encode(array('code'=>404,'error'=>'DATA_NOT_FOUND:AV'.$av.'P'.$page.' does not have cache'));
		}
	else
		{
		$apijson = mysql_fetch_array($apijson);
		$datatime = $apijson['LASTUPDATE'];
		$info = json_decode($apijson['DATA'],true);
		if (!array_key_exists("code",$info))
			{
			if (isset($info['list'][($page-1)]['cid']))
				{
				$cid = $info['list'][($page-1)]['cid'];
				$vid = $info['list'][($page-1)]['vid'];
				if (empty($vid)) $vid = 'N/A';
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
							$videolength = ($play['timelength'])/1000;
							$part = 0;
							$video = '';
							if (!isset($play['durl'][0]['url']))
								{
								$durldata = $play['durl'];
								$play['durl'] = '';
								$play['durl'][0] = $durldata;
								}
							while(!empty($play['durl'][$part]['url']))
								{
								$partlength = (string)($play['durl'][$part]['length'])/1000;
								$parturl = (string)$play['durl'][$part]['url'];
								$videopart[$part] = array('url'=>$parturl,'length'=>$partlength);
								$part++;
								}
							$getmp4 = json_decode($apijson['MP4'],true);
							if (!isset($getmp4['src']))
								{
								$getmp4['from'] = null;
								$getmp4['src'] = null;
								}
							$from_real = (string)$play['from'];
							$from_src = (string)$info['list'][($page-1)]['type'];
							if(empty($from_real)) $from_real = null;
							$src = (string)$play['src'];
							if($src==400) $from_real='sina';
							if(empty($from_src)) $from_src = null;
							echo json_encode(array('code'=>200,'datatime'=>$datatime,'data'=>array('title'=>$info['title'],'pagecount'=>$info['pages'],'pagetitle'=>$info['list'][($page-1)]['part'],'author'=>array('id'=>$info['mid'],'nick'=>$info['author']),'info'=>array('time'=>$info['created_at'],'play'=>$info['play'],'danmaku'=>$info['video_review'],'score'=>$info['credit'],'coin'=>$info['coins'],'favourite'=>$info['favorites']),'video'=>array('source_real'=>$from_real,'source_current'=>$from_src,'length'=>$videolength,'source'=>$videopart,'mp4'=>array('from'=>$getmp4['from'],'src'=>$getmp4['src']),'danmaku'=>'http://comment.bilibili.com/'.$cid.'.xml'))));
							}
						else
							{
							echo json_encode(array('code'=>500,'error'=>'VIDEO_PARSING_ERROR:BAD_API_XML:['.$play->type.']'.$play->message));
							}
						}
					else
						{
						echo json_encode(array('code'=>500,'error'=>'VIDEO_PARSING_ERROR:BAD_API_RESPONSE:[-500]invalid xml data'));
						}
					}
				else
					{
					echo json_encode(array('code'=>500,'error'=>'VIDEO_PARSING_ERROR:API_ERROR:['.$errorcheck["error_code"].']'.$errorcheck["error_text"]));
					}
				}
			else
				{
				echo json_encode(array('code'=>404,'error'=>'PAGE_NOT_FOUND: AV'.$av.' does not have page '.$_GET["page"]));
				}
			}
		else
			{
			if ($info["code"]==-403)
				{
				echo json_encode(array('code'=>403,'error'=>'PERMISSION_DENIED:['.$info["code"].']'.$info["error"]));
				}
			else
				{
				echo json_encode(array('code'=>404,'error'=>'CID_NOT_FOUND:['.$info["code"].']'.$info["error"]));
				}
			}
		}
	}
else
	{
	echo json_encode(array('code'=>400,'error'=>'Bad Request'));
	}