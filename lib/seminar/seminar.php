<?php

ms_load_module('google');

function seminar_version()
{
}

function seminar_build_table_name($index = '')
{
 global $ms_config;
 if (!strcmp($index, ''))
 {
   return $ms_config['db']['table_prefix'].'seminar';
 }
 return $ms_config['db']['table_prefix'].'seminar_'.$index;
}

function seminar_load_one_series($dblink, $seminar_id)
{
  $table_name = seminar_build_table_name('session');
  $sql = 'SELECT id,session_serial,date,time_begin,time_end,'.
      'topic_cht, topic_enu,'.
      'location_cht,location_enu,file_types,file_names '.
      'FROM '.$table_name.' WHERE master_id='.intval($seminar_id).
      ' ORDER BY session_serial ASC';
  echo 'SQL: '.$sql."\n";
  $ret = Array();
  if (!($res = $dblink->query($sql)))
  {
    $ret[] = Array(
      'location_cht' => 'seminar_load_one_series: failed',
      'location_enu' => mysqli_error($dblink) );
  }
  else
  {
    while ($item = $res->fetch_assoc())
    {
      $ret[] = $item;
    }
  }
  return $ret;
}

function seminar_load_one_session($dblink, $session_id)
{
  $table_name = seminar_build_table_name('session');
  $sql = 'SELECT id,master_id,date,'.
         'DATE_FORMAT(time_begin, \'%H:%i\') AS time_begin,'.
	 'DATE_FORMAT(time_end, \'%H:%i\') AS time_end,'.
	 'topic_cht, topic_enu, file_names, file_types,'.
	 'location_cht, location_enu, session_serial, gcal_edit '.
	 ' FROM '.$table_name.' WHERE id='.
         intval($session_id).' LIMIT 1';
  if (!($res = $dblink->query($sql)))
  {
    return Array(
      'location_cht' => 'seminar_load_one_session: failed',
      'location_enu' => mysqli_error($dblink) );
  }
  else
  {
    return $res->fetch_assoc();
  }
}

function seminar_load_sessions_by_master($dblink, $master_id)
{
  $ret = seminar_load_sessions_by_time($dblink, 0, $master_id, 128);
  return $ret;
}

function seminar_load_sessions_by_time($dblink, $base = 0,
                                       $master_id = 0,
                                       $length = PAGE_LENGTH)
{
  $table_name = seminar_build_table_name('session');
  $master_table = seminar_build_table_name();
  $sql = 'SELECT '.$table_name.'.id AS id,session_serial,date,time_begin,'.
         'time_end,location_cht,location_enu,file_types,file_names,'.
	 'master_id,'.$master_table.'.topic_cht AS master_topic_cht,'.
	 $master_table.'.topic_enu AS master_topic_enu, '.
	 $master_table.'.firstname_enu AS firstname_enu, '.
	 $master_table.'.lastname_enu AS lastname_enu, '.
	 $master_table.'.firstname_cht AS firstname_cht, '.
	 $master_table.'.lastname_cht AS lastname_cht, '.
	 $master_table.'.title_enu AS title_enu, '.
	 $master_table.'.title_cht AS title_cht, '.
	 $master_table.'.organization_enu AS organization_enu, '.
	 $master_table.'.organization_cht AS organization_cht, '.
	 $table_name.'.topic_cht AS topic_cht, '.
	 $table_name.'.topic_enu AS topic_enu FROM '.$table_name.
	 ' JOIN '.$master_table.' ON master_id='.$master_table.'.id ';
  if ($master_id > 0)
  {
    $sql .= ' WHERE master_id='.$master_id.' ';
  }
  else
  {
    $sql .= ' WHERE 1 ';
  }
  $sql .= ' ORDER BY date DESC, time_begin DESC '.
	 ' LIMIT '.intval($base).','.intval($length);
  if (!($res = $dblink->query($sql)))
  {
    return Array(
      Array(
      'location_cht' => 'seminar_load_sessions_by_time: failed',
      'location_enu' => mysqli_error($dblink) ) );
  }
  else
  {
    $ret = Array();
    while ($item = $res->fetch_assoc())
    {
      $ret[] = $item;
    }
    return $ret;
  }
}

function seminar_load_topics($dblink, $base = 0, $length = PAGE_LENGTH)
{
  $table_name = seminar_build_table_name();
  $sql = 'SELECT * FROM '.$table_name.' ORDER BY id DESC '.
         'LIMIT '.$base.','.$length;
  if (!($res = $dblink->query($sql)))
  {
    return Array( 'topic_cht' => 'seminar_load_topics: Error',
                  'topic_enu' => mysqli_error($dblink) );
  }
  else
  {
    $ret = Array();
    while ($item = $res->fetch_assoc())
    {
      $ret[] = $item;
    }
    return $ret;
  }
}

