<?php

$accountphp_content = '';
$accountphp_user = 0;

$result = '';

if ( $page_account == 'result' )
	{ if ( '/id'.$_SESSION['sess_user'] == $_POST['user'] ){ $accountphp_user = 1; } } else
	{ if ( $_SESSION['sess_user'] == $id[1] ) { $accountphp_user = 1; } }

if ( $_POST['webid'] == 'messages' )
{
	$accountphp_user = 1; if ( $_POST['user'] != '/id'.$_SESSION['sess_user'] && $_POST['user'] != '' ) { $result = '<y>id'.$_SESSION['sess_user'].'<y>webid.in'; }
}




if ( $accountphp_user == 1 )
{ // ваш аккаутн

	$accountphp_content = '<h1>ваш аккаунт: сообщения</h1>';

} else
{ // другого пользователя

	$accountphp_content = '<h1>другой пользователь</h1>';

}




if ( $page_account == 'result' ) { if ( $result == '' ) { $result = $accountphp_content; } } else { $paper_content = $accountphp_content; }


?>