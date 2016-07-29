<?php
ini_set('display_errors', '0');     # don't show any errors...
error_reporting(E_ALL | E_STRICT);  # ...but do log them

session_start();
if($_SESSION["verify"] != "FileManager4TinyMCE") die('forbidden');
include 'config.php';
include 'utils.php';

if (empty($_POST['path'])) {
    die('Forbidden Path');
}

$path=joinPaths($root,$upload_dir,$_POST['path']);
$path=str_replace(LC_DS . '..', '', $path);
$name=$_POST['name'];

if (!file_exists($path)) die('File not found');

header('Pragma: private');
header('Cache-control: private, must-revalidate');
header("Content-Type: application/octet-stream");
header("Content-Length: " .(string)(filesize($path)) );
header('Content-Disposition: attachment; filename="'.($name).'"');
readfile($path);
exit;
?>
