<?php
$pppnews = $ppp;

$sTitle .= 'Новости';

$temp_content = '<h1>Новости</h1>'; $tekdate = date("Y-m-d"); $nl = mysql_query("select * from news order by id desc");
if (mysql_num_rows($nl) == 0) { $temp_content .= '<p>Сегодня: '.$tekdate.' - новостей нет.</p>'; } else { for ($i = 0; $i < mysql_num_rows($nl); $i++) {
$ni = mysql_result($nl, $i, "info"); $na = mysql_result($nl, $i, "author"); $nt = mysql_result($nl, $i, "time");
$nd = mysql_result($nl, $i, "date"); $ni = mysql_result($nl, $i, "id"); if ( $tekdate == $nd ) { $nd = 'Сегодня'; }
$temp_content .= '<p>'.$na.' ('.$nd.' в '.$nt.'): '.$ni.'</p>'; }} $iContent = $temp_content; ?>