function seminar_load_one_topic($dblink, $target_id)
{
  $table_name = seminar_build_table_name();
  $sql = 'SELECT * FROM '.$table_name.' WHERE id='.intval($target_id).
         ' LIMIT 1';
  if (!($res = $dblink->query($sql)))
  {
    return Array( 'topic_cht' => 'seminar_load_topics: Error',
                  'topic_enu' => mysqli_error($dblink) );
  }
  else
  {
    return $res->fetch_assoc();
  }
}

function seminar_load_similar_topics($dblink, $reference_id)
{
  $table_name = seminar_build_table_name();
  $sql1 = 'SELECT topic_cht FROM '.$table_name.' WHERE id='.
          intval($reference_id).' LIMIT 1';
  if (!($r_res = $dblink->query($sql1)))
  {
    return Array( Array(
      'topic_cht' => 'seminar_load_similar_topics: reference failed',
      'topic_enu' => 'refid:'.$reference_id.','.mysqli_error($dblink) ));
  }
  $ref = $r_res->fetch_assoc();
  $sql2 = 'SELECT * FROM '.$table_name.' WHERE topic_cht LIKE \'%'.
          ms_escape_string($dblink, $ref['topic_cht']).'%\''.
	  'ORDER BY id DESC';
  if (!($res = $dblink->query($sql2)))
  {
    return Array( Array(
      'topic_cht' => 'seminar_load_similar_topics: load failed',
      'topic_enu' => 'refid:'.$reference_id.','.mysqli_error($dblink) ));
  }
  $ret = Array();
  while ($item = $res->fetch_assoc())
  {
    $ret[] = $item;
  }
  return $ret;
}

function seminar_process_files($file_types, $file_names, $padding = FALSE)
{
  $type_a = explode(',', $file_types);
  $name_a = explode(',', $file_names);
  $ct = count($type_a);
  $cn = count($name_a);
  $ret = Array ();
  if (FALSE == $padding &&
      (!strcmp($file_names, '') || $ct == 0 || $cn == 0))
   return $ret;

  for ($i = 0;$i < 4;$i ++)
  {
    if ($i >= min($cn, $ct))
    {
      if (FALSE == $padding)
      {  break;  }
      $ret[] = Array( 'type' => '', 'name' => '' );
    }
    else
    {
      $ret[] = Array( 'type' => $type_a[$i], 'name' => $name_a[$i] );
    }
  }
  return $ret;
}

function seminar_implode_files($types, $files)
{
  $ts = ''; $ct = count($types);
  $fs = ''; $cf = count($files);
  for ($i = 0;$i < $ct;$i ++)
  {
    if ($i < $cf && strcmp($types[$i], 'IGNORE')
        && strcmp(trim($files[$i]), ''))
    {
      if ($i > 0)
      {
        $ts .= ','; $fs .= ',';
      }
      $ts .= $types[$i];
      $fs .= $files[$i];
    }
  }
  return Array( 'type' => $ts, 'file' => $fs );
}

function seminar_walk_session_files($item, $index)
{
  echo '['.$item['type'].': '.$item['name'].']';
}

function seminar_build_type_options($selected = 'NULL')
{
  $seminar_valid_file_types = Array('公告', '簡報', '論文');
  $match = FALSE;

  for ($i = 0;$i < 3;$i ++)
  {
    echo ' <option value="'.$seminar_valid_file_types[$i].'" ';
    if (!strcmp($selected, $seminar_valid_file_types[$i]))
    {
     echo ' selected="selected"';
     $match = TRUE;
    }
    echo '>'.$seminar_valid_file_types[$i].'</option>'."\n";
  }
  echo ' <option value="IGNORE" ';
  if (FALSE == $match)
  {
    echo 'selected="selected"';
  }
  echo '> </option>'."\n";
}

