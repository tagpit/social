<?php

$temp_content = '';

if ( isset($_SESSION['sess_user']))
{
	if ( $uID == $pID )
	{
		
		$eto = 'вашей ';

		$open_data = 1;
		
		$userpage_all = $uFULL;

	} else
	{
		$eto = '';
		// владелец этой страницы добавил текущего активного
		$contact_add_activ = mysql_query("select * from contact where contact_id='$uID' and user_id='$pID'");
		// текущий активный добавил владельца этой страницы
		$contact_add_passiv = mysql_query("select * from contact where user_id='$uID' and contact_id='$pID'");
		if ( mysql_num_rows($contact_add_activ) == 1 && mysql_num_rows($contact_add_passiv) == 1 ) { $open_data = 1; } else { $open_data = 0; }

		$userpage_all = mysql_query("select * from users where id='$pID'");
	}


	// $temp_content .= ' #'.$pID.'</h1><p>';
	

	if (mysql_num_rows($userpage_all) == 1 )
	{
		$userpage_name = mysql_result($userpage_all, 0, "name");

		$userpage_years = mysql_result($userpage_all, 0, "years");
		if ( $userpage_years == '0000-00-00' ) { $userpage_years = ''; } else	
		{
			$date1 = $userpage_years;
			$date2 = date("Y-m-d");
			$diff = abs(strtotime($date2) - strtotime($date1));
			$years = floor($diff / (365*60*60*24));
			$userpage_years = $years.' лет';
		}

		$userpage_addr = mysql_result($userpage_all, 0, "gorod");

		if ( $userpage_addr != '' && $userpage_years != '' ) { $userpage_years .= ', '.$userpage_addr; } elseif ( $userpage_addr != '' ) { $userpage_years = $userpage_addr; }
		
		$userpage_addr = mysql_result($userpage_all, 0, "strana");

		if ( $userpage_addr != '' && $userpage_years != '' ) { $userpage_years .= ', '.$userpage_addr; } elseif ( $userpage_addr != '' ) { $userpage_years = $userpage_addr; }
		

		$temp_content = '<div class="uPhoto">
							<h5 class="uNoPhoto">?</h5>
							<h5>Загрузить фотографию</h5>
						</div>
		<div class="uinfolist">
			<div>
				<h1 style="display:inline-block; margin-bottom:7px;">'.$userpage_name.'</h1>
				<p style="display:inline-block; padding:0; margin:0;">'.$userpage_years.'</p>
			</div>
			<hr class="uhr" />
			<p style="margin:0;">
				<b style="font-size:small;">КОНТАКТНАЯ ИНФОРМАЦИЯ:</b>
				<a class="sColor" style="float:right" href="/edit">Редактировать</a>
			</p>
			<p>';

		$userpage_email = mysql_result($userpage_all, 0, "email");
		if ( $userpage_email == '' )
		{
			$userpage_email = 'не указано';
		} else
		{
			if ( $open_data == 0 )
			{
				$userpage_email = explode('@', $userpage_email); $userpage_email = 'скрыто@'.$userpage_email[1];
			}
		}

		$userpage_tel = mysql_result($userpage_all, 0, "sms");
		if ( $userpage_tel == '' )
		{
			$userpage_tel = 'не указан';
		} else
		{
			if ( $open_data == 0 )
			{
				$userpage_tel = 'скрыто';
			}
		}

		$temp_content .= 'E-mail: '.$userpage_email.'<br />Телефон: '.$userpage_tel.'<br />';

	} else { $temp_content .= 'Ошибка обработки данных о пользователе'; }

	$temp_content .= '<hr class="uhr" />
					<p style="margin:0;">';

	if ( isset($_SESSION['sess_user'])  && $pID != $_SESSION['sess_user']  ) {


		// если оба добавили друг друга - они друзья
		// если только владелец страницы добавил активного - одобрить заявку
		// если только текущий активный добавил владельца страницы - отменить запрос

		$iits = "'";

		if ( mysql_num_rows($contact_add_activ) == 1 && mysql_num_rows($contact_add_passiv) == 1 )
		{
			$temp_content .= '<a class="sColor" href="/sendto'.$pID.'">Написать сообщение</a><br />
								<a class="sColor" href="/nofriends'.$pID.'">Удалить из друзей</a>';
		} elseif ( mysql_num_rows($contact_add_activ) == 1 )
		{
			$temp_content .= '<a class="sColor" href="/friends'.$pID.'">Подтвердить дружбу</a><br />';
		} elseif ( mysql_num_rows($contact_add_passiv) == 1 )
		{
			$temp_content .= 'Ваш запрос еще не был подтвержден<br />';
		} else
		{
			$temp_content .= '<a class="sColor" href="/friends'.$pID.'">Добавить в друзья</a><br />';
		}



	}

$sTitle = $userpage_name;

	if ( $eto != '' )
	{
		
		$balance = mysql_query("select * from balance where uid='$uID'");

if (mysql_num_rows($balance) != 0 )
{ /* проверка баланса */

	$rublik = mysql_result($balance, 0, "rublik");
	$point = mysql_result($balance, 0, "point");
	$temp_content .= '<p style="margin:0;">
						<b style="font-size:small; ">ВНУТРЕННИЙ СЧЕТ:</b><a class="sColor" style="float:right" href="/pay-history">История</a>
					</p>
					<p>
						Баланс: '.$rublik.' руб <a class="sColor" href="/pay-in">пополнить</a> / <a class="sColor" href="/pay-out">вывести</a><br />
						Бонусов: '.$point.' <a class="sColor" href="/pay-obmen">обмен</a><br />
					</p>
					<hr class="uhr" />
					<p>';
}


	}
	
	$temp_content .= '<br /><br /><br />'; 
	
	$temp_content .= 'Адрес '.$eto.'страницы: <a class="sColor" href="http://webid.in/id'.$pID.'">http://webid.in/id'.$pID.'</a>
		</p>
	</div>';

	$iContent = $temp_content;

}

?>