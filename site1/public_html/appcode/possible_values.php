<?php
// This file includes some labels for common fields

function get_possible_values ($field)
{
  switch ($field) {
    case 'gender':  return array(array('1','мужской'),Array('2','женский'));
    case 'medic':   return array(array('0','Нет'), Array('1','медсестра/медбрат'), array('2','фельдшер'), array('4','врач'), array('4','врач-травматолог/реаниматолог/анестезиолог'));
    case 'status':  return array(array('1','подана'), array('2','обсуждается'), array('3','принята'), array('4','отклонена'));
  }
  echo "Unknown field $field";
  error_log ("Unknown field $field");
  die();
}

function get_label_from_id ($id, $field)
{
  $dict = get_possible_values ($field);
  foreach ($dict as $key)
  {
    if ($key[0] == $id)
    {
      return $key[1];
    }
  }
  return $id;
}

?>