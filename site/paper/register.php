<?php

$_SESSION = array(); session_destroy; session_start;

$sTitle .= 'Новая регистрая';

if (isset($_POST['contact']))
{

	$uContact = trim($_POST['contact']);
	
	if (substr_count($uContact,"@") == 1 )
	{ $email = $uContact; } else { $tel =  $uContact; }


} else
{

$name = ''; $email = ''; $tel = ''; $pwd = '';
}

$er_red = 'style="color: red;"';

if ( isset($_POST['name']) )
{
	$pwd = trim($_POST['pwd']); 
	if ( strlen($pwd) < 4 || strlen($pwd) > 30 ) { $err = 1; $err_info = 'Введите пароль от 4 до 30 символов'; $pwd_err = $er_red; }
	$pwd = userinput($pwd);

	$email = trim($_POST['email']); $email = userinput($email);
	$tel = trim($_POST['tel']); $tel = userinput($tel);
	if ( strlen($email) == 0 && strlen($tel) == 0 )	{ $err = 1; $err_info = 'Укажите свои контактные данные!'; $email_err = $er_red; $tel_err = $er_red; } else
	{
		if ( strlen($email) > 0 )
		{
			if ( substr_count($email,"@") != 1 ) { $err = 1; $err_info = 'Введите правильный e-mail адрес!'; $email_err = $er_red;  }
		} else
		{ // telephone
			if ( strlen($tel) < 10 || strlen($tel) > 12  ) { $err = 1; $err_info = 'Введите правильный номер телефона! Шаблон: +71231234567 ( только +7 )'; $tel_err = $er_red; }
		}
	}	

	$name = trim($_POST['name']);
	if ( strlen($name) <= 2 ) { $err = 1; $err_info = 'Введите свое имя!'; $name_err = $er_red; }
	$name = userinput($name);
	if ( strlen($name) > 50 ) { $err = 1; $err_info = 'Превышена допустимая длина для ввода имени!'; $name_err = $er_red; }
	
	if ( $err == 1 ) { $err_info = '<p style="color: red;">'.$err_info.'</p>'; } else
	{
		$pwdo = date("s").date("i"); $pwdo = $pwdo + 1986; $date = date("Y-m-d H:i:s"); $br = $_SERVER['HTTP_USER_AGENT']; $ip = $_SERVER['REMOTE_ADDR'];
		$p = str_replace(array ('$1$'.$pwdo.'$'), array(''), crypt($pwd, '$1$'.$pwdo.'$')); $pwdo = $pwdo + 1205;
	//	$err_info = '<p style="color: red;">'.$br.' '.$ip.'</p>';

// подробная проверка контактов и регистрация

if ( $tel != '' )
{
	if ( $tel[0] == '8' ) { $tel[0] = '7'; $tel = '+'.$tel; } if ( $tel[1] == '8' ) { $tel[1] = '7'; }
	if ( preg_match('/^[+]\d+$/', $tel) && $tel[0] == '+' && $tel[1] == '7' ) { $tel_good = $tel; } else 
	{ $err = 1; $err_info = 'Введите правильный номер телефона! Шаблон: +71231234567 ( только +7 )'; $tel_err = $er_red; }
}

if ( $err != 1 )
{
	if ( $email != '' ) { $NEWemail = mysql_query("select * from users where email='$email'"); $femail = mysql_num_rows($NEWemail); } else { $femail = 0; }
	if ( $tel != '' ) { $NEWsms = mysql_query("select * from users where sms='$tel'"); $ftel = mysql_num_rows($NEWsms); } else { $ftel = 0; }

	if ( $femail == 0 && $ftel == 0 )
	{ // такого пользователя нет

		$new = mysql_query("insert into users (sms, email, name, pwd, reg_date, reg_ip, reg_browser, pwdone) 
							values ('$tel', '$email', '$name', '$p', '$date', '$ip', '$br', '$pwdo')") or die(mysql_error());
			
		$err_info = 'Регистрация прошла успешно!';

	} else { $err = 1; $err_info = 'Простите, но у вас уже есть зарегистрированный аккаунт на webid.in!'; }


}	

// ------------------------------------------


		if ( $err == 1 ) { $err_info = '<p style="color: red;">'.$err_info.'</p>'; } else { $err_info = '<p id="good_reg" style="color: green;">'.$err_info.'</p>'; }
	}
} else { $name_err = ' '; }

if ( $name_err != '' ) { $name_focus = 'autofocus'; } else
{
	if ( $email_err != '' ) { $email_focus = 'autofocus'; } else
	{
		if ( $tel_err != '' ) { $tel_focus = 'autofocus'; } else
		{
			if ( $pwd_err != '' ) { $pwd_focus = 'autofocus'; }
		}
	}
}

/*


<input type="submit" id="sptbtn" class="btn" value="Написать" />
 onclick="process(10);"
*/

$temp_content = '<h1>Новая регистрация</h1><p>Заполните поля указанные ниже:</p>'.$err_info.'
		
<form method="post" action="/register">		
		<div class="forms"><div class="left" '.$name_err.'>Ваше имя:</div>
		<div class="right"><input id="newuser-name" type="text" name="name" value="'.$name.'" '.$name_focus.' /></div></div>

		<div class="forms"><div class="left" '.$email_err.'>Ваш E-mail:</div>
		<div class="right"><input id="newuser-email" type="email" name="email" value="'.$email.'" '.$email_focus.' /></div></div>

		<div class="forms"><div class="left" '.$tel_err.'>или номер телефона:</div>
		<div class="right"><input id="newuser-tel" type="tel" name="tel" value="'.$tel.'" '.$tel_focus.' /></div></div>
		<div class="forms"><br /></div>

		<div class="forms"><div class="left" '.$pwd_err.'>Пароль:</div>
		<div class="right"><input id="newuser-pwd" type="password" name="pwd" value="" '.$pwd_focus.' autocomplete="off" /></div></div>

		<div class="forms"><div class="left"></div>
		<div class="right"><input type="submit" id="sptbtn" class="btn" value="Продолжить" /></div></div></form>';

$iContent = $temp_content;


?>