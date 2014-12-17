<?php
if(!defined('SSH2DEF')) {//防止重定义
define('SSH2DEF', 'OK');

$ini_path = realpath(dirname(__FILE__)).'/ini.class.php';
include($ini_path);
define('SSH_PORT', 22);

/*
*在目标服务器上执行先关命令
*返回值：执行结果
*错误代码：
*0:Ip有误，在配置文件中无法查询
*-1:连接失败，即无法连接上改主机
*-2:认证错误，用户名或密码有误
*/
function ssh2execu($host, $cmd) {
	$ini = new ini();
	$board_count = $ini->iniRead("device_info", "board_count");
	for($i = 1; $i <= $board_count; $i++)
	{
		if(strcmp($ini->iniRead("board".$i, "ipaddr"), $host)==0)
			break;
	}
	if($i == 6) {
		if(strcmp($ini->iniRead("shmc", "ipaddr"), $host)==0) {	
			$auth = $ini->iniRead("shmc", "user");
			$pass = $ini->iniRead("shmc", "password");
		}
		else {
			error_log("ssh2:wrong ip addr.", 0);
			return 0;
		}
	}
	else {
		$auth = $ini->iniRead("board".$i, "user");
		$pass = $ini->iniRead("board".$i, "password");
	}

	return ssh2exe($host, $auth, $pass, $cmd);
}

function ssh2Shmc($cmd) {
	$ini = new ini();
	$shmc_ip = $ini->iniRead("shmc", "ipaddr");
	$auth = $ini->iniRead("shmc", "user");
	$pass = $ini->iniRead("shmc", "password");
	return ssh2exe($shmc_ip, $auth, $pass, $cmd);
}

/*
*真正执行连接函数
*返回值：执行结果
*错误代码：
*-1:连接失败，即无法连接上改主机
*-2:认证错误，用户名或密码有误
*/
function ssh2exe($host, $auth, $pass, $cmd) {

	$connection = ssh2_connect($host, SSH_PORT);
	if (!$connection) {
		error_log("ssh2:cannot connect.", 0);
		return -1;
	}
	else {
		 if(ssh2_auth_password($connection, $auth, $pass)) {
			$stream = ssh2_exec($connection, $cmd);
			stream_set_blocking($stream, true);
			$ref = stream_get_contents($stream);
			return $ref;
		 }
		 else {
			error_log("ssh2:login failure.", 0);
			return -2;
		}
	}
}

}//end SSH2DEF