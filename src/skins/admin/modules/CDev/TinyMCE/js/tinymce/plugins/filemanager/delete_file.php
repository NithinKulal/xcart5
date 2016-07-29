<?php
ini_set('display_errors', '0');     # don't show any errors...
error_reporting(E_ALL | E_STRICT);  # ...but do log them

session_start();
include 'config.php';
include('utils.php');
$response_array = array();
if($_SESSION["verify"] != "FileManager4TinyMCE") {
  $response_array['status'] = 'failure';
  $response_array['reason'] = 'Forbidden';
  returnJSON($response_array);
}

// Check to make sure we are not traversing the filesystem
if(strpos($_POST['path'],'..') !== false) {
  $response_array['status'] = 'failure';
  $response_array['reason'] = 'Forbidden Path';
  returnJSON($response_array);
}

$path=joinPaths($root,$upload_dir,$_POST['path']);
$path_thumbs=joinPaths($root,$thumbs_dir,$_POST['path']);

if(unlink($path) && unlink($path_thumbs)) {
  $response_array['status'] = 'Success';
  $response_array['reason'] = 'Deleted '.$_POST['path'];
  returnJSON($response_array);
} else {
  $response_array['status'] = 'failure';
  $response_array['reason'] = 'Error deleting '.$_POST['path'];
  returnJSON($response_array);
}

?>
