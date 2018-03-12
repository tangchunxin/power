<?php

/*


*/

class Business
{
    private $log = './log/business.log';


    //连接数据库
    public function keep_connect()
    {
        $response = array('code' => kxRespCode::OK, 'desc' => __LINE__, 'sub_code' => 0);
        //$rawsqls = array();
        //$itime = time();
        $data = array();

        do {
            getDB();
            $response['data'] = $data;
        } while (false);

        return $response;
    }

    //获取配置数据
    public function get_conf()
    {
        $response = array('code' => kxRespCode::OK, 'desc' => __LINE__, 'sub_code' => 0);
        //$rawsqls = array();
        //$itime = time();
        $data = array();

        do {

            $mcobj = getMC();

            $power_template = array();
            $power_modular_arr = array_keys(kxConstant::$power_modular);	//模块
            $power_type = 2;	//类型 1 用户权限 2 模板
            foreach ($power_modular_arr as $power_modular_arr_item)
            {
            	$obj_power_list_factory = new kxPowerListFactory($mcobj, $power_modular_arr_item, $power_type);
            	if ($obj_power_list_factory->initialize() && $obj_power_list_factory->get()) {
            		$obj_power_multi_factory = new kxPowerMultiFactory($mcobj, $obj_power_list_factory);
            		if ($obj_power_multi_factory->initialize()) {
            			$obj_power_multi = $obj_power_multi_factory->get();
            			foreach ($obj_power_multi as $obj_power_multi_item) {
                            $obj_power_multi_item->get_power();
            				$power_template[$power_modular_arr_item][] = $obj_power_multi_item;
            			}
            		}
            	}
            }
            
            $chain_shop_city_arr = array();
            $chain_shop_aity_aity_arr = array();
            foreach (kxConstant::$chain_shop_arr as $chain_shop_item)
            {
            	if(!in_array($chain_shop_item[1], $chain_shop_aity_aity_arr))
            	{
            		$chain_shop_aity_aity_arr[] = $chain_shop_item[1];
            	}
            	$chain_shop_city_arr[$chain_shop_item[0]] = $chain_shop_item;
            }
            
            if(isset( kxConstant::$power_modular[6]['power']['power1'] ))
            {
            	kxConstant::$power_modular[6]['power']['power1'] = (object)kxConstant::$power_modular[6]['power']['power1'];
            }

            $data['power_modular'] = kxConstant::$power_modular;
            $data['power_template'] = $power_template;
			$data['city_arr'] = kxConstant::$city_arr;
			$data['city_info_arr'] = kxConstant::$city_info_arr;
			$data['chain_shop_arr'] = kxConstant::$chain_shop_arr;
			$data['chain_shop_city_arr'] = $chain_shop_city_arr;
			$data['chain_shop_aity_aity_arr'] = $chain_shop_aity_aity_arr;
			
            $response['data'] = $data;

        } while (false);

        return $response;
    }

