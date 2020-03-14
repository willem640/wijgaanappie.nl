<?php
session_start();
require_once 'header.php';
require_once 'setup.php';
$error = '';
$email=($_GET['email']??'');
$token=($_GET['token']??'');
if(!empty($email)&&!empty($token)){
    $actQuery = DB::query('SELECT * FROM email_activate WHERE email=%s0 AND token=%s1',$email,$token);   if(isset($actQuery[0]['token'])){ 
    DB::delete('email_activate','email = %s0 AND token = %s1',$email,$token);
    $error = '<h3>Je email is geactiveerd!<br><a href="login.php">Inloggen</a></h3>';
    } else {
    $error = '<h3>Link is al gebruikt of klopt niet</h3>';
    }
} else {
    header('Location: index.php');
}
?>
<!DOCTYPE HTML>
<html>
<?php echo $header;?>
<?php
echo $error;
?>
</body>
</html>
