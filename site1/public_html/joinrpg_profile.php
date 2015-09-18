<?php
include_once("db.inc");
include_once("classes_objects_allrpg.php");
require_once "appcode/data/common.php";

start_mysql();
# Установление соединения с MySQL-сервером

$content='';

$email = $_GET['email'];
$key = $_GET['key'];

$key_to_check = sha1("$email$joinrpg_connector_secret_key");
if ($key != $key_to_check)
{
  echo '{"success":false, "error":"Key is wrong!"}';
  die();
}

$email_escaped = mysql_real_escape_string($email);

$row =  db_get_row("SELECT * FROM {$prefix}users WHERE em LIKE '$email_escaped' OR em2 LIKE '$email_escaped'");

$whitelist = array("sid", "fio", "nick", "gender", "medic", "em", "em2", "phone2", "icq", "skype", "jabber", "vkontakte", "tweeter", "livejournal", "googleplus", "facebook", "photo", "login",   "birth", "city", "sickness", "additional", "prefer", "prefer2", "prefer3", "prefer4", "specializ", "ingroup", "hidesome", "date");

$row_whitelisted = array();
foreach ($row as $key => $value)
{
  if ($key && in_array($key, $whitelist))
  {
    $row_whitelisted[$key] = $value;
  }
}

echo json_encode($row_whitelisted);
?>