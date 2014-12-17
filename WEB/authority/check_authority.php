<?php
    @session_start();
    if (!isset($_SESSION['out_time'])) {//没有登录
        echo "no login";
        //header("Location: http://www.baidu.com"); 
        //确保重定向后，后续代码不会被执行 
        exit;
    } else if ($_SESSION['out_time'] < time()) {//超时
        echo "time out";
        //header("Location: http://www.baidu.com"); 
        //确保重定向后，后续代码不会被执行 
        exit;
    }
    include_once("init_authority.php");
    //echo "login";
?>