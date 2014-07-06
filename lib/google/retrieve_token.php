<?php
 include ('../../config.php');
 include ('google.php');

 session_start();

 echo 'Entering retrieve_token'."\n";
 echo '======== SESSION ========'."\n";
 print_r($_SESSION);
 echo '======== COOKIE ========'."\n";
 print_r($_COOKIE);
 echo '========= GET =========='."\n";
 print_r($_GET);
 echo "---------\n";

 google_oauth_get_access_token($_GET);

 echo 'after get_access_token'."\n";
 echo '======== SESSION ========'."\n";
 print_r($_SESSION);
 echo '======== COOKIE ========'."\n";
 print_r($_COOKIE);
 echo '========= GET =========='."\n";
 print_r($_GET);
 echo "---------\n";

 google_check_auth();

 google_oauth_revoke_token();
 echo 'after revoke_token'."\n";
 echo '======== SESSION ========'."\n";
 print_r($_SESSION);
 echo '======== COOKIE ========'."\n";
 print_r($_COOKIE);
 echo '========= GET =========='."\n";
 print_r($_GET);
 echo "---------\n";
?>