    //查询权限列表
    public function get_power_list($params)
    {

        $response = array('code' => kxRespCode::OK, 'desc' => __LINE__, 'sub_code' => 0);
        $data = array();

        do {
            if (!isset($params['modular']) || !$params['modular']
                || !isset($params['type']) || !$params['type']
                || !isset($params['page'])
                ){
                $response['code'] = kxRespCode::ERROR; $response['desc'] = __line__; break;
            }
            if(!isset($params['page'])) {
                $params['page'] = 1;
            }

            $mcobj = getMC();
            $count_per_page = 20;
            $data['all_count'] = 0;
            $power_arr = array();

            if(!isset($params['uid']) || !$params['uid'])
            {
            	$obj_power_list_factory = new kxPowerListFactory($mcobj,intval($params['modular']),intval($params['type'])
                    );

            	if ($obj_power_list_factory->initialize() && $obj_power_list_factory->get())
            	{
            		$id_arr = $obj_power_list_factory->get();
            		$data['all_count'] = count($id_arr);

            		{
            			for ($i = 0; $i < $count_per_page; $i++) {
            				$tmp_id_key = ($params['page'] - 1) * $count_per_page + $i;
            				if ($tmp_id_key > $data['all_count'] - 1) {
            					break;
            				}
            				$tmp_id = $id_arr[$tmp_id_key];
            				$obj_power_mul    = new kxPowerMultiFactory($mcobj,null,$tmp_id);
            				if($obj_power_mul->initialize() && $obj_power_mul->get() ){
            					$obj_power_mul_list  = $obj_power_mul->get();
            					if($obj_power_mul_list && is_array($obj_power_mul_list))
            					{
            						foreach($obj_power_mul_list as $obj_power_multi_item)
            						{
            							$obj_power_multi_item->get_power();
            							$power_arr[] = $obj_power_multi_item;
            						}
            					}
            				}
            			}
            		}
            	}
            }
            else
            {
                $modular_arr =  explode(',',$params['modular']);
                foreach ($modular_arr as $modular_id) {
                    $power_id_temp = kxPower::is_power_exist($params['uid'], $modular_id, $params['type']);
                    $obj_power_mul = new kxPowerMultiFactory($mcobj, null, $power_id_temp);
                    if($obj_power_mul->initialize() && $obj_power_mul->get() ){
                        $obj_power_mul_list  = $obj_power_mul->get();
                        if($obj_power_mul_list && is_array($obj_power_mul_list))
                        {
                            foreach($obj_power_mul_list as $obj_power_multi_item)
                            {
                                $obj_power_multi_item->get_power();
                                $power_arr[] = $obj_power_multi_item;
                                break;
                            }
                        }
                    }
                }
                $data['all_count'] = count($power_arr);
            }

            $data['count_per_page'] = $count_per_page;
            $data['power_arr']      = $power_arr;
            $response['data'] = $data;

        } while (false);

        return $response;
    }
    
    //查询单个权限
    public function get_power($params)
    {
        $response = array('code' => kxRespCode::OK, 'desc' => __LINE__, 'sub_code' => 0);
        //$rawsqls = array();
        //$itime = time();
        $data = array();
        $power_arr = '';

        do {
            if (!isset($params['id']) || !$params['id']){
                $response['code'] = kxRespCode::ERROR; $response['desc'] = __line__; break;
            }

            $mcobj = getMC();
            $obj_power_mul    = new kxPowerMultiFactory($mcobj,null,intval($params['id']));
            if($obj_power_mul->initialize() && $obj_power_mul->get() ){
                $obj_power_mul_list  = $obj_power_mul->get();
                if($obj_power_mul_list && is_array($obj_power_mul_list))
                {
                    foreach($obj_power_mul_list as $obj_power_multi_item)
                    {
                        $obj_power_multi_item->get_power();
                        $power_arr = $obj_power_multi_item;
                    }
                }
            }
            else{
                $response['sub_code'] = 1;
                $response['desc'] = __line__;
                break;
            }

            $data['power_data'] = $power_arr;
            $response['data'] = $data;

        } while (false);

        return $response;
    } 
    
