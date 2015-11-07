<?php
require_once("../db.inc");
require_once("../classes_objects_allrpg.php");
require_once "../appcode/data/common.php";

start_mysql();
# Установление соединения с MySQL-сервером

$content='';

$email = $_GET['email'];
$key = $_GET['key'];



$email_escaped = mysql_real_escape_string($email);

$row =  db_get_row("SELECT * FROM {$prefix}users WHERE em LIKE '$email_escaped' OR em2 LIKE '$email_escaped'");

if (!intval($row['sid']))
{
  echo 'ERROR_NO_SUCH_USER';
  die();
}

$key_to_check = sha1("$email@{$row['pass']}$joinrpg_connector_secret_key");
if ($key != $key_to_check)
{
  echo 'ERROR_WRONG_KEY';
  die();
}

echo "{'success':1}"; //If key matches key_to_check than password is right
?>