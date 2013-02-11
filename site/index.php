<?php

if(ob_get_length()) ob_clean(); error_reporting(0); session_start();
require_once('config/params.php'); mysql_connect($db_host, $db_user, $db_pwd); mysql_select_db($db_name); mysql_query("set names utf8");


require_once('config/sitecmd.php');
$sUSERS = usercount();
if ( $sUSERS == '' ) { require_once('close.php'); $sitedown = 1; }

/* uID тек. пользователь */
if (isset($_SESSION['sess_user'])) 
{ $uID = $_SESSION['sess_user'];
$pID = $uID; $uFULL = mysql_query("select * from users where id='$pID'");
} else { $uID = 'none'; }

$ppp = 'class="oppage"';

/* Контент страницы */ $iContent == '';

if ( isset($_POST['xml']) || isset($_GET['xml']) )
{ $xml = 'true'; $pXML = explode('_', $_POST['xml']); $pCMD = '/'.$pXML[1]; } else { $xml = 'false'; $pCMD = trim($_SERVER[REQUEST_URI]); }

if ( $sitedown != 1 ) {

	if ($pCMD == '/') { if ($uID == 'none') { $pCMD = '/about'; } else { header('Location: /id'.$uID); exit; } }

	$pfile = 'paper'.$pCMD.'.php';
	
	if (file_exists($pfile)) { require_once($pfile); } else 
	{
		$fPage = 0;	/* поиск варианта обработки страницы */

		/* id */ if ( substr_count($pCMD, "id") == 1 )
		{ $pID = substr($pCMD, strpos($pCMD, "id") + 2); if ( $pID > 0 && $sUSERS >= $pID ) { $fPage = 1; require_once('paper/account.php'); } else
			{ if ( $uID != 'none' ) { header('Location: /id'.$uID); exit; } }
		} elseif /* nofriends */ ( substr_count($pCMD, "nofriends") == 1 )
		{ $pID = substr($pCMD, strpos($pCMD, "nofriends") + 9); if ( $pID > 0 && $sUSERS >= $pID ) { $fPage = 1; $iZapros = 'del'; require_once('paper/friends.php'); } else
			{ header('Location: /friends'); exit; }
		} elseif /* friendsall */ ( substr_count($pCMD, "friends") == 1 )
		{ $pID = substr($pCMD, strpos($pCMD, "friends") + 7); if ( $pID > 0 && $sUSERS >= $pID ) { $fPage = 1; $iZapros = 'add'; require_once('paper/friends.php'); } else
			{ if ( $pID == 'all' ) { $pID = $uID; $iZapros = 'all'; $fPage = 1; require_once('paper/friends.php'); } else { header('Location: /friends'); exit; }}
		} elseif /* sendto */ ( substr_count($pCMD, "sendto") == 1 )
		{ $pID = substr($pCMD, strpos($pCMD, "sendto") + 6); if ( $pID > 0 && $sUSERS >= $pID ) { $fPage = 1; $iZapros = 'sendto'; require_once('paper/messages.php'); } else
			{ header('Location: /messages'); exit; }
		} elseif /* message */ ( substr_count($pCMD, "message") == 1 )
		{ $pID = substr($pCMD, strpos($pCMD, "message") + 7); if ( $pID > 0 && $sUSERS >= $pID ) { $fPage = 1; $iZapros = 'history'; require_once('paper/messages.php'); } else
			{ header('Location: /messages'); exit; }
		} elseif /* pay- */ ( substr_count($pCMD, "pay-") == 1 )
		{ $pID = substr($pCMD, strpos($pCMD, "pay-") + 4);
			if ($pID == 'in') { $fPage = 1; require_once('paper/pay.php'); } elseif ($pID == 'out') { $fPage = 1; require_once('paper/pay.php'); }
			elseif ($pID == 'history') { $fPage = 1; require_once('paper/pay.php'); } elseif ($pID == 'obmen') { $fPage = 1; require_once('paper/pay.php'); }
			elseif ($pID == 'good') { $fPage = 1; require_once('paper/pay.php'); } elseif ($pID == 'fail') { $fPage = 1; require_once('paper/pay.php'); }
		}


		/* если ничего не помогло */ if ( $fPage == 0 ) { $iContent = 'error'; }
	}

	if ( $iContent == 'error' || $iContent == '' )
	{ $sTitle = 'Ошибка доступа к странице'; $iContent = '<h1>Страница не доступна!</h1><p>Простите, но страница не найдена или доступ на нее запрещен!</p>'; }


	$iAdver = '<p>Место для вашей рекламы.</p>';
}

if ( $xml == 'false' ) {

	/* меню страницы */
	if ( !isset($_SESSION['sess_user']) )
	{ $iMenu = '<ul><li><a '.$pppnews.' href="/news">НОВОСТИ</a></li><li><a '.$pppinfostat.' href="/infostat">СТАТИСТИКА</a></li>
			<li><a '.$pppadvers.' href="/advers">РЕКЛАМОДАТЕЛЯМ</a></li><li><a '.$pppusers.' href="/users">ПОЛЬЗОВАТЕЛЯМ</a></li>
			<li><a '.$ppphelp.' href="/help">ПОМОЩЬ</a></li></ul>';
	} else
	{
		$cFriends = mysql_result(mysql_query("select count(*) from contact where contact_id='$uID' and friends=1"), 0);
		$cMessages = mysql_result(mysql_query("select count(*) from messages where toid='$uID' and view='0'"), 0);
	
	if ( $cFriends > 0 ) { $iMenufriends = '<li><a href="/friends">ДРУЗЬЯ +'.$cFriends.'</a></li>'; } else { $iMenufriends = '<li><a href="/friends">ДРУЗЬЯ</a></li>'; }

	if ( $cMessages > 0 ) { $iMenumessages = '<li><a href="/messages">СООБЩЕНИЯ +'.$cMessages.'</a></li>'; } else { $iMenumessages = '<li><a href="/messages">СООБЩЕНИЯ</a></li>'; }

		$iMenu = '<ul><li><a href="/id'.$_SESSION['sess_user'].'">ПРОФИЛЬ</a></li>'.$iMenufriends.$iMenumessages.'<li><a href="/advers">РЕКЛАМОДАТЕЛЯМ</a></li>
			<li><a href="/exit">ВЫХОД</a></li></ul>'; /* <li><a href="/bonus">Бонусы ТУТ!</a></li> */
	} 

require_once("index.html"); } else
{
	/* вывод XML */
	header('Expires: Fri, 04 Jul 2008 08:42:36 GMT');		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-cache, must-revalidate');		header('Pragma: no-cache');		header('Content-Type: text/xml');

	$dom = new DOMDocument();
	$response = $dom->createElement('response');
	$dom->appendChild($response);
	$responseText = $dom->createTextNode($iContent);
	$response->appendChild($responseText);
	$xmlString = $dom->SaveXML();
	echo $xmlString;
}

?>