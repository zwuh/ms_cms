<?php

function people_version()
{
}

function people_build_table_name($index = '')
{
  global $ms_config;
//  if (!strcmp($index, ''))
  {
    return $ms_config['db']['table_prefix'].'people';
  }
//  else
//  {
//  }
}

function people_get_titles($dblink, $lang = LANG_CHT)
{
  $table_name = people_build_table_name();
  $sql = 'SELECT DISTINCT title_cht, title_enu FROM '.
         $table_name.' ORDER BY title_cht DESC';
  if (!($res = $dblink->query($sql)))
  {
    die ('Error: people_get_category: '.mysqli_error($dblink));
  }
  $ret = array();
  while ($item = $res->fetch_assoc())
  {
   $ret[] = $item;
  }
  return $ret;
}


function people_load_segment($dblink, $type_str = '中心成員',
           $base = 0, $lang = LANG_CHT, $length = PAGE_LENGTH)
{
  $table_name = people_build_table_name();
  // TODO: make use of 'lang' parameter
  $sql = 'SELECT * FROM '.$table_name.' ';
  if (!people_verify_type($type_str))
  {
    echo 'WARNING: people_load_segment: ignoring invalid type \''.
         $type_str.'\'<br />';
    $sql .= 'WHERE 1 ';
  }
  else
  {
    $sql .= 'WHERE type_str=\''.ms_escape_string($dblink,$type_str).'\' ';
  }
  $sql .= 'ORDER BY id DESC LIMIT '.intval($base).','.intval($length);

  if (!($res = $dblink->query($sql)))
  {
    return array(
            array(
              'firstname_cht' => 'people_load_segment: Load Failed',
              'firstname_enu' => mysqli_error($dblink)
           ));
  }

  $ret = array();
  while ($item = $res->fetch_assoc())
  {
    $ret[] = $item;
  }
  return $ret;
}

function people_load_one($dblink, $target_id)
{
  $table_name = people_build_table_name();
  $sql = 'SELECT * FROM '.$table_name.
         ' WHERE id='.intval($target_id);
  if (!($res = $dblink->query($sql)))
  {
    $ret = array(
      'firstname_cht' => 'people_load_one: Load Failed',
      'firstname_enu' => mysqli_error($dblink)
    );
  }
  else
  {
    $ret = $res->fetch_assoc();
  }
  return $ret;
}

function people_get_type($lang = LANG_CHT)
{
  // 這些要配合資料庫裡的資料, 不能隨便改
  $people_types = array (
   LANG_CHT =>  array(
    '行政人員', '執行委員', '中心成員', '執行委員,組主任',
    '訪問學者', '博士後研究員', '研究助理' ),
   LANG_ENU => array(
    'Staff', 'Panel Committee', 'Center Members', 'Office Director',
    'Center Visitors', 'Post Doctoral Fellows', 'Research Assistants' )
  );
  return $people_types[$lang];
}

function people_verify_type($type, $lang = LANG_CHT)
{
  $people_types = people_get_type($lang);
  foreach ($people_types as $t)
  {
    if (!strcmp($type, $t))
    {  return TRUE;  }
  }
  return FALSE;
}

function people_edit_tail($dblink, $target_id, $msg)
{
  echo 'people_'.$msg.' [target='.$target_id.']. '.mysqli_error($dblink);
}

