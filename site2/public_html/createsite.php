<?php
function createsite() {
	global
		$dynrequest,
		$link,
		$prefix,
		$leadc1,
		$leadc2,
		$server_inner_path,
		$admin,
		$direct,
		$trouble,
		$trouble2,
		$stayhere,
		$_REQUEST,
		$_SESSION,
		$server_absolute_path_site;

	$errorinstall=false;

	$author=$_SESSION["user_id"];
	if($author=='') {
		dynamic_err_one('error',"Пользователь не найден.");
    }

    if(encode_to_cp1251($_REQUEST["name"])!='') {
    	$name=strtolower(encode_to_cp1251($_REQUEST["name"]));
    }

    if(encode_to_cp1251($_REQUEST["title"])!='') {
    	$title=addslashes(encode_to_cp1251($_REQUEST["title"]));
    }
    else {
    	dynamic_err_one('error',"Не заполнено обязательное поле «Название проекта».",array('title'));
    }

    if(encode_to_cp1251($_REQUEST["datestart"])!='') {
    	$datestart=encode_to_cp1251($_REQUEST["datestart"]);
    }
    else {
    	dynamic_err_one('error',"Не заполнено обязательное поле «Дата начала».",array('datestart'));
    }

    if(encode_to_cp1251($_REQUEST["datefinish"])!='') {
    	$datefinish=encode_to_cp1251($_REQUEST["datefinish"]);
    }
    else {
    	dynamic_err_one('error',"Не заполнено обязательное поле «Дата окончания».",array('datefinish'));
    }

    if(encode_to_cp1251($_REQUEST["region"])!='') {
    	$region=encode_to_cp1251($_REQUEST["region"]);
    }
    else {
    	dynamic_err_one('error',"Не заполнено обязательное поле «Регион».",array('region'));
    }

    if(encode_to_cp1251($_REQUEST["description"])!='') {
    	$description=encode_to_cp1251($_REQUEST["description"]);
    }
    else {
    	dynamic_err_one('error',"Не заполнено обязательное поле «Подробное описание проекта».",array('description'));
    }

    if(encode_to_cp1251($_REQUEST["date"])!='') {
    	$date=encode_to_cp1251($_REQUEST["date"]);
    }
    else {
    	$date=time();
    }

	if($dynrequest==1) {
		dynamic_err(array(),'submit');
	}

	require_once($server_inner_path.$direct."/classes/base_mails.php");

	mysql_query("INSERT INTO ".$prefix."orders (author, usetemp, name, title, datestart, datefinish, region, em, blog, blogname, description, date) values ($author, 2, '".$name."', '".$title."', '".$datestart."', '".$datefinish."', ".$region.", '".$em."', '".$blog."', '".$blogname."', '".$description."', ".$date.")");
	$inz=mysql_insert_id($link);
	mysql_query("UPDATE ".$prefix."orders SET sid=".$inz." WHERE id=".$inz);

		mysql_query("INSERT INTO `".$prefix."sites` (sio, path2, title, usetemp, rolesubs, sorter, money, status, status2, allspace, datestart, datefinish) VALUES (".$inz.", '".$name."', '".$title."', 2, 'Добрый день!&lt;br /&gt;&lt;br&gt;&lt;br /&gt;&lt;br&gt;&lt;br /&gt;&lt;br&gt;&lt;br /&gt;&lt;br&gt;С уважением,&lt;br /&gt;&lt;br&gt;&nbsp;&nbsp;&nbsp; мастерская группа проекта «".$title."».', 0, '0р.', 1, 1, 104857600, '".$datestart."', '".$datefinish."');");
	
	$prefix2=mysql_insert_id($link);

	$result2=mysql_query("SELECT * from ".$prefix."temps limit 0,1");
	$b=mysql_fetch_array($result2);

	mysql_query("UPDATE ".$prefix."sites SET sid=".$prefix2.", htmlcodeindex=".$b["id"].", htmlcode='".$b["htmlcode"]."', css='".$b["css"]."', usercss='".$b["usercss"]."', menualign=".$b["menualign"].", submenualign=".$b["submenualign"].", newsformat1='".$b["newsformat1"]."', newsformat2='".$b["newsformat2"]."', separ='".$b["separ"]."', separkind='".$b["separkind"]."', separsub='".$b["separsub"]."', htmlmade='".time()."', date='".time()."' where id=".$prefix2);

	mysql_query("INSERT INTO `".$prefix."rolefields` (`site_id`, `rolename`, `roletype`, `rolemustbe`, `roledefault`, `rolerights`, `rolehelp`, `rolevalues`, `rolecode`, `rolewidth`, `roleheight`, `team`, `date`) VALUES (".$prefix2.", 'Персонаж', 'h1', '0', '', 4, '', '', 1, 0, 0, '0', '1163501641');");
	mysql_query("INSERT INTO `".$prefix."rolefields` (`site_id`, `rolename`, `roletype`, `rolemustbe`, `roledefault`, `rolerights`, `rolehelp`, `rolevalues`, `rolecode`, `rolewidth`, `roleheight`, `team`, `date`) VALUES (".$prefix2.", 'Имя персонажа', 'text', '1', '', 4, '', '', 2, 0, 0, '0', '1163501641');");

	$rolefieldid=mysql_insert_id($link);

	mysql_query("UPDATE `".$prefix."sites` SET sorter=".$rolefieldid." where id=".$prefix2);

	mysql_query("INSERT INTO `".$prefix."allrights2` (user_id, rights, site_id, signtonew, signtochange, signtocomments) VALUES (".$_SESSION["user_sid"].", 1, ".$prefix2.", '1', '1', '1');");

	mysql_query("INSERT INTO `".$prefix."pages` (code, alias, parent, active, name, author, http, content, metacont, metakey, date, rights, site_id) VALUES (1, 'main', 0, '1', 'О проекте', '', '', '{menu}', '', '', '1163703678', '-0-', ".$prefix2.");");
	$ppid=mysql_insert_id($link);

	mysql_query("UPDATE ".$prefix."sites SET defcode=".$ppid." where id=".$prefix2);

	mysql_query("INSERT INTO `".$prefix."pages` (code, parent, active, name, author, http, content, metacont, metakey, date, rights, site_id) VALUES (1, ".$ppid.", '1', '', '', '', 'Информация о проекте', '', '', '1167389761', '-0-', ".$prefix2.");");
	mysql_query("INSERT INTO `".$prefix."pages` (code, alias, parent, active, name, author, http, content, metacont, metakey, date, rights, site_id) VALUES (2, 'news', 0, '1', 'Новости', '', '', '{menu}', '', '', '1167387928', '-0-', ".$prefix2.");");
	$ppid=mysql_insert_id($link);

	mysql_query("UPDATE ".$prefix."sites SET newscode=".$ppid." where id=".$prefix2);

	mysql_query("INSERT INTO `".$prefix."pages` (code, alias, parent, active, name, author, http, content, metacont, metakey, date, rights, site_id) VALUES (3, 'roles', 0, '1', 'Список заявок', '', '', '{menu}', '', '', '1167387928', '-0-', ".$prefix2.");");
	$ppid=mysql_insert_id($link);

	mysql_query("UPDATE ".$prefix."sites SET rolescode=".$ppid." where id=".$prefix2);

	mysql_query("INSERT INTO `".$prefix."pages` (code, parent, active, name, author, http, content, metacont, metakey, date, rights, site_id) VALUES (1, ".$ppid.", '1', 'Текст на верху страницы списка ролей', '', '', '&lt;center&gt;&lt;div id=&quot;cb_editor&quot;&gt;&lt;h3&gt;Для того чтобы подать заявку на игру Вам потребуется &lt;a href=&quot;http://www.allrpg.info/register/redirectobj=order&amp;redirectid=".$prefix2."&quot;&gt;зарегистрироваться&lt;/a&gt; на сайте allrpg.info.&br;&lt;br&gt;После регистрации Вы сможете подать заявку &lt;a href=&quot;http://www.allrpg.info/order/act=add&amp;subobj=".$prefix2."&quot;&gt;здесь&lt;/a&gt;.&lt;/h3&gt;&lt;/div&gt;&lt;/center&gt;', '', '', '1177663197', '-0-', ".$prefix2.");");

	err('Проект создан. Вы можете переключиться на управление проектом <a href="'.$server_absolute_path_site.'site='.$prefix2.'">здесь</a>.');

	$result2=mysql_query("SELECT * from ".$prefix."users where id=".$author);
	$b=mysql_fetch_array($result2);

	$myname="allrpg.info";
	$myemail="project@allrpg.info";
	$contactemail=decode($b["em"]);

	$message='Уважаемый '.usname($b,true).'!

Ваш проект активирован. Теперь на сайте allrpg.info в разделе «Проекты» Вы можете настроить его и подготовить к полноценному запуску.
Вы можете переключиться на управление проектом здесь: '.$server_absolute_path_site.'site='.$prefix2.'.';

	$subject='Активирован проект «'.decode($title).'» на сайте allrpg.info';

	if($contactemail!='') {
		if(send_mail($myname, $myemail, $contactemail, $subject, $message, false)) {
			err('Вам отправлено письмо об активации проекта.');
		}
		else {
			err_red("При отправке письма об активации проекта на сервере возникли проблемы.");
		}
	}

	$result=mysql_query("SELECT * from ".$prefix."users where id=".$_SESSION['user_id']);
	$a=mysql_fetch_array($result);
	$myname=usname($a,true);
	$myemail=decode($a["em"]);
	$contactemail="project@allrpg.info";

		$subject='Новая система заявок на allrpg.info';
		$message='Пользователь '.$myname.' создал новую систему заявок. Пожалуйста, проверьте правильность данных.
Внешний сайт: '.$name.'
Название проекта: '.$title.'
Общее описание проекта:

'.strip_tags(encode_to_cp1251($_REQUEST["description"])).'

Дата начала: '.$datestart.'
Дата окончания: '.$datefinish;
	
	send_mail($myname, $myemail, $contactemail, $subject, $message);
}
?>