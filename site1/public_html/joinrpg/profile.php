<?php
require_once("../db.inc");
require_once("../classes_objects_allrpg.php");
require_once "..//appcode/data/common.php";

require_once 'joinrpg_common.php';

start_mysql();
# Установление соединения с MySQL-сервером

$content='';

$email = $_GET['email'];
$key = $_GET['key'];

check_key($email, $key);

$email_escaped = mysql_real_escape_string($email);

$row =  db_get_row("SELECT * FROM {$prefix}users WHERE em LIKE '$email_escaped' OR em2 LIKE '$email_escaped'");

if (!intval($row['sid']))
{
  echo 'ERROR_NO_SUCH_USER';
  die();
}

echo json_encode(whitelist($row, 'user'));
?>