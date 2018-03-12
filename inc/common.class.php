<?php

/*
common.class.php define ORM Object and it's factory
*/

/*
* kxFactory:	add memcached support to mysql,dbobj be iHashDB implementation
*
* kxMutiStoreFactory: extend kxFactory,
*				the Array data hold is not stored by one key,every element in the Array have it's own storage,and
*				another key store the index array,normally the element store key prefex
* attention:
* 		false,true,null is reserved in hash db store value
*/

class kxObject
{
	public function before_writeback()
	{
		;
	}
}

class kxMCAction
{
	const SET = 1;
	const GET = 2;
	const DEL = 3;
}

interface iHashDB
{
	// whether connection to db is ok
	public function ok();

	// get values from db
	// if keys is array,return array(keys[0] => value...) else return value
	// value of some key not exist,if keys is array,no value in return array,else return null
	public function get( $server_key, $strkey);

	// set values to db
	// if values is array,otherwise it is the key of strobj
	public function set( $server_key, $strkey, $strobj=null, $timeout=0);

	// set value to db if it not exist
	// return false if it exist else true
	public function setKeep($strkey, $strobj, $timeout=0);

	// delete values from db
	public function del($server_key, $strkey);

	//
	public function get_multi( $server_key, $keys_arr );
	//
	public function set_multi( $server_key, $values, $timeout=0 );

	public function del_multi( $server_key, $keys );

}

class kxMemcache implements iHashDB
{
	private $conn;
	private $ok;


	function __construct($servers)
	{
		$this->conn = new Memcached;
		$this->ok = false;

		$this->conn->setOption(Memcached::OPT_COMPRESSION, true);
		$this->conn->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);	//一致性hash
		$this->conn->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);

		foreach( $servers as $server )
		{
			if( $this->conn->addServer($server[0], $server[1]) !== false )
			{
				$this->ok = true;
			}
		}
	}

	public function ok()
	{
		return $this->ok;
	}

	public function flush()
	{
		if( $this->conn )
		{
			$this->conn->flush();
		}
	}

	public function get($server_key, $strkey)
	{
		if( $this->conn )
		{
			$strobjs = $this->conn->getByKey($server_key, $strkey);
			if( $strobjs !== false )
			{
				return $strobjs;
			}
			else
			{
				return null;
			}
		}
		return null;
	}

	public function  get_multi($server_key, $keys_arr )
	{
		if( $this->conn )
		{
			$objs_arr = $this->conn->getMultiByKey($server_key, $keys_arr);
			if( $objs_arr != null )
			{
				return $objs_arr;
			}
			else
			{
				return null;
			}
		}
		return null;
	}

	public function set_multi( $server_key, $values, $timeout=0 )
	{
		//$values : An array of key/value pairs to store on the server.
		if( is_array($values) )
		{
			return $this->conn->setMultiByKey( $server_key, $values, $timeout);
		}
		return false;
	}

	public function set($server_key, $strkey, $strobj=null, $timeout=0)
	{
		return $this->conn->setByKey($server_key, $strkey, $strobj, $timeout);
	}

	public function setKeep($strkey, $strobj, $timeout=0)
	{
		return $this->conn->add($strkey, $strobj, $timeout);
	}

	public function append($strkey, $strobj, $timeout=0)
	{
		//追加模式不能使用压缩
		$this->conn->setOption(Memcached::OPT_COMPRESSION, false);
		if(!$this->setKeep($strkey, $strobj, $timeout))
		{
			$this->conn->append($strkey, $strobj);
		}
		$this->conn->setOption(Memcached::OPT_COMPRESSION, true);
	}

	public function del_multi( $server_key, $keys )
	{
		if( is_array($keys) )
		{
			return $this->conn->deleteMultiByKey( $server_key, $keys);
		}
		return false;
	}

	public function del($server_key, $strkey )
	{
		return $this->conn->deleteByKey($server_key, $strkey);
	}

	public function get_result()
	{
		return $this->conn->getResultCode();
	}
}

