<?php
	//由于cache_start、cache_end是连用，所以直接可以用cache_start的变量
	
	$contents = ob_get_contents();//从缓存中取内容
	file_put_contents(CACHE_PATH.$pageid.(time()+TTL), $contents);//写入缓存文件中
	ob_end_flush();//将缓存flush