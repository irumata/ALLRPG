<?php
require_once("../db.inc");
require_once("../classes_objects_allrpg.php");
require_once "../appcode/data/common.php";

function check_key($payload, $key)
{
  global $joinrpg_connector_secret_key;
  $key_to_check = sha1("$payload$joinrpg_connector_secret_key");
  if ($key != $key_to_check)
  {
    echo 'ERROR_WRONG_KEY';
    die();
  }
}

function whitelist($row, $type)
{
  $whitelist = get_whitelist($type);
  $row_whitelisted = array();
  foreach ($row as $key => $value)
  {
    if ($key && in_array($key, $whitelist))
    {
      $row_whitelisted[$key] = $value;
    }
  }
  return $row_whitelisted;
}

function get_whitelist($type)
{
 switch ($type)
  {
      case 'user': return array("sid", "fio", "nick", "gender", "medic", "em", "em2", "phone2", "icq", "skype", "jabber", "vkontakte", "tweeter", "livejournal", "googleplus", "facebook", "photo", "login",   "birth", "city", "sickness", "additional", "prefer", "prefer2", "prefer3", "prefer4", "specializ", "ingroup", "hidesome", "date");
      default: return array();
  }
}
 
?>