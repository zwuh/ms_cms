<?php

function workshop_version()
{
}

function workshop_build_table_name($index = '')
{
  global $ms_config;
  return $ms_config['db']['table_prefix'].'workshop';
}

function workshop_load_brief_list($dblink, $base = 0, $length = PAGE_LENGTH)
{
  $table_name = workshop_build_table_name();
  $sql = 'SELECT id,title_cht,title_enu,date_begin,date_end,files '.
         ' FROM '.$table_name.' ORDER BY id DESC LIMIT '.$base.','.$length;
  $res = $dblink->query($sql);
  if (!$res)
  {
    $ret = array( array(
      'id' => '0',
      'title_cht' => 'workshop_load_brief_list: Load failed.'.
                      mysqli_error($dblink)
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

function workshop_load_one($dblink, $target_id)
{
  $table_name = workshop_build_table_name();
  if ($res = $dblink->query(
    'SELECT * FROM '.$table_name.' WHERE id='.intval($target_id).' LIMIT 1'))
  {
    $ret = $res->fetch_assoc();
  }
  else
  {
   $ret = array(
    'id' => 0,
    'title_cht' => 'Node load failed',
    'title_enu' => '',
    'context_cht' => mysqli_error($dblink),
    'context_enu' => '',
    'file' => '',
    'date_begin' => '',
    'date_end' => ''
   );
  }
  return $ret;
}

function workshop_delete($dblink, $target_id)
{
 $table_name = ms_build_table_name();
 $sql = 'DELETE FROM '.workshop_build_table_name().
        ' WHERE id='.intval($target_id).' LIMIT 1';
 if (!$dblink->query($sql))
 {
   echo 'workshop_delete: Error,'.mysqli_error($dblink);
 }
 else
 {
   echo 'workshop_delete, affected entries: '.$dblink->affected_rows;
 }
}

function workshop_process_files($file_names)
{
  $files = explode(',', $file_names);
  $ret = Array();
  if (isset($files[0]))
  {
   $ret[] = Array( 'field' => 'file_ann', 'type' => '公告', 'name' => $files[0] );
  }
  else
  { $ret[] = Array ( 'field' => 'file_ann', 'type' => '公告', 'name' => '' ); }

  if (isset($files[1]))
  {
   $ret[] = Array( 'field' => 'file_agen', 'type' => '議程', 'name' => $files[1] );
  }
  else
  { $ret[] = Array ( 'field' => 'file_agen', 'type' => '議程', 'name' => '' ); }

  if (isset($files[2]))
  {
   $ret[] = Array( 'field' => 'file_other', 'type' => '其他說明', 'name' => $files[2] );
  }
  else
  { $ret[] = Array ( 'field' => 'file_other', 'type' => '其他說明', 'name' => '' ); }

  return $ret;
}

function workshop_implode_files($request_array)
{
  $fs = '';

  if (isset($request_array['file_ann']))
  { $fs .= trim($request_array['file_ann']); }
  $fs .= ',';
  if (isset($request_array['file_agen']))
  { $fs .= trim($request_array['file_agen']); }
  $fs .= ',';
  if (isset($request_array['file_other']))
  { $fs .= trim($request_array['file_other']); }

  return $fs;
}

function workshop_write($dblink, $target)
{
 $table_name = workshop_build_table_name();

 $ret_id = 0;
 $files = workshop_implode_files($target);
 $sql = 'INSERT INTO '.$table_name.
        ' SET `title_cht`=\''.ms_escape_string($dblink, $target['title_cht']).
	'\', `title_enu`=\''.ms_escape_string($dblink, $target['title_enu']).
	'\', `context_cht`=\''.ms_escape_string($dblink,$target['context_cht']).
	'\', `context_enu`=\''.ms_escape_string($dblink,$target['context_enu']).
	'\', `files`=\''.ms_escape_string($dblink,$files).
	'\', `date_begin`=\''.ms_escape_string($dblink, $target['date_begin']).
	'\', `date_end`=\''.ms_escape_string($dblink, $target['date_end']).
	'\', `accomodation`=\''.ms_escape_string($dblink, $target['accomodation']).
	'\'';
 if (!$dblink->query($sql))
 {
  echo 'workshop_write: Error, '.mysqli_error($dblink);
 }
 else
 {
  $ret_id = $dblink->insert_id;
 }
 return $ret_id;
}

function workshop_update($dblink, $target)
{
 $table_name = workshop_build_table_name();
 if (!isset($target['id']))
 {
   die('workshop_update: updating with no id ?');
 }
 $files = workshop_implode_files($target);
 $sql = 'UPDATE '.$table_name.
        ' SET `title_cht`=\''.ms_escape_string($dblink, $target['title_cht']).
	'\', `title_enu`=\''.ms_escape_string($dblink, $target['title_enu']).
	'\', `context_cht`=\''.ms_escape_string($dblink,$target['context_cht']).
	'\', `context_enu`=\''.ms_escape_string($dblink,$target['context_enu']).
	'\', `files`=\''.ms_escape_string($dblink, $files).
	'\', `date_begin`=\''.ms_escape_string($dblink, $target['date_begin']).
	'\', `date_end`=\''.ms_escape_string($dblink, $target['date_end']).
	'\', `accomodation`=\''.ms_escape_string($dblink, $target['accomodation']).
	'\' LIMIT 1';
 if (!$dblink->query($sql))
 {
  echo 'workshop_update: Error, '.mysqli_error($dblink);
 }
 else
 {
  echo 'workshop_update: succeeded, af: '.$dblink->affected_rows;
 }
}

?>
