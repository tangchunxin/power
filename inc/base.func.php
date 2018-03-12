<?php

/*
base.func.php 提供公用函数
*/

//function check_char($content)
//{
//	$preg = "([\\<>&\"\'\.\*\r\n])+";
//	return !(ereg($preg, $content));
//}


function time4str($itime)
{
	if ($itime) {
		return date('m-d H:i', $itime);
	}
	return false;
}

function time2str($itime)
{
	if ($itime) {
		return date('Y-m-d H:i:s', $itime);
	}
	return false;
}

function time3str($itime)
{
	if ($itime) {
		return date('Y.m.d H:i:s', $itime);
	}
	return false;
}

function time2str_day($itime = 0)
{
	if ($itime) {
		return date('Y.m.d', $itime);
	}
	return date('Y.m.d');
}

function time4str_day($itime = 0)
{
	if ($itime) {
		return date('ymd', $itime);
	}
	return date('ymd');
}

function microtime_float()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

function getkxmonday()
{
	return (int)date("Ymd", mktime(0, 0, 0, date('m'), date('d') - (date('w') == 0 ? 6 : date('w') - 1), date('Y')));
}

function getkxtimeday($itime = 0)
{
	if (!$itime) {
		$itime = time();
	}
	return mktime(0, 0, 0, date('m', $itime), date('d', $itime), date('Y', $itime));
}

function getkxday($itime = null)
{
	if (!$itime) {
		$itime = time();
	}
	return (int)date('Ymd', $itime);
}

function output($response)
{

	header('Cache-Control: no-cache, must-revalidate');
	header("Content-Type: text/json; charset=UTF-8");

	if (isset($_REQUEST['callback']) && $_REQUEST['callback']) {
		echo $_REQUEST['callback'] . '(' . json_encode($response) . ')';
	} else {
		echo json_encode($response);
	}
}

function output_html($html)
{

	header('Cache-Control: no-cache, must-revalidate');
	header("Content-Type: text/html; charset=utf-8");

	echo($html);
}

function encryptMD5($data)
{
	$content = '';
	if (!$data || !is_array($data)) {
		return $content;
	}
	ksort($data);
	foreach ($data as $key => $value)
	{
		$content = $content . $key . $value;
	}
	if (!$content) {
		return $content;
	}

	return sub_encryptMD5($content);

}

function sub_encryptMD5($content)
{
	global $RPC_KEY;
	$content = $content . $RPC_KEY;
	$content = md5($content);
	if (strlen($content) > 10) {
		$content = substr($content, 0, 10);
	}
	return $content;
}

//function decryptRandAuth($authKey, $data)
//{
//	$data = handleDecrypt(base64_decode($data), $authKey);
//	$content = '';
//	for( $i=0; $i<strlen($data); $i++ )
//	{
//		$md5 = $data[$i];
//		$content .= $data[++$i] ^ $md5;
//	}
//	return $content;
//}
//
//function encryptRandAuth($authKey, $data)
//{
//	$encrypt_key = md5(date("md"));
//	$ctr = 0;
//	$content = '';
//	for( $i=0;$i<strlen($data);$i++ )
//	{
//		$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
//		$content .= $encrypt_key[$ctr].($data[$i] ^ $encrypt_key[$ctr++]);
//	}
////	$length = strlen($content) ;
//	$content = handleDecrypt($content, $authKey);
//	$content = base64_encode($content);
//	if( strlen($content) > 15 )
//	{
//		$content = substr($content, 6, 9);
//	}
//	else if( strlen($content) > 9 )
//	{
//		$content = substr($content, 0, 9);
//	}
//	return $content;
//}
//
//
//function handleDecrypt($data, $key)
//{
//	$encrypt_key = md5($key);
//	$ctr = 0;
//	$content = '';
//	for( $i=0; $i<strlen($data); $i++ )
//	{
//		$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
//		$content .= $data[$i] ^ $encrypt_key[$ctr++];
//	}
//	return $content;
//}


function https_request($url, $data = null)
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	if (!empty($data)) {
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	}
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}


// 打印log
function logger($file, $word)
{
	$fp = fopen($file, "a");
	flock($fp, LOCK_EX);
	fwrite($fp, "执行日期：" . strftime("%Y-%m-%d %H:%M:%S", time()) . "\n" . $word . "\n\n");
	flock($fp, LOCK_UN);
	fclose($fp);
}


function get_key($arr, $val)
{
	if (!is_array($arr)) {
		return false;
	}
	foreach ($arr as $arr_item) {
		if (isset($arr_item['min']) && isset($arr_item['max'])) {
			if ($val >= $arr_item['min'] && ($arr_item['max'] == -1 || $val < $arr_item['max'])) {
				return $arr_item['key'];
			}
		}
	}
	return false;
}

function get_name($arr, $key)
{
	foreach ($arr as $arr_item) {
		if (isset($arr_item['key']) && $arr_item['key'] == $key && isset($arr_item['name'])) {
			return $arr_item['name'];
		}
	}
	return '';
}

function get_age($year, $month)
{
	$itime = time();
	$now_year = intval(date('Y', $itime));
	$now_month = intval(date('m', $itime));
	$diff_y = $now_year - $year;
	$diff_m = $now_month - $month;
	if ($diff_y >= 0 && $diff_m >= 0) {
		return $diff_y;
	} elseif ($diff_y > 0 && $diff_m < 0) {
		return $diff_y - 1;
	} else {
		return 0;
	}
}

function get_random_id()
{
	global $_SGLOBAL;
	return ( time() . (intval($_SGLOBAL['m_secend'] * 1000)) );
}
