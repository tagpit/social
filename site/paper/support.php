<?php

/* работа скрипта */

$sTitle .= 'Обратная связь';

$temp_content = '<h1>Обратная связь</h1>';

$support_info = '<p>Если возникли вопросы, нашли ошибки или есть предложения и идеи как сделать проект лучше - пишите нам.</p>';
$support_info_textarea = '';

$message = '';

if ( $uID == 'none' || isset($_POST['message']) ) { $name = ''; $email = ''; $tel = ''; } else 
{ $name = mysql_result($uFULL, 0, "name"); $email = mysql_result($uFULL, 0, "email"); $tel = mysql_result($uFULL, 0, "sms"); }




if ( isset($_POST['message']) )
{

	$message = trim($_POST['message']);
	$name = trim($_POST['name']);
	$email = trim($_POST['email']);
	$tel = trim($_POST['tel']);

	if ( strlen($message) >= 10 && strlen($name) >= 2 && ( ( strlen($email) > 4 ) || ( strlen($tel) >= 10) )  )
	{

		$to  = "selant@rambler.ru";
		$subject = 'Message from webid.in ot e-mail: '.$email; 
		$message = $name.' : email: '.$email.' or telephone:'.$tel.' :

Текст сообщения: '.$message; 

		$headers = "From: support webid.in <tagpit@gmail.com>\r\n";

		mail($to, $subject, $message, $headers);

		$support_info_textarea = $support_info;
		$support_info = '<p style="color: green;">Сообщение успешно отправлено!</p>';
		$message = '';

	} else
	{ // есть ошибки

$support_err = 0;

		$support_info_textarea = $support_info;
		$support_info = '<p style="color: red;">Исправьте ошибки: ';

		if ( strlen($message) < 10 ) { $support_info .= 'слишком короткое сообщение'; $support_err = 1; }

		if ( strlen($name) < 2 ) { if ( $support_err == 1 ) { $support_info .= ','; } $support_info .= ' укажите свое имя'; $support_err = 1; }

		if ( $support_err == 0 ) { $support_info .= ' укажите свои контактные данные'; }

		$support_info = $support_info.'.</p>';
	}


}


/* вывод информации */
$iContent = $temp_content.$support_info.'<div class="forms">

<form method="post" action="/support">

<div class="left">'.$support_info_textarea.'</div>
<div class="right"><textarea id="message" name="message" rows="10" autofocus >'.$message.'</textarea></div></div>
		<div class="forms"><div class="left">Ваше имя:</div><div class="right"><input type="text" id="support_user-name" name="name" value="'.$name.'" /></div></div>
		<div class="forms"><div class="left">Ваш E-mail:</div><div class="right"><input type="email" id="support_user-email" name="email" value="'.$email.'" /></div></div>
		<div class="forms"><div class="left">или номер телефона:</div><div class="right"><input type="tel" id="support_user-tel" name="tel" value="'.$tel.'" /></div></div>
		<div class="forms"><div class="left"></div><div class="right"><input type="submit" id="sptbtn" class="btn" value="Продолжить" /></div>

</form>

</div>';


?>