function people_write($dblink, $target)
{
  $table_name = people_build_table_name();
  if (!people_verify_type($target['type_str']))
  {
    die ('Error: people_write: invalid type \''.$target['type_str'].'\'');
  }

  $sql = 'INSERT INTO '.$table_name.' SET firstname_cht=\''.
      ms_escape_string($dblink, $target['firstname_cht']).'\','.
      ' lastname_cht=\''.ms_escape_string($dblink, $target['lastname_cht']).
      '\', title_cht=\''.ms_escape_string($dblink, $target['title_cht']).
      '\', firstname_enu=\''.ms_escape_string($dblink, $target['firstname_enu']).
      '\', lastname_enu=\''.ms_escape_string($dblink, $target['lastname_enu']).
      '\', title_enu=\''.ms_escape_string($dblink, $target['title_enu']).
      '\', address_cht=\''.ms_escape_string($dblink, $target['address_cht']).
      '\', address_enu=\''.ms_escape_string($dblink, $target['address_enu']).
      '\', telephone=\''.ms_escape_string($dblink, $target['telephone']).
      '\', fax=\''.ms_escape_string($dblink, $target['fax']).
      '\', email=\''.ms_escape_string($dblink, $target['email']).
      '\', website=\''.ms_escape_string($dblink, $target['website']).
      '\', photo_file=\''.ms_escape_string($dblink, $target['photo_file']).
      '\', type_str=\''.ms_escape_string($dblink, $target['type_str']).
      '\', organization_cht=\''.ms_escape_string($dblink, $target['organization_cht']).
      '\', organization_enu=\''.ms_escape_string($dblink, $target['organization_enu']).
      '\', field_cht=\''.ms_escape_string($dblink, $target['field_cht']).
      '\', field_enu=\''.ms_escape_string($dblink, $target['field_enu']).
      '\', date_begin=\''.ms_escape_string($dblink, $target['date_begin']).
      '\', date_end=\''.ms_escape_string($dblink, $target['date_end']).'\'';

  if (!$dblink->query($sql))
  {
    people_edit_tail($dblink, $target['id'], 'write: failed');
  }
  else
  {
    people_edit_tail($dblink, $dblink->insert_id, 'write: succeeded');
  }
}

function people_update($dblink, $target)
{
  $table_name = people_build_table_name();
  if (!people_verify_type($target['type_str']))
  {
    die ('Error: people_write: invalid type \''.$target['type_str'].'\'');
  }

  $sql = 'UPDATE '.$table_name.' SET firstname_cht=\''.
      ms_escape_string($dblink, $target['firstname_cht']).'\','.
      ' lastname_cht=\''.ms_escape_string($dblink, $target['lastname_cht']).
      '\', title_cht=\''.ms_escape_string($dblink, $target['title_cht']).
      '\', firstname_enu=\''.ms_escape_string($dblink, $target['firstname_enu']).
      '\', lastname_enu=\''.ms_escape_string($dblink, $target['lastname_enu']).
      '\', title_enu=\''.ms_escape_string($dblink, $target['title_enu']).
      '\', address_cht=\''.ms_escape_string($dblink, $target['address_cht']).
      '\', address_enu=\''.ms_escape_string($dblink, $target['address_enu']).
      '\', telephone=\''.ms_escape_string($dblink, $target['telephone']).
      '\', fax=\''.ms_escape_string($dblink, $target['fax']).
      '\', email=\''.ms_escape_string($dblink, $target['email']).
      '\', website=\''.ms_escape_string($dblink, $target['website']).
      '\', photo_file=\''.ms_escape_string($dblink, $target['photo_file']).
      '\', type_str=\''.ms_escape_string($dblink, $target['type_str']).
      '\', organization_cht=\''.ms_escape_string($dblink, $target['organization_cht']).
      '\', organization_enu=\''.ms_escape_string($dblink, $target['organization_enu']).
      '\', field_cht=\''.ms_escape_string($dblink, $target['field_cht']).
      '\', field_enu=\''.ms_escape_string($dblink, $target['field_enu']).
      '\', date_begin=\''.ms_escape_string($dblink, $target['date_begin']).
      '\', date_end=\''.ms_escape_string($dblink, $target['date_end']).'\''.
      ' WHERE id='.intval($target['id']).' LIMIT 1';

  if (!$dblink->query($sql))
  {
    people_edit_tail($dblink, $target['id'], 'update: failed');
  }
  else
  {
    people_edit_tail($dblink, $target['id'], 'update: succeeded');
  }
}

function people_delete($dblink, $target_id)
{
  $table_name = people_build_table_name();
  $sql = 'DELETE FROM '.$table_name.
         ' WHERE id='.intval($target_id).' LIMIT 1';
  if (!$dblink->query($sql))
  {
    people_edit_tail($dblink, $target_id, 'delete: failed');
  }
  else
  {
    people_edit_tail($dblink, $target_id, 'delete: succeeded');
  }
}

?>
