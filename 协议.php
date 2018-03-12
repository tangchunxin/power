<?php
exit();

#兔卡 20151112

//测试地址(内网/公网)，如果是服务端调用可以用内网地址
http://10.51.107.252/power_control/index.php
http://123.57.249.74/power_control/index.php

////////////////////////////////////////////////

//正式本系统地址(内网/公网)
http://10.45.36.175/power_control/index.php
http://101.201.222.137/power_control/index.php


//协议规则
urlencode的格式用户信息（源格式json的）

//生成 randkey 函数
function encryptMD5($data)
{
    $content = '';
    if (!$data || !is_array($data)) {
        return $content;
    }
    ksort($data);
    foreach ($data as $key => $value) ;
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

//例子
$data = array('mod' => 'Business', 'act' => 'login', 'platform' => 'tocar', 'uid' => '13671301110');
$randkey = encryptMD5($data);
$_REQUEST = array('randkey' => $randkey, 'c_version' => '0.0.1', 'parameter' => json_encode($data));


//获取配置数据
request:
	randkey
	c_version
	user_end: 1		//后台协议(可以不传这个参数，就代表非后端协议)
	parameter 
		mod: 'Business'
		act: 'get_conf'
		key: 'NCBDtocarpower'	//后台协议校验
		platform: 'tocar'
response:
	code //是否成功 0成功
	desc	//描述
	sub_code	//出错类型 0 成功
	sub_desc	//sub_code 描述	
	data:
		power_modular	//模块权限参数
		power_template	//权限模板
		city_arr	//城市
		city_info_arr
		chain_shop_arr
		
//查询权限列表
request:
	randkey
	c_version
	user_end: 1		//后台协议(可以不传这个参数，就代表非后端协议)
	parameter
		mod: 'Business'
		act: 'get_power_list'
		key: 'NCBDtocarpower'	//后台协议校验
		platform: 'tocar'
		modular: 1 或 1,2,3 //模块 1 c2c平台 2 竞价平台 “1,2,3”在获取 个人 权限 使用
		type: 1	//类型 1 用户权限 2 模板
        uid: 17701360024         // 非必须字段
		page:1		//页码(从1开始)
response:
	code //是否成功 0成功
	desc	//描述
	sub_code	//出错类型 0 成功
	sub_desc	//sub_code 描述
	data:{
		all_count	//记录总数
		count_per_page		//每页数量		
     	power_arr:[
    {
        "id": 1,
          "modular": 1,
          "type": 1,
          "uid": 17701360024,
          "name": "",
          "power1": 5,
          "power2": 5,
          "power3": 5,
          "power4": 5,
          "power5": 1,
          "power6": 1,
          "init_time": "2015-11-17 10:14:23",
          "update_time": 1447726463,
          "power1_str": "1,2",
          "power2_str": "0,2",
          "power3_str": "0,2",
          "power4_str": "0,2",
          "power5_str": "0",
          "power6_str": "0"
     },
     {
           "id": 1,
          "modular": 1,
          "type": 1,
          "uid": 17701360024,
          "name": "",
          "power1": 5,
          "power2": 5,
          "power3": 5,
          "power4": 5,
          "power5": 1,
          "power6": 1,
          "init_time": "2015-11-17 10:14:23",
          "update_time": 1447726463,
          "power1_str": "0,2",
          "power2_str": "0,2",
          "power3_str": "0,2",
          "power4_str": "0,2",
          "power5_str": "0",
          "power6_str": "0"
     }
    ......

    ]
     }

//查询单个权限
request:
	randkey
	c_version
	user_end: 1		//后台协议(可以不传这个参数，就代表非后端协议)
	parameter
		mod: 'Business'
		act: 'get_power'
		key: 'NCBDtocarpower'	//后台协议校验
		platform: 'tocar'
		id: 123	//权限序号
response:
	code //是否成功 0成功
	desc	//描述
	sub_code	//出错类型 0 成功
	sub_desc	//sub_code 描述
	data:{
     "power_data": {
          "id": 1,
          "modular": 1,
          "type": 1,
          "uid": 17701360024,
          "name": "",
          "power1": 5,
          "power2": 5,
          "power3": 5,
          "power4": 5,
          "power5": 1,
          "power6": 1,
          "init_time": "2015-11-17 10:14:23",
          "update_time": 1447726463,
          "power1_str": "0,2",
          "power2_str": "0,2",
          "power3_str": "0,2",
          "power4_str": "0,2",
          "power5_str": "0",
          "power6_str": "0"
       }
    }
'sub_code_1'=>'没有此用户权限记录'
          
//添加用户或模板权限
request:
	randkey
	c_version
	user_end: 1		//后台协议(可以不传这个参数，就代表非后端协议)
	parameter
		mod: 'Business'
		act: 'add_power'
		key: 'NCBDtocarpower'	//后台协议校验
		platform: 'tocar'
		modular: 1   //模块 1 c2c平台 2 竞价平台
		type: 1	//类型 1 用户权限 2 模板
        uid: 13671301110 	//uid
        name:"兔卡DDC部"    //权限模板名称 或者 部门和个人名字
        power1_str: '1,2'	//权限串
        power2_str: '1,2'	//权限串
        power3_str: ''	//权限串
        power4_str: ''	//权限串
        power5_str: ''	//权限串
        power6_str: ''	//权限串
response:
	code //是否成功 0成功
	desc	//描述
	sub_code	//出错类型 0 成功
	sub_desc	//sub_code 描述
	data:
'sub_code_1'=>'重复添加'

//修改用户或者模板权限
request:
	randkey
	c_version
	user_end: 1		//后台协议(可以不传这个参数，就代表非后端协议)
	parameter
		mod: 'Business'
		act: 'update_power'
		key: 'NCBDtocarpower'	//后台协议校验
		platform: 'tocar'
		id: 123	//权限序号
		modular: 1	//模块 1 c2c平台 2 竞价平台
        power1_str: '1'	//权限串
        power2_str: '1,2'	//权限串
        power3_str: ''	//权限串
        power4_str: ''	//权限串
        power5_str: ''	//权限串
        power6_str: ''	//权限串
        name:"兔卡DDC部"    //权限模板名称 或者 部门和个人名字（可以空）
response:
	code //是否成功 0成功
	desc	//描述
	sub_code	//出错类型 0 成功
	sub_desc	//sub_code 描述
	data:
'sub_code_1'=>'没有此数据记录，无法修改'

//删除 用户权限 或者 模板
request:
	randkey
	c_version
	user_end: 1		//后台协议(可以不传这个参数，就代表非后端协议)
	parameter
		mod: 'Business'
		act: 'delete_power'
		key: 'NCBDtocarpower'	//后台协议校验
		platform: 'tocar'
		id: 123	//权限序号
response:
	code //是否成功 0成功
	desc	//描述
	sub_code	//出错类型 0 成功
	sub_desc	//sub_code 描述
	data:

'sub_code_1'=>'没有此数据记录，无法删除'


//登录
request:
  randkey
  c_version
  user_end: 1   //后台协议(可以不传这个参数，就代表非后端协议)
  parameter
    mod: 'Business'
    act: 'login'
    platform: 'tocar'
    key: 'NCBDtocarpower' 

response:
  code //是否成功 0成功
  desc  //描述
  sub_code  //出错类型 0  1 key值错误
  sub_desc  //sub_code 描述  
  data:
