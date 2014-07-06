<?php
  global $ms_config;
  // load other necessary modules here
  ms_load_module('seminar');

  $cnt = '';
  $self_url = $_SERVER['PHP_SELF'].'?node='.NODE_SEMINAR_CONTENT.
                  '&lang='.$_COOKIE['lang'];
  $base = 0;
  if (isset($_GET['similar']))
  {
   $ref_id = intval($_GET['similar']);
   $res = seminar_load_similar_topics($dblink, $ref_id);
   $cnt .= '<table>'."\n";
   $cnt .= '<tr><th>Speaker</th><th>Topic</th></tr>'."\n";
   foreach ($res as $s)
   {
    $cnt .= '<tr>';
    $cnt .= '<td>'.$s['firstname_enu'].' '.$s['lastname_enu'].'<br />'.
            $s['lastname_cht'].' '.$s['firstname_cht'].'</td><td>'.
	    '<a href="'.$self_url.'&sid='.$s['id'].'">'.
	    $s['topic_enu'].'<br >'.$s['topic_cht'].'</a></td>';
    $cnt .= '</tr>'."\n";
   }
   $cnt .= '</table>'."\n";
  }
  else
  {
   if (isset($_GET['sid']))
   {
    $master_id = intval($_GET['sid']);
    $res = seminar_load_sessions_by_master($dblink, $master_id);
   }
   else
   {
    if (isset($_GET['base']))
    { $base = intval($_GET['base']); }
    $res = seminar_load_sessions_by_time($dblink, $base);
    $cnt .= '<a href="'.$self_url.'&base='.max($base-PAGE_LENGTH, 0).'">'.
            '上一頁</a>';
    $cnt .= "\n";
    $cnt .= '<a href="'.$self_url.'&base='.($base+PAGE_LENGTH).'">'.
            '下一頁</a>';
    $cnt .= "\n";
   }
   $cnt .= '<table><tr>'."\n";
   if (LANG_CHT == $lang)
   {
     $cnt .= '<th>時間</th><th>講者</th><th>主題/地點</th><th>檔案</th>'."\n";
   }
   else
   {
     $cnt .= '<th>Time</th><th>Speaker</th><th>Title/Place</th><th>Files</th>'."\n";
   }
   $cnt .= "</tr>\n";
   $i = 0;
   foreach ($res as $s)
   {
    if ($i % 2 == 0)
    { $cnt .= '<tr class="table_even_row">'."\n"; }
    else
    { $cnt .= '<tr class="table_odd_row">'."\n"; }
    $i ++;
    if (LANG_ENU == $lang)
    {
     $cnt .= '<td>'.$s['date'].'<br />'.$s['time_begin'].' - '.$s['time_end'].
             '</td><td>'.$s['title_enu'].' '.$s['firstname_enu'].
 	    $s['lastname_enu'].'<br />'.$s['organization_enu'].'</td><td>'.
            '<a href="'.$_SERVER['PHP_SELF'].'?node='.NODE_SEMINAR_CONTENT.
	    '&similar='.$s['master_id'].'"><strong>'.
	    $s['master_topic_enu'].'</strong></a><br />'.
 	    $s['topic_enu'].'<br />'.$s['location_enu'].'</td>';
    }
    else
    {
     $cnt .= '<td>'.$s['date'].'<br />'.$s['time_begin'].' - '.$s['time_end'].
             '</td><td>'.$s['lastname_cht'].$s['firstname_cht'].' '.
 	    $s['title_cht'].'<br />'.$s['organization_cht'].'</td><td>'.
	    '<a href="'.$_SERVER['PHP_SELF'].'?node='.NODE_SEMINAR_CONTENT.
            '&similar='.$s['master_id'].'">'.
 	    '<strong>'.$s['master_topic_cht'].'</strong></a><br />'.
 	    $s['topic_cht'].'<br />'.$s['location_cht'].'</td>';
    }
    $cnt .= '<td>';
    $files = seminar_process_files($s['file_types'], $s['file_names']);
    foreach ($files as $f)
    {
     $type = $f['type'];
     if (LANG_ENU == $lang)
     {
      switch ($type)
      {
       case '公告': $type = 'Annc'; break;
       case '簡報': $type = 'Pres'; break;
       case '論文': $type = 'Paper'; break;
       default: $type = 'Others'; break;
      }
     }
     $cnt .= '<a href="'.UPLOAD_URL_BASE.'seminar/'.$f['name'].'">'.$type.
             '</a><br />';
    }
    $cnt .= '</td></tr>'."\n";
   }
   $cnt .= '</table>'."\n";
  }
  $ret['title'] = '演講 Seminar';
  $ret['content'] = $cnt;
?>
