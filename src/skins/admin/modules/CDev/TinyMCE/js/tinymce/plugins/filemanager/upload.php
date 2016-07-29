<?php
ini_set('display_errors', '0');     # don't show any errors...
error_reporting(E_ALL | E_STRICT);  # ...but do log them

session_start();
if($_SESSION["verify"] != "FileManager4TinyMCE") die('forbidden');

include('config.php');
include('utils.php');

// Upload is restricted for the non-admin users
if (!\XLite\Core\Auth::getInstance()->isAdmin()) {
    die('forbidden');
}

if (!empty($_FILES) && $upload_files) {
    $tempFile = $_FILES['file']['tmp_name'];

    $pathinfo = pathinfo(strtolower($_FILES['file']['name']));

    $fileExt = isset($pathinfo['extension']) ? ('.' . $pathinfo['extension']) : '';

    $fileName = $pathinfo['filename'] . $fileExt;

    $targetFile = joinPaths($root,$upload_dir,$_POST['path'],$fileName);
    $targetFileThumb = joinPaths($root,$thumbs_dir,$_POST['path'],$fileName);

    if (file_exists($targetFile) || file_exists($targetFileThumb)) {
        $fileName = $pathinfo['filename'] . '_' . hash('md4', $pathinfo['filename']) . $fileExt;
        $targetFile = joinPaths($root,$upload_dir,$_POST['path'],$fileName);
        $targetFileThumb = joinPaths($root,$thumbs_dir,$_POST['path'],$fileName);
    }

    move_uploaded_file($tempFile,$targetFile);

    $is_img=(in_array(substr(strrchr($fileName,'.'),1),$ext_img) ? true : false);

    if($is_img) {
      create_img_gd($targetFile, $targetFileThumb, $thumbnail_width, $thumbnail_height);

      $imginfo =getimagesize($targetFile);
      $srcWidth = $imginfo[0];
      $srcHeight = $imginfo[1];

      if($image_resizing){
        if($image_width==0){
          if($image_height==0){
            $image_width=$srcWidth;
            $image_height =$srcHeight;
          } else {
            $image_width=$image_height*$srcWidth/$srcHeight;
          }
        } elseif ($image_height==0) {
          $image_height =$image_width*$srcHeight/$srcWidth;
        }
        $srcWidth=$image_width;
        $srcHeight=$image_height;
        create_img_gd($targetFile, $targetFile, $image_width, $image_height);
      }

      //max resizing limit control
      $resize=false;
      if ($image_max_width!=0 && $srcWidth >$image_max_width) {
        $resize=true;
        $srcHeight=$image_max_width*$srcHeight/$srcWidth;
        $srcWidth=$image_max_width;
      }

      if ($image_max_height!=0 && $srcHeight >$image_max_height) {
        $resize=true;
        $srcWidth =$image_max_height*$srcWidth/$srcHeight;
        $srcHeight =$image_max_height;
      }
      if ($resize) {
        create_img_gd($targetFile, $targetFile, $srcWidth, $srcHeight);
      }
    }
}
if(isset($_POST['submit'])){
    $query = http_build_query(array(
        'type'      => $_POST['type'],
        'lang'      => $_POST['lang'],
        'popup'     => $_POST['popup'],
        'field_id'  => $_POST['field_id'],
        'editor'    => $_POST['editor'],
        'fldr'      => $_POST['fldr'],
    ));
    header("location: dialog.php?" . $query);
}

?>
