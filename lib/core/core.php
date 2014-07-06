<?php
/*
 *  MS-Project CMS Main Engine Library
 *
 *  This file should not be included directly.
 *  It should be included via config.php instead.
 */

function ms_version()
{
}

function ms_load_module($module_name)
{
  global $ms_config;
  if (isset($ms_config['modules'][$module_name]))
  {
    require_once($ms_config['modules'][$module_name]);
    if (!function_exists($module_name.'_version'))
    {
      die ('ms_load_module: Module '.$module_name.' load failed '.
           'or *_version() function not defined.');
    }
    return true;
  }
  return false;
}

function ms_debug($str)
{
  global $ms_config;

  if ($ms_config['debug'] == 1)
  {
    echo $str."\n";
  }
}

function ms_set_lang_cht()
{
  global $ms_config;
  setcookie('lang', $ms_config['lang_str'][LANG_CHT]);
  $_COOKIE['lang'] = $ms_config['lang_str'][LANG_CHT];
}

function ms_set_lang_enu()
{
  global $ms_config;
  setcookie('lang', $ms_config['lang_str'][LANG_ENU]);
  $_COOKIE['lang'] = $ms_config['lang_str'][LANG_ENU];
}

function ms_set_lang()
{
  global $ms_config;
  if (isset($_REQUEST['lang']))
  {
    if (!strcmp($_REQUEST['lang'], $ms_config['lang_str'][LANG_ENU]))
    { ms_set_lang_enu(); }
    else
    { ms_set_lang_cht(); }
  }
  else if (isset($_COOKIE['lang']) &&
           !strcmp($_COOKIE['lang'], $ms_config['lang_str'][LANG_ENU]))
  { ms_set_lang_enu(); }
  else if(LANG_ENU == $ms_config['lang'])
  { ms_set_lang_enu(); }
  else
  { ms_set_lang_cht(); }
}

function ms_get_lang()
{
  global $ms_config;
  if (!isset($_COOKIE['lang']))
  { return LANG_CHT; }
  if (!strcmp($_COOKIE['lang'], $ms_config['lang_str'][LANG_ENU]))
  { return LANG_ENU; }
  return LANG_CHT;
}

function ms_build_table_name($index = '')
{
  global $ms_config;
  return $ms_config['db']['table_prefix'].'nodes';
}

function ms_load_topic_list($dblink, $base = 0, $length = PAGE_LENGTH)
{
  $table_name = ms_build_table_name();
  $sql = 'SELECT id,is_sidebar,is_program,sidebar,mod_time,title_cht '.
         ' FROM '.$table_name.' ORDER BY id LIMIT '.$base.','.$length;
  $res = $dblink->query($sql);
  if (!$res)
  {
    $ret = array( array(
      'id' => '0',
      'title_cht' => 'ms_load_topic_list: Error,'.mysqli_error($dblink)
    ));
  }
  else
  {
    $ret = array ();
    while ($item = $res->fetch_assoc())
    {
      $ret[] = $item;
    }
  }
  return $ret;
}

function ms_load_full_node($dblink, $id = NODE_HOMEPAGE)
{
  $table_name = ms_build_table_name();
  if ($res = $dblink->query(
    'SELECT * FROM '.$table_name.' WHERE id='.$id.' LIMIT 1'))
  {
    $ret = $res->fetch_assoc();
  }
  else
  {
   $ret = array(
    'id' => 0,
    'title_cht' => 'Node load failed',
    'title_enu' => '',
    'sidebar' => NODE_DEFAULT_SIDEBAR,
    'is_sidebar' => 0,
    'is_program' => 0,
    'context_cht' => mysqli_error($dblink),
    'context_enu' => ''
   );
  }
  return $ret;
}

function ms_load_node($dblink, $id = NODE_HOMEPAGE, $lang = LANG_CHT)
{
  $ret = array(
   'id' => $id,
   'title' => 'Title Here',
   'is_sidebar' => 0,
   'sidebar' => NODE_DEFAULT_SIDEBAR,
   'content' => 'Content Here'
  );

  $node = ms_load_full_node($dblink, $id);

  $ret['is_sidebar'] = $node['is_sidebar'];
  $ret['sidebar'] = $node['sidebar'];

  /* 'title_enu' is used as pointer to target */
  if ($node['is_program'] == TRUE)
  {
    /* The invoked program is responsible for
     * filling $ret[] with proper data.
     * 'content' is required. */
    include('program/'.$node['title_enu']);
  }
  else
  {
    if (LANG_ENU == $lang)
    {
      $ret['title'] = $node['title_enu'];
      $ret['content'] = $node['context_enu'];
    }
    else
    {
      $ret['title'] = $node['title_cht'];
      $ret['content'] = $node['context_cht'];
    }
  }

  return $ret;
}