    //添加用户或模板权限
    public function add_power($params)
    {
        $response = array('code' => kxRespCode::OK, 'desc' => __LINE__, 'sub_code' => 0);
        $rawsqls = array();
        $itime = time();
        $data = array();

        do {
            if (!isset($params['modular']) || !$params['modular']
                || !isset($params['type']) || !$params['type']
                || !isset($params['name']) || !$params['name']
                || !isset($params['power1_str'])
                || !isset($params['power2_str'])
                || !isset($params['power3_str'])
                || !isset($params['power4_str'])
                || !isset($params['power5_str'])
                || !isset($params['power6_str'])
            ){
                $response['code'] = kxRespCode::ERROR; $response['desc'] = __line__; break;
            }

            //添加 个人 ，必须 添加 uid名称
            if(($params['type']== 1) && (!isset($params['uid'])|| !$params['uid'])){
                $response['code'] = kxRespCode::ERROR; $response['desc'] = __line__; break;
            }
            //添加 模板 ，必须 添加 模板名称
            if($params['type']== 2)
            {
                $params['uid'] = get_random_id();
            }

            //判断 是否重复
            $power_id = kxPower::is_power_exist($params['uid'],$params['modular'],$params['type']);
            if($power_id)
            {
                $response['sub_code'] = 1;
                $response['desc'] = __line__;
                break;
            }

            $kxPower  = new kxPower();

            $kxPower->modular =  $params['modular'];
            $kxPower->type =  $params['type'];
            $kxPower->uid =  $params['uid'];
            $kxPower->name = $params['name'];
            $kxPower->init_time =  $itime;
            $kxPower->update_time =  $itime;


            //此 权限 是否 被包含 在配置里面
            $power_modular_temp = kxConstant::$power_modular;
            foreach($power_modular_temp[intval($params['modular'])]['power'] as $key=>$value)
            {
            	$key_sign = $key.'_str';
               if($value && isset($params[$key_sign]))
               {
                   $kxPower->$key_sign = $params[$key_sign];
               }
            }

            $kxPower->set_power();
            $rawsqls[] = $kxPower->getInsertSql();

            if ($rawsqls && !(execute_sql_backend($rawsqls))) {
                logger($this->log, "【add_power】:\n" . var_export($rawsqls, true) . "\n" . __LINE__ . "\n");
                $response['code'] = kxRespCode::ERROR_UPDATE;
                $response['desc'] = __line__;
                break;
            }
            $mcobj = getMC();
            $obj_power_list    = new kxPowerListFactory($mcobj,$params['modular'],$params['type']);
            $obj_power_list->clear();

            $response['data'] = $data;
        } while (false);
        return $response;
    }

    //修改用户或者模板权限
    public function update_power($params)
    {
        $response = array('code' => kxRespCode::OK, 'desc' => __LINE__, 'sub_code' => 0);
        $rawsqls = array();
        $itime = time();
        $data = array();

        do {
            if (!isset($params['id'])  || !$params['id']
                || !isset($params['modular']) || !$params['modular']
                || !isset($params['power1_str'])
                || !isset($params['power2_str'])
                || !isset($params['power3_str'])
                || !isset($params['power4_str'])
                || !isset($params['power5_str'])
                || !isset($params['power6_str'])
            ){
                $response['code'] = kxRespCode::ERROR; $response['desc'] = __line__; break;
            }

            $mcobj = getMC();
            $obj_powerMulti = new kxPowerMultiFactory($mcobj,null,$params['id']);
            if($obj_powerMulti->initialize() && $obj_powerMulti->get()){
                $obj_powerMulti_items = $obj_powerMulti->get();
                if($obj_powerMulti_items && is_array($obj_powerMulti_items))
                {
                    foreach ($obj_powerMulti_items as $obj_powerMulti_item)
                    {
                        $obj_powerMulti_item->power1_str = $params['power1_str'];
                        $obj_powerMulti_item->power2_str = $params['power2_str'];
                        $obj_powerMulti_item->power3_str = $params['power3_str'];
                        $obj_powerMulti_item->power4_str = $params['power4_str'];
                        $obj_powerMulti_item->power5_str = $params['power5_str'];
                        $obj_powerMulti_item->power6_str = $params['power6_str'];
                        if(isset($params['name']) && $params['name'])
                        {
                        	$obj_powerMulti_item->name = $params['name'];
                        }
                        $obj_powerMulti_item->update_time = $itime;
                        $obj_powerMulti_item->set_power();

                        $rawsqls[] = $obj_powerMulti_item->getUpdateSql();
                    }
                }
            }
            else{
                $response['sub_code'] = 1;
                $response['desc'] = __line__;
                break;
            }

            if ($rawsqls && !execute_sql_backend($rawsqls)) {
                logger($this->log, "【update_power】:\n" . var_export($rawsqls, true) . "\n" . __LINE__ . "\n");
                $response['code'] = kxRespCode::ERROR_UPDATE;
                $response['desc'] = __line__;
                break;
            }

            $obj_powerMulti->writeback();

            $response['data'] = $data;

        } while (false);

        return $response;
    }

