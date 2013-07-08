<?php
include_once 'config.php';
include_once 'common.php';
include_once 'twitteroauth/twitteroauth/twitteroauth.php';

session_id("HoleInW-h-zh-x-tk");
session_start();

if ( isset($_REQUEST['oauth_token']) && $_SESSION['twitter_oauth_token'] !== $_REQUEST['oauth_token'] ) {
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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>無良大叔家的垃圾桶.</title>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <style type="text/css">
            img {border-width: 0}
            * {font-family:'Lucida Grande', sans-serif;}
        </style>
    </head>
    <body style="background-image: <?= $twitter->profile_background_image_url ?>;">
        <div>
            <h2>無良大叔家的垃圾桶.</h2>

            <div>
                <?php if (!empty($twitter)): ?>
                    <h3>Twitter</h3>
                    <img src="<?= $twitter->profile_image_url_https ?>" title="<?= $twitter->name ?>"/><br/>
                    name:<?= $twitter->name ?><br/>
                    bio:<?= $twitter->description ?><br/>
                    <p><a href='./index.php?setp=0'>退出登录</a></p>
                <?php else:?>
                    <a href='<?=$oauth_url?>'>twitter</a>
                <?php endif; ?>
            </div>

            <div>

				<?php
				    $hitw = get_twitter_config(T_ID);
				    echo "<pre>";
					var_dump($hitw);
					echo "</pre>";
				    if( !empty( $hitw ) && !empty( $_POST['dn'] ) ){
				        $value["content"] = ($_POST['dn']);
				        
				        $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $hitw['access_token']['oauth_token'], $hitw['access_token']['oauth_token_secret']);
				        
				        $result = $twitteroauth->post('statuses/update', array('status' => $value["content"]));
				        if (!empty($result->id_str)) {
				            $href = "https://twitter.com/#!/{$result->user->screen_name}/status/{$result->id_str}";
				            echo "成功：<a target='_blank' href='{$href}'>地址</a><br/>\n\n";
				                    
				            $value["twitter_href"] = $href;
				        } else {
				            set_douban_error_log($douban_id, array("douban" => $value, "result" => $result));
				            echo "发布失败<br/>\n\n";
						}
	                }
				?>
                <?php if (empty($hitw)): ?>
                    <h3>请管理員先授权叔垃圾桶帐号</h3>
                <?php else:?>
                    <h4></h4>
                    <form method="post" action="index.php?setp=1">
                        <input type="text" name="dn">
                        <input type="submit">
                    </form>
                <?php endif; ?>
            </div>
    </body>
</html>

