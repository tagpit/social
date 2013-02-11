<?php

$temp_content = '';

$msg_send = 0;


if ( isset($_SESSION['sess_user']))
{
	if ( $iZapros == 'history' )
	{
		$sTitle .= 'История сообщений';
			$mSend = 1;
			
		/* проверка на дружбу с пользователем $pID */
		$friend = mysql_result(mysql_query("select count(*) from contact where user_id='$uID' and contact_id='$pID' and friends=2"), 0);
		if ( $friend == 1 )
		{
			if ( $pID > $uID ) { /* номер "от кого" меньше чем "кому" */ $user1 = $uID; $user2 = $pID; } else {	$user1 = $pID; $user2 = $uID; }
			$fGroup = mysql_query("select id from groups where user1='$user1' and user2='$user2'");
			if (mysql_num_rows($fGroup) == 1)
			{ /* группа найдена */
				$groupID = mysql_result($fGroup, 0, "id");
				$fMessages = mysql_query("select * from messages where groupid='$groupID'");



				if (mysql_num_rows($fMessages) == 0) { $temp_content .= '<p>Сообщений нет</p>'; } else
				{

					$mutname = mysql_result(mysql_query("select name from users where id='$pID'"), 0);
					for ($i = 0; $i < mysql_num_rows($fMessages); $i++)
					{
						$mID = mysql_result($fMessages, $i, "id"); $mTO = mysql_result($fMessages, $i, "toid"); $mOT = mysql_result($fMessages, $i, "otid");
						$mMessage = mysql_result($fMessages, $i, "textmsg"); $mDate = mysql_result($fMessages, $i, "datemsg"); $mView = mysql_result($fMessages, $i, "view");
						if ( $mOT == $uID ) { $mOT = '. я: '; } else {  $mOT = '. '.$mutname.': '; } $iplus = $i + 1;
						$temp_content .= '<p>'.$iplus.$mOT.$mMessage.'</p>';
					}
				}







			} else
			{
				$temp_content = '<h5>Доступ запрещен!</h5>';
			}

		} else
		{
			$temp_content = '<h5>Доступ запрещен!</h5>';
		}
	}
	elseif ( $iZapros == 'sendto' )
	{
		$sTitle .= 'Внутренняя переписка';

		/* проверка на дружбу с пользователем $pID */
		$friend = mysql_result(mysql_query("select count(*) from contact where user_id='$uID' and contact_id='$pID' and friends=2"), 0);
		if ( $friend == 1 )
		{
			$puFULL = mysql_query("select * from users where id='$pID'");	$puName = mysql_result($puFULL, 0, "name");
			$temp_content = '<h1>Получатель сообщения: '.$puName.' | <a href="/id'.$pID.'">страница друга</a></h1>'; $mSend = 1;

			if ( $pID > $uID ) { /* номер "от кого" меньше чем "кому" */ $user1 = $uID; $user2 = $pID; } else {	$user1 = $pID; $user2 = $uID; }

			$fGroup = mysql_query("select id from groups where user1='$user1' and user2='$user2'");
			if (mysql_num_rows($fGroup) == 1) { /* группа найдена */ $groupID = mysql_result($fGroup, 0, "id"); } else
			{ $addgroup = mysql_query("insert into groups (user1, user2) values ('$user1', '$user2')") or die(mysql_error()); $groupID = mysql_insert_id(); }

			$fMessages = mysql_query("select * from messages where groupid='$groupID'");

			$temp_content .= '<div id="msgtek">';

if ( $xml == 'true' ) { $temp_content = ''; }

			if (mysql_num_rows($fMessages) == 0) { $temp_content .= '<p>Сообщений нет</p>'; } else
			{ // $temp_content .= '<p>Сообщений: '.mysql_num_rows($fMessages).'</p>';

$col = 0;

				if ( mysql_num_rows($fMessages) > 10 ) { $start = mysql_num_rows($fMessages) - 10; $col = mysql_num_rows($fMessages) - 10; } else { $start = 0; }
			$mutname = mysql_result(mysql_query("select name from users where id='$pID'"), 0);
				for ($i = $start; $i < mysql_num_rows($fMessages); $i++)
				{
					$mID = mysql_result($fMessages, $i, "id"); $mTO = mysql_result($fMessages, $i, "toid"); $mOT = mysql_result($fMessages, $i, "otid");
					$mMessage = mysql_result($fMessages, $i, "textmsg"); $mDate = mysql_result($fMessages, $i, "datemsg"); $mView = mysql_result($fMessages, $i, "view");
$col++;
					if ( $mOT == $uID ) { $mOT = '. я: '; } else {  $mOT = '. '.$mutname.': '; }
					$temp_content .= '<p>'.$col.$mOT.$mMessage.'</p>';

					if ( $mTO == $uID && $mView == 0 )
					{	
						$user_update = mysql_query("update messages set view='1' where id='$mID'") or die(mysql_error());

						if ( $xml == 'true' ) { $temp_content = '<p>'.$col.$mOT.$mMessage.'</p>_xmlmsgsend=0'; }

					} else
					{
						
						
						if ( $xml == 'true' ) {
							
							$msg = trim($_POST['message']);
							$msg = userinput($msg);
							
							if ( $msg == $mMessage ) { $temp_content = '<p>'.$col.$mOT.$mMessage.'</p>_xmlmsgsend=1'; } else { $temp_content = 'nonetext_xmlmsgsend=0'; }
						}
					}




				}
			}
				$date = date("Y-m-d H:i:s");

			if (isset($_POST['message'])  && strlen(trim($_POST['message'])) >= 1)
			{
				 
				 $msg = trim($_POST['message']);
				$msg = userinput($msg);

				if ( $mMessage != $msg || $mTO == $uID  )
				{ $col++;
					$addmsg = mysql_query("insert into messages (groupid, otid, toid, datemsg, textmsg) values ('$groupID', '$uID', '$pID', '$date', '$msg')") or die(mysql_error());
					$mID++;	$temp_content .= '<p>'.$col.'. я: '.$msg.'</p>';
					$msg_send = 1;

					if ( $xml == 'true' ) { $temp_content = '<p>'.$col.'. я: '.$msg.'</p>_xmlmsgsend=1'; }
				}	
			}

/*
<form method="post" action="/sendto'.$pID.'">
<input type="submit" id="sptbtn" class="btn" value="Написать" />
</form>
*/

		if ( $xml == 'false' )		$temp_content .= '</div><div class="forms">



<div class="left">Ваше сообщение:</div>
<div class="right"><textarea id="message" name="message" rows="5" onkeypress="if(event.keyCode==13) { process(12); event.returnValue = false; }" autofocus >'.$message.'</textarea></div></div>

		<div class="forms"><div class="left"></div><div class="right"><input type="button" id="sptbtn" class="btn" onclick="process(12)" value="Отправить" /> <a id="refresh" href="/sendto'.$pID.'">Обновить</a></div>



</div>';

/* else { if ( $msg_send == 1 ) { $temp_content .= '_xmlmsgsend=1'; } else { $temp_content .= '_xmlmsgsend=0';  */


// if ( $mView != 0 || $mOT == $uID ) { $temp_content = 'nonetext_xmlmsgsend=0'; }



// } } 
		} else { $temp_content = '<h5>Простите, но обмен сообщениями возможен только между друзьями!</h5>'; }

		
	}

	if ( $mSend == 0 )
	{ $sTitle .= 'Внутренняя переписка';
		$temp_content .= '<h1>Внутренняя переписка</h1>';
		/* переписки пользователя */

		$fGroup = mysql_query("select * from groups where user1='$uID' or user2='$uID'");

		if ( mysql_num_rows($fGroup) > 0 )
		{

			for ($i = 0; $i < mysql_num_rows($fGroup); $i++)
			{
				/* !!!!!!! */

				$itemel = mysql_result($fGroup, $i, "id");
				$user1 = mysql_result($fGroup, $i, "user1"); $user2 = mysql_result($fGroup, $i, "user2");
				if ( $user1 == $uID ) { $papeID = $user2; } else { $papeID = $user1; }

				$fMessages = mysql_query("select * from messages where groupid='$itemel'");
				$counts = mysql_num_rows($fMessages);

				$pBase = mysql_query("select * from users where id='$papeID'");
				$pName = mysql_result($pBase, 0, "name");
				
				/* новых сообщений */
				$pcountnew =  mysql_result(mysql_query("select count(*) from messages where toid='$uID' and groupid='$itemel' and view='0'"), 0);

				$onemessagebb = mysql_query("select * from messages where groupid='$itemel' order by id desc LIMIT 0 , 1");
				$onemessage = mysql_result($onemessagebb, 0, "textmsg");
				$onemessagedat = mysql_result($onemessagebb, 0, "datemsg");
				$otidd = mysql_result($onemessagebb, 0, "otid");

				if ( $otidd == $uID ) { $otidd = 'от вас'; } else { $otidd = 'вам'; }

				if ( $pcountnew > 0 ) { $counts .=', новых: '.$pcountnew; }

				/* енд новых сообщений */

				if ( $counts > 0 )
				{
					$temp_content .= '<hr class="uhr" /><p><b>'.$pName.'</b> (сообщений: '.$counts.') <a class="sColor" style="float:right" href="/message'.$papeID.'">все сообщения</a><br />Последнее сообщение '.$otidd.' было написано '.$onemessagedat.': <a class="sColor" href="/sendto'.$papeID.'">"'.$onemessage.'" </a></p>';


				}

			}
			$temp_content .= '<hr class="uhr" />';

		}

		if ( $temp_content == '<h1>Внутренняя переписка</h1>' ) { $temp_content .= '<p>Сообщения не обнаружены!</p>'; }

	}

$iContent = $temp_content;
	
}


?>