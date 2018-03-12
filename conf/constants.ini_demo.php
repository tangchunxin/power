<?php

/*
constants.ini.php
*/

class kxConstant
{

	const C_VERSION = '0.0.1';
	const CONF_VERSION = '0.0.1';
	const SECRET = 'Keep it simple stupid!';
	const CDKEY  = 'God bless you!';

	public static $chain_shop_arr = array(0=>array('平台', '平台', '', '')
											, 1=>array('tocar0001', '南昌', '江西省南昌市红谷滩汽车广场B区1号（二手车市场交易大厅西侧）', '13970090229')
											//, 2=>array('tocar0002', '南昌', '江西省南昌市红谷滩汽车广场B区1号（二手车市场交易大厅西侧）', '13970090229')
										);//连锁店店铺  与后面$power_modular的顺序和key对应

	public static $city_arr = array(0=>'不限', 1=>'南昌');//与后面$power_modular的城市顺序和key对应
	public static $city_info_arr = array('南昌'=>array('transaction_address'=>'江西省南昌市红谷滩新区红谷中大道1619号金融大厦3c未来谷1楼 江西美车快拍科技有限公司'));
	public static $power_modular = array( 7=> array('modular'=>'麻将后台管理系统', 'key'=>7, 'power'=>array(
													'power1'=>array( '0'=>array('', '平台')
																	,'1'=>array('', 'tocar0001')

																	)
													, 'power2'=>array(1=>array('handler_buy', '手动购钻')
																	,2=>array('add_user', '添加工作人员')
																	,3=>array('close_down_aid', '查封大客户账号')
																	,4=>array('all_agent_list', '全部大客户列表')
																	,5=>array('unban_aid', '解封账号')

																	,6=>array('handler_buy_list', '手动充钻列表')
																	,7=>array('statistics_data', '统计数据列表')

				                                                     	)
													, 'power3'=>array()
													, 'power4'=>array()
													, 'power5'=>array()
													, 'power6'=>array()
													)
										)

										,8=> array('modular'=>'麻将后台管理系统', 'key'=>8, 'power'=>array(
											'power1'=>array( '0'=>array('', '平台')
															,'1'=>array('', 'sichuan0001')
															,'2'=>array('', 'shaanxi0001')
															,'3'=>array('', 'beimei0001')
															,'4'=>array('', 'chengde0001')
															,'5'=>array('', 'baoding0001')
															,'6'=>array('', 'lishui0001')
															,'7'=>array('', 'xinji0001')
															,'8'=>array('', 'dezhou0001')
															,'9'=>array('', 'kpi0001')
															,'10'=>array('', 'fair0001')
															,'11'=>array('', 'hb0001')
															,'12'=>array('', 'jiamusi0001')

															)
											, 'power2'=>array(1=>array('add_agent', '添加客服经理')
															,2=>array('chmod_agent_list', '审核客服经理列表')
															,3=>array('chmod_agent_pass', '审核客服经理')
															,4=>array('statistics_data', '统计数据')
															,5=>array('history_statistics_data', '历史统计数据')

															,6=>array('delete_agent_info', '直接删除人员信息')
															,7=>array('get_agent_info_excel', '列表打印')
															,8=>array('kpi_get', 'KPI数据统计')
															,9=>array('kpi_get_all', '全部KPI数据统计')
															,10=>array('get_agent_buy_excel', '财务报表')





		                                                     	)
											, 'power3'=>array()
											, 'power4'=>array()
											, 'power5'=>array()
											, 'power6'=>array()
											)
										)

										,9=> array('modular'=>'微信后台管理系统', 'key'=>8, 'power'=>array(
											'power1'=>array( '0'=>array('', '平台')
															,'1'=>array('', 'sichuan0001')
															,'2'=>array('', 'shaanxi0001')
															,'3'=>array('', 'beimei0001')
															,'4'=>array('', 'chengde0001')
															,'5'=>array('', 'baoding0001')
															,'6'=>array('', 'lishui0001')
															,'7'=>array('', 'xinji0001')
															,'8'=>array('', 'dezhou0001')
															,'9'=>array('', 'fair0001')
															,'10'=>array('', 'hb0001')
															,'11'=>array('', 'jiamusi0001')
															,'12'=>array('', 'kpi0001')
															,'13'=>array('', 'chifeng0001')

															)
											, 'power2'=>array(1=>array('add_agent', '添加客服经理')
															,2=>array('chmod_agent_list', '审核客服经理列表')
															,3=>array('chmod_agent_pass', '审核客服经理')
															,4=>array('statistics_data', '统计数据')
															,5=>array('history_statistics_data', '历史统计数据')

															,6=>array('delete_agent_info', '直接删除人员信息')
															,7=>array('get_agent_info_excel', '列表打印')
															,8=>array('kpi_get', '本游戏KPI数据统计')
															,9=>array('boss_recharge_agent', '给全部代理充值')
															,10=>array('del_agent_amount', '扣钻')

															,11=>array('get_agent_buy_excel', '财务报表')
															,12=>array('boss_add_agent', '添加公司人员')
															,13=>array('all_agent', '下载全部代理信息')
															,14=>array('all_agent_buy_list', '下载全部代理购钻信息')
															,15=>array('kpi_get_all', '全部游戏KPI数据统计')

															,16=>array('chmod_agent_id', '强制更换代理手机号')
															,17=>array('chmod_p_aid', '强制代理转移')
															,18=>array('play_pull_black', '拉入黑名单')
															,19=>array('find_play_recharge', '查询玩家钻石变化')
															,20=>array('find_play_video', '查询玩家录像播放码')


		                                                     	)
											, 'power3'=>array()
											, 'power4'=>array()
											, 'power5'=>array()
											, 'power6'=>array()
											)
										)
										,10=> array('modular'=>'城市合伙人后台管理系统', 'key'=>10, 'power'=>array(
											'power1'=>array( '0'=>array('', '平台')
															,'1'=>array('', 'city_agent0001')
															,'2'=>array('', 'city_tianjin0001')
															,'3'=>array('', 'city_langfang0001')
															,'4'=>array('', 'city_baodingnew0001')

															)
											, 'power2'=>array(1=>array('add_service', '添加客服或公司身份')
															,2=>array('delete_agent_info', '直接删除人员信息')
															,3=>array('get_agent_info_excel', '列表打印*')
															,4=>array('kpi_get', '全部数据总揽(公司)')
															,5=>array('play_recharge_list_new', '全部玩家充值记录(公司)')

															,6=>array('income_statistics', '公司身份能看见合伙人收益统计(公司)')
															,7=>array('play_recharge', '给玩家充值')
															,8=>array('find_play_recharge', '查询玩家钻石变化')
															,9=>array('find_play_video', '查询玩家录像播放码')
															,10=>array('all_agent', '下载全部代理信息')
															
															,11=>array('chmod_p_aid', '强制代理转移')
															,12=>array('play_list', '全部公会玩家(公司)')
															,13=>array('add_rechargeable_card', '添加充值卡密码(客服)')
															,14=>array('show_rechargeable_card', '查询充值卡密码(客服)')
															,15=>array('change_delivery_information', '更改发货信息(客服)')
															
															,16=>array('show_gift_exchange_log', '查看礼物兑换记录(客服)')
															,17=>array('del_agent_amount', '给玩家扣钻(公司)')
															,18=>array('extract_income_list_excel', '代理提现记录下载(财务)')
															,19=>array('income_statistics_excel', '代理收益下载(财务)')
															,20=>array('boss_add_agent', '添加公司身份及合伙人,包含分成比例(公司)')
															,21=>array('set_shared', '修改分成比例(公司)')
															,22=>array('bind_out_agent_id', '9,8权限踢出玩家')





		                                                     	)
											, 'power3'=>array()
											, 'power4'=>array()
											, 'power5'=>array()
											, 'power6'=>array()
											)
										)

								);

}


class kxRespCode
{
        const OK = 0;
        const ERROR = 1;
        const ERROR_MC = 2;
        const ERROR_INIT = 3;
        const ERROR_UPDATE = 4;
        const ERROR_VERIFY = 5;
        const ERROR_ARGUMENT = 6;
        const ERROR_VERSION = 7;
}


class kxSubCode
{
        public $desc = array(
        				'Business_get_conf' => array('sub_code_1'=>''),
        				'Business_add_power' => array('sub_code_1'=>'重复添加'),
        				'Business_update_power' => array('sub_code_1'=>'没有此数据记录，无法修改'),
        				'Business_delete_power' => array('sub_code_1'=>'没有此数据记录，无法删除'),
        				'Business_get_power' => array('sub_code_1'=>'没有此用户权限记录'),
        				'Business_login' => array('sub_code_1'=>'key值错误'),


        			);
}



