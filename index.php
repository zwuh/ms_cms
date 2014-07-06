<?php

require_once('config.php');
global $ms_config;


$dblink = ms_get_db_link();
ms_set_lang();
$n_lang = ms_get_lang();

if (isset($_GET['node']))
{
 $n_node = intval($_GET['node']);
}
else
{ /* Default Main Content is node #1 */
  $n_node = NODE_HOMEPAGE;
}

/* Main Content */
$node1 = ms_load_node($dblink, $n_node, $n_lang);
$node_id = $n_node;
$page_lang = $n_lang;
$page_title = $ms_config['sitename'].' - '.$node1['title'];
$top_title = $node1['title'];
$content = $node1['content'];

/* Sidebar */
if ($node1['sidebar'] == 0)
{ /* Default Sidebar is node #2 */
  $n_sidebar = NODE_DEFAULT_SIDEBAR;
}
else
{
  $n_sidebar = $node1['sidebar'];
}
$node_s = ms_load_node($dblink, $n_sidebar, $n_lang);
$nav = $node_s['content'];


/* Footer */
$node_f = ms_load_node($dblink, NODE_FOOTER, $n_lang);
$footer = $node_f['content'];


ms_close_db_link($dblink);

include('theme/layout.tpl.php');
?>
