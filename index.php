<?php
header("Access-Control-Allow-Origin：*");
header("Access-Control-Allow-Headers DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type");
header("Access-Control-Allow-Methods GET,POST,OPTIONS");
    
require_once('./inc/head.inc.php');

$response = array('code' => kxRespCode::OK,'desc' => __LINE__);

//模块名
$modules = array(
	'Business' => './controller/business.php'
);

do {
	$requests = array_merge($_GET, $_POST, $_COOKIE, $_REQUEST );

	if( isset($requests['parameter']) )
	{
		$requests['parameter'] = rawurldecode($requests['parameter']);
		$parameter = json_decode($requests['parameter'], true);

		if( null === $parameter || false === $parameter )
		{
			$response['code'] = kxRespCode::ERROR; $response['desc'] = __LINE__; break;
		}
	}
	else
	{
		var_dump($requests);
		$response['code'] = kxRespCode::ERROR; $response['desc'] = __LINE__; break;
	}

	if( !isset( $parameter ) )
	{
		$response['code'] = kxRespCode::ERROR; $response['desc'] = __LINE__; break;
	}
	
	$params = array();
	foreach( $parameter as $key => $value )
	{
		$params[$key] = $value;
		if($value == 'undefined')
		{
			$params[$key] = '';
		}
	}

	if( !isset($params['mod']) || !isset($params['act']) )
	{
		$response['code'] = kxRespCode::ERROR;
		$response['desc'] = __LINE__; break;
	}

	if(
		( !isset($requests['c_version']) || $requests['c_version'] != kxConstant::C_VERSION )
		&& isset($params['act']) && $params['act'] != 'get_conf'
		&& (!isset($requests['user_end']) || !$requests['user_end'])
		&& (!isset($requests['randkey']) || !$requests['randkey'])
	)
	{
		$response['code'] = kxRespCode::ERROR_VERSION;
		$response['desc'] = __LINE__; break;
	}
	
	$bVerified = false;

	if(isset($requests['randkey']) && $requests['randkey'] != '' && encryptMD5($params) == $requests['randkey'])
	{
		$bVerified = true;
	}

	$module = $params['mod'];
	$action = $params['act'];

	if(  $module == 'Business' && ($action == 'get_conf' || $action == 'get_power_list'|| $action == 'login'))
	{
		//不需要校验的接口
		$bVerified = true;
	}
	elseif( isset($requests['user_end']) && $requests['user_end'] )
	{//后台管理协议 使用 key 校验
		if(isset($params['key']) && $params['key'] && $params['key']==$API_KEY)
		{
			$bVerified = true;
		}
	}

	if( !$bVerified )
	{
		$response['code'] = kxRespCode::ERROR_VERIFY; $response['desc'] = __LINE__; break;
	}

	if( !isset($modules[$module]) )
	{
		$response['code'] = kxRespCode::ERROR;$response['desc'] = __LINE__; break;
	}

	require($modules[$module]);

	$obj = new $module();
	if( !method_exists($obj, $action) )
	{
		$response['code'] = kxRespCode::ERROR;$response['desc'] = __LINE__.$action; break;
	}
	//	var_dump($action);
	//var_dump($obj);

	$response = $obj->$action($params);
	//var_dump($response);
	//	if( kxRespCode::OK == $response['code'] )
	//	{
	//		// 如果操作正确,执行任务,统计更新
	//	}

	if( isset($response['sub_code']) && $response['sub_code'] )
	{
		$subCode = new kxSubCode();
		if(isset($subCode->desc[$module.'_'.$action]['sub_code_'.$response['sub_code']]))
		{
			$response['sub_desc'] = $subCode->desc[$module.'_'.$action]['sub_code_'.$response['sub_code']];
		}
	}
	else
	{
		$response['sub_code'] = 0;
	}

	if( isset($module) && $module )
	{
		$response['module'] = $module;
	}

	if( isset($action) && $action )
	{
		$response['action'] = $action;
	}

}while(false);

if(!isset($response['data']['html']))
{
	output($response);
}
else
{
	//	output_html($response['data']['html']);
}
