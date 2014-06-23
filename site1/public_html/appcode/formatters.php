<?php
// This file includes some quick formatting functions

function phone_formatter($obj, $row)
{
  return phone_formatter_raw($row [$obj -> getName()]);
}

function phone_formatter_raw ($phone)
{
  if (strlen ($phone) > 20) 
  {
    return $phone;
  }
  $phone = trim (preg_replace("/[^0-9,.]/", "", $phone));
  if (strlen($phone) == 11 && $phone[0] == '8')
  {
    $phone = substr_replace ($phone, '+7', 0, 1);
  }
  elseif (strlen($phone) == 11 && $phone[0] == '7')
  {
    $phone = '+' . $phone;
  }
  return  $phone; 
}


function name_as_master_formatter_row ($row, $options) //Master should always ignore 'hidesome'
{
  $sid = strpos($options, 'skipsid') !== FALSE ? '' : $row['sid'];
  $fio = $row['fio'];
  $nick = $row['nick'] ? "({$row['nick']})" : '';
  $parts = array_filter( //Remove empty elements
    array($fio, $nick, $sid));
  return implode (' ',  $parts);
}

function name_public_compact_formatter_row ($row, $options)
{
  global $server_absolute_path_info;
  $use_link = strpos($options, 'uselink') !== FALSE;
  $use_tooltip = strpos ($options, 'usetooltip') !== FALSE;
  $nick = (strpos($row['hidesome'], '-0-') === false) ? trim(decode($row['nick'])) : NULL;
  $fio = strpos($row['hidesome'], '-10-') === false ? trim(decode($row['fio'])) : NULL;
  $inp = "ИНП {$row['sid']}";

  $value = array_slice(array_filter( array ($nick, $fio, $inp)),0);
  $value = $value[0];

  if ($use_tooltip)
  {
    $tooltip = $fio ? "$fio ($nick)" : $nick;
    $tooltip = $tooltip ? " title=\"$tooltip\"" : '';
  }
  else 
  {
    $tooltip = '';
  }

  $value =  $use_link ? "<a href=\"{$server_absolute_path_info}users/{$row["sid"]}/\">$value</a>" : $value;
  return "<span class=\"username_label\"$tooltip>$value</span>";
}

?>