class kxFactory
{
	protected $server_key;
	protected $objkey;
	protected $timeout;
	protected $dbobj;
	protected $obj;

	function __construct($dbobj, $server_key, $objkey, $timeout=3600)
	{
		$this->dbobj = $dbobj;
		$this->server_key = $server_key;
		$this->objkey = $objkey;
		$this->timeout = $timeout;
	}

	public function get()
	{
		return $this->obj;
	}

	public function set($obj)
	{
		return $this->obj = $obj;
	}

	public function clear()
	{
		if($this->timeout === null)
		{
			return false;
		}
		if($this->dbobj && is_object($this->dbobj)) {
			$this->dbobj->del( $this->server_key, $this->objkey);
		}
		return true;
	}

	public function writeback()
	{
		if($this->timeout === null)
		{
			return false;
		}
		if(is_object($this->obj))
		{
			$this->obj->before_writeback();
		}
		$strobj = igbinary_serialize($this->obj);
		$this->dbobj->set($this->server_key, $this->objkey, $strobj, $this->timeout);
		return true;
	}

	public function initialize()
	{
		$strobj = null;
		if($this->objkey == null || $this->server_key == null){
			//			trace('error', 'kxfactory initialize objkey null');
			return false;
		}
		if($this->timeout !== null)
		{
			$strobj = $this->dbobj->get($this->server_key, $this->objkey);
		}
		if( $strobj === false ){
			return false;
		}
		if( $strobj !== null )
		{
			$obj = igbinary_unserialize($strobj);
			if($obj !== false && $obj !== null)
			{
				$this->obj = $obj;
			}
		}
		else
		{
			$this->obj = $this->retrive();
			if( $this->obj !== null  )
			{
				$this->writeback();
			}
		}
		return ($this->obj !== null );
	}

	// if you want to retrive data from some other place,if it not store in hash db
	// please override retrive function
	public function retrive()
	{
		return null;
	}
}

class kxMutiStoreFactory extends kxFactory
{
	public $key_objfactory = null;	// key list objfactory
	public $key_obj = null;	//key list
	protected $bInitMuti = true;

	public function __construct($dbobj, $server_key, $objkey, $key_objfactory, $key_id=null, $timeout=3600)
	{
		parent::__construct($dbobj, $server_key, $objkey, $timeout);

		if($this->bInitMuti)
		{
			$this->key_objfactory = $key_objfactory;
			$this->key_objfactory->initialize();
			$this->key_obj = $this->key_objfactory->get();
		}
		elseif($key_id)
		{
			$this->key_obj = array($key_id);
		}
		else
		{
			$this->key_obj = null;
		}

		$tmp_arr = null;
		if($this->key_obj && is_array($this->key_obj))
		{
			foreach ($this->key_obj as $item)
			{
				$tmp_arr[] = $this->objkey . '_' . $item;
			}
		}
		$this->key_obj = $tmp_arr;
	}

	public function clear()
	{
		if($this->dbobj && is_object($this->dbobj)) {
			$this->dbobj->del_multi($this->server_key, $this->key_obj);	//用数组做参数，删除多个
		}
		$this->clear_key_list();	//delete key list
	}

	public function clear_key_list()
	{
		if($this->key_objfactory)
		{
			$this->key_objfactory->clear();
		}
	}

	public function initialize()
	{
		if($this->objkey == null || $this->key_obj == null || $this->server_key == null)
		{
			return false;
		}
		//logger($this->log, "【1111】:\n" . var_export(111, true) . "\n" . __LINE__ . "\n");
		if($this->key_obj && is_array($this->key_obj) && $this->server_key)
		{	//key list is array
			$strobj_arr = $this->dbobj->get_multi($this->server_key, $this->key_obj);
			if($strobj_arr && is_array($strobj_arr) && count($this->key_obj) == count($strobj_arr))
			{
				$tmp_arr = null;
				foreach ($strobj_arr as $key=>$item)
				{
					$tmp_arr[$key] = igbinary_unserialize($item);
				}
				$this->obj = $tmp_arr;
				//logger($this->log, "【1111】:\n" . var_export($this->obj, true) . "\n" . __LINE__ . "\n");
			}
			else
			{	//not in cache
				$this->obj = $this->retrive();
				if( $this->obj !== null  )
				{
					if($this->bInitMuti)
					{
						$this->clear();
					}
					$this->writeback();
				}
			}
		}
		else
		{
			return false;
		}

		return ($this->obj !== null );

	}

