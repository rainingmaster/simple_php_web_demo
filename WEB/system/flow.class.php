<?php
if(!defined('FLOWDEF')) {//��ֹ�ض���
define('FLOWDEF', 'OK');

$ssh2_path = realpath(dirname(__FILE__).'/../').'/include/ssh2.php';//������һ����Ȼ���ٽ���include
include($ssh2_path);
Class flow {
	public $interfaces = array();
	function __construct($ip) {
		//��ø����ӿ�������Ϣ
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
		$inter["if_name"] = $mess_array[0];//�ӿ���
		$inter["rece_bytes"] = $mess_array[1];//�����ֽ���
		$inter["rece_packets"] = $mess_array[2];//���հ���
		$inter["rece_errs"] = $mess_array[3];//���մ�����
		$inter["rece_drop"] = $mess_array[4];//���ն�����
		$inter["trans_bytes"] = $mess_array[9];//�����ֽ���
		$inter["trans_packets"] = $mess_array[10];//���Ͱ���
		$inter["trans_errs"] = $mess_array[11];//���ʹ�����
		$inter["trans_drop"] = $mess_array[12];//���Ͷ�����
		return $inter;
	}
};

}//end FLOWDEF