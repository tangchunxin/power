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
	public static $power_modular = array( 
										1 => array('modular'=>'C2C平台', 'key'=>1, 'power'=>array(
											'power1'=>array('3'=>array('', '南昌')
															)
											, 'power2'=>array(1=>array('admin_agent_apply_list', '经纪人申请的列表')
																, 2=>array('admin_agent_apply_del', '出纳删除经纪人申请记录')
																, 3=>array('admin_agent_apply_money', '出纳给经纪人申请人转账钱')
																, 4=>array('admin_agent_apply_check', '客服审核经纪人')
																
																, 5=>array('add_tech', '添加tech员工')
																, 6=>array('admin_app_list', '预约看车列表')
																, 7=>array('admin_car_list', '卖车列表')
																, 8=>array('sell_car_send_tech', '客服处理卖车单子派单')
																
																, 9=>array('admin_up_pic_report', '客服填写卖车图片总路径，审核通过卖车单')
																, 10=>array('send_sms', '客服后台发送短信')
																, 11=>array('appointment_set_app', '客服设置预约看车单子')
																, 12=>array('admin_car_change', '客服修改卖车信息')
																
																, 13=>array('admin_sail_car_submit', '客服创建简版卖车单子')
																, 14=>array('admin_mobile_list', '客服后台申请卖车的列表')
																, 15=>array('admin_mobile_change_status', '修改电话记录状态')
																, 16=>array('admin_score', '客服回访给评估师用户评价')
																
																, 17=>array('get_all_tech_task_list', '所有评估师 、销售的单子')
																, 18=>array('get_person_task_list', '某个评估师 、销售的单子')
																, 19=>array('admin_unpaid_agent_list', '出纳获取未付款奖励经纪人账单列表')
																, 20=>array('get_unpaid_agent_bill_list', '获取某个经纪人付款')
																
																, 21=>array('add_admin_change_bill_statustech', '添加tech员工')
																, 22=>array('admin_agent_list', '添加出纳修改bill状态tech员工')
																, 23=>array('admin_del_agent', '客服取消经纪人资格')
																, 24=>array('admin_agent_car', '客服后台获取 某个经纪人 代理的car list')
																
																, 25=>array('admin_send_benefit', '添客服权限操作完成实物卡的派送加tech员工')
																, 26=>array('admin_benefit_list', '客服权限 获取benfit list')
																
																)
											, 'power3'=>array()
											, 'power4'=>array()
											, 'power5'=>array()
											, 'power6'=>array()
											)
										)
										, 2=> array('modular'=>'竞价平台', 'key'=>2, 'power'=>array(
											'power1'=>array('3'=>array('', '南昌')
															)
											, 'power2'=>array(1=>array('button200', '竞价开启200元按钮权限')
																, 2=>array('new_auction', '建立新的竞价场')
																, 3=>array('del_auction', '删除一场竞价会')
																, 4=>array('add_auctionCar', '把车加入竞价场')
																, 5=>array('modifyAuctionCarSeq', '修改竞价序号')
																
																, 6=>array('setUserAuctionQualification', '审核竞价资格')
																, 7=>array('setAssuranceOffline', '线下支付保证金')
																, 8=>array('setAssuaranceBack', '退保证金')
																, 9=>array('DistributeCoupons', '代金券发放')
																, 10=>array('setUserPayCar', '买主支付车款')
																
																, 11=>array('setAuctionCarPermission', '竞价会中管理员审核一辆车通过权限')
																, 12=>array('getUserList', '获取所有竞价会的用户列表')
																, 13=>array('get_user_info', '管理员获得一个用户详细信息')
																, 14=>array('del_auction_car', '删除竞价的车')
																, 15=>array('get_auction_car_excel', '一场竞价会的车列表打印')

																, 16=>array('set_bargain_car', '议价成交')
																, 17=>array('clear_bid_bond', '保证金一键清零')
																, 18=>array('auction_car_order', '拍卖会车辆排序')
																)
											, 'power3'=>array()
											, 'power4'=>array()
											, 'power5'=>array()
											, 'power6'=>array()
											)
										)
										, 3=> array('modular'=>'派工系统', 'key'=>3, 'power'=>array(
											'power1'=>array('3'=>array('', '南昌')
															)
											, 'power2'=>array(1 => array('send_task', '发起一个task')
                                                             , 2 => array('send_work', '建立一个work')
                                                             , 3 => array('accept_task', '接受一个task')
                                                             , 4 => array('complete_or_canel_task', '完成task或取消task')
                                                             , 5 => array('admin_get_driver_queue', '获取司机排队等待列表')

                                                             , 6 => array('admin_task_list', '调度者查看task列表')
                                                             , 7 => array('admin_work_list', '调度者查看work列表')
                                                             , 8 => array('add_tech', '添加tech')
                                                             , 9 => array('update_tech', '修改tech信息')
                                                             , 10 => array('admin_tech_list', '调度者查看tech列表')
                                                            )
											, 'power3'=>array()
											, 'power4'=>array()
											, 'power5'=>array()
											, 'power6'=>array()
											)
										)
										, 4=> array('modular'=>'评估系统', 'key'=>4, 'power'=>array(
											'power1'=>array('3'=>array('', '南昌')
															)
											, 'power2'=>array(1=>array('update_tech', '评估师/业务员/客户的添加和更新')
																, 2=>array('new_car', '录入新车待评估')
																, 3=>array('get_pinggu_assign', '指派评估师')
																, 4=>array('get_pinggu_car_list_all', '查看评估师的单子列表')
																, 5=>array('update_car_pinggu_start', '档案管理员操作评估师接单')
																
																, 6=>array('update_car_pinggu_ok', '管理员操作评估完成')
																, 7=>array('update_car_pinggu_cancel', '管理员操作评估取消')
																, 8=>array('update_car', '修改车辆状态')
																, 9=>array('update_car_info', '修改车的详细信息')
																, 10=>array('update_car_position', '修改车的车位号和档案柜号')
																
																, 11=>array('tech_car_list', '客户名下或者业务员联系的车列表')
																, 12=>array('change_pre_sale_info', '修改 卖车信息')
																, 13=>array('get_pre_sale_info', '获取 卖车信息列表')
																)
											, 'power3'=>array()
											, 'power4'=>array()
											, 'power5'=>array()
											, 'power6'=>array()
											)
										)
										, 5=> array('modular'=>'过户系统', 'key'=>5, 'power'=>array(
											'power1'=>array('3'=>array('', '南昌')
															)
											, 'power2'=>array(1=>array('admin_un_transfer_ownership_list', '从评估系统获取  待处理的车辆')
		                                                     	, 2=>array('add_transfer_ownership', '管理员 提交待处理过户车信息到本系统')
		                                                     	, 3=>array('update_transfer_ownership_status', '管理员 批量修改过户车信息 状态')
		                                                     	, 4=>array('update_transfer_ownership_info', '管理员 修改某个过户车信息')
		                                                     	, 5=>array('admin_transfer_ownership_bystatus_list', '管理员 根据状态 获取车列表')
		                                                     	, 6=>array('admin_del_transfer_ownership', '管理员 修改 过户状态  为放弃')

		                                                     	)
											, 'power3'=>array()
											, 'power4'=>array()
											, 'power5'=>array()
											, 'power6'=>array()
											)
										)
										, 6=> array('modular'=>'二手车连锁店系统', 'key'=>6, 'power'=>array(
											'power1'=>array( '0'=>array('', '平台')
															,'1'=>array('', 'tocar0001')
															//, '2'=>array('', 'tocar0002')
															)
											, 'power2'=>array(1=>array('new_car', '录入新车待评估')
		                                                     	, 2=>array('update_tech', '添加和更新评估师/业务员/店铺联系人')
		                                                     	, 3=>array('get_tech_list', '获取店铺评估师/业务员等排序列表')
		                                                     	, 4=>array('get_pinggu_assign', '按顺序指派评估师或者选择指派评估师')
		                                                     	, 5=>array('get_pinggu_car_list_all', '查看评估师的单子列表')
		                                                     	
		                                                     	, 6=>array('update_car_pinggu_start', '档案管理员操作评估师接单')
		                                                     	, 7=>array('update_car', '修改车辆状态')
		                                                     	, 8=>array('update_car_info', '定价/修改车的详细信息(过户要求/违规说明等）')
		                                                     	, 9=>array('to_sale_car', '把定价完成并被审核通过的车上架销售')
		                                                     	, 10=>array('deal_car', '成交一个车')
		                                                     	
		                                                     	, 11=>array('get_car_list', '根据状态获取店铺车列表')
		                                                     	, 12=>array('cancel_car', '管理员操作评估取消/或者强制删除一个未派评估的车')
		                                                     	, 13=>array('pre_sale_list', '获取卖车信息记录列表（平台管理员权限）')
		                                                     	, 14=>array('pre_sale_push_shop', '把卖车信息推送给店铺（平台管理员权限）')
		                                                     	, 15=>array('audit_pricing_car', '审核店铺定价的车（平台管理员权限）')
		                                                     	
		                                                     	, 16=>array('get_user_list', '获取用户列表(分页)（平台管理员权限）')
		                                                     	, 17=>array('transfer_info_update', '过户车信息提交')
		                                                     	
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

						
        			);
}



