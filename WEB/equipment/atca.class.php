<?php
if(!defined('ATCADEF')) {//��ֹ�ض���
define('ATCADEF', 'OK');
/*
*0930:������д�ã�����atcaSnmpWalk�Դ����ԣ��������ܻ�ȡ��ȷ����
*1009:����������ݣ���������ҳ��sel��lan��Ҫ��������������ʵ��
*1010:������־ģ�飬ʹ��ssh2
*1011:����������Ҳ�г�Ϊ����fru���������÷��ȵȼ�
*1013:��Ĺ���
*1015:�������·ּ�atca->device->{ipmc; fru; sensor},���ֹ������ssh2�����ȡ��������Ϣ
*1103:���ȴ��������������ݱȽ����أ�������Ҫ�����ü����ٽ�ֵ����Ҫ��β�ѯ������ʹ��ajax��������Ҫ���д�ӿ�
*1112:��ȡ�����ļ��й����Ip��ַ
*/
//��дini�����ļ���
$ssh2_path = realpath(dirname(__FILE__).'/../').'/include/ssh2.php';//������һ����Ȼ���ٽ���include
include($ssh2_path);

/*some define*/
$ini = new ini();
$man_ip = $ini->iniRead("shmc", "ipaddr");
define('MANAGE_IP', $man_ip);
define('USER', 'adlinkonly');

/**************************common function****************************/
/*
*�м��ȡ����
*haystack:�������ַ���
*front:ǰ����ַ���
*behind:������ַ�����Ϊ����ȫ��
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
*״̬���뺯��
*/
function stateDict($state) { 
	switch($state) {
		case 0:
			return "δ��װ";
		case 1:
			return "�ǻ";
		case 2:
			return "��������";
		case 3:
			return "���ڼ���";
		case 4:
			return "FRU�";
		case 5:
			return "ȡ����������";
		case 6:
			return "����ȡ������";
		case 7:
			return "ͨ�Ŷ�ʧ";
		default:
			return "wrong data:".$state;
	}
}

/*
*fru���෭�뺯��
*/
function frutypeDict($frutype) { 
	switch($frutype) {
		case 0:
			return "ATCA����";
		case 1:
			return "��Դ����ģ��";
		case 2:
			return "����FRU��Ϣ";
		case 3:
			return "ShMC";
		case 4:
			return "��������";
		case 5:
			return "���ȹ���������";
		case 192:
			return "BMCģ��";
		default:
			error_log("atca:num is".$frutype.",need updata dictionary", 0);
			return "num is".$frutype.",need updata dictionary";
	}
}

/*
*���������෭�뺯��
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
*����������ת��Ϊ�ַ����������ȥ��ǰ����������
*/
function atcaSnmpWalk($mib_oid, $tar_ip = MANAGE_IP, $usr = USER) {//�����Ļ��Զ�����snmpget������getͨ��
	$temp = snmpwalk($tar_ip, $usr, $mib_oid);
	$len = count($temp);
	if(!$temp || $len == 0){
		$single = snmpget($tar_ip, $usr, $mib_oid);//����ʹ��snmpget��õ����ڵ���Ϣ
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
*��׼��snmpset����������ĳЩ����
*/
function atcaSnmpSet($mib_oid, $type, $val, $tar_ip = MANAGE_IP, $usr = USER) {
	return snmpset($tar_ip, $usr, $mib_oid, $type, $val);
}

/*
*��׼��ssh2����ִ��
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
	*sensor���캯��
	*/
	function __construct($owner_ipmb, $id, $type='', $str_name='', $cur_val='') {
		$this->Owner_ipmb = $owner_ipmb;
		$this->Id = $id;
		
		/*��������ֵΪ��*/
		//û�е��Լ���
		$this->Type = ($type == '' ? atcaSnmpWalk("enterprises.16394.2.1.1.3.1.11.".$this->Owner_ipmb.".".$this->Id) : $type);
		$this->Str_name = ($str_name == '' ? atcaSnmpWalk("enterprises.16394.2.1.1.3.1.43.".$this->Owner_ipmb.".".$this->Id) : $str_name);
		@$this->Cur_val = number_format(($cur_val == '' ? atcaSnmpWalk("enterprises.16394.2.1.1.3.1.29.".$this->Owner_ipmb.".".$this->Id) : $cur_val), 2, ".", "");	
		
	}
	
	function __set($property,$value) {
		$this->$property = $value;
	}
	
	function __get($property) {
		if(isset($this->$property) === false)
			return "δ����";
		return ($this->$property);
	}
	
	public function loadThreshold() {//��ʱԼ1s
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

/*Fru��field replaceable unit�ֳ��ɸ�����Ԫ�� ������*/
class Fru {
	public $Ipmb_addr;//ipbm��ַ����10���Ʊ�ʾ
	public $Fru_id;//fru��ţ�����͹���ģ�鶼ֻ��һ��0���
	public $Slot_site;//���λ�ñ��
	public $Type;//fru����
	public $State;//����״̬ M0-M7
	public $Str_name;//�ַ�����
	public $Power;//���Ĺ���
	
	/*
	*���캯��
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
			return "δ����";
		return ($this->$property);
	}
	
	/*
	*�����������رո�Fruģ��
	*/
	public function shutdownFru() {
		return atcaSnmpSet("enterprises.16394.2.1.1.2.1.12.".$this->Ipmb_addr.".".$this->Fru_id, "i", 0);
	}
	
	/*
	*����������������Fruģ��
	*/
	public function startFru() {
		return atcaSnmpSet("enterprises.16394.2.1.1.2.1.12.".$this->Ipmb_addr.".".$this->Fru_id, "i", 1);
	}
}

/*�����࣬��һ��fru�����Ϊ0*/
class Board extends Fru {
	public $Manufacture_time;//����ʱ��
	private $ManIf;//����˿�
	private $ManIpaddr;//����IP
	
	/*
	*board���캯��
	*/
	function __construct($include = true, $ipmb_addr = '', $fru_id = '', $slot_site = '', $type = '', $state = '', $str_name = '', $power = '') {//α����
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
	*��������
	*/
	public function resetBoard() {
		return atcaSnmpSet("enterprises.16394.2.1.1.32.1.4.$this->Slot_site.0", "i", 1);
	}
	
	/*
	public function loadManInfo() {//ͨ����۱�Ż�ù�����Ϣ
		$ini = new ini(CONF_PATH);
		$this->ManIf = $ini->iniRead("board".$this->Slot_site, "man_if");
		$this->ManIpaddr = $ini->iniRead("board".$this->Slot_site, "man_ip");
	}
	
	public function loadIfLst($man_ip) {//ʹ��rtm��snmp
		$mib_oid = "1.3.6.1.2.1.4.24.9.3.0";
		$tar_ip = $this->ManIpaddr;
		$usr = "public";
		$if_lst = atcaSnmpWalk($mib_oid, $tar_ip, $usr);
		if(count(if_lst) <= 0)return false;
		return $if_lst;
	}
	
	public function setInterface($eth, $addr, $submask, $gateway = '') {
		if($eth == $this->ManIf) {//��Ҫд�������ļ�����
			$ini->iniWrite("board".$this->Slot_site, $addr);
			$ini->iniUpdate();
		}
		//����ϵͳ����ip
		$original = atcaSSHExec('awk "/IPADDR/" /etc/sysconfig/network-scripts/ifcfg-eth0', $this->ManIpaddr);//ԭ����ip��Ϣ
		atcaSSHExec('sed -i "s/'.trim($original).'/IPADDR='.$addr.'/g" $file_path', $this->ManIpaddr);
		//����ϵͳ������������
		$original = atcaSSHExec('awk "/PREFIX/" /etc/sysconfig/network-scripts/ifcfg-eth0', $this->ManIpaddr);//ԭ����ip��Ϣ
		atcaSSHExec('sed -i "s/'.trim($original).'/PREFIX='.$submask.'/g" $file_path', $this->ManIpaddr);
		//����ϵͳ��������
		$original = atcaSSHExec('awk "/GATEWAY/" /etc/sysconfig/network-scripts/ifcfg-eth0', $this->ManIpaddr);//ԭ����ip��Ϣ
		atcaSSHExec('sed -i "s/'.trim($original).'/GATEWAY='.$gateway.'/g" $file_path', $this->ManIpaddr);
		atcaSSHExec('service network restart', $this->ManIpaddr);
	}
	*/
};

/*����ģ���࣬��һ��fru�����Ϊ0*/
class Shmc extends Fru {
	public $Manufacture_time;//����ʱ��
	
	/*
	*Shcm���캯��
	*/
	function __construct($include = true, $ipmb_addr, $fru_id, $slot_site = '', $type = '', $state = '', $str_name = '', $power = '') {//α����
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
	*��������ģ��
	*/
	public function resetShmc() {
		return atcaSnmpSet("enterprises.16394.2.1.1.35.1.6.$this->Slot_site.0", "i", 1);
	}
	
	public function setInterface($eth, $addr, $submask, $gateway = '') {
		
	}
};

/*���������࣬�����ж��*/
class Fantray extends Fru {
	private $Minfanlevel;//��С���ȵȼ�
	private $Maxfanlevel;//�����ȵȼ�
	private $Curfanlevel;//��ǰ���ȵȼ�
	
	/*
	*Fantray���캯��
	*/
	function __construct($ipmb_addr, $fru_id, $slot_site = '', $type = '', $state = '', $str_name = '', $power = '') {//α����
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
	*���÷���������͵ȼ�
	*/
	public function loadMinFanlevel($level) {
		return atcaSSHExec("clia minfanlevel ".dechex($this->Ipmb_addr)." ".$this->Fru_id." ".$level);
	}
	
	/*
	*���÷������̵�ǰ�ȼ�
	*����¶Ȳ���Ҫ�������½�
	*/
	public function setCurFanlevel($level) {
		return atcaSSHExec("clia setfanlevel ".dechex($this->Ipmb_addr)." ".$this->Fru_id." ".$level);
	}
};

/*ipmc��*/
class Ipmc {
	private $Ipmb_addr;//ipmb��ַ
	private $Str_name;//�ַ�����
	/*
	*Ipmc���캯��
	*/
	function __construct($ipmb, $name) {
		$this->Ipmb_addr = $ipmb;
		$this->Str_name = $name;
	}
	
	public static function getInstance($ipmb, $str_name = '') {
		$name = ($str_name == '' ? atcaSnmpWalk("enterprises.16394.2.1.1.1.1.9.".$ipmb) : $str_name);
		if($name == false)//���ܱ��ر���
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

/*�豸�࣬������ipд���̶��ļ�*/
class Device {
	private $Ipmb_addr;//ipmb��ַ
	private $Present;//�Ƿ���ذ�װ
	private $Base_type;//�������ͣ��ɷ�Ϊboard(����), shmc(����ģ��), shelf(����)
	private $Ipmc;//Ipm����ģ��
	private $Fru_lst = array();//fru�б�����board, shmc, fantray������
	private $Sensor_lst = array();//sensor�б�
	/*private $Manager_if;//����˿�
	private $Manager_addr;//����ip��ַ
	private $Manager_pass;//��������
	private $Interface_lst = array();//�ӿ��б�*/
	
	/*
	*�豸���캯��
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
	*����fru�б�
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
	*���ش������б�
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
	*��ȡ�ð���ȴ������б�type == 4
	*���ط��ȴ������б�
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
	*��ȡ�ð��ѹ�������б�type == 2
	*���ص�ѹ�������б�
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
	*��ȡ�ð��¶ȴ������б�type == 1
	*�����¶ȴ������б�
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

/*atca�����࣬�洢����ϵͳ��Ϣ*/
class Atca {
	public $Device_lst = array();//�豸�б�
	
	/*
	*���캯��
	*/
	function __construct($include = "IPMC|FRU|SENSOR") {
		if(stripos($include, "NULL") !== false)
			return;
			
		$this->loadShmc($include);
		$this->loadBoard($include);
		$this->loadShelf($include);
	}
	
	/*
	*��ȡ���й���ģ����Ϣ
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
	*��ȡ���е�����Ϣ
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
	*��ȡ���л�����Ϣ
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
	*������Ѱ�װ���豸��Ϣ
	*û�з��豸����
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
	*����û�Ծ���豸��Ϣ
	*û�з��豸����
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
	*��õ����豸�б�
	*/
	public function getBoardIpmbLst() {
		$ref = atcaSnmpWalk("enterprises.16394.2.1.1.4.1.5");
		return $ref;
	}
	
	/*
	*��ù���ģ���豸�б�
	*/
	public function getShmcIpmbLst() {
		$ref = atcaSnmpWalk("enterprises.16394.2.1.1.35.1.2");
		return $ref;
	}
	
	/*
	*��û����豸�б�
	*/
	public function getShelfIpmbLst() {
		$board_ipmb = atcaSnmpWalk("enterprises.16394.2.1.1.4.1.5");
		$shmc_ipmb = atcaSnmpWalk("enterprises.16394.2.1.1.35.1.2");
		$ref = array_diff(atcaSnmpWalk("enterprises.16394.2.1.1.1.1.4"), array_merge($board_ipmb, $shmc_ipmb));
		$ref = array_merge($ref);
		return $ref;
	}
		
	/*
	*չʾ����fru�����µ�sensor��Ϣ����ʱ���ڲ���
	*/
	public function showAll($detail = true) {
		print("<b>�豸�б�Ϊ��</b><br/>");
		for($n = 0, $l = count($this->Device_lst); $n < $l; $n++) {
			print("�豸".$n."��ipmb��ַΪ".$this->Device_lst[$n]->Ipmb_addr."������Ϊ".$this->Device_lst[$n]->Base_type."��ǰ".($this->Device_lst[$n]->Present == true ? "����" : "δ����")."<br/>");
			if($this->Device_lst[$n]->Present) {
				print("ipmc��ַΪ".$this->Device_lst[$n]->Ipmc->Ipmb_addr."<br/>");
				print("fru�б�Ϊ:<br/>");
				$fru = $this->Device_lst[$n]->Fru_lst;
				for($i = 0; $i < count($fru); $i++) {
					print($i."��fru����Ϊ".$fru[$i]->Str_name.",");
					print("��ǰ״̬Ϊ".stateDict($fru[$i]->State)."<br/>");
					print("fruIDΪ".$fru[$i]->Fru_id."<br/>");
					print("��۱��Ϊ".$fru[$i]->Slot_site."<br/>");
					print("����ΪΪ".frutypeDict($fru[$i]->Type)."<br/>");
					print("����Ϊ".$fru[$i]->Power."<br/>");
				}
				print("sensor�б�Ϊ:<br/>");
				$sensor = $this->Device_lst[$n]->Sensor_lst;
				for($i = 0; $i < count($sensor); $i++) {
					print($i."��Sensor����Ϊ".$sensor[$i]->Str_name."<br/>");
				}
			}
		}
	}
};

/*��־ʵ����*/
class SelEntry {
	public $Id;
	public $Create_time;
	public $Ipmb_addr;
	public $Sensor_id;
	public $Sensor_type;
	public $Event;
	
	/*
	*���캯��
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

/*��־�б���*/
class Sel {//��Ҫʹ��ssh������snmp��ò�֪����ν���
	public $Sel_lst = array();
	public $Sel_count;
	
	/*
	*���캯��
	*/
	function __construct() {
		/*if($this->clearSel())
			print("success");*/
	}
	
	/*
	*��ȡ��־�б�˽�з���
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
	*��ȡtail��־�б�
	*/
	public function getSelTail() {
		$this->loadSel("clia sel | tail");
	}
	
	/*
	*��ȡȫ����־�б�������ʹ�ã���ʮ�־ã�
	*/
	public function getSelAll() {
		$this->loadSel("clia sel");
	}
	
	/*
	*��ȡĳipmb��־�б�������ʹ�ã���־�����ʮ�־ã�
	*/
	public function getSelByIpmb($ipmb) {
		$this->loadSel('clia sel | | grep -i "0x'.dechex($ipmb));
	}
	
	/*
	*��ȡĳsensor��־�б�������ʹ�ã���־�����ʮ�־ã�
	*/
	public function getSelBySensor($ipmb, $sensor) {
		$this->loadSel('clia sel | | grep -i "0x<ipmb addr>'.dechex($ipmb).'.*sensor.*'.dechex($sensor).'"');
	}
	
	/*
	*���ȫ����־�б�
	*/
	public function clearSel() {
		$result = atcaSSHExec("clia sel clear");
		if(stripos($result, "success"))
			return true;
		return false;
	}
};

/*�ӿ�ʵ����*/
class InterfaceEntry {
	public $Device_name;
	public $Ip_addr;
	public $Sub_mask;
	public $Dft_gw_ip;
	
	/*
	*���豸��ýӿ���Ϣ
	*/
	public function getIfConfig() {
	}
	
	/*
	*�����豸�ӿ���Ϣ
	*/
	public function setIfConfig() {
	}
	
};

}//end ATCADEF
