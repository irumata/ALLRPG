<?php

	$path='../../site1/public_html/';

include_once($path.'db.inc');
include_once($path.'classes_objects_allrpg.php');
include_once($path.$direct.'/classes/classes_objects.php');
require_once ($path."appcode/possible_values.php");
require_once ($path."appcode/data/common.php");

function get_link_targets($roles2)
		{
      global $subobj, $link;
      $roles2=substr($roles2,1,strlen($roles2));
      $roles2=explode('-',$roles2);
      foreach($roles2 as $r) {
        $vac=0;
        if(strpos($r,'all')===false) {
          $result2=mysql_query("SELECT * FROM ".$prefix."roles WHERE site_id=".$subobj." and id=".$r);
          $b=mysql_fetch_array($result2);
          $vac=$b["vacancy"];
        }
        else {
          $vac=str_replace('all','',$r);
        }
        $result2=mysql_query("SELECT * FROM ".$prefix."rolevacancy WHERE site_id=".$subobj." and id=".$vac);
        $b=mysql_fetch_array($result2);
        if($b["name"]!='') {
          if(strpos($r,'all')!==false) {
            $result2=mysql_query("SELECT player_id,sorter FROM ".$prefix."roles WHERE site_id=".$subobj." and vacancy=".$vac);
          }
          else {
            $result2=mysql_query("SELECT player_id,sorter FROM ".$prefix."roles WHERE site_id=".$subobj." and vacancy=".$vac);
          }
          if(mysql_affected_rows($link)>0) {
            $link_target = array();
            while($b=mysql_fetch_array($result2)) {
                                $result6=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$b["player_id"]);
                                $f=mysql_fetch_array($result6);
                                $link_target [] = '«'.decode($b["sorter"]).'» ('.usname($f,true,true).'), ';
            }
            return implode(', ', $link_target);
          }
          else {
            return '«'.$b["name"].'»';
          }
        }
        elseif($r==0) {
          return '<i>глобальный сюжет</i>';
        }
        else {
          return '<i>удаленную роль</i>';
        }
      }
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
      if ($subobj == 592)
      {
        $estates = array();
        foreach ($all_links_list as $c)
        {

          //IF VEDMAK MODE
          if (substr(trim($c['sujet_name']), 0, 2) == '!!')
          {
            $this_link  =  '<table width=537 height=749 style="page-break-after:always;page-break-before:always;margin-top:200px;margin-bottom:200px;margin-left:50px" background="http://vedmak2014.ru/userfiles/image/pretenz.jpg">
 <tbody  >
 <tr>
  <td valign=bottom height=270>
  <p style="font-family: Adana script; font-size: 25; text-align: center;">Настоящим подтверждается, что</center></p>
  </td></tr>
 <tr> <td height=110   valign=center>
  <p style="font-family: Adana script; font-size: 40; margin-left: 120; margin-right: 120; text-align: center;"><i>[Имя роли]</i></p>
  </td>
 </tr>
 <tr>
  <td valign=center height=25>
  <p style="font-family: Adana script; font-size: 25; text-align: center;">имеет <b><i>претензию</i></b> на:</center></p>
  </td>
 </tr>
 <tr>
  <td height=110 valign=center>
  <p style="font-family: Adana script; font-size: 40; margin-left: 120; margin-right: 120; text-align: center;"><i>[Поместье]</i></p>
  </td>
  <td>
  <p>&nbsp;</p>
  </td>
 </tr>
 <tr>
  <td valign=top>
  <p style="font-size: small; text-align: right; margin-left: 120; margin-right: 100; "><i>Сертификат можно показывать, но нельзя уничтожить или отнять. При отказе от претензии отдайте сертификат региональному мастеру.<br><b>&nbsp; ИНП [ИНП]</i></b></p>
  </td>
 </tr>
</tbody></table>';
          $estate_name = $c['content'];
          $owner = strpos($estate_name, 'Владелец') !== FALSE;
          $estate_name = str_replace("Претензия", '', $c['content']);
          $estate_name = str_replace("(см. Правила по поместьям и титулам):", '', $estate_name);
          $estate_name = str_replace("Владелец", '', $estate_name);
          $estate_name = str_replace("(получает доход)", '', $estate_name);
          $estate_name = str_replace("Владеет на начало игры", '', $estate_name);
          $estate_name = str_replace("Поместье", '', $estate_name);
          $estate_name = str_replace("<br>", '', $estate_name);

          $this_link = str_replace('[Поместье]', $estate_name, $this_link);
					$estates[] = $this_link;
          }
        }
        if (count($estates))
        {
          $alllinks[] = "<span style='page-break-after:always;page-break-before:always;'>";
          $alllinks[] = implode($estates);
          $alllinks[] = "</span>";
        }
			}
		}
			foreach ($all_links_list as $c)
			{
        if ($subobj == 592)
      {
        if (substr(trim($c['sujet_name']), 0, 2) == '!!')
          {
          continue;
          }
      }
				if(strpos($c["roles"],'-'.$role_id.'-')!==false || ($role_status == 3 && strpos($c["roles"],'-all'.$role_vacancy .'-')!==false)) {
          $this_link = '<b>Про ';

					if($c["hideother"]=='0') {
						$this_link.= get_link_targets ($c['roles2']);
					}
					else {
						$this_link.='<i>скрыто</i>';
					}
					$this_link.='</b><br>';
					$this_link.=decode($c["content"]);
					$alllinks[] = $this_link;
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