	public function writeback($id=null)
	{
		// 如果是初始化所有对象,则分别写回
		$tmp_arr = array();
		foreach( $this->obj as $key => $obj )
		{
			if(is_object($obj))
			{
				$obj->before_writeback();
			}
			if( $id !== null && $key !== $this->objkey . '_' .$id )
			continue;
			$tmp_arr[$key] = igbinary_serialize($obj);
		}
		if($tmp_arr)
		{
			$this->dbobj->set_multi($this->server_key, $tmp_arr, $this->timeout );
		}
		unset($tmp_arr);
	}

	public function retrive()
	{
		return array();
	}
}

class kxListFactory extends kxFactory
{
	public $sql='';
	public $list_key;
	public $id_arr;

	public function __construct($dbobj, $key, $timeout = null, $id_multi_str = '' )
	{
		$this->list_key = $key;
		if($id_multi_str)
		{
			$this->id_arr = explode(',', $id_multi_str);
		}
		parent::__construct($dbobj, $this->list_key, $this->list_key, $timeout);
	}

	public function retrive()
	{
		$list_arr = array();
		$records = null;
		if($this->id_arr && is_array($this->id_arr))
		{
			return $this->id_arr;
		}
		else
		{
			if($this->sql)
			{
				$records = query_sql_backend($this->sql);
			}

			if ( $records )
			{
				while ( ($row = $records->fetch_row()) != false )
				{
					$list_arr[] = $row[0];
				}
				$records->free();
				unset($records);
				return $list_arr;
			}
		}

		return $list_arr;
	}
}

/////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////

class kxPower extends kxObject
{
	const TABLE_NAME = 'power';

    public $id;	//id
    public $modular = 0;	//模块 1 兔卡c2c平台 2 兔卡拍卖平台
    public $type = 0;	//类型 1 用户权限 2 模板
    public $uid = 0;	//uid
    public $name = '';	//命名：例如（北京客服模板）

    public $power1 = '';	//权限
    public $power2 = '';	//权限
    public $power3 = '';	//权限
    public $power4 = '';	//权限
    public $power5 = '';	//权限

    public $power6 = '';	//权限
    public $init_time = 0;	//创建时间
    public $update_time = 0;	//更新时间

    public function getUpdateSql()
    {
	return "update `power` SET
               `modular`=" . intval($this->modular) . "
        	, `type`=" . intval($this->type) . "
      		, `uid`=" . intval($this->uid) . "
        	, `name`='" . my_addslashes($this->name) . "'

        	, `power1`=" . intval($this->power1) . "
        	, `power2`=" . intval($this->power2) . "
        	, `power3`=" . intval($this->power3) . "
        	, `power4`=" . intval($this->power4) . "
        	, `power5`=" . intval($this->power5) . "

        	, `power6`=" . intval($this->power6) . "
        	, `init_time`=" . intval($this->init_time) . "
        	, `update_time`=" . intval($this->update_time) . "

        	where `id`=" . intval($this->id) . "";
    }

    public function getInsertSql()
    {
	return "insert into `power` SET

        	`modular`=" . intval($this->modular) . "
        	, `type`=" . intval($this->type) . "
        	, `uid`=" . intval($this->uid) . "
        	, `name`='" . my_addslashes($this->name) . "'

        	, `power1`=" . intval($this->power1) . "
        	, `power2`=" . intval($this->power2) . "
        	, `power3`=" . intval($this->power3) . "
        	, `power4`=" . intval($this->power4) . "
        	, `power5`=" . intval($this->power5) . "

        	, `power6`=" . intval($this->power6) . "
        	, `init_time`=" . intval($this->init_time) . "
        	, `update_time`=" . intval($this->update_time) . "
        	";
    }