    //删除 用户 或者 模板权限
    public function delete_power($params)
    {
        $response = array('code' => kxRespCode::OK, 'desc' => __LINE__, 'sub_code' => 0);
        $rawsqls = array();
//        $itime = time();
        $data = array();

        do {
            if (!isset($params['id'])  || !$params['id'])
            {
                $response['code'] = kxRespCode::ERROR; $response['desc'] = __line__; break;
            }

            $mcobj = getMC();
            $obj_powerMulti = new kxPowerMultiFactory($mcobj,null,$params['id']);
            if($obj_powerMulti->initialize() && $obj_powerMulti->get()){
                $obj_powerMulti_items = $obj_powerMulti->get();
                if($obj_powerMulti_items && is_array($obj_powerMulti_items))
                {
                    foreach ($obj_powerMulti_items as $obj_powerMulti_item)
                    {
                      $rawsqls[] = $obj_powerMulti_item->getDelSql();
                    }
                }
            }
            else{
                $response['sub_code'] = 1;
                $response['desc'] = __line__;
                break;
            }

            if ($rawsqls && !execute_sql_backend($rawsqls)) {
                logger($this->log, "【delete_power】:\n" . var_export($rawsqls, true) . "\n" . __LINE__ . "\n");
                $response['code'] = kxRespCode::ERROR_UPDATE;
                $response['desc'] = __line__;
                break;
            }
            $obj_power_list_factory = new kxPowerListFactory($mcobj,intval($obj_powerMulti_item->modular),intval($obj_powerMulti_item->type));
            $obj_power_list_factory->clear();
            $obj_powerMulti->clear();

            $response['data'] = $data;
        } while (false);

        return $response;
    }

     //登录
    public function login($params)
    {
        global $API_KEY;
        $response = array('code' => kxRespCode::OK, 'desc' => __LINE__, 'sub_code' => 0);
        $rawsqls = array();
        $data = array();

        do {
            if (!isset($params['key'])  || !$params['key'])
            {
                $response['code'] = kxRespCode::ERROR; $response['desc'] = __line__; break;
            }
           
            if($params['key'] != $API_KEY )
            {
                 $response['sub_code'] = 1; $response['desc'] = __line__; break;
            }

            $response['data'] = $data;
        } while (false);

        return $response;
    }


    //更新+86
   /* public function updata($params)
    {
        $response = array('code' => kxRespCode::OK, 'desc' => __LINE__, 'sub_code' => 0);
        $rawsqls = array();
        $data = array();
        $tmp = array();
        $phone = 86;
        //构造分页参数

        do {
            $mcobj = getMC();


            //  agent_info      
            $obj_power_list_factory = new  kxPowerListFactory($mcobj,8,1);
            if($obj_power_list_factory->initialize() && $obj_power_list_factory->get())
            {
                $obj_power_multi_factory = new kxPowerMultiFactory($mcobj,$obj_power_list_factory);
                if($obj_power_multi_factory->initialize() && $obj_power_multi_factory->get())
                {
                    $obj_power_multi = $obj_power_multi_factory->get();
        
                    if(is_array($obj_power_multi))
                    {
                        foreach ($obj_power_multi as $key => $obj_power_multi_item)
                        {
                            $obj_power_multi_item->uid = $phone.$obj_power_multi_item->uid;
                         
                            $rawsqls[] = $obj_power_multi_item->getUpdateSql();

                        }
                    }

                }
                else
                {
                    $obj_power_multi_factory->clear();
                    $response['sub_code'] = 2; $response['desc'] = __line__; break;
                }
            }
            else
            {
                $obj_power_list_factory->clear();
                $response['sub_code'] = 1; $response['desc'] = __line__; break;
            }



            if ($rawsqls && !execute_sql_backend($rawsqls))
            {
                logger($this->log, "【rawsqls】:\n" . var_export($rawsqls, true) . "\n" . __LINE__ . "\n");
                $response['code'] = 1; $response['desc'] = __line__; break;
            } 
            $obj_power_multi_factory->writeback();


            $response['data'] = $data;
        } while (false);

        return $response;
    }*/

}