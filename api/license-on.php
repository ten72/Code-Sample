<?
session_start();
function html_sanitize($string){
	return(htmlentities($string));
}
$_SESSION['license_on']=html_sanitize($_REQUEST['email']);
$_SESSION['stateNew']=html_sanitize($_REQUEST['stateNew']);
$_SESSION['districtNew']=html_sanitize($_REQUEST['districtNew']);
$_SESSION['schoolNew']=html_sanitize($_REQUEST['schoolNew']);
$_SESSION['schoolID']=html_sanitize($_REQUEST['schoolID']);

echo $_SESSION['license_on'].'<br>';
echo $_SESSION['stateNew'].'<br>';
echo $_SESSION['districtNew'].'<br>';
echo $_SESSION['schoolNew'].'<br>';
echo $_SESSION['schoolID'].'<br>';
?>