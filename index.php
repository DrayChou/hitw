<?php

include 'config.php';
include 'common.php';
include 'twitteroauth/twitteroauth/twitteroauth.php';

$hitw = get_twitter_config(T_ID);
if( !empty( $hitw ) && !empty( $_POST['content'] ) ){
    $content = mb_substr($_POST['content'], 0, 139);

    if ( preg_match("/@/i", $content) ) {
	   $result = "不可以@别人哦~";
	} else {
	    $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $hitw['access_token']['oauth_token'], $hitw['access_token']['oauth_token_secret']);

	    $result = $twitteroauth->get('users/lookup', array('screen_name' => $hitw['access_token']["screen_name"]));
	    if( isset($result[0]) && ( empty($hitw['twitter']) || $hitw['twitter'] !== $result[0] ) ){
		    $hitw['twitter'] = @$result[0];
		    set_twitter_config($hitw);
	    }
	    
	    $result = $twitteroauth->post('statuses/update', array('status' => $content));
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
    <body style="background-color: powderblue;">
        <div style="text-align: center; margin: 160px auto auto;">
            <h1>無良大叔家的垃圾桶.</h1>

			<div>
			<?php if(empty( $hitw )): ?>
				<?php include('oauth.php'); ?>
			<?php else: ?>
				<?php if(empty( $result )): ?>
				<h2>說點什麼吧</h2>
                <form method="post" action="/">
                    <textarea name="content" style="width: 400px; height: 100px;"></textarea>
                    <p>
                    	<input type="submit" style="font-size: 50px;" value="我丢">
                    </p>
                </form>
                <?php else: ?>
                <?php 
					if (!empty($result->id_str)) {
				        $href = "https://twitter.com/#!/{$result->user->screen_name}/status/{$result->id_str}";
				        echo "成功：<a href='/'>返回</a>&nbsp;<a target='_blank' href='{$href}'>查看</a><br/>\n\n";
				    } else {
				        echo "<a href='/'>返回</a>&nbsp;发布失败<br/>\n\n";
				        if( is_object($result) ) {
					        var_dump($result);
				        }
				        echo "<p>",$result,"</p>";
					}
                ?>
                <?php endif;?>
			<?php endif;?>
			</div>
		</div>
    </body>
</html>

