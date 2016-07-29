<?php
ini_set('display_errors', '0');     # don't show any errors...
error_reporting(E_ALL | E_STRICT);  # ...but do log them

if($_SESSION["verify"] != "FileManager4TinyMCE") die('forbidden');

function deleteDir($dir) {
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) return unlink($dir);
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        if (!deleteDir($dir.DIRECTORY_SEPARATOR.$item)) return false;
    }
    return rmdir($dir);
}

function create_img_gd($imgfile, $imgthumb, $newwidth, $newheight="") {
    require_once('php_image_magician.php');  
    $magicianObj = new imageLib($imgfile);
    // *** Resize to best fit then crop
    $magicianObj -> resizeImage($newwidth, $newheight, 'crop');  

    // *** Save resized image as a PNG
    $magicianObj -> saveImage($imgthumb);
}

function makeSize($size) {
   $units = array('B','KB','MB','GB','TB');
   $u = 0;
   while ( (round($size / 1024) > 0) && ($u < 4) ) {
     $size = $size / 1024;
     $u++;
   }
   return (number_format($size, 1, ',', '') . " " . $units[$u]);
}

function create_folder($path=false,$path_thumbs=false){
	$oldumask = umask(0); 
	if ($path && !file_exists($path))
		mkdir($path, 0775); // or even 01777 so you get the sticky bit set 
	if($path_thumbs && !file_exists($path_thumbs)) 
		mkdir($path_thumbs, 0775); // or even 01777 so you get the sticky bit set 
	umask($oldumask);
  return true;
}

function joinPaths() {
  $args = func_get_args();
  $paths = array();
  foreach ($args as $arg) {
    $paths = array_merge($paths, (array)$arg);
  }

  $paths = array_map(create_function('$p', 'return rtrim($p, DIRECTORY_SEPARATOR);'), $paths);
  $paths = array_filter($paths);
  return join(DIRECTORY_SEPARATOR, $paths);
}

function joinURL() {
  $args = func_get_args();
  $paths = array();
  foreach ($args as $arg) {
    $paths = array_merge($paths, (array)$arg);
  }

  $paths = array_map(create_function('$p', 'return rtrim($p, "/");'), $paths);
  $paths = array_filter($paths);
  return join('/', $paths);
}

function returnJSON($response_array) {
  header('Content-type: application/json');
  die(json_encode($response_array));
}

?>