<?php
include_once("../db.inc");
include_once("../classes_objects_allrpg.php");
require_once ("../appcode/formatters.php");

//$value = iconv('utf-8','cp1251',encode($_REQUEST['value']));
$value = encode($_REQUEST['value']);

header('Access-Control-Allow-Origin: *');

if(isset($value) && $value!='')
{
	start_mysql();
	# Установление соединения с MySQL-сервером
	
	$value = intval($value);

	if($value)
	{		
		$return_arr[0]['id'] = 'all0';
		$return_arr[0]['value'] = '<i>глобальный сюжет</i>';
		$i = 1;
		
		$result=mysql_query("SELECT * FROM ".$prefix."roleslinks WHERE id=".$value);
		$a=mysql_fetch_array($result);
		$values=substr($a["vacancies"],1,strlen($a["vacancies"])-2);
		$values=explode('-',$values);
		
		foreach($values as $v) {
	  		if($v ==0) {
          continue;
        }
		  		$result2=mysql_query("SELECT * from ".$prefix."rolevacancy where id=".$v);
		  		$b=mysql_fetch_array($result2);
		  		$vacancy_name = decode3($b["name"]);
		  		
          $result2=mysql_query("SELECT * from ".$prefix."roles where vacancy=".$v." and todelete2!=1 order by sorter asc");
          $sorters = array(); $names_list = array();
          
				while($b=mysql_fetch_array($result2)) {
					$result3=mysql_query("SELECT * from ".$prefix."users where id=".$b["player_id"]);
					$c=mysql_fetch_array($result3);
					$names_list[] = name_public_compact_formatter_row($c, 'usetooltip');
					$sorters[] = str_replace('&#39','`',decode3($b["sorter"]));
				}
				if ((count($sorters) == 1) && ($sorters[0] == $vacancy_name))
				{
          $sorters_result = "(игрок: " . implode(', ', $names_list). ")"; 
				}
				else if (count($sorters) == 0)
				{
          $sorters_result = 'ЗАЯВОК НЕТ';
				}
				else 
				{
          $sorters_result = ' заявка(и) ' . implode (', ', $sorters) . " (игрок(и): " . implode(', ', $names_list). ")";
				}
				$return_arr[$i]['id'] = "all$v";
        $return_arr[$i]['value'] = $vacancy_name . " — " .  $sorters_result ;
        $i++;
		}
	}

	print(json_encode($return_arr));
	# Вывод основного содержания страницы

	stop_mysql();
	# Разрыв соединения с MySQL-сервером
}
?>