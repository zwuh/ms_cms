<?php
  global $ms_config;
  // load other necessary modules here
  ms_load_module('people');

  $types = people_get_type(LANG_CHT);
  $base = 0;
  $type_index = 0;
  if (isset($_GET['base']))
  { $base = intval($_GET['base']); }
  if (isset($_GET['type']))
  { $type_index = intval($_GET['type']); }
  $cnt = '';

  $cnt .= '['.$types[$type_index].'] ';
  $cnt .= '<a href="'.$_SERVER['PHP_SELF'].'?node='.NODE_PEOPLE_CONTENT.
          '&lang='.$_COOKIE['lang'].'&base='.max($base-PAGE_LENGTH, 0).
	  '&type='.$type_index.'">上一頁</a>';
  $cnt .= "\n";
  $cnt .= '<a href="'.$_SERVER['PHP_SELF'].'?node='.NODE_PEOPLE_CONTENT.
          '&lang='.$_COOKIE['lang'].'&base='.($base+PAGE_LENGTH).
	  '&type='.$type_index.'">下一頁</a>';
  $cnt .= "\n";

  $res = people_load_segment($dblink, $types[$type_index], $base, $lang);

  $ret['title'] = '人員 People';
  $cnt .= '<table>'."\n";

  // 這裡用的字串必須跟 people.php:people_get_type() 中設定的一致
  switch ($types[$type_index])
  {
    case '執行委員':
    case '執行委員,組主任':
    case '中心成員':
     $i = 0;
     foreach ($res as $p)
     {
      if ($i % 2 == 1)
      { $cnt .= '<tr class="table_even_row">'; }
      else
      { $cnt .= '<tr class="table_odd_row">'; }
      $i ++;
      if (LANG_CHT == $lang)
      {
       $cnt .= '<td>'."\n";
       $cnt .= ' 姓名: '.$p['lastname_cht'].$p['firstname_cht'].' '.
               $p['title_cht']."<br />\n";
       $cnt .= ' 任職單位: '.$p['organization_cht']."<br />\n";
       $cnt .= ' 地址: '.$p['address_cht']."<br />\n";
       $cnt .= ' 電話: '.$p['telephone'].' 傳真: '.$p['fax']."<br />\n";
       $cnt .= ' E-mail: '.$p['email']."<br />\n";
       $cnt .= ' 網站: '.$p['website']."<br />\n";
       $cnt .= '</td>';
      }
      $cnt .= '<tr>'."\n";
     }
     break;
    case '行政人員':
     $i = 0;
     foreach ($res as $p)
     {
      if ($i % 2 == 0)
      { $cnt .= '<tr class="table_even_row">'; }
      else
      { $cnt .= '<tr class="table_odd_row">'; }
      $i ++;
      if (LANG_CHT == $lang)
      {
       $cnt .= '<td>'."\n";
       $cnt .= ' 姓名: '.$p['lastname_cht'].$p['firstname_cht'].' '.
               $p['title_cht']."<br />\n";
       $cnt .= ' 業務: '.$p['field_cht']."<br />\n";
       $cnt .= ' 電話: '.$p['telephone'].' 傳真: '.$p['fax']."<br />\n";
       $cnt .= ' E-mail: '.$p['email']."<br />\n";
      }
      $cnt .= '<tr>'."\n";
     }
     break;
    case '博士後研究員':
    case '訪問學者':
    case '研究助理':
     $i = 0;
     foreach ($res as $p)
     {
      if ($i % 2 == 1)
      { $cnt .= '<tr class="table_even_row">'; }
      else
      { $cnt .= '<tr class="table_odd_row">'; }
      $i ++;
      $cnt .= '<td>';
      if (LANG_CHT == $lang)
      {
       $cnt .= '姓名: '.$p['lastname_cht'].$p['firstname_cht']."<br />\n";
       $cnt .= '辦公室: '.$p['address_cht']."<br />\n";
       $cnt .= '電子郵件: '.$p['email']."<br />\n";
       $cnt .= '任職期間: '.$p['date_begin'].' - '.$p['date_end']."<br />\n";
       $cnt .= '研究領域: '.$p['field_cht']."<br />\n";
      }
      $cnt .= '</td></tr>'."\n";
     }
     break;
    default:
     $cnt .= 'default';
     break;
  }

  $cnt .= '</table>'."\n";
  $ret['content'] = $cnt;
?>
