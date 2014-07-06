<?php
  global $ms_config;
  // load other necessary modules here
  ms_load_module('techrep');

  $cnt = '';

  if (isset($_GET['year']))
  {
   $year = intval($_GET['year']);
   $res = techrep_load_year($dblink, $year);
   $cnt .= '<table>'."\n".'<tr>';
   if (LANG_ENU == $lang)
   {
    $cnt .= '<th>Year-Serial</th><th>Title/Authors</th>';
   }
   else
   {
    $cnt .= '<th>年序</th><th>主題/作者</th>';
   }
   $cnt .= '</tr>'."\n";
   $i = 0;
   foreach ($res as $t)
   {
    if ($i % 2 == 0)
    { $cnt .= '<tr class="table_even_row">'."\n"; }
    else
    { $cnt .= '<tr class="table_odd_row">'."\n"; }
    $i ++;
    $cnt .= '<td>'.$t['year'].'-'.$t['serial'].'</td>';
    $cnt .= '<td>(<a href="'.UPLOAD_URL_BASE.'techrep/'.$t['file_abstract'].'">'.
            'abstract</a>|'.
	    '<a href="'.UPLOAD_URL_BASE.'techrep/'.$t['file_fulltext'].'">'.
            'fulltext</a>) '.
	    $t['title'].' <strong>by</strong> '.$t['author'].'</td>';
    $cnt .= '</tr>'."\n";
   }
   $cnt .= '</table>';
  }
  else
  {
   $y_res = techrep_get_existing_years($dblink);
   foreach ($y_res as $y)
   {
     $cnt .= '[<a href="'.$_SERVER['PHP_SELF'].'?node='.NODE_TECHREP_CONTENT.
             '&year='.$y.'&lang='.$_COOKIE['lang'].'">'.$y.'</a>]';
   }
   if (LANG_ENU == $lang)
   { $cnt .= $node['context_enu']; }
   else
   { $cnt .= $node['context_cht']; }
  }
  $ret['content'] = $cnt;
  $ret['title'] = '技術報告 Tech Report';
?>
