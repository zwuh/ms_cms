<?php
  global $ms_config;
  // load other necessary modules here
  ms_load_module('people');

  // database link: $dblink (*DO NOT* close it)
  // language indicator: $lang = { LANG_CHT, LANG_ENU } (*DO NOT* change it)

  $ret['content'] = '';
  $ret['title'] = '';

  $cnt = '';
  $types = people_get_type($lang);
  $cnt .= '<ul>'."\n";
  $i = 0;
  foreach ($types as $t)
  {
    $cnt .= ' <li><a href="'.$_SERVER['PHP_SELF'].'?node='.NODE_PEOPLE_CONTENT.
            '&lang='.$_COOKIE['lang'].'&type='.$i.'">'.$t.'</a></li>'."\n";
    $i ++;
  }
  $cnt .= '</ul>';

  $ret['content'] = $cnt;
?>
