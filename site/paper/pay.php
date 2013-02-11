<?php

/* работа скрипта */
if ( $uID != 'none' )
{

if ($pID == 'in' || $pID == $uID )
{
	$error_pay = 0;
	$sTitle .= 'Пополнить баланс';
	$temp_content = '<h1>Пополнить баланс с помощью WebMoney</h1>';


	if ( isset($_POST['summa']) )
	{ // merchant
	
	} else $error_pay = 1;


	if ( $error_pay == 1 )
	{
	
$comment = base64_encode('Пополнение баланса пользователя webid.in/id'.$uID);
$temp_content .= '<div class="forms">

<form method="post" action="https://merchant.webmoney.ru/lmi/payment.asp">
		<div class="forms"><div class="left">Сумма в рублях:</div><div class="right">
			<input type=hidden name=LMI_PAYEE_PURSE value=R275538568929>
			<input type=hidden name=userid value='.$uID.'>
			<input type=hidden name=LMI_SIM_MODE value=0>
			<input type=hidden name=LMI_PAYMENT_DESC_BASE64 value="'.$comment.'">
		<input type="text" id="in_summa" name=LMI_PAYMENT_AMOUNT onkeyup="this.value = this.value.replace (/\D/, 1)"  value="100" /></div></div>
		<div class="forms"><div class="left"></div><div class="right"><input type="submit" id="sptbtn" class="btn" value="Продолжить" /></div></div>
</form>

</div>';


	}


} elseif ($pID == 'out')
{
	
	if ( $_POST['summa'] )
	{
		$balance = mysql_query("select * from balance where uid='$uID'");

		if (mysql_num_rows($balance) != 0 )
		{ /* проверка баланса */
			$rublik = mysql_result($balance, 0, "rublik");
			
			if ( $rublik - $_POST['summa'] >= 0 && $_POST['summa'] >= 1 )
			{
				$paid2out = mysql_result($balance, 0, "paid2out");
				$paid2out = $paid2out + $_POST['summa'];
				$rublik = $rublik - $_POST['summa'];
				$cost = $_POST['summa'];
				$wmr = $_POST['wmr'];
				$user_update = mysql_query("update balance set rublik='$rublik', paid2out='$paid2out' where id='$uID'") or die(mysql_error());
				$new = mysql_query("insert into wmout (uid, cost, wmr) values ('$uID', '$cost', '$wmr')") or die(mysql_error());
			
			} else
			{
				$sTitle .= 'Ошибка при заказе выплаты';
				$temp_content = '<h1>Ошибка при заказе выплаты</h1><p>У вас не достаточно средств для заказа выплаты в размере: '.$_POST['summa'].' руб.<br />Минимальная сумма для вывода: 1 рубль!</p>';
				
			}

		}
	} else
	{
		$sTitle .= 'Заказ выплаты';
		$temp_content = '<h1>Заказ выплаты</h1>';

		$temp_content .= '<div class="forms">

<form method="post" action="/pay-out">
		<div class="forms"><div class="left">Сумма в рублях:</div><div class="right">
		<input type="text" id="in_summa" name=summa onkeyup="this.value = this.value.replace (/\D/, 1)"  value="10" /></div></div>
		<div class="forms"><div class="left">Номер вашего WMR-кошелька или телефона:</div><div class="right">
		<input type="text" id="in_summa" name=wmr value="" /></div></div>
		<div class="forms"><div class="left"></div><div class="right"><input type="submit" id="sptbtn" class="btn" value="Продолжить" /></div></div>
</form>

</div>';
	}


} elseif ($pID == 'history')
{
	$sTitle .= 'История внутреннего счета';
	$temp_content = '<h1>История внутреннего счета</h1>';
	
	$balance = mysql_query("select * from balance where uid='$uID'");
	$rublik = mysql_result($balance, 0, "rublik");
	$paidin = mysql_result($balance, 0, "paidin");
	$paid2out = mysql_result($balance, 0, "paid2out");
	$paidout = mysql_result($balance, 0, "paidout");
	
	$temp_content .= '<p>Текущий баланс: '.$rublik.' руб.<br />
	Введено в систему: '.$paidin.' руб.<br />
	Выплат на сумму: '.$paidout.' руб.<br />
	Ожидаемых выплат на сумму: '.$paid2out.' руб.<br />	
	</p>';


} elseif ($pID == 'obmen')
{
	$sTitle .= 'Внутренний обмен';
	$temp_content = '<h1>Внутренний обмен</h1>';

	$temp_content .= '<p>Не доступно!</p>';
} elseif ($pID == 'good')
{
	$sTitle .= 'Баланс пополнен';
	$temp_content = '<h1>Баланс пополнен</h1>';
	$temp_content .= '<p>Пополнение баланса произведено.<br /> При возникновении вопросов - пишите нам, через обрутную связь!</p>';
}  elseif ($pID == 'fail')
{
	$sTitle .= 'Отказ от платежа';
	$temp_content = '<h1>Отказ от платежа</h1>';

	$temp_content .= '<p>Вы отказались от оплаты.<br /> При возникновении вопросов - пишите нам, через обрутную связь!</p>';
}



/* вывод информации */ $iContent = $temp_content;

}


?>