<?php

if( $_REQUEST['step'] == 0 ) {
	session_destroy();
} elseif ( isset($_REQUEST['oauth_token']) && $_SESSION['twitter_oauth_token'] !== $_REQUEST['oauth_token'] ) {
	echo "<pre>";
	var_dump($_REQUEST);
	var_dump($_SESSION);
	echo "</pre>";
	
  	session_destroy();
    echo "调用 twitter API 查询用户信息失败，请刷新页面重新验证，或者通知管理员<br/>";
    echo '<a href="/" >返回</a>';
    die();
    
} elseif ( isset($_REQUEST['oauth_token']) && !empty($_REQUEST['oauth_verifier']) && !empty($_SESSION['twitter_oauth_token']) && !empty($_SESSION['twitter_oauth_token_secret']) ) {
    // 数据合法，继续
	//echo "<pre>";
	//var_dump($_SESSION);
	//echo "</pre>";
    
    $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['twitter_oauth_token'], $_SESSION['twitter_oauth_token_secret']);

	//echo "<pre>";
	//var_dump($twitteroauth);
	//echo "</pre>";

    // 获取 access token
    $access_token = $twitteroauth->getAccessToken($_REQUEST['oauth_verifier']);

 //   echo "<pre>";
	//var_dump($access_token);
	//echo "</pre>";
    
    // 将获取到的 access token 保存到 Session 中
    $_SESSION['access_token'] = $access_token;
    $_SESSION['user_id'] = $access_token["user_id"];
    $_SESSION['screen_name'] = $access_token["screen_name"];
    unset($_SESSION['twitter_oauth_token']);
    unset($_SESSION['twitter_oauth_token_secret']);

    set_twitter_config($_SESSION);

 //   echo "<pre>";
	//var_dump($_SESSION);
	//echo "</pre>";
    //header('Location: /index.php');
    
    $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
    
    $result = $twitteroauth->get('users/lookup', array('screen_name' => $access_token["screen_name"]));

    if(!isset($result[0])){
		echo "<pre>";
		var_dump($_SESSION);
		var_dump($result);
		echo "</pre>";
	    
        session_destroy();
        echo "调用 twitter API 查询用户信息失败，请刷新页面重新验证，或者通知管理员<br/>";
        echo '<a href="javascript:window.top.location.reload();" >返回</a>';
        die();
    }
    $twitter = @$result[0];

} else {
    // 数据不完整，转到上一步
    unset($_SESSION['access_token']);

    // 创建 TwitterOAuth 对象实例
	$twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);

	// Requesting authentication tokens, the parameter is the URL we will be redirected to
	$request_token = $twitteroauth->getRequestToken(OAUTH_CALLBACK);
	//echo "<pre>";
	//var_dump($request_token);
	//echo "</pre>";

	// 保存到 session 中
	$_SESSION['twitter_oauth_token'] = $request_token['oauth_token'];
	$_SESSION['twitter_oauth_token_secret'] = $request_token['oauth_token_secret'];

	//echo "<pre>";
	//var_dump($_SESSION);
	//echo "</pre>";

	// 如果没有错误发生
	if ($twitteroauth->http_code == 200) {
	    // Let's generate the URL and redirect
	    $oauth_url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
	} else {
		//echo "<pre>";
		//var_dump($twitteroauth);
		//echo "</pre>";
	    // 发生错误，你可以做一些更友好的处理
	    die('Something wrong happened.');
	}
}

?>