function seminar_write_master($dblink, $target)
{
  $table_name = seminar_build_table_name();
  $sql = 'INSERT INTO '.$table_name.' SET '.
       '`topic_cht`=\''.ms_escape_string($dblink, $target['title_cht']).'\','.
       '`topic_enu`=\''.ms_escape_string($dblink, $target['title_cht']).'\','.
       '`firstname_cht`=\''.ms_escape_string($dblink, $target['firstname_cht']).'\','.
       '`lastname_cht`=\''.ms_escape_string($dblink, $target['lastname_cht']).'\','.
       '`title_cht`=\''.ms_escape_string($dblink, $target['title_cht']).'\','.
       '`firstname_enu`=\''.ms_escape_string($dblink, $target['firstname_enu']).'\','.
       '`lastname_enu`=\''.ms_escape_string($dblink, $target['lastname_enu']).'\','.
       '`title_enu`=\''.ms_escape_string($dblink, $target['title_enu']).'\','.
       '`organization_cht`=\''.ms_escape_string($dblink, $target['organization_cht']).'\','.
       '`organization_enu`=\''.ms_escape_string($dblink, $target['organization_enu']).'\' ';
  if (!$dblink->query($sql))
  {
    echo 'seminar_write_master: Error, '.mysqli_error($dblink);
  }
  else
  {
    echo 'seminar_write_master: Succeeded, af:'.$dblink->affected_rows;
  }
}

function seminar_write_session($dblink, $target)
{
  $table_name = seminar_build_table_name('session');
  $files = seminar_implode_files($target['type'], $target['file']);
  $sql = 'INSERT INTO '.$table_name.' SET '.
     '`topic_cht` = \''.ms_escape_string($dblink, $target['topic_cht']).'\','.
     '`topic_enu` = \''.ms_escape_string($dblink, $target['topic_enu']).'\','.
     '`location_cht` = \''.ms_escape_string($dblink, $target['location_cht']).'\','.
     '`location_enu` = \''.ms_escape_string($dblink, $target['location_enu']).'\','.
     '`date` = \''.ms_escape_string($dblink, $target['date']).'\','.
     '`time_begin` = \''.ms_escape_string($dblink, $target['time_begin']).'\','.
     '`time_end` = \''.ms_escape_string($dblink, $target['time_end']).'\','.
     '`file_types` = \''.ms_escape_string($dblink, $files['type']).'\','.
     '`file_names` = \''.ms_escape_string($dblink, $files['file']).'\','.
     '`master_id` = '.intval($target['master_id']);
  if (!$dblink->query($sql))
  {
    echo 'seminar_write_session: failed, '.mysqli_error($dblink);
  }
  else
  {
    echo 'seminar_write_session: succeeded, af:'.$dblink->affected_rows;
    if (isset($_SESSION['google_access_token']))
    {
      $client = google_get_client();
      // TODO: desc --> a hyper link to local info node
      $edit_url = google_create_event($client,
        $target['topic_enu'], '', $target['location_enu'],
        $target['date'], $target['time_begin'],
        $target['date'], $target['time_end']);
      $sql_ge = 'UPDATE '.$table_name.' SET gcal_edit=\''.
           $edit_url.'\' WHERE id='.$dblink->insert_id.' LIMIT 1';
      if ($dblink->query($sql_ge))
      {
        echo "\n".'seminar_write_session: write Google URL succeeded.';
      }
      else
      {
        echo "\n".'seminar_write_session: write Google URL failed.';
      }
    }
  }
}

function seminar_update_master($dblink, $target)
{
  $table_name = seminar_build_table_name();
  $sql = 'UPDATE '.$table_name.' SET '.
       '`topic_cht`=\''.ms_escape_string($dblink, $target['title_cht']).'\','.
       '`topic_enu`=\''.ms_escape_string($dblink, $target['title_cht']).'\','.
       '`firstname_cht`=\''.ms_escape_string($dblink, $target['firstname_cht']).'\','.
       '`lastname_cht`=\''.ms_escape_string($dblink, $target['lastname_cht']).'\','.
       '`title_cht`=\''.ms_escape_string($dblink, $target['title_cht']).'\','.
       '`firstname_enu`=\''.ms_escape_string($dblink, $target['firstname_enu']).'\','.
       '`lastname_enu`=\''.ms_escape_string($dblink, $target['lastname_enu']).'\','.
       '`title_enu`=\''.ms_escape_string($dblink, $target['title_enu']).'\','.
       '`organization_cht`=\''.ms_escape_string($dblink, $target['organization_cht']).'\','.
       '`organization_enu`=\''.ms_escape_string($dblink, $target['organization_enu']).'\' '.
       ' WHERE id='.intval($target['modify']).' LIMIT 1';
  if (!$dblink->query($sql))
  {
    echo 'seminar_update_master: Error, '.mysqli_error($dblink);
  }
  else
  {
    echo 'seminar_update_master: Succeeded, af:'.$dblink->affected_rows;
  }
}

function seminar_update_session($dblink, $target)
{
  $table_name = seminar_build_table_name('session');
  $file = seminar_implode_files($target['type'], $target['file']);
  $sql = 'UPDATE '.$table_name.' SET '.
     '`topic_cht` = \''.ms_escape_string($dblink, $target['topic_cht']).'\','.
     '`topic_enu` = \''.ms_escape_string($dblink, $target['topic_enu']).'\','.
     '`location_cht` = \''.ms_escape_string($dblink, $target['location_cht']).'\','.
     '`location_enu` = \''.ms_escape_string($dblink, $target['location_enu']).'\','.
     '`date` = \''.ms_escape_string($dblink, $target['date']).'\','.
     '`time_begin` = \''.ms_escape_string($dblink, $target['time_begin']).'\','.
     '`time_end` = \''.ms_escape_string($dblink, $target['time_end']).'\','.
     '`file_types` = \''.ms_escape_string($dblink, $file['type']).'\','.
     '`file_names` = \''.ms_escape_string($dblink, $file['file']).'\''.
     ' WHERE id='.intval($target['modify']).' LIMIT 1';
  if (!$dblink->query($sql))
  {
    echo 'seminar_update_session: failed, '.mysqli_error($dblink);
  }
  else
  {
    echo 'seminar_update_session: succeeded, af:'.$dblink->affected_rows;
    if (isset($_SESSION['google_access_token']) && isset($target['gcal_edit']))
    {
      $client = google_get_client();
      google_delete_event($client, $target['gcal_edit']);
      // TODO: desc --> a hyper link to local info node
      $edit_url = google_create_event($client,
        $target['topic_enu'], '', $target['location_enu'],
        $target['date'], $target['time_begin'],
        $target['date'], $target['time_end']);
      $sql_ge = 'UPDATE '.$table_name.' SET gcal_edit=\''.
           $edit_url.'\' WHERE id='.intval($target['modify']).' LIMIT 1';
      if ($dblink->query($sql_ge))
      {
        echo "\n".'seminar_update_session: write Google URL succeeded.';
      }
      else
      {
        echo "\n".'seminar_update_session: write Google URL failed.';
      }
    }

  }
}

function seminar_delete_master($dblink, $target_id)
{
  $table_name = seminar_build_table_name();

  // Manually cascade delete in order to delete Google Events
  $session_table = seminar_build_table_name('session');
  $sql_session = 'SELECT id FROM '.$session_table.' WHERE master_id='.intval($target_id);
  $res = $dblink->query($sql_session);
  while ($item = $res->fetch_assoc())
  {
    seminar_delete_session($dblink, $item['id']);
  }

  // Though we have foreign key with cascaded delete
  $sql_master = 'DELETE FROM '.$table_name.' WHERE id='.
         intval($target_id).' LIMIT 1';
  if ($dblink->query($sql_master))
  {
    echo 'seminar_delete_master: succeeded, af:'.$dblink->affected_rows;
  }
  else
  {
    echo 'seminar_delete_master: failed, '.mysqli_error($dblink);
  }
}

function seminar_delete_session($dblink, $target_id)
{
  $table_name = seminar_build_table_name('session');
  $entry = seminar_load_one_session($dblink, $target_id);
  $sql = 'DELETE FROM '.$table_name.' WHERE id='.
         intval($target_id).' LIMIT 1';
  if ($dblink->query($sql))
  {
    echo 'seminar_delete_session: succeeded, af:'.$dblink->affected_rows;
  }
  else
  {
    echo 'seminar_delete_session: failed, '.mysqli_error($dblink);
  }

  if (isset($_SESSION['google_access_token']))
  {
    $client = google_get_client();
    google_delete_event($client, $entry['gcal_edit']);
  }
}

function seminar_template_master()
{
  return Array(
    'id' => 0,
    'topic_cht' => '',
    'topic_enu' => '',
    'firstname_cht' => '',
    'lastname_cht' => '',
    'firstname_enu' => '',
    'lastname_enu' => '',
    'title_cht' => '',
    'title_enu' => '',
    'organization_cht' => '',
    'organization_enu' => '',
  );
}

function seminar_template_session()
{
  return Array(
    'id' => 0,
    'topic_cht' => '',
    'topic_enu' => '',
    'date' => '',
    'time_begin' => '',
    'time_end' => '',
    'file_names' => '',
    'file_types' => '',
    'location_cht' => '',
    'location_enu' => '',
    'session_serial' => 0,
    'gcal_edit' => ''
  );
}

?>
