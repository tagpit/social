<?php

$contact = $_POST['login'];
$pwd = $_POST['pwd'];






if ( substr_count($contact,"@") == 1 ) { $NEWemail = mysql_query("select * from users where email='$contact'"); } else
{

	$csms = $contact;

	if ( strlen($csms) == 10 ) { $csms = '+7'.$csms; }

	if ( strlen($csms) == 11 && $csms[0] == '8' ) { $csms[0] = '7'; $csms = '+'.$csms; }

	if ( strlen($csms) == 11 && $csms[0] == '7' ) { $csms = '+'.$csms; }

	if ( strlen($csms) == 12 && preg_match('/^[+]\d+$/', $csms) && $csms[0] == '+' && $csms[1] == '7' && $csms[2] == '9' )
	{
		/* добавки если введен номер телефона */ $NEWsms = mysql_query("select * from users where sms='$csms'");
		$contact = $csms;
	}
}




if ( mysql_num_rows($NEWemail) != 0 || mysql_num_rows($NEWsms) != 0 )
{
	if ( mysql_num_rows($NEWemail) != 0 ) { $login = $NEWemail; } else { $login = $NEWsms; }

	$LOGINpwd = mysql_result($login, 0, "pwd");
	$LOGINcode = mysql_result($login, 0, "pwdone");
	$LOGINid = mysql_result($login, 0, "id");
	$LOGINname = mysql_result($login, 0, "name");
	$LOGINcode -= 1205; $input_pwd = str_replace(array ('$1$'.$LOGINcode.'$'), array(''), crypt($pwd, '$1$'.$LOGINcode.'$'));

	if ( $input_pwd == $LOGINpwd )
	{ 	session_destroy(); session_start(); $_SESSION['sess_name'] = $LOGINname; $_SESSION['sess_passwd'] = $input_pwd; $_SESSION['sess_user'] = $LOGINid;
		$result = '<y>#id'.$LOGINid;
	} else { $result = '<e>'.$_POST['login'].'<e>Пароль не верен!'; }
} else { $result = '<e>'.$_POST['login'].'<e>Пользователь не найден!'; }

?>