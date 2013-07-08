<?php

function set_twitter_config($access_token) {
    //保存下来用户的access_token
    $user_file_name = USER_DIR . $access_token["screen_name"] . ".twitter.config";
    return file_put_contents($user_file_name, serialize($access_token));
}

function get_twitter_config($twitter_id) {
    $Str = array();
    //保存下来用户的access_token
    $user_file_name = USER_DIR . $twitter_id . ".twitter.config";
    if (file_exists($user_file_name)) {
        $Str = file_get_contents($user_file_name);
        $Str = unserialize($Str);
    }
    return $Str;
}

function get_log($log_id) {
    $Str = array();
    $user_file_name = LOG_DIR . $log_id . ".log";
    if (file_exists($user_file_name)) {
        $Str = file_get_contents($user_file_name);
        $Str = unserialize($Str);
    }
    return $Str;
}

function set_log($log_id, $str) {
    $user_file_name = LOG_DIR . $log_id . ".log";
    return file_put_contents($user_file_name, serialize($str));
}

function set_error_log($log_id, $str) {
    $user_file_name = LOG_DIR . $log_id . ".error.log";
    $fh = fopen($user_file_name, 'a');
    $result = fwrite($fh, print_r($str,true) . "\n");
    fclose($fh);
    return $result;
}

function set_debug_log($log_id, $str) {
    $user_file_name = LOG_DIR . $log_id . ".debug.log";
    $fh = fopen($user_file_name, 'a');
    $result = fwrite($fh, print_r($str,true) . "\n");
    fclose($fh);
    return $result;
}