    public function getDelSql() 
    {
        return "delete from `power`
            where `id`=".intval($this->id)."";
    }

    public function before_writeback() 
    {
        parent::before_writeback();
        return true;
    }

	public function get_time_str()
	{
		$this->init_time_str = time2str($this->init_time);
		$this->update_time_str = time2str($this->update_time);
	}

	public function get_power()
	{
		$this->power1_str = kxPower::power2str($this->power1);
		$this->power2_str = kxPower::power2str($this->power2);
		$this->power3_str = kxPower::power2str($this->power3);
		$this->power4_str = kxPower::power2str($this->power4);
		$this->power5_str = kxPower::power2str($this->power5);
		$this->power6_str = kxPower::power2str($this->power6);
	}

	public function set_power()
	{
		$this->power1 = kxPower::str2power($this->power1_str);
		$this->power2 = kxPower::str2power($this->power2_str);
		$this->power3 = kxPower::str2power($this->power3_str);
		$this->power4 = kxPower::str2power($this->power4_str);
		$this->power5 = kxPower::str2power($this->power5_str);
		$this->power6 = kxPower::str2power($this->power6_str);
	}

	// 将int型变量a的第k位清0，即 a=a&~(1<<k)
	// 将int型变量a的第k位置1， 即 a=a|(1<<k)
	//取int型变量a的第k位 a>>k&1

	/*a % 2 等价于 a & 1  ( a  & log2(2))
	a % 4 等价于 a & 2  ( a  & log2(4))
	.....
	a % 32 等价于 a & 5
	*/
	public static function str2power($str)
	{
		$power_int_arr = array(0,0,0,0,0,0,0,0,0,0);
		$power_arr = explode(',', $str);
		if ($power_arr)
		{
			foreach ($power_arr as $power_arr_item)
			{
				$point = $power_arr_item % 31;
				$position = floor($power_arr_item / 31);				
				$power_int_arr[$position] = $power_int_arr[$position] | (1 << intval($point));
			}
		}
		return implode('|', $power_int_arr);
	}

	public static function power2str($power_int_str)
	{
		$str = '';
		$str_arr = array();
		$power_int_arr = explode('|', $power_int_str);
		foreach ($power_int_arr as $position => $power_int)
		{
			$power_int = intval($power_int);
			if ($power_int)
			{
				for ($i = 0; $i < 31; $i++) 
				{
					if ($power_int >> $i & 1) 
					{
						$str_arr[] = $i + (30 * $position);
					}
				}
			}
		}
		$str = implode(',', $str_arr);
		return $str;
	}

	public static function is_power_exist( $uid, $modular, $type)
	{
		$return_id = 0;
		$sql = '';

		if($uid && $modular && $type)
		{
			$sql = "select `id` from `power` where modular = " . $modular . " and type = " . $type ." and uid = " . $uid ;
		}
		else{
			return $return_id;
		}

		$records = query_sql_backend($sql);
		if ($records) {
			while (($row = $records->fetch_row()) != false) {
				$return_id = intval($row[0]);
				break;
			}
			$records->free();
		}
		return $return_id;
	}
}


//class kxPowerListFactory extends kxListFactory 
//{
//    public $key = 'power_control_power_list_';
//    public function __construct($dbobj, $uid = null, $id_multi_str='') 
//    {
//        //$id_multi_str 是用,分隔的字符串
//        if($uid) 
//        {
//            $this->key = $this->key.$uid;
//            $this->sql = "select `id` from `power` where uid=".intval($uid)."";
//            parent::__construct($dbobj, $this->key);
//            return true;
//        }
//        elseif ($id_multi_str) 
//        {
//            $this->key = $this->key.md5($id_multi_str);
//            parent::__construct($dbobj, $this->key, null, $id_multi_str);
//            return true;
//        }
//        return false;
//    }
//}

class kxPowerListFactory extends kxListFactory 
{
	public $key = 'power_control_power_list_';
	public function __construct($dbobj, $modular, $type) {
		$this->key = $this->key.$modular.$type;
		$this->sql = "select `id` from `power` where  modular=".intval($modular)." and type=".intval($type)."";
		parent::__construct($dbobj, $this->key);
		return true;
	}
}

class kxPowerMultiFactory extends kxMutiStoreFactory 
{
    public $key = 'power_control_power_multi_';
    private $sql;

    public function __construct($dbobj, $key_objfactory=null, $id=null, $key_add='') 
    {
        if( !$key_objfactory && !$id )
        {
            return false;
        }
        $this->key = $this->key.$key_add;
        $ids = '';
        if($key_objfactory) 
        {
            if($key_objfactory->initialize()) 
            {
                $key_obj = $key_objfactory->get();
                $ids = implode(',', $key_obj);
            }
        }
        $fields = "
            `id`
            , `modular`
            , `type`
            , `uid`
            , `name`

            , `power1`
            , `power2`
            , `power3`
            , `power4`
            , `power5`

            , `power6`
            , `init_time`
            , `update_time`
            ";

        if( $id != null )
        {
            $this->bInitMuti = false;
            $this->sql = "select $fields from power where `id`=".intval($id)."";
        }
        else
        {
            $this->sql = "select $fields from power ";
            if($ids)
            {
                $this->sql = $this->sql." where `id` in ($ids) ";
            }
        }
        parent::__construct($dbobj, $this->key, $this->key, $key_objfactory, $id);
        return true;
    }

    public function retrive() 
    {
        $records = query_sql_backend($this->sql);
        if( !$records ) 
        {
            return null;
        }

        $objs = array();
        while ( ($row = $records->fetch_row()) != false ) 
        {
            $obj = new kxPower;

            $obj->id = intval($row[0]);
            $obj->modular = intval($row[1]);
            $obj->type = intval($row[2]);
            $obj->uid = intval($row[3]);
            $obj->name = ($row[4]);

            $obj->power1 = ($row[5]);
            $obj->power2 = ($row[6]);
            $obj->power3 = ($row[7]);
            $obj->power4 = ($row[8]);
            $obj->power5 = ($row[9]);

            $obj->power6 = ($row[10]);
            $obj->init_time = intval($row[11]);
            $obj->update_time = intval($row[12]);

            $obj->before_writeback();
            $objs[$this->key.'_'.$obj->id] = $obj;
        }
        $records->free();
        unset($records);
        return $objs;
    }
}

class kxPowerFactory extends kxFactory
{
    const objkey = 'power_control_power_multi_';
    private $sql;
    public function __construct($dbobj, $id) 
    {
        $serverkey = self::objkey;
        $objkey = self::objkey."_".$id;
        $this->sql = "select
            `id`
            , `modular`
            , `type`
            , `uid`
            , `name`

            , `power1`
            , `power2`
            , `power3`
            , `power4`
            , `power5`

            , `power6`
            , `init_time`
            , `update_time`

            from `power`
            where `id`=".intval($id)."";

        parent::__construct($dbobj, $serverkey, $objkey);
        return true;
    }

    public function retrive() 
    {
        $records = query_sql_backend($this->sql);
        if( !$records ) 
        {
            return null;
        }

        $obj = null;
        while ( ($row = $records->fetch_row()) != false ) 
        {
            $obj = new kxPower;

            $obj->id = intval($row[0]);
            $obj->modular = intval($row[1]);
            $obj->type = intval($row[2]);
            $obj->uid = intval($row[3]);
            $obj->name = ($row[4]);

            $obj->power1 = ($row[5]);
            $obj->power2 = ($row[6]);
            $obj->power3 = ($row[7]);
            $obj->power4 = ($row[8]);
            $obj->power5 = ($row[9]);

            $obj->power6 = ($row[10]);
            $obj->init_time = intval($row[11]);
            $obj->update_time = intval($row[12]);

            $obj->before_writeback();
            break;
        }
        $records->free();
        unset($records);
        return $obj;
    }
}
