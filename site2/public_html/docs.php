<?php

	$path='../../site1/public_html/';

include_once($path.'db.inc');
include_once($path.'classes_objects_allrpg.php');
include_once($path.$direct.'/classes/classes_objects.php');
require_once ($path."appcode/possible_values.php");
require_once ($path."appcode/formatters.php");
require_once ($path."appcode/data/common.php");

function get_link_target($r)
{
      global $subobj, $link;
      
        $vac=0;
        if(strpos($r,'all')===false) {
          $result2=mysql_query("SELECT * FROM ".$prefix."roles WHERE site_id=".$subobj." and id=".$r);
          $b=mysql_fetch_array($result2);
          $vac=$b["vacancy"];
        }
        else {
          $vac=str_replace('all','',$r);
        }
        
        $vac = intval ($vac);
        
        if (!$vac)
        {
          return '';
        }
        
        $result2=mysql_query("
          SELECT name, player_id, sorter, p.*
          FROM {$prefix}rolevacancy rv 
          LEFT JOIN {$prefix}roles r ON r.vacancy = rv.id AND r.todelete = 0 AND r.todelete2 = 0 AND r.status=3
          LEFT JOIN {$prefix}users p ON r.player_id = p.id
          WHERE rv.id=$vac");
        
        $link_target = array();
        while ($target_row = mysql_fetch_array($result2))
        {
          $player_id = $target_row['player_id'];
          if ($player_id)
          {
            $link_target [] = "«".$target_row["name"] . " — " . decode($target_row["sorter"]).'» ('.name_public_compact_formatter_row($target_row).')';
          }
          else
          {
            $link_target[] = '«'.$target_row["name"].'»';
          }
        }
        
        return count($link_target) ? implode(', ', $link_target) : '<i>удаленную роль</i>';
}

function get_link_targets($roles2)
		{
      $targets = array();
      global $subobj, $link;
      $roles2=substr($roles2,1,strlen($roles2));
      $roles2=explode('-',$roles2);
      foreach($roles2 as $r) {
        $target = get_link_target($r);
        if ($target)
        {
          $targets[] = $target;
        }
      }
      if (count($targets))
      {
        return "<b>Про " . implode(", ", $targets) . "</b>";
      }
      return '';
		}

session_start();
start_mysql();

if($dynrequest==1) {
	dynamic_err(array(),'submit');
}

if(isset($_REQUEST["roles"])) {
  $pagebreak = $_POST['pagebreak'] ? 'page-break-after:always;' : 'float: left;';
	$roles=Array();
	$result=mysql_query("SELECT * FROM ".$prefix."roles where site_id=".$_SESSION["siteid"]." order by team, sorter");
	while($a=mysql_fetch_array($result)) {
		if(encode($_POST["roles"][$a["id"]])=='on') {
			$roles[]=$a["id"];
		}
	}
	$result=mysql_query("SELECT * FROM ".$prefix."sites where id=".$_SESSION["siteid"]);
	$a=mysql_fetch_array($result);
	$docs="<div style=\"margin-right: 10px;$pagebreak\">";
	if(encode($_POST["doc"])==1) {
		$docs.=decode($a["docs"]);
	}
	elseif(encode($_POST["doc"])==2) {
		$docs.=decode($a["docs2"]);
	}
	elseif(encode($_POST["doc"])==3) {
		$docs.=decode($a["docs3"]);
	}
	$docs.='</div>';

	$content='<html>
<head>
<title>Генератор аусвайсов</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="'.$server_absolute_path.'libraries/ckeditor/contents.css">
</head>

<body>';

	$locatpermit=make5fieldtree(false,$prefix."roleslocat","parent",0," AND site_id=".$_SESSION["siteid"],"code asc, name asc",0,"id","name",1000000);

    $rolefields=virtual_structure("SELECT * from ".$prefix."rolefields where site_id=".$_SESSION["siteid"]." order by rolecode","allinfo","role");
    $rolefields[]=Array(
			'name'	=>	"locat",
			'sname'	=>	"Локация",
			'type'	=>	"select",
			'values'	=>	$locatpermit,
			'read'	=>	1,
			'write'	=>	100000,
	);
	$rolefields[]= array(
			'name'	=>	"sid",
			'sname'	=>	"ИНП",
			'type'	=>	"text",
			'read'	=>	1,
			'write'	=>	100000,
	);
		$rolefields[]= array(
			'name'	=>	"vacancy_name",
			'sname'	=>	"Имя роли",
			'type'	=>	"text",
			'read'	=>	1,
			'write'	=>	100000,
	);
	$rolefields[]=Array(
			'name'	=>	"id",
			'sname'	=>	"№ заявки",
			'type'	=>	"text",
			'read'	=>	1,
			'write'	=>	100000,
	);
    $rolefields[]=Array(
			'name'	=>	"money",
			'sname'	=>	"Взнос",
			'type'	=>	"text",
			'read'	=>	1,
			'write'	=>	100000,
	);
	$rolefields[]=Array(
			'name'	=>	"moneydone",
			'sname'	=>	"Взнос сдан",
			'type'	=>	"checkbox",
			'read'	=>	1,
			'write'	=>	100000,
	);
	$rolefields[]=Array(
			'name'	=>	"fio",
			'sname'	=>	"Ф.И.О.",
			'type'	=>	"text",
			'read'	=>	1,
			'write'	=>	100000,
	);
	$rolefields[]=Array(
			'name'	=>	"nick",
			'sname'	=>	"Никнейм",
			'type'	=>	"text",
			'read'	=>	1,
			'write'	=>	100000,
	);
	$rolefields[]=Array(
			'name'	=>	"photo",
			'sname'	=>	"Фотография",
			'type'	=>	"file",
			'upload'	=>	4,
			'read'	=>	1,
			'write'	=>	100000,
	);
	$rolefields[]=Array(
			'name'	=>	"sickness",
			'sname'	=>	"Медицинские противопоказания",
			'type'	=>	"textarea",
			'read'	=>	1,
			'write'	=>	100000,
	);
		$rolefields[]=Array(
		'name'	=>	"medic",
				'sname'	=>	"Медицинская квалификация",
				'type'	=>	"select",
				'values'	=>	get_possible_values ('medic'),
				'help'	=>	'Указание медицинской квалификации в профиле позволит мастерам обратиться за помощью в экстренных случаях. Это актуально как на маленьких играх (где может не быть выделенного медика), так и на больших (где медик не всегда может быть в прямом доступе).',
			'read'	=>	1,
			'write'	=>	100000,
	);
	
    $subobj=$_SESSION["siteid"];
    
    for($i=0;$i<count($roles);$i++) {
    
		$id=$roles[$i];
		$role_data = db_get_row("
      SELECT *, rv.name AS vacancy_name, r.id as role_id
      from {$prefix}roles  r
      LEFT JOIN {$prefix}rolevacancy rv ON rv.id = r.vacancy
      LEFT JOIN {$prefix}users u ON r.player_id = u.id
      where r.id=$id and r.site_id=$subobj");
		$alllinks='';
		
		$role_vacancy = $role_data["vacancy"];
		$role_id = $role_data['role_id'];
		$role_status = $role_data['status'];
		$vacancy_name = $role_data['vacancy_name'];
		
    $alllinks = array();
		if($role_vacancy || true) {
      $all_links_list = array();
			$result3=db_query("
        SELECT rl.*, rp.name AS sujet_name 
        FROM {$prefix}roleslinks rl
        INNER JOIN {$prefix}roleslinks rp ON rp.id = rl.parent
        WHERE (rl.roles LIKE '%-all{$role_vacancy}-%' OR rl.roles LIKE '%-{$role_id}-%') and rl.content!='' 
          and rl.site_id=$subobj and (rl.notready!='1' OR 1=1) 
        ORDER by date desc");
			while($c=mysql_fetch_array($result3)) {
        $all_links_list [] = $c;
			}
		}
			foreach ($all_links_list as $c)
			{
				if(strpos($c["roles"],'-'.$role_id.'-')!==false || ($role_status == 3 && strpos($c["roles"],'-all'.$role_vacancy .'-')!==false)) {
				
          $target = $c["hideother"] ? '' : get_link_targets ($c['roles2']);
          $target = $target ? $target :'<b>***</b>';
					$alllinks[] = $target . '<br>' . decode($c["content"]);
				}
			}
		$rolelinks = implode('<br><br>', $alllinks);

		$b=array_merge($role_data, unmakevirtual($role_data['allinfo']));
		$tbd=$docs;
		
		$tbd = str_replace('[Связи]', $rolelinks, $tbd);
		foreach($rolefields as $v) {
			if($v['type']!='h1' && strpos($tbd,'['.$v["sname"].']')!==false && $v["name"]!="allinfo") {
				$obj_n=createElem($v);
				$obj_n->setVal($b);
				$tbd=str_replace('['.$v["sname"].']',$obj_n->draw(1,"read"),$tbd);
			}
		}
		$content.=$tbd;
    }

	$content.='
</body>
</html>
';
stop_mysql();
}
else {
	$content='<html>
<head>
<title>Генератор аусвайсов</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style>

</style>
</head>

<body>
Не выбрано ни одной заявки!
</body>
</html>
';
}

print($content);

?>