<?php

// include('close.php');exit;

$sTitle = 'WEBid.in: ';

/* Данные баз данных   */
$db_host = 'a56979.mysql.mchost.ru'; 
$db_name = 'a56979_webid';
$db_user = 'a56979_webid';
$db_pwd = 'bazeweb1d';

/* email: служба поддержки */

$rootEmail = 'selant@rambler.ru';



function userinput($stroka)
{
	$stroka = str_replace(array ('<' ,'>','\'','"'), array('&lt;','&gt;', '&#39;','&quot;'), $stroka); return $stroka;

}

?>