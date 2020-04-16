<?php
require_once 'setup.php';
session_start();
DB::delete('cookie users','username = %s', $_SESSION['username']);
setcookie('logintoken',' ',0, '/');
setcookie('username',' ',0,'/');
session_destroy();
session_write_close();
echo('<script type="text/javascript">window.location="index.php"</script>');
?>