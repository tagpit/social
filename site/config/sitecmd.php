<?php



function usercount() {
$users=@mysql_num_rows(mysql_query("SELECT * FROM users"));
return $users;
}

?>