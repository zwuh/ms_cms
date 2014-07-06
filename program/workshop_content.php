<?php
  global $ms_config;
  // load other necessary modules here
  ms_load_module('workshop');

  $cnt = '';
  if (isset($_GET['wid']))
  {
    $wid = intval($_GET['wid']);
    $res = workshop_load_one($dblink, $wid);
    $files = workshop_process_files($res['files']);
    foreach ($files as $f)
    {
     $type = $f['type'];
     if (LANG_ENU == $lang)
     {
      switch ($type)
      {
       case '公告': $type = 'Announcements'; break;
       case '議程': $type = 'Program'; break;
       default: $type = 'Others'; break;
      }
     }
     if (strlen(trim($f['name'])) > 0)
     {
      $cnt .= '[<a href="'.UPLOAD_URL_BASE.'seminar/'.
              $f['name'].'">'.$type.'</a>] ';
     }
    }
    $cnt .= "<br />\n<hr />\n";
    if (LANG_ENU == $lang)
    {
     $ret['title'] = $res['title_enu'];
     $cnt .= $res['context_enu'];
    }
    else
    {
     $ret['title'] = $res['title_cht'];
     $cnt .= $res['context_cht'];
    }
  }
  else
  {
   $base = 0;
   if (isset($_GET['base']))
   { $base = intval($_GET['base']); }
   $cnt .= '<a href="'.$_SERVER['PHP_SELF'].'?node='.NODE_WORKSHOP_CONTENT.
           '&lang='.$_COOKIE['lang'].'&base='.max($base-PAGE_LENGTH, 0).
           '">上一頁</a>';
   $cnt .= "\n";
   $cnt .= '<a href="'.$_SERVER['PHP_SELF'].'?node='.NODE_WORKSHOP_CONTENT.
           '&lang='.$_COOKIE['lang'].'&base='.($base+PAGE_LENGTH).
           '">下一頁</a>';
   $cnt .= "\n";
   $res = workshop_load_brief_list($dblink, $base);
   $cnt .= '<table><tr>'."\n";
   if (LANG_CHT == $lang)
   {
     $cnt .= '<th>主題</th><th>日期</th>'."\n";
   }
   else
   {
     $cnt .= '<th>Title</th><th>Date</th>'."\n";
   }
   $cnt .= "</tr>\n";
   $i = 0;
   foreach ($res as $w)
   {
     if ($i % 2 == 0)
     { $cnt .= '<tr class="table_even_row">'."\n"; }
     else
     { $cnt .= '<tr class="table_odd_row">'."\n"; }
     $i ++;
     if (LANG_ENU == $lang)
     {
     }
     else
     {
       $cnt .= '<td><a href="'.$_SERVER['PHP_SELF'].'?node='.
               NODE_WORKSHOP_CONTENT.'&lang='.$_COOKIE['lang'].'&wid='.
	       $w['id'].'">'.$w['title_cht'].'</a></td><td>'.$w['date_begin'].
	       ' - '.$w['date_end'].'</td>';
     }
     $cnt .= '</tr>'."\n";
   }
   $cnt .= '</table>'."\n";
   $ret['title'] = '研討會 Workshop';
  }
  $ret['content'] = $cnt;
?>
