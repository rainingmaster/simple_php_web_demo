<?php
if(!defined('FLOWDEF')) {//防止重定义
define('FLOWDEF', 'OK');

$ssh2_path = realpath(dirname(__FILE__).'/../').'/include/ssh2.php';//返回上一级，然后再进入include
include($ssh2_path);
Class flow {
	public $interfaces = array();
	function __construct($ip) {
		//获得各个接口流量信息
		$ref = ssh2execu($ip, "cat /proc/net/dev");
		if (strlen($ref) < 3)
			return NULL;
		$ref = str_replace(":", " ", $ref);
		$if_array = explode("\n", $ref);
		for ($i = 2, $l = count($if_array); $i < $l; $i++) {
			if ($if_array[$i] != "")
				$this->interfaces[] = $this->getInterFlow($if_array[$i]);
		}
	}
	
	public function getInterFlow($if_mess) {
		$mess = trim(preg_replace('/\s\s+/', ' ', $if_mess));
		$mess_array = explode(" ", $mess);
		$inter = array();
		$inter["if_name"] = $mess_array[0];//接口名
		$inter["rece_bytes"] = $mess_array[1];//接收字节数
		$inter["rece_packets"] = $mess_array[2];//接收包数
		$inter["rece_errs"] = $mess_array[3];//接收错误数
		$inter["rece_drop"] = $mess_array[4];//接收丢包数
		$inter["trans_bytes"] = $mess_array[9];//发送字节数
		$inter["trans_packets"] = $mess_array[10];//发送包数
		$inter["trans_errs"] = $mess_array[11];//发送错误数
		$inter["trans_drop"] = $mess_array[12];//发送丢包数
		return $inter;
	}
};

}//end FLOWDEF