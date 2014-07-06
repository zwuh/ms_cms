<?php
 include ('../../config.php');
 include ('google.php');
 print_r($_COOKIE);
 echo '========================'."\n";
 $session_token = google_authsub_get_session_token();
 print_r($_COOKIE);
 echo '========================'."\n";

 $r_token = google_get_session_token();
 if (strcmp($session_token, $r_token))
 {
   echo 'Error: session token mismatch'."\n";
   echo 'session_token: '.$session_token."\n";
   echo 'r_token: '.$r_token."\n";
 }

 if (google_check_auth())
 { echo 'SUCCESS'."\n"; }
 else
 { echo 'FAILED'."\n";
   echo '<a href="'.google_authsub_url().'">URL</a>'."\n"; }

 google_authsub_revoke();
 if (google_check_auth())
 { echo 'SUCCESS'."\n"; }
 else
 { echo 'FAILED'."\n";  }
?>
