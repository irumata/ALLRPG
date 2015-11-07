<?php

require_once("../db.inc");
require_once("../classes_objects_allrpg.php");

require_once '../appcode/data/roles_main.php';
require_once 'joinrpg_common.php';

start_mysql();
# Установление соединения с MySQL-сервером

$id = intval($_GET['id']);
$key = $_GET['key'];

check_key($id, $key);

$user_table = array();

$roles = load_all_roles ($id, /*team:*/ FALSE);

foreach ($roles as $role)
{
  if (!array_key_exists($role['sid'], $user_table))
  {
  
    $user_table[$role['sid']] = whitelist($role, 'user');
  }
}

$result['project_name'] = "project$id";
$result['users'] = array_values($user_table);

echo json_encode($result);
?>