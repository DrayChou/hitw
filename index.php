<?php

echo "<pre>";
var_dump($_SERVER);
echo "</pre>";
die();

include 'config.php';
include 'common.php';
include 'twitteroauth/twitteroauth/twitteroauth.php';

echo "<pre>";
var_dump($_SESSION);
echo "</pre>";

$hitw = get_twitter_config(T_ID);
echo "<pre>";
var_dump($hitw);
echo "</pre>";

if( !empty( $hitw ) && !empty( $_POST['content'] ) ){
    $content = substr($_POST['content'], 0, 140);
    echo "<pre>";
	var_dump($content);
	echo "</pre>";
    
    $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $hitw['access_token']['oauth_token'], $hitw['access_token']['oauth_token_secret']);
    echo "<pre>";
	var_dump($twitteroauth);
	echo "</pre>";
    
    $result = $twitteroauth->post('statuses/update', array('status' => $content));
    echo "<pre>";
	var_dump($result);
	echo "</pre>";
    if (!empty($result->id_str)) {
        $href = "https://twitter.com/#!/{$result->user->screen_name}/status/{$result->id_str}";
        echo "成功：<a target='_blank' href='{$href}'>地址</a><br/>\n\n";
    } else {
        echo "发布失败<br/>\n\n";
	}
}else{
    include('oauth.php');
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

			<?php if(empty( $hitw )): >
			<h3>请管理員先授权叔垃圾桶帐号</h3>
            <div>
                <?php if (!empty($twitter)): ?>
                    <h3>Twitter</h3>
                    <img src="<?= $twitter->profile_image_url_https ?>" title="<?= $twitter->name ?>"/><br/>
                    name:<?= $twitter->name ?><br/>
                    bio:<?= $twitter->description ?><br/>
                    <p><a href='./index.php?step=0'>退出登录</a></p>
                <?php else:?>
                    <a href='<?=$oauth_url?>'>twitter</a>
                <?php endif; ?>
            </div>
			<?php else:?>
				<h4>說點什麼吧</h4>
                <form method="post" action="/">
                    <input type="text" name="content">
                    <input type="submit">
                </form>
			<?php endif;>
    </body>
</html>

