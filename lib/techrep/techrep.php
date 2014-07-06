<?php

function techrep_version()
{
}

function techrep_build_table_name($table = '')
{
  global $ms_config;
  return $ms_config['db']['table_prefix'].'techreport';
}

function techrep_get_existing_years($dblink)
{
  $sql = 'SELECT DISTINCT year FROM '.
    techrep_build_table_name();
  if (!($res = $dblink->query($sql)))
  {
    die ('techrep_get_valid_years: Error, '.mysqli_error($dblink));
  }
  $ret = Array ();
  while ($t = $res->fetch_assoc())
  {
    $ret[] = $t['year'];
  }
  return $ret;
}

function techrep_load_year($dblink, $year = 0)
{
  $sql = 'SELECT * FROM '.techrep_build_table_name().' ';
  if ($year != 0)
  {
    $sql .= ' WHERE year='.intval($year).' ';
  }
  else
  {
    $sql .= ' WHERE year=YEAR(NOW()) ';
  }
  $sql .= ' ORDER BY serial DESC';

  $res = $dblink->query($sql);
  $ret = Array();
  if (!$res)
  {
    $ret[] = Array(
      'title' => 'techrep_load_year: Failed, '.mysqli_error($dblink) );
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

function techrep_load_one_by_id($dblink, $target_id)
{
  $sql = 'SELECT * FROM '.techrep_build_table_name().
         ' WHERE id='.intval($target_id).' LIMIT 1';
  $res = $dblink->query($sql);
  if (!$res)
  {
    return Array ('title'=>'techrep_load_one_by_id: Failed,'.
            mysqli_error($dblink));
  }
  return $res->fetch_assoc();
}

function techrep_load_one_by_serial($dblink, $year, $serial)
{
  $sql = 'SELECT * FROM '.techrep_build_table_name().
         ' WHERE year='.intval($year).' AND serial='.intval($serial).
	 ' LIMIT 1';
  $res = $dblink->query($sql);
  if (!$res)
  {
    return Array ('title'=>'techrep_load_one_by_serial: Failed,'.
            mysqli_error($dblink));
  }
  else
  {
    return $res->fetch_assoc();
  }
}

function techrep_update($dblink, $target)
{
  $sql = 'UPDATE '.techrep_build_table_name().
    ' SET year='.intval($target['year']).', '.
    ' serial='.intval($target['serial']).', '.
    ' title=\''.ms_escape_string($dblink, $target['title']).'\', '.
    ' file_abstract=\''.ms_escape_string($dblink, $target['file_abstract']).'\', '.
    ' file_fulltext=\''.ms_escape_string($dblink, $target['file_fulltext']).'\', '.
    ' author=\''.ms_escape_string($dblink, $target['author']).'\' '.
    ' WHERE id='.intval($target['id']).' LIMIT 1';

  if (!$dblink->query($sql))
  {
    echo 'techrep_update: Failed, '.mysqli_error($dblink);
  }
  else
  {
    echo 'techrep_update: succeeded, af='.$dblink->affected_rows;
  }
}

function techrep_write($dblink, $target)
{
  $sql = 'INSERT INTO '.techrep_build_table_name().
    ' SET year='.intval($target['year']).', '.
    ' serial='.intval($target['serial']).', '.
    ' title=\''.ms_escape_string($dblink, $target['title']).'\', '.
    ' file_abstract=\''.ms_escape_string($dblink, $target['file_abstract']).'\', '.
    ' file_fulltext=\''.ms_escape_string($dblink, $target['file_fulltext']).'\', '.
    ' author=\''.ms_escape_string($dblink, $target['author']).'\' ';

  if (!$dblink->query($sql))
  {
    echo 'techrep_write: Failed, '.mysqli_error($dblink);
  }
  else
  {
    echo 'techrep_write: succeeded, id='.$dblink->insert_id;
  }
}

function techrep_delete($dblink, $target_id)
{
  $sql = 'DELETE FROM '.techrep_build_table_name().
         ' WHERE id='.intval($target_id).' LIMIT 1';
  if (!$dblink->query($sql))
  {
    echo 'techrep_delete['.$target_id.']: Failed, '.mysqli_error($dblink);
  }
  else
  {
    echo 'techrep_delete['.$target_id.']: affected_row: '.$dblink->affected_rows;
  }
}

?>
