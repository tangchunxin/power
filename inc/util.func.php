<?php

/*
util.func.php
*/

$gCache = array();

function getMC()
{
	//单例	
	global $MC_SERVERS,$gCache;

	if( !isset($gCache['mcobj']) )
	{
		$mcobj = new kxMemcache($MC_SERVERS);
		$gCache['mcobj'] = $mcobj;
	}

	return  $gCache['mcobj'];
}

function get_client_ip()
{
	$s_client_ip = '';

	if ($_SERVER['HTTP_X_REAL_IP'])
	{
		$s_client_ip = $_SERVER['HTTP_X_REAL_IP'];
	}
	elseif ($_SERVER['REMOTE_ADDR'])
	{
		$s_client_ip = $_SERVER['REMOTE_ADDR'];
	}
	elseif (getenv('REMOTE_ADDR'))
	{
		$s_client_ip = getenv('REMOTE_ADDR');
	}
	elseif (getenv('HTTP_CLIENT_IP'))
	{
		$s_client_ip = getenv('HTTP_CLIENT_IP');
	}
	else
	{
		$s_client_ip = 'unknown';
	}
	return $s_client_ip;
}

function getDB()
{
	//单例
	global $DB_HOST, $DB_USERNAME, $DB_PASSWD, $DB_DBNAME, $gCache, $DB_PORT;

	if( !isset($gCache['mysqli']) )
	{
		$gCache['mysqli'] = new mysqli($DB_HOST, $DB_USERNAME, $DB_PASSWD, $DB_DBNAME, $DB_PORT);
		if(empty($gCache['mysqli']) || !$gCache['mysqli']->ping())
		{
			@$gCache['mysqli']->close();
			if (!$gCache['mysqli']->real_connect($DB_HOST, $DB_USERNAME, $DB_PASSWD, $DB_DBNAME ,$DB_PORT)) 
			{
				return false;
			}
		}
		$gCache['mysqli']->query("set names 'utf8'");
		mb_internal_encoding('utf-8');					
	}
	
	return  $gCache['mysqli'];
}

function execute_sql_backend($rawsqls)
{
	$result_arr = null;
	$is_rollback = false;

	if(!$rawsqls || !is_array($rawsqls))
	{
		return $result_arr;
	}

	$db_connect = getDB();
	$db_connect->autocommit(false);
	foreach ($rawsqls as $item_sql)
	{
		$result = null;
		$result = $db_connect->query($item_sql);
		if(!$result)
		{
			if($db_connect->rollback())
			{
				$is_rollback = true;
			}
			else
			{
				$db_connect->rollback();
				$is_rollback = true;
			}
			$result_arr = null;
			break;
		}
		if($db_connect->insert_id)
		{
			$result_arr[] = array('result'=>$result, 'insert_id'=>$db_connect->insert_id);
		}
		else 
		{
			$result_arr[] = array('result'=>$result);
		}
	}

	if(!$is_rollback)
	{
		$db_connect->commit();
	}
	$db_connect->autocommit(true);
	return $result_arr;
}

function query_sql_backend($rawsql)
{
	$db_connect = getDB();

	$result = $db_connect->query($rawsql);

	return $result;
}


//
//function _execute_sql_backend($rawsqls)
//{
//	global $FCGI_SERVER;
//
//	if(count($rawsqls) == 0)
//		return true;
//	$strsqls = sql_assemble($rawsqls);
//
//	$url = "$FCGI_SERVER?mod=execsql&act=direct&sqlnum=".count($rawsqls);
//	$ch = curl_init();
//	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Expect:'));
//	curl_setopt($ch, CURLOPT_POST, 1);
//	curl_setopt($ch, CURLOPT_URL,$url);
//	curl_setopt($ch, CURLOPT_POSTFIELDS, $strsqls);
//	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//	$result = curl_exec($ch);
//	curl_close($ch);
//
//	return $result;
//}
//
//function execute_sql_backend($rawsqls)
//{
//	$result = _execute_sql_backend($rawsqls);
//
//	return ($result != 'Error');
//}
//
//function query_sql_backend($rawsql)
//{
//	global $FCGI_SERVER;
//
//	$strsql = "sql=".urlencode($rawsql);
//
//	$url = "$FCGI_SERVER?mod=querysql&act=direct";
//	$ch = curl_init();
//	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Expect:'));
//	curl_setopt($ch, CURLOPT_POST, 1);
//	curl_setopt($ch, CURLOPT_URL,$url);
//	curl_setopt($ch, CURLOPT_POSTFIELDS, $strsql);
//	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//	$result = curl_exec($ch);
//	curl_close($ch);
//	if( $result !== 'NULL' && $result !== 'Error'  && $result !== false)
//	{
//		$result = sqlret_parse($result);
//	}
//
//	return $result;
//}

//function sqlret_parse($sqlret)
//{
//	$result = array();
//	$lines = explode("\n",$sqlret);
//	foreach($lines as $line)
//	{
//		$columns = explode("\t",$line);
//		$result[] = $columns;
//	}
//
//	return $result;
//}

//function sql_assemble($rawsqls)
//{
//	$strsqls = array();
//	for($i=0;$i<count($rawsqls);$i++)
//	{
//		$strsqls[] = "sql$i=".urlencode($rawsqls[$i]);
//	}
//
//	return join("&",$strsqls);
//}

/*
* @inout $weights : array(1=>20, 2=>50, 3=>100);
* @putput array
*/
function w_rand($weights)
{

	$r = mt_rand(1, array_sum($weights));

	$offset = 0;
	foreach ( $weights as $k => $w )
	{
		$offset += $w;
		if ($r <= $offset)
		{
			return $k;
		}
	}

	return null;
}

function my_addslashes($str)
{
	$str = str_replace(array("\r\n", "\r", "\n"), '', $str);
	return addslashes(stripcslashes($str));
}
