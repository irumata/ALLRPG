<?php

if($_SESSION["user_id"]!='' && $workrights["site"]["gamereport"]) {

  require_once ($server_inner_path."appcode/data/common.php");
  require_once ($server_inner_path."appcode/data/roles_linked.php");
  require_once ($server_inner_path."appcode/possible_values.php");
  
  $site_id = intval($_SESSION['siteid']);
  
  function locatpath($id) {
      global $site_id;
      $return = implode ('→', get_location_path ($id, $site_id));
      return $return ? $return : '(не указана)';
    }
  
	$result = db_query ("
SELECT r.*, 'Не привязана к роли' AS role_error FROM {$prefix}roles r
LEFT JOIN {$prefix}rolevacancy rv ON rv.id = r.vacancy
WHERE r.site_id = $site_id AND todelete!='1' AND todelete2!='1' AND status <> 4 AND rv.id IS NULL
UNION ALL
SELECT r.*, 'Нет локации' AS role_error FROM {$prefix}roles r
LEFT JOIN {$prefix}roleslocat rl ON rl.id = r.locat
WHERE r.site_id = $site_id AND todelete!='1' AND todelete2!='1' AND status <> 4 AND rl.id IS NULL
");
    
    $items  = array();
    
    while ($row = mysql_fetch_assoc($result))
		{
			$items  [] = $row;
		}

    $obj_html .= '
    <table class="menutable"><tr class="menu" style="font-size:90%">
    <th style="max-width:25%">Локация / Команда</th>
      <th>Заявка</th>
            <th>Статус</th>
      <th>Ошибка/недоработка</th>
       </tr>';
  function status_format ($row)
{
  $status = $row['status'];
  $values = get_possible_values('status');
  foreach ($values as $value)
  {
    if ($value[0] == $status)
    {
      return $value[1];
    }
  }
  return $status;
}
    foreach ($items as $row)
    {
      $locatname = locatpath($row['locat']);
      $status = status_format($row);
      $sorter = $row['sorter'] ? $row['sorter'] : '(Не указана)';
      if ($row['locat'])
      {
        $locatname = "<a href=\"/locations/locations/{$row['locat']}/act=view\">{$locatname}</a>";
      }
      $obj_html .= "<tr><td>$locatname</td>

      <td><a href=\"/orders/orders/{$row['id']}/act=view\">$sorter</td>
            <td>$status</td>
            <td>{$row['role_error']}</td>
      </tr>";
    }
    $obj_html.= "</table>";

    

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$pagetitle=h1line('Проблемные заявки',$curdir.$kind.'/');
	$content2.='<div class="narrow">'.$obj_html.'</div>';
}
?>