<?php
    @session_start();
    $live=180;//过期时间为180s，在refresh时重新加上
    // store session data
    $_SESSION['out_time']=time()+$live;
    //echo $_SESSION['out_time'];
?>