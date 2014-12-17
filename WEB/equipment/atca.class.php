<?php
if(!defined('ATCADEF')) {//防止重定义
define('ATCADEF', 'OK');
/*
*0930:整体框架写好，其中atcaSnmpWalk仍待测试！！还不能获取正确数据
*1009:正常获得数据，整理入网页。sel和lan需要用其他操作方法实现
*1010:新增日志模块，使用ssh2
*1011:将风扇托盘也列出为特殊fru，便于设置风扇等级
*1013:大改构造
*1015:改造重新分级atca->device->{ipmc; fru; sensor},部分功能添加ssh2替代获取和设置信息
*1103:风扇传感器读到的数据比较奇特，可能需要单独裁剪；临界值等需要多次查询的数据使用ajax，可能需要配合写接口
*1112:读取配置文件中管理端Ip地址
*/
//读写ini配置文件类
$ssh2_path = realpath(dirname(__FILE__).'/../').'/include/ssh2.php';//返回上一级，然后再进入include
include($ssh2_path);

/*some define*/
$ini = new ini();
$man_ip = $ini->iniRead("shmc", "ipaddr");
define('MANAGE_IP', $man_ip);
define('USER', 'adlinkonly');

/**************************common function****************************/
/*
*中间截取函数
*haystack:待查找字符串
*front:前面的字符串
*behind:后面的字符串，为空则全部
*/
function getMidStr($haystack, $front, $behind = '') { 
	$fro_pos = strpos($haystack, $front);
	if($fro_pos === false) return false;
	$fro_pos = $fro_pos + strlen($front);
	if($behind == '')
		return substr($haystack, $fro_pos);
		
	$haystack = substr($haystack, $fro_pos);
	$be_pos = strpos($haystack, $behind);
	if($be_pos === false) return false;
	
	return substr($haystack, 0, $be_pos);
}

/*
*状态翻译函数
*/
function stateDict($state) { 
	switch($state) {
		case 0:
			return "未安装";
		case 1:
			return "非活动";
		case 2:
			return "激活请求";
		case 3:
			return "正在激活";
		case 4:
			return "FRU活动";
		case 5:
			return "取消激活请求";
		case 6:
			return "正在取消激活";
		case 7:
			return "通信丢失";
		default:
			return "wrong data:".$state;
	}
}

/*
*fru种类翻译函数
*/
function frutypeDict($frutype) { 
	switch($frutype) {
		case 0:
			return "ATCA单板";
		case 1:
			return "电源输入模块";
		case 2:
			return "机框FRU信息";
		case 3:
			return "ShMC";
		case 4:
			return "风扇托盘";
		case 5:
			return "风扇过滤器托盘";
		case 192:
			return "BMC模块";
		default:
			error_log("atca:num is".$frutype.",need updata dictionary", 0);
			return "num is".$frutype.",need updata dictionary";
	}
}

/*
*传感器种类翻译函数
*/
function sensortypeDict($sensortype) { 
	switch($sensortype) {
		case 1:
			return "temperature";
		case 2:
			return "voltage";
		case 4:
			return "fan";
		case 18:
			return "systemEvent";
		case 35:
			return "watchdog2";
		case 37:
			return "entityPresence";
		case 40:
			return "managementSubsystemHealth";
		case 43:
			return "reserved43";
		case 221:
			return "reserved221";
		case 222:
			return "reserved222";
		case 223:
			return "reserved223";
		case 240:
			return "hotswap";
		case 241:
			return "ipmblink";
		case 244:
			return "reserved244";
		default:
			error_log("atca:num is".$sensortype.",need updata dictionary", 0);
			return "num is".$sensortype.",need updata dictionary";
	}
}

/*
*将单个数组转化为字符，多个数组去掉前面数据类型
*/
function atcaSnmpWalk($mib_oid, $tar_ip = MANAGE_IP, $usr = USER) {//单个的会自动调用snmpget，即和get通用
	$temp = snmpwalk($tar_ip, $usr, $mib_oid);
	$len = count($temp);
	if(!$temp || $len == 0){
		$single = snmpget($tar_ip, $usr, $mib_oid);//尝试使用snmpget获得单个节点信息
		if($single === false) {
			error_log("atca:can not connect management cotroller,in ".$tar_ip." ".$mib_oid."is wrong", 0);
			print("<br/>in ".$tar_ip." ".$mib_oid."is wrong");
			die("<br/>can not connect management cotroller<br/>");
		}
			
		return trim(stripos($single, ':') !== false ? substr($single, stripos($single, ':') + 1) : $single);
	}
	switch($len) {
		case 1:
			return trim(stripos($temp[0], ':') !== false ? substr($temp[0], stripos($temp[0], ':') + 1) : $temp[0]);
			break;
		default:
			$ret = array();
			for($i = 0; $i < $len; $i++) {
				$ret[] = trim(stripos($temp[$i], ':') !== false ? substr($temp[$i], stripos($temp[$i], ':') + 1) : $temp[$i]);
			}
			return $ret;
	}
}

/*
*标准化snmpset，用于设置某些参数
*/
function atcaSnmpSet($mib_oid, $type, $val, $tar_ip = MANAGE_IP, $usr = USER) {
	return snmpset($tar_ip, $usr, $mib_oid, $type, $val);
}

/*
*标准化ssh2命令执行
*/
function atcaSSHExec($cmd) {
	return ssh2Shmc($cmd);
}

/**************************some class****************************/

/*sensor type list*/
class Sensor {
	private $Owner_ipmb;
	private $Id;
	private $Type;
	private $Str_name;
	private $Cur_val;
	private $Threshold = array();
	
	/*
	*sensor构造函数
	*/
	function __construct($owner_ipmb, $id, $type='', $str_name='', $cur_val='') {
		$this->Owner_ipmb = $owner_ipmb;
		$this->Id = $id;
		
		/*允许下列值为空*/
		//没有的自己找
		$this->Type = ($type == '' ? atcaSnmpWalk("enterprises.16394.2.1.1.3.1.11.".$this->Owner_ipmb.".".$this->Id) : $type);
		$this->Str_name = ($str_name == '' ? atcaSnmpWalk("enterprises.16394.2.1.1.3.1.43.".$this->Owner_ipmb.".".$this->Id) : $str_name);
		@$this->Cur_val = number_format(($cur_val == '' ? atcaSnmpWalk("enterprises.16394.2.1.1.3.1.29.".$this->Owner_ipmb.".".$this->Id) : $cur_val), 2, ".", "");	
		
	}
	
	function __set($property,$value) {
		$this->$property = $value;
	}
	
	function __get($property) {
		if(isset($this->$property) === false)
			return "未设置";
		return ($this->$property);
	}
	
	public function loadThreshold() {//耗时约1s
		$result = atcaSSHExec("clia getthreshold ".dechex($this->Owner_ipmb)." ".$this->Id);
		$thres_lst = explode("\n", $result);
		for($n = 0, $l = count($thres_lst); $n < $l; $n++) {
			if(stripos($thres_lst[$n], "Upper Non-Recoverable") !== false) {
				@$this->Threshold["unr"] = number_format(getMidStr($thres_lst[$n], "Processed data: ", " "), 2, ".", "");//Upper_non_recoverable_threshold
			}
			else if(stripos($thres_lst[$n], "Upper Critical") !== false) {
				@$this->Threshold["uc"] = number_format(getMidStr($thres_lst[$n], "Processed data: ", " "), 2, ".", "");//Upper_critical_threshold
			}
			else if(stripos($thres_lst[$n], "Upper Non-Critical") !== false) {
				@$this->Threshold["unc"] = number_format(getMidStr($thres_lst[$n], "Processed data: ", " "), 2, ".", "");//Upper_non_critical_threshold
			}
			else if(stripos($thres_lst[$n], "Lower Non-Critical") !== false) {
				@$this->Threshold["lnc"] = number_format(getMidStr($thres_lst[$n], "Processed data: ", " "), 2, ".", "");//Lower_non_critical_threshold
			}
			else if(stripos($thres_lst[$n], "Lower Critical") !== false) {
                $try_lc = getMidStr($thres_lst[$n], "Processed data: ", " ");//Lower_critical_threshold
				if ($try_lc == false) {
					@$this->Threshold["lc"] = number_format(getMidStr($thres_lst[$n], "(Lower Critical Threshold): ", " RPM"), 2, ".", "");//for fan
				}
                else {
                    @$this->Threshold["lc"] = number_format($try_lc, 2, ".", "");
                }
			}
			else if(stripos($thres_lst[$n], "Lower Non-Recoverable") !== false) {
				@$this->Threshold["lnr"] = number_format(getMidStr($thres_lst[$n], "Processed data: ", " "), 2, ".", "");//Lower_non_recoverable_threshold
			}
		}
	}
	
	public function updataThreshold($unr = '', $uc = '', $unc = '', $lnc = '', $lc = '', $lnr = '') {
		if($unr != '') {atcaSSHExec("clia setthreshold ".dechex($this->Owner_ipmb)." ".$this->Id." unr ".$unr);}
		if($uc != '') {atcaSSHExec("clia setthreshold ".dechex($this->Owner_ipmb)." ".$this->Id." uc ".$uc);}
		if($unc != '') {atcaSSHExec("clia setthreshold ".dechex($this->Owner_ipmb)." ".$this->Id." unc ".$unc);}
		if($lnc != '') {atcaSSHExec("clia setthreshold ".dechex($this->Owner_ipmb)." ".$this->Id." lnc ".$lnc);}
		if($lc != '') {atcaSSHExec("clia setthreshold ".dechex($this->Owner_ipmb)." ".$this->Id." lc ".$lc);}
		if($lnr != '') {atcaSSHExec("clia setthreshold ".dechex($this->Owner_ipmb)." ".$this->Id." lnr ".$lnr);}
	}
};

/*Fru（field replaceable unit现场可更换单元） 基础类*/
class Fru {
	public $Ipmb_addr;//ipbm地址，用10进制表示
	public $Fru_id;//fru编号，单板和管理模块都只有一个0编号
	public $Slot_site;//插槽位置编号
	public $Type;//fru种类
	public $State;//开启状态 M0-M7
	public $Str_name;//字符串名
	public $Power;//消耗功率
	
	/*
	*构造函数
	*/
	function __construct($ipmb_addr, $fru_id, $slot = '', $type = '', $state = '', $strname = '', $power = '') {
		$this->Ipmb_addr = $ipmb_addr;
		$this->Fru_id = $fru_id;

		if($slot === '') {
			$result = atcaSSHExec("clia fru -v ".dechex($this->Ipmb_addr)." ".$this->Fru_id);
			if(strpos($result, "No IPM FRUs found")) return false;
			
			$this->Slot_site = intval(getMidStr($result, 'Site Number: ', "\n"), 10);
			$this->Type = hexdec(getMidStr($result, 'Site Type: 0x', ', Site Number'));
			$this->State = intval(getMidStr($result, 'Hot Swap State: M', ' ('), 10);
			$this->Str_name = getMidStr($result, 'Device ID String: "', "\"\n");
			$this->Power = getMidStr($result, 'Current Power Allocation: ', ' Watts');
		}
		else {
			$this->Slot_site = $slot;
			$this->Type = $type;
			$this->State = $state;
			$this->Str_name = $strname;
			$this->Power = $power;
		}	
	}

	function __set($property,$value) {
		$this->$property = $value;
	}
	
	function __get($property) {
		if(isset($this->$property) === false)
			return "未设置";
		return ($this->$property);
	}
	
	/*
	*公共方法，关闭该Fru模块
	*/
	public function shutdownFru() {
		return atcaSnmpSet("enterprises.16394.2.1.1.2.1.12.".$this->Ipmb_addr.".".$this->Fru_id, "i", 0);
	}
	
	/*
	*公共方法，开启该Fru模块
	*/
	public function startFru() {
		return atcaSnmpSet("enterprises.16394.2.1.1.2.1.12.".$this->Ipmb_addr.".".$this->Fru_id, "i", 1);
	}
}

/*单板类，仅一个fru，编号为0*/
class Board extends Fru {
	public $Manufacture_time;//运行时间
	private $ManIf;//管理端口
	private $ManIpaddr;//管理IP
	
	/*
	*board构造函数
	*/
	function __construct($include = true, $ipmb_addr = '', $fru_id = '', $slot_site = '', $type = '', $state = '', $str_name = '', $power = '') {//伪重载
		if($include) {
			parent::__construct($ipmb_addr, $fru_id, $slot_site, $type, $state, $str_name, $power);
			
			$this->Manufacture_time = atcaSnmpWalk("enterprises.16394.2.1.1.32.1.19.".$this->Slot_site);
		}
	}
	
	function __set($property,$value) {
		$this->$property = $value;
	}
	
	function __get($property) {
		return ($this->$property);
	}
	
	/*
	*重启单板
	*/
	public function resetBoard() {
		return atcaSnmpSet("enterprises.16394.2.1.1.32.1.4.$this->Slot_site.0", "i", 1);
	}
	
	/*
	public function loadManInfo() {//通过插槽编号获得管理信息
		$ini = new ini(CONF_PATH);
		$this->ManIf = $ini->iniRead("board".$this->Slot_site, "man_if");
		$this->ManIpaddr = $ini->iniRead("board".$this->Slot_site, "man_ip");
	}
	
	public function loadIfLst($man_ip) {//使用rtm的snmp
		$mib_oid = "1.3.6.1.2.1.4.24.9.3.0";
		$tar_ip = $this->ManIpaddr;
		$usr = "public";
		$if_lst = atcaSnmpWalk($mib_oid, $tar_ip, $usr);
		if(count(if_lst) <= 0)return false;
		return $if_lst;
	}
	
	public function setInterface($eth, $addr, $submask, $gateway = '') {
		if($eth == $this->ManIf) {//需要写入配置文件更新
			$ini->iniWrite("board".$this->Slot_site, $addr);
			$ini->iniUpdate();
		}
		//更新系统网卡ip
		$original = atcaSSHExec('awk "/IPADDR/" /etc/sysconfig/network-scripts/ifcfg-eth0', $this->ManIpaddr);//原来的ip信息
		atcaSSHExec('sed -i "s/'.trim($original).'/IPADDR='.$addr.'/g" $file_path', $this->ManIpaddr);
		//更新系统网卡子网掩码
		$original = atcaSSHExec('awk "/PREFIX/" /etc/sysconfig/network-scripts/ifcfg-eth0', $this->ManIpaddr);//原来的ip信息
		atcaSSHExec('sed -i "s/'.trim($original).'/PREFIX='.$submask.'/g" $file_path', $this->ManIpaddr);
		//更新系统网卡网关
		$original = atcaSSHExec('awk "/GATEWAY/" /etc/sysconfig/network-scripts/ifcfg-eth0', $this->ManIpaddr);//原来的ip信息
		atcaSSHExec('sed -i "s/'.trim($original).'/GATEWAY='.$gateway.'/g" $file_path', $this->ManIpaddr);
		atcaSSHExec('service network restart', $this->ManIpaddr);
	}
	*/
};

/*管理模块类，仅一个fru，编号为0*/
class Shmc extends Fru {
	public $Manufacture_time;//运行时间
	
	/*
	*Shcm构造函数
	*/
	function __construct($include = true, $ipmb_addr, $fru_id, $slot_site = '', $type = '', $state = '', $str_name = '', $power = '') {//伪重载
		if($include) {
			parent::__construct($ipmb_addr, $fru_id, $slot_site, $type, $state, $str_name, $power);
			
			$this->Manufacture_time = atcaSnmpWalk("enterprises.16394.2.1.1.35.1.18.".$this->Slot_site);
		}
	}
	
	function __set($property,$value) {
		$this->$property = $value;
	}
	
	function __get($property) {
		if($property == "Manufacture_time")
			return date("Y-m-d H:i:s", $this->Manufacture_time);
		else
			return ($this->$property);
	}
	/*
	*重启管理模块
	*/
	public function resetShmc() {
		return atcaSnmpSet("enterprises.16394.2.1.1.35.1.6.$this->Slot_site.0", "i", 1);
	}
	
	public function setInterface($eth, $addr, $submask, $gateway = '') {
		
	}
};

/*风扇托盘类，可能有多个*/
class Fantray extends Fru {
	private $Minfanlevel;//最小风扇等级
	private $Maxfanlevel;//最大风扇等级
	private $Curfanlevel;//当前风扇等级
	
	/*
	*Fantray构造函数
	*/
	function __construct($ipmb_addr, $fru_id, $slot_site = '', $type = '', $state = '', $str_name = '', $power = '') {//伪重载
		parent::__construct($ipmb_addr, $fru_id, $slot_site, $type, $state, $str_name, $power);
		
		$result = atcaSSHExec("clia fans ".dechex($this->Ipmb_addr)." ".$this->Fru_id);
		$this->Minfanlevel = getMidStr($result, "Minimum Speed Level: ", ", Maximum");
		$this->Maxfanlevel = getMidStr($result, "Maximum Speed Level: ", "    Dynamic");
		$this->Curfanlevel = getMidStr($result, "Current Level: ", "\n");
	}
	
	function __set($property,$value) {
		$this->$property = $value;
	}
	
	function __get($property) {
		return ($this->$property);
	}
	
	/*
	*设置风扇托盘最低等级
	*/
	public function loadMinFanlevel($level) {
		return atcaSSHExec("clia minfanlevel ".dechex($this->Ipmb_addr)." ".$this->Fru_id." ".$level);
	}
	
	/*
	*设置风扇托盘当前等级
	*如果温度不需要将慢慢下降
	*/
	public function setCurFanlevel($level) {
		return atcaSSHExec("clia setfanlevel ".dechex($this->Ipmb_addr)." ".$this->Fru_id." ".$level);
	}
};

/*ipmc类*/
class Ipmc {
	private $Ipmb_addr;//ipmb地址
	private $Str_name;//字符串名
	/*
	*Ipmc构造函数
	*/
	function __construct($ipmb, $name) {
		$this->Ipmb_addr = $ipmb;
		$this->Str_name = $name;
	}
	
	public static function getInstance($ipmb, $str_name = '') {
		$name = ($str_name == '' ? atcaSnmpWalk("enterprises.16394.2.1.1.1.1.9.".$ipmb) : $str_name);
		if($name == false)//可能被关闭了
			return false;
			
		return (new Ipmc($ipmb, $name));
	}
	
	function __set($property,$value) {
		$this->$property = $value;
	}
	
	function __get($property) {
		return ($this->$property);
	}
};

/*设备类，将管理ip写到固定文件*/
class Device {
	private $Ipmb_addr;//ipmb地址
	private $Present;//是否挂载安装
	private $Base_type;//基础类型，可分为board(单板), shmc(控制模块), shelf(机框)
	private $Ipmc;//Ipm控制模块
	private $Fru_lst = array();//fru列表，包括board, shmc, fantray和其他
	private $Sensor_lst = array();//sensor列表
	/*private $Manager_if;//管理端口
	private $Manager_addr;//管理ip地址
	private $Manager_pass;//管理密码
	private $Interface_lst = array();//接口列表*/
	
	/*
	*设备构造函数
	*/
	function __construct($ipmb, $present = "", $base = "", $include = "IPMC|FRU|SENSOR") {
		$this->Ipmb_addr = $ipmb;
		$this->Base_type = $base;
		
		$this->Present = $present;
		
		if($this->Present == 1) {
			if(stripos($include, "IPMC") !== false)
				$this->Ipmc = Ipmc::getInstance($this->Ipmb_addr);
			if(stripos($include, "FRU") !== false)
				$this->loadFruLst();
			if(stripos($include, "SENSOR") !== false)
				$this->loadSensorLst();
		}
	}
	
	/*
	*加载fru列表
	*/
	private function loadFruLst() {
		$result = atcaSSHExec("clia fru -v ".dechex($this->Ipmb_addr));
		$fru_ar = explode( "\n\n", $result);
		for($n = 1, $l = count($fru_ar); $n < $l; $n++) {
			$fru_id = getMidStr($fru_ar[$n], 'FRU # ', "\n");
			if($fru_id === false) continue;
			$slot_site = intval(getMidStr($fru_ar[$n], 'Site Number: ', "\n"), 10);
			$type = hexdec(getMidStr($fru_ar[$n], 'Site Type: 0x', ', Site Number'));
			$state = intval(getMidStr($fru_ar[$n], 'Hot Swap State: M', ' ('), 10);
			$str_name = getMidStr($fru_ar[$n], 'Device ID String: "', "\"\n");
			$power = getMidStr($fru_ar[$n], 'Current Power Allocation: ', ' Watts');
			
			switch($type) {
				case 1:
					$new_fru = new Board(true, $this->Ipmb_addr, $fru_id, $slot_site, $type, $state, $str_name, $power);
					break;
				case 3:
					$new_fru = new Shmc(true, $this->Ipmb_addr, $fru_id, $slot_site, $type, $state, $str_name, $power);
					break;
				case 4:
					$new_fru = new Fantray($this->Ipmb_addr, $fru_id, $slot_site, $type, $state, $str_name, $power);
					break;
				default:
					$new_fru = new Fru($this->Ipmb_addr, $fru_id, $slot_site, $type, $state, $str_name, $power);
			}
			$this->Fru_lst[] = $new_fru;
		}
	}
	
	/*
	*加载传感器列表
	*/
	private function loadSensorLst() {
		$sensor_id = atcaSnmpWalk("enterprises.16394.2.1.1.3.1.6.".$this->Ipmb_addr);
		$sensor_type = atcaSnmpWalk("enterprises.16394.2.1.1.3.1.11.".$this->Ipmb_addr);
		$sensor_str_name = atcaSnmpWalk("enterprises.16394.2.1.1.3.1.43.".$this->Ipmb_addr);
		$sensor_val = atcaSnmpWalk("enterprises.16394.2.1.1.3.1.29.".$this->Ipmb_addr);
		for($n = 0, $l = count($sensor_id); $n < $l; $n++) {
			$new_sensor = new Sensor($this->Ipmb_addr, $sensor_id[$n], $sensor_type[$n], $sensor_str_name[$n], $sensor_val[$n]);
			$this->Sensor_lst[] = $new_sensor;
		}
	}
	
	function __set($property,$value) {
		$this->$property = $value;
	}
	
	function __get($property) {
		return ($this->$property);
	}

	/*
	*获取该板风扇传感器列表，type == 4
	*返回风扇传感器列表
	*/
	public function getFanSensorLst() {
		$ref = array();
		$result = atcaSSHexec("clia sensordata ".dechex($this->Ipmb_addr));
		if(strpos($result, '"Fan"') === false)
			return $this->Sensor_lst;
		
		$sensor_str_lst = explode("\n\n", $result);
		for($n = 0, $l = count($sensor_str_lst); $n < $l; $n++) {
			if(strpos($sensor_str_lst[$n], '"Fan"') !== false) {
				$id = getMidStr($sensor_str_lst[$n], "Sensor # ", " (");
				$type = hexdec(getMidStr($sensor_str_lst[$n], " (0x", '), "'));
				$name = getMidStr($sensor_str_lst[$n], ' ("', '")');
				$val = getMidStr($sensor_str_lst[$n], "Processed data: ", " RPM\n");
				$new_sensor = new Sensor($this->Ipmb_addr, $id, $type, $name, $val);
				$this->Sensor_lst[] = $new_sensor;
			}
		}
		return $this->Sensor_lst;
	}
	
	/*
	*获取该板电压传感器列表，type == 2
	*返回电压传感器列表
	*/
	public function getVolSensorLst() {
		$ref = array();
		$result = atcaSSHexec("clia sensordata ".dechex($this->Ipmb_addr));
		if(strpos($result, '"Voltage"') === false)
			return $this->Sensor_lst;
		
		$sensor_str_lst = explode("\n\n", $result);
		for($n = 0, $l = count($sensor_str_lst); $n < $l; $n++) {
			if(strpos($sensor_str_lst[$n], '"Voltage"') !== false) {
				$id = getMidStr($sensor_str_lst[$n], "Sensor # ", " (");
				$type = hexdec(getMidStr($sensor_str_lst[$n], " (0x", '), "'));
				$name = getMidStr($sensor_str_lst[$n], ' ("', '")');
				$val = getMidStr($sensor_str_lst[$n], "Processed data: ", " Volts\n");
				$new_sensor = new Sensor($this->Ipmb_addr, $id, $type, $name, $val);
				$this->Sensor_lst[] = $new_sensor;
			}
		}
		return $this->Sensor_lst;
	}
	
	/*
	*获取该板温度传感器列表，type == 1
	*返回温度传感器列表
	*/
	public function getTemperSensorLst() {
		$ref = array();
		$result = atcaSSHexec("clia sensordata ".dechex($this->Ipmb_addr));
		if(strpos($result, '"Temperature"') === false)
			return $this->Sensor_lst;
		
		$sensor_str_lst = explode("\n\n", $result);
		for($n = 0, $l = count($sensor_str_lst); $n < $l; $n++) {
			if(strpos($sensor_str_lst[$n], '"Temperature"') !== false) {
				$id = getMidStr($sensor_str_lst[$n], "Sensor # ", " (");
				$type = hexdec(getMidStr($sensor_str_lst[$n], " (0x", '), "'));
				$name = getMidStr($sensor_str_lst[$n], ' ("', '")');
				$val = getMidStr($sensor_str_lst[$n], "Processed data: ", " degrees C\n");
				$new_sensor = new Sensor($this->Ipmb_addr, $id, $type, $name, $val);
				$this->Sensor_lst[] = $new_sensor;
			}
		}
		return $this->Sensor_lst;
	}
};

/*atca整体类，存储整个系统信息*/
class Atca {
	public $Device_lst = array();//设备列表
	
	/*
	*构造函数
	*/
	function __construct($include = "IPMC|FRU|SENSOR") {
		if(stripos($include, "NULL") !== false)
			return;
			
		$this->loadShmc($include);
		$this->loadBoard($include);
		$this->loadShelf($include);
	}
	
	/*
	*获取所有管理模块信息
	*/
	public function loadShmc($include) {
		$ref = array();
		$shmc_ipmb = atcaSnmpWalk("enterprises.16394.2.1.1.35.1.2");
		$shmc_present = atcaSnmpWalk("enterprises.16394.2.1.1.35.1.3");
		for($n = 0, $l = count($shmc_ipmb); $n < $l; $n++) {
			$ref[] = new Device($shmc_ipmb[$n], $shmc_present[$n], "Shmc", $include);
		}
		$this->Device_lst = array_merge($this->Device_lst, $ref);
		return $ref;
	}
	
	/*
	*获取所有单板信息
	*/
	public function loadBoard($include) {
		$ref = array();
		$board_ipmb = atcaSnmpWalk("enterprises.16394.2.1.1.4.1.5");
		$board_present = atcaSnmpWalk("enterprises.16394.2.1.1.32.1.8");
		for($n = 0, $l = count($board_ipmb); $n < $l; $n++) {
			$ref[] = new Device($board_ipmb[$n], $board_present[$n], "Board", $include);
		}
		$this->Device_lst = array_merge($this->Device_lst, $ref);
		return $ref;
	}
	
	/*
	*获取所有机箱信息
	*/
	public function loadShelf($include) {
		$ref = array();
		$board_ipmb = atcaSnmpWalk("enterprises.16394.2.1.1.4.1.5");
		$shmc_ipmb = atcaSnmpWalk("enterprises.16394.2.1.1.35.1.2");
		$other_ipmb = array_diff(atcaSnmpWalk("enterprises.16394.2.1.1.1.1.4"), array_merge($board_ipmb, $shmc_ipmb));
		$other_ipmb = array_merge($other_ipmb);
		for($n = 0, $l = count($other_ipmb); $n < $l; $n++) {
			$ref[] = new Device($other_ipmb[$n], 1, "Shelf", $include);
		}
		$this->Device_lst = array_merge($this->Device_lst, $ref);
		return $ref;
	}
	
	/*
	*仅获得已安装的设备信息
	*没有分设备类型
	*/
	public function getPresentLst($include = "IPMC") {
		$ref = array();
		$ipmb = atcaSnmpWalk("enterprises.16394.2.1.1.2.1.3");
		$ipmb_unique = array_unique($ipmb);
		foreach($ipmb_unique as $addr) {
			$new = new Device($addr, 1, "present", $include);
			$ref[] = $new;
		}
		return $ref;
	}
	
	/*
	*仅获得活跃的设备信息
	*没有分设备类型
	*/
	public function getLiveLst($include = "IPMC") {
		$ref = array();
		$ipmb = atcaSnmpWalk("enterprises.16394.2.1.1.1.1.4");
		for($n = 0, $l = count($ipmb); $n < $l; $n++) {
			$new = new Device($ipmb[$n], 1, "live", $include);
			$ref[] = $new;
		}
		return $ref;
	}
	
	/*
	*获得单板设备列表
	*/
	public function getBoardIpmbLst() {
		$ref = atcaSnmpWalk("enterprises.16394.2.1.1.4.1.5");
		return $ref;
	}
	
	/*
	*获得管理模块设备列表
	*/
	public function getShmcIpmbLst() {
		$ref = atcaSnmpWalk("enterprises.16394.2.1.1.35.1.2");
		return $ref;
	}
	
	/*
	*获得机箱设备列表
	*/
	public function getShelfIpmbLst() {
		$board_ipmb = atcaSnmpWalk("enterprises.16394.2.1.1.4.1.5");
		$shmc_ipmb = atcaSnmpWalk("enterprises.16394.2.1.1.35.1.2");
		$ref = array_diff(atcaSnmpWalk("enterprises.16394.2.1.1.1.1.4"), array_merge($board_ipmb, $shmc_ipmb));
		$ref = array_merge($ref);
		return $ref;
	}
		
	/*
	*展示所有fru和其下的sensor信息，暂时用于测试
	*/
	public function showAll($detail = true) {
		print("<b>设备列表为：</b><br/>");
		for($n = 0, $l = count($this->Device_lst); $n < $l; $n++) {
			print("设备".$n."的ipmb地址为".$this->Device_lst[$n]->Ipmb_addr."，种类为".$this->Device_lst[$n]->Base_type."当前".($this->Device_lst[$n]->Present == true ? "挂载" : "未挂载")."<br/>");
			if($this->Device_lst[$n]->Present) {
				print("ipmc地址为".$this->Device_lst[$n]->Ipmc->Ipmb_addr."<br/>");
				print("fru列表为:<br/>");
				$fru = $this->Device_lst[$n]->Fru_lst;
				for($i = 0; $i < count($fru); $i++) {
					print($i."号fru名字为".$fru[$i]->Str_name.",");
					print("当前状态为".stateDict($fru[$i]->State)."<br/>");
					print("fruID为".$fru[$i]->Fru_id."<br/>");
					print("插槽编号为".$fru[$i]->Slot_site."<br/>");
					print("种类为为".frutypeDict($fru[$i]->Type)."<br/>");
					print("功率为".$fru[$i]->Power."<br/>");
				}
				print("sensor列表为:<br/>");
				$sensor = $this->Device_lst[$n]->Sensor_lst;
				for($i = 0; $i < count($sensor); $i++) {
					print($i."号Sensor名字为".$sensor[$i]->Str_name."<br/>");
				}
			}
		}
	}
};

/*日志实体类*/
class SelEntry {
	public $Id;
	public $Create_time;
	public $Ipmb_addr;
	public $Sensor_id;
	public $Sensor_type;
	public $Event;
	
	/*
	*构造函数
	*/
	function __construct($id, $time, $ipmb, $sensor_id, $sensor_type, $event) {
		$this->Id = $id;
		$this->Create_time = $time;
		$this->Ipmb_addr = $ipmb;
		$this->Sensor_id = $sensor_id;
		$this->Sensor_type = $sensor_type;
		$this->Event = $event;	
	}
};

/*日志列表类*/
class Sel {//需要使用ssh方法，snmp获得不知道如何解码
	public $Sel_lst = array();
	public $Sel_count;
	
	/*
	*构造函数
	*/
	function __construct() {
		/*if($this->clearSel())
			print("success");*/
	}
	
	/*
	*获取日志列表私有方法
	*/
	private function loadSel($str_cmd) {
		$result = atcaSSHExec($str_cmd);
		$lst = explode("\n", $result);
		for($n = 0, $l = count($lst); $n < $l; $n++) {
			$id = hexdec(substr($lst[$n], 0, 5));
			$time = substr($lst[$n], 17, 21);
			$ipmb = hexdec(substr($lst[$n], 46, 4));
			$sensor_id = hexdec(substr($lst[$n], 65, 4));
			$sensor_type = substr($lst[$n], 70, 1);
			$event = substr($lst[$n], 96);
			//print("id:$id, time:$time, ipmb:$ipmb, sensor:$sensor_id, sensort:$sensor_type, event:$event<br/>");
			$sel = new SelEntry($id, $time, $ipmb, $sensor_id, $sensor_type, $event);
			$this->Sel_lst[] = $sel;
		}
		$this->Sel_count = $n;
	}
	
	/*
	*获取tail日志列表
	*/
	public function getSelTail() {
		$this->loadSel("clia sel | tail");
	}
	
	/*
	*获取全部日志列表（不建议使用，将十分久）
	*/
	public function getSelAll() {
		$this->loadSel("clia sel");
	}
	
	/*
	*获取某ipmb日志列表（不建议使用，日志量大会十分久）
	*/
	public function getSelByIpmb($ipmb) {
		$this->loadSel('clia sel | | grep -i "0x'.dechex($ipmb));
	}
	
	/*
	*获取某sensor日志列表（不建议使用，日志量大会十分久）
	*/
	public function getSelBySensor($ipmb, $sensor) {
		$this->loadSel('clia sel | | grep -i "0x<ipmb addr>'.dechex($ipmb).'.*sensor.*'.dechex($sensor).'"');
	}
	
	/*
	*清空全部日志列表
	*/
	public function clearSel() {
		$result = atcaSSHExec("clia sel clear");
		if(stripos($result, "success"))
			return true;
		return false;
	}
};

/*接口实体类*/
class InterfaceEntry {
	public $Device_name;
	public $Ip_addr;
	public $Sub_mask;
	public $Dft_gw_ip;
	
	/*
	*从设备获得接口信息
	*/
	public function getIfConfig() {
	}
	
	/*
	*设置设备接口信息
	*/
	public function setIfConfig() {
	}
	
};

}//end ATCADEF
