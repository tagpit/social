<?php

$sTitle .= 'Ваши друзья';
$temp_content = '';

if ( isset($_SESSION['sess_user']))
{
		
	/* $uFULL - о пользователе */

if ( $uID != $pID )
{
	if ( $iZapros == 'del' )
	{ // отмена дружбы
	
		$temp_content = '<h5>Удаление из друзей пользователя #'.$pID.': ';
		$friends = mysql_query("select * from contact where contact_id='$pID' and user_id='$uID'");
		if (mysql_num_rows($friends) == 1)
		{ // удаление

			$attxs = mysql_query("select * from contact where contact_id='$pID' and user_id='$uID'");
			if (mysql_num_rows($attxs) == 1 )
			{ $att = 1; $attxssupd = mysql_query("update contact set friends='$att' where contact_id='$uID' and user_id='$pID'") or die(mysql_error()); }

			$res = mysql_query("delete from contact where contact_id='$pID' and user_id='$uID'");
			
			$temp_content .= 'удаление произведено.';
		} else
		{ // повторная попытка удаления
			$temp_content .= 'повторная попытка.';
		}
		$temp_content .= '</h5>';
	} elseif ( $iZapros == 'add' )
	{
	
	 // добавление дружбы
		$temp_content = '<h5>Добавление пользователя #'.$pID.': ';

		$date = date("Y-m-d H:i:s");

		/* ---------------- */
		$attxss = mysql_query("select * from contact where contact_id='$uID' and user_id='$pID'");
		if (mysql_num_rows($attxss) != 1 ) { $att = 1; } else
		{ $att = 2; $attxssupd = mysql_query("update contact set friends='$att' where contact_id='$uID' and user_id='$pID'") or die(mysql_error()); }
		/* ---------------- */

		$friends = mysql_query("select * from contact where contact_id='$pID' and user_id='$uID'");
		if (mysql_num_rows($friends) != 1 )
		{ // add friends

			$new = mysql_query("insert into contact (user_id, contact_id, date, friends) values ('$uID', '$pID', '$date', '$att')") or die(mysql_error());
			$temp_content .= 'успешно добавлено.';
		} else
		{ // friends added
			$temp_content .= 'повторная попытка.';
		}
		$temp_content .= '</h5>';
	
	}
	
}


	if ( $iZapros == 'all' )
	{
		$temp_content .= '<h1>Все зарегистрированные | <a class="sColor" href="/friends">Ваши друзья</a></h1>';

		$res_all = mysql_query("select id, name from users");	$count = 0;
		
		for ($i = 0; $i < mysql_num_rows($res_all); $i++) {
		
			$usr_id = mysql_result($res_all, $i, "id");
			
			if ( $usr_id != $uID )
			{

				$count++;
				$usr_name = mysql_result($res_all, $i, "name");

				/* проверка - может пользователь уже в друзьях */
				$fCounttm = mysql_result(mysql_query("select count(*) from contact where contact_id='$usr_id' and user_id='$uID' and friends=2"), 0);
			//	$temp_content .= '<div class="user_p">'.$count.'. <a href="/id'.$usr_id.'"><b>'.$usr_name.'</b></a>';

				$temp_content .= '<div class="uPhoto" style="float:left">
							<h5 class="uNoPhoto">?</h5>
							<h5>'.$count.'. <b><a class="sColor" href="/id'.$usr_id.'">'.$usr_name.'</a></b>';

				if ( $fCounttm == 0 )
				{
					$temp_content .= '</h5>
							<h5><a class="sColor" href="/friends'.$usr_id.'">Добавить в друзья</a></h5>
							</div>';
				} else
				{
					$temp_content .= '</h5>
							<h5><a class="sColor" href="/sendto'.$usr_id.'">Написать сообщение</a></h5>
							</div>';
				}
			}
		}
	} else
	{


	$temp_content .= '<h1>Ваши друзья | <a class="sColor" href="/friendsall">Все зарегистрированные</a></h1>';




	// цикл по запросам к текущему 
	$fCount_to_me = mysql_result(mysql_query("select count(*) from contact where contact_id='$uID' and friends=1"), 0);
	
	if ( $fCount_to_me > 0 )
	{
		$temp_content .= '<p>Запросили дружбу: '.$fCount_to_me.'</p>';

		$res_all = mysql_query("select user_id from contact where contact_id='$uID' and friends=1");	$count = 0;
		
		for ($i = 0; $i < mysql_num_rows($res_all); $i++) {
		
			$usr_id = mysql_result($res_all, $i, "user_id");		$count++;

			$usr_FULL = mysql_query("select * from users where id='$usr_id'");
			$usr_name = mysql_result($usr_FULL, 0, "name");

			// <div class="user_p">'.$count.'. </div>

			$temp_content .= '<div class="uPhoto" style="float:left">
							<h5 class="uNoPhoto">?</h5>
							<h5>'.$count.'. <b><a class="sColor" href="/id'.$usr_id.'">'.$usr_name.'</a></b></h5>
							<h5><a class="sColor" href="/friends'.$usr_id.'">Подтвердить дружбу</a></h5>
						</div>';


		}
		$temp_content .= '<div class="clear"></div>';
	}

	// цикл по друзьям
	$fCount_all = mysql_result(mysql_query("select count(*) from contact where user_id='$uID' and friends=2"), 0);

	if ( $fCount_all > 0 )
	{
		$temp_content .= '<p>Друзей: '.$fCount_all.'</p>';

		$res_all = mysql_query("select contact_id from contact where user_id='$uID' and friends=2");	$count = 0;
		
		for ($i = 0; $i < mysql_num_rows($res_all); $i++) {
		
			$usr_id = mysql_result($res_all, $i, "contact_id");		$count++;

			$usr_FULL = mysql_query("select * from users where id='$usr_id'");
			$usr_name = mysql_result($usr_FULL, 0, "name");

			// $temp_content .= '<div class="user_p">'.$count.'. <a href="/id'.$usr_id.'"><b>'.$usr_name.'</b></a></div>';

						$temp_content .= '<div class="uPhoto" style="float:left">
							<h5 class="uNoPhoto">?</h5>
							<h5>'.$count.'. <b><a class="sColor" href="/id'.$usr_id.'">'.$usr_name.'</a></b></h5>
							<h5><a class="sColor" href="/sendto'.$usr_id.'">Написать сообщение</a></h5>
						</div>';
		}
		$temp_content .= '<div class="clear"></div>';
	}

	// цикл по запросам от текущего
	$fCount_ot_me = mysql_result(mysql_query("select count(*) from contact where user_id='$uID' and friends=1"), 0);

	if ( $fCount_ot_me > 0 )
	{
		$temp_content .= '<p>Ваших запросов дружбы: '.$fCount_ot_me.'</p>';

		$res_all = mysql_query("select contact_id from contact where user_id='$uID' and friends=1");	$count = 0;
		
		for ($i = 0; $i < mysql_num_rows($res_all); $i++) {
		
			$usr_id = mysql_result($res_all, $i, "contact_id");		$count++;

			$usr_FULL = mysql_query("select * from users where id='$usr_id'");
			$usr_name = mysql_result($usr_FULL, 0, "name");

		//	$temp_content .= '<div class="user_p">'.$count.'. <a href="/id'.$usr_id.'"><b>'.$usr_name.'</b></a><a href="/nofriends'.$usr_id.'">Отменить запрос</a></div>';

						$temp_content .= '<div class="uPhoto" style="float:left">
							<h5 class="uNoPhoto">?</h5>
							<h5>'.$count.'. <b><a class="sColor" href="/id'.$usr_id.'">'.$usr_name.'</a></b></h5>
							<h5><a class="sColor" href="/nofriends'.$usr_id.'">Отменить запрос</a></h5>
						</div>';
		
		}
		$temp_content .= '<div class="clear"></div>';
	}
	}
$iContent = $temp_content;


}





?>