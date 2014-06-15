<?php
include_once("../db.inc");
require_once ($server_inner_path."appcode/data/common.php");

$input = intval($_GET['input']);
$all = encode($_GET['all']);

if($input)
{
	start_mysql();
	# Установление соединения с MySQL-сервером

  $remove_unknown_condition = ($all == 1) ? "1=1" : "parent!=2562 and id!=2562";
  $result = db_query("SELECT * FROM {$prefix}geography WHERE parent=$input and $remove_unknown_condition order by name");
  while($a = mysql_fetch_array($result))
  {
    $return_arr[]=Array('id'=>$a["id"],'value'=>$a["name"]);
  }

	header('Access-Control-Allow-Origin: *');
	print(json_encode($return_arr));
	# Вывод основного содержания страницы

	stop_mysql();
	# Разрыв соединения с MySQL-сервером
}

function printOutcity($id)
{
	global
		$prefix;

	$result=mysql_query("SELECT * FROM ".$prefix."geography WHERE id=".$id);
	while($a = mysql_fetch_array($result))
	{
		if($a["parent2"]>0)
		{
			$result2=mysql_query("SELECT * FROM ".$prefix."objects WHERE id=".$a["parent2"]);
			$b = mysql_fetch_array($result2);
			$content.=decode($a["name"]).' ('.decode($b["name"]).')';
		}
		else
		{
			$content.=decode($a["name"]);
		}
	}

	return ($content);
}
?>