<?php

function arrayPrint($data=[]){
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

function exceutionStartTimeCheck(){
    return microtime(true);
}

function exceutionEndTimeCheck(){
    return microtime(true);
}

function exceutionTimeCheck($start_time, $end_time){
    return ($end_time - $start_time);
}

function rand_color() {
    return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
}
