<?php

$_SESSION = array(); session_destroy; session_start;

$pppusers = $ppp;

/* работа скрипта */

$temp_content = '<h1>Пользователям</h1>';

$uContact = trim($_POST['contact']); $uPwd = trim($_POST['pwd']);

if ( isset($_POST['contact']) && isset($_POST['pwd']) && strlen($_POST['contact']) > 4 )
{
	if ( substr_count($uContact,"@") == 1 ) { $fcEmail = mysql_query("select * from users where email='$uContact'"); } else
	{
		$csms = $uContact; if ( strlen($csms) == 10 ) { $csms = '+7'.$csms; }
		if ( strlen($csms) == 11 && $csms[0] == '8' ) { $csms[0] = '7'; $csms = '+'.$csms; }
		if ( strlen($csms) == 11 && $csms[0] == '7' ) { $csms = '+'.$csms; }
		if ( strlen($csms) == 12 && preg_match('/^[+]\d+$/', $csms) && $csms[0] == '+' && $csms[1] == '7' && $csms[2] == '9' )
		{ /* введен номер телефона */ $uContact = $csms; $fcSms = mysql_query("select * from users where sms='$uContact'"); }
	}

	if ( mysql_num_rows($fcEmail) == 1 || mysql_num_rows($fcSms) == 1 )
	{
		if ( mysql_num_rows($fcEmail) != 0 ) { $fcLogin = $fcEmail; } else { $fcLogin = $fcSms; }
		
		$LOGINpwd = mysql_result($fcLogin, 0, "pwd");
		$LOGINcode = mysql_result($fcLogin, 0, "pwdone");
		$LOGINid = mysql_result($fcLogin, 0, "id");
		// $LOGINname = mysql_result($fcLogin, 0, "name");
		$LOGINcode -= 1205; $input_pwd = str_replace(array ('$1$'.$LOGINcode.'$'), array(''), crypt($uPwd, '$1$'.$LOGINcode.'$'));
		
		if ( $input_pwd == $LOGINpwd )
		{
			session_destroy(); session_start();
			$_SESSION['sess_name'] = $uContact; $_SESSION['sess_passwd'] = $input_pwd; $_SESSION['sess_user'] = $LOGINid;
			header( 'Location: /id'.$LOGINid );

			$temp_content .= '<p style="color:green">Пароль верен!</p>';
		} else { $temp_content .= '<p style="color:red">Пароль не верен!</p>'; $uPwdfocus = 'autofocus'; }

	} else {
$regopen = 1;
require_once('paper/register.php');

// $temp_content .= '<p>Для доступа в закрытую часть сайта, необходимо создать себе учетную запись: <a href="/register">регистрация</a></p>';
}
} else { $temp_content .= '<p>Чтобы зарегистрироваться, просто введите свой номер мобильного телефона или адрес e-mail и нажмите продолжить.</p>'; $uContactfocus = 'autofocus'; }

if ( $regopen != 1 )
{
$sTitle .= 'Войти';

/* вывод информации */ $iContent = $temp_content.'<div class="forms"><form method="post" action="/users">
		<div class="forms"><div class="left">Ваш контакт:</div><div class="right"><input type="text" id="user-contact" name="contact" value="'.$uContact.'" '.$uContactfocus.' /></div></div>
		<div class="forms"><div class="left">Ваш пароль:</div><div class="right"><input type="password" id="user-fwd" name="pwd" value="'.$uPwd.'" '.$uPwdfocus.' /></div></div>
		<div class="forms"><div class="left"></div><div class="right"><input type="submit" id="sptbtn" class="btn" value="Продолжить" /></div></div></form></div>';
}

?>