<?php

/* прием платежа */

$secretkey = 'transfer';

 $string=$HTTP_POST_VARS["LMI_PAYEE_PURSE"].$HTTP_POST_VARS["LMI_PAYMENT_AMOUNT"].$HTTP_POST_VARS["LMI_PAYMENT_NO"].$HTTP_POST_VARS["LMI_MODE"].$HTTP_POST_VARS["LMI_SYS_INVS_NO"].$HTTP_POST_VARS["LMI_SYS_TRANS_NO"].$HTTP_POST_VARS["LMI_SYS_TRANS_DATE"].$secretkey.$HTTP_POST_VARS["LMI_PAYER_PURSE"].$HTTP_POST_VARS["LMI_PAYER_WM"];

 $md=strtoupper(md5($string));

if($md==$HTTP_POST_VARS["LMI_HASH"] && $HTTP_POST_VARS["LMI_MODE"] == 0 )
{

$userid = $HTTP_POST_VARS["userid"];
$LMIPAYMENTAMOUNT = $HTTP_POST_VARS["LMI_PAYMENT_AMOUNT"];
$LMIPAYMENTNO = $HTTP_POST_VARS["LMI_PAYMENT_NO"];
$LMIMODE = $HTTP_POST_VARS["LMI_MODE"];
$LMISYSINVSNO = $HTTP_POST_VARS["LMI_SYS_INVS_NO"];
$LMISYSTRANSNO = $HTTP_POST_VARS["LMI_SYS_TRANS_NO"];
$LMIPAYERPURSE = $HTTP_POST_VARS["LMI_PAYER_PURSE"];
$LMIPAYERWM = $HTTP_POST_VARS["LMI_PAYER_WM"];
$LMICAPITALLERWMID = $HTTP_POST_VARS["LMI_CAPITALLER_WMID"];
$LMIPAYMERNUMBER = $HTTP_POST_VARS["LMI_PAYMER_NUMBER"];
$LMIPAYMEREMAIL = $HTTP_POST_VARS["LMI_PAYMER_EMAIL"];
$LMIEURONOTENUMBER = $HTTP_POST_VARS["LMI_EURONOTE_NUMBER"];
$LMIEURONOTEEMAIL = $HTTP_POST_VARS["LMI_EURONOTE_EMAIL"];
$LMIWMCHECKNUMBER = $HTTP_POST_VARS["LMI_WMCHECK_NUMBER"];
$LMITELEPATPHONENUMBER = $HTTP_POST_VARS["LMI_TELEPAT_PHONENUMBER"];
$LMITELEPATORDERID = $HTTP_POST_VARS["LMI_TELEPAT_ORDERID"];
$LMIHASH = $HTTP_POST_VARS["LMI_HASH"];
$LMISYSTRANSDATE = $HTTP_POST_VARS["LMI_SYS_TRANS_DATE"];
$LMISDPTYPE = $HTTP_POST_VARS["LMI_SDP_TYPE"];
$LMIPAYMENTDESC = $HTTP_POST_VARS["LMI_PAYMENT_DESC"];
$done = 1;

mysql_query("insert into wmpay ( userid, LMIPAYMENTAMOUNT, LMIPAYMENTNO, LMIMODE, LMISYSINVSNO, LMISYSTRANSNO, LMIPAYERPURSE, LMIPAYERWM, LMICAPITALLERWMID, LMIPAYMERNUMBER, LMIPAYMEREMAIL, LMIEURONOTENUMBER, LMIEURONOTEEMAIL, LMIWMCHECKNUMBER, LMITELEPATPHONENUMBER, LMITELEPATORDERID, LMIHASH, LMISYSTRANSDATE, LMISDPTYPE, LMIPAYMENTDESC, done) values ('$userid', '$LMIPAYMENTAMOUNT', '$LMIPAYMENTNO', '$LMIMODE', '$LMISYSINVSNO', '$LMISYSTRANSNO', '$LMIPAYERPURSE', '$LMIPAYERWM', '$LMICAPITALLERWMID', '$LMIPAYMERNUMBER', '$LMIPAYMEREMAIL', '$LMIEURONOTENUMBER', '$LMIEURONOTEEMAIL', '$LMIWMCHECKNUMBER', '$LMITELEPATPHONENUMBER', '$LMITELEPATORDERID', '$LMIHASH', '$LMISYSTRANSDATE', '$LMISDPTYPE', '$LMIPAYMENTDESC', '$done') ");

$balance = mysql_query("select * from balance where uid='$userid'");

if (mysql_num_rows($balance) == 0 )
{ /* создать кошелек пользователю */
		$new = mysql_query("insert into balance (uid, rublik, paidin) values ('$userid', '$LMIPAYMENTAMOUNT', '$LMIPAYMENTAMOUNT')") or die(mysql_error());
} else
{ /* проверка баланса на поинты */
		$rublik = mysql_result($balance, 0, "rublik"); $rublik = $rublik + $LMIPAYMENTAMOUNT;
		$paidin = mysql_result($balance, 0, "paidin"); $paidin = $paidin + $LMIPAYMENTAMOUNT;
		$user_update = mysql_query("update balance set rublik='$rublik', paidin = '$paidin' where id='$userid'") or die(mysql_error());

}

}

exit;



?>