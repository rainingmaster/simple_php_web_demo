<?php
define("CACHE_PATH", realpath(dirname(__FILE__).'/../').'/cache/');//缓存存储路径。返回上一级，然后再进入include
define("TTL", 60);//缓存存储时间，单位秒

if(isset($_GET['cache']))
	$cache_flag = $_GET['cache'];
else
	$cache_flag = 1;
	

if (! is_dir(CACHE_PATH)) {
	if (! mkdir(CACHE_PATH,0777)) {
		error_log("cache:".CACHE_PATH."目录创建失败", 0); 
		die("cache:".CACHE_PATH."目录创建失败");
	}
}

$url = $_SERVER['SCRIPT_NAME'];//子url一般是唯一的
$pageid = md5($url);//md5作为唯一id

$file = glob(CACHE_PATH.$pageid."*");//查找任意以改pageid为前缀的文件

if ($cache_flag != '0' and count($file) != 0) {//存在缓存文件
	$time = str_replace($pageid, '', basename($file[0]));
	if (time() < $time + TTL) { //时间没过期
		echo file_get_contents($file[0]);
		exit(0);
	}
}

for ($i = 0, $l = count($file); $i < $l; $i++) { //删掉全部旧文件
	unlink($file[$i]);
}

//开始缓存，将内容存入缓冲区
ob_start();
