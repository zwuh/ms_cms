<?php
  // A sample for program-driven nodes
  // This program is to be include()-ed by ms_load_node() in core module
  // config.php and core module will/must be loaded a priori

  global $ms_config;
  // load other necessary modules here

  // current node id: $ret['id'], sidebar: $ret['sidebar']
  // database link: $dblink (*DO NOT* close it)
  // language indicator: $lang = { LANG_CHT, LANG_ENU } (*READ-ONLY*)
  // string type indicator: $_COOKIE['lang'] (* READ-ONLY *)

  // Your initial page body
  $ret['content'] = '';
  // Your initial page title
  $ret['title'] = '';
  // You can change the companion sidebar number here, if necessary
  // This is NOT recommended.
  //$ret['sidebar'] = NODE_DEFAULT_SIDEBAR;

?>