function ms_delete_node($dblink, $id)
{
 global $ms_config;
 $table_name = ms_build_table_name();

 $n_id = intval($id);
 if ($n_id == 1 || $n_id == 2)
 {
   die ('ms_delete_node: Error: '.
        'You may NOT delete node #1 (the Default Homepage) and '.
        'node #2 (the Default Sidebar) !');
 }
 if ($n_id <= $ms_config['special_nodes'])
 {
   die ('ms_delete_node: Error: '.
        'Node #'.$n_id.' is a special node, which you may NOT delete!');
 }
 if (!$dblink->query('DELETE FROM '.$table_name.
                     ' WHERE `id`='.$n_id.' LIMIT 1'))
 {
   echo 'ms_delete_node: Error,'.mysqli_error($dblink);
 }
 echo 'ms_delete_node: Done, af: '.$dblink->affected_rows;
}

function ms_write_new_node($dblink, $node)
{
 $table_name = ms_build_table_name();

 $ret_id = 0;
 $sql = 'INSERT INTO '.$table_name.
        ' SET `title_cht`=\''.$node['title_cht'].'\','.
        '`title_enu`=\''.$node['title_enu'].'\','.
        '`is_sidebar`=\''.$node['is_sidebar'].'\','.
        '`is_program`=\''.$node['is_program'].'\','.
        '`sidebar`=\''.$node['sidebar'].'\','.
        '`context_cht`=\''.$node['context_cht'].'\','.
        '`context_enu`=\''.$node['context_enu'].'\'';
 if (!$dblink->query($sql))
 {
  echo 'ms_write_new_node: Error, '.mysqli_error($dblink);
 }
 else
 {
  $ret_id = $dblink->insert_id;
 }
 return $ret_id;
}

function ms_update_old_node($dblink, $node)
{
 $table_name = ms_build_table_name();
 if (!isset($node['id']))
 {
   die('ms_update_old_node: updating with no id ?');
 }
 $sql = 'UPDATE '.$table_name.
        ' SET `title_cht`=\''.$node['title_cht'].'\','.
        '`title_enu`=\''.$node['title_enu'].'\','.
        '`is_sidebar`=\''.$node['is_sidebar'].'\','.
        '`is_program`=\''.$node['is_program'].'\','.
        '`sidebar`=\''.$node['sidebar'].'\','.
        '`context_cht`=\''.$node['context_cht'].'\','.
        '`context_enu`=\''.$node['context_enu'].'\''.
        ' WHERE `id`='.$node['id'].' LIMIT 1';
 if (!$dblink->query($sql))
 {
  echo 'ms_update_old_node: Error, '.mysqli_error($dblink);
 }
}

function ms_get_db_link()
{
  global $ms_config;

  if (!($link = mysqli_init()))
  {
    die ('ms_get_db_link: mysqli_init() failed.');
  }
  if (!$link->real_connect($ms_config['db']['hostname'],
    $ms_config['db']['username'], $ms_config['db']['password'],
    $ms_config['db']['database']))
  {
    die ('ms_get_db_link: Cannot connect: '.mysqli_error($link));
  }
  if (!$link->set_charset("utf8"))
  {
    die ('ms_get_db_link: Cannot set_charset() to utf8, '.mysqli_error($link));
  }

  return $link;
}

function ms_close_db_link($link)
{
  $link->close();
}

function ms_escape_string($link, $str)
{
  $s1 = $link->real_escape_string($str);
  $s2 = addcslashes($s1, '%_');
//  return $s2; -- filenames contain '_', and there is no direct user input
  return $s1;
}

function ms_manage_check_auth()
{
  if (!ms_check_auth())
  {
    header('Location: '.BASE_URL.'manage/auth.php');
  }
}

function ms_check_auth()
{
  global $ms_config;
  // For W3C Validation
  //return TRUE;
  if (isset($_SESSION['username']) && isset($_SESSION['auth']) &&
   !strcmp($_SESSION['auth'], sha1($ms_config['secret'].$_SESSION['username'])))
  {
    return TRUE;
  }
  return FALSE;
}

function ms_auth($dblink, $user, $password)
{
  global $ms_config;

  $n_user = ms_escape_string($dblink, trim($user));
  if (strcmp($n_user, $user))
  {
    die ('ms_auth: user dirty');
  }

  return FALSE;
}

function ms_auth_logout($dblink)
{
  $_SESSION['auth'] = null;
  $_SESSION['username'] = null;
}

function ms_base($base, $step = 0)
{
  $r = $base+$step;
  if ($r < 0)
   return 0;
  return $r;
}

function ms_strip_path($path)
{
  $ar = explode('/', $path);
  $c = count($ar);
  if ($c > 0)
  {
    return $ar[$c-1];
  }
  return '';
}

?>
