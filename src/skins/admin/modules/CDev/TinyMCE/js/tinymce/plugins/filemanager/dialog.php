<?php
ini_set('display_errors', '0');     # don't show any errors...
error_reporting(E_ALL | E_STRICT);  # ...but do log them

session_start();
$_SESSION["verify"] = "FileManager4TinyMCE";

if(isset($_POST['submit'])) {
  include('upload.php');
} else {
  include 'config.php';
  include('utils.php');

  //Prepare $_GET variables
  if(!isset($_GET['type'])) $_GET['type']=0;
  if(!isset($_GET['field_id'])) $_GET['field_id']='';
 (isset($_GET['popup']) ? $popup= $_GET['popup'] :$popup=0);
 (isset($_GET['fldr']) && !empty($_GET['fldr'])) ? $subdir = trim($_GET['fldr'],'/') . '/' : $subdir = '';
        
  //'current' variable definitions 
  //(ie Not from the config file, but generated from its' variables)
  $cur_upload_url = joinURL($base_url,$upload_dir,$subdir);
  $cur_upload_path = joinPaths($root,$upload_dir,$subdir);
  $cur_thumbs_url = joinURL($base_url,$thumbs_dir,$subdir);
  $cur_thumbs_path = joinPaths($root,$thumbs_dir,$subdir);
  
  //create the upload and thumbs directories if they do not exist
  create_folder($cur_upload_path,$cur_thumbs_path);
  
  if (empty($base_url)) {
    $cur_upload_url = "/".$cur_upload_url;
    $cur_thumbs_url = "/".$cur_thumbs_url;
  }

  $cur_upload_url = str_replace(DIRECTORY_SEPARATOR, '/', $cur_upload_url);
  $cur_thumbs_url = str_replace(DIRECTORY_SEPARATOR, '/', $cur_thumbs_url);

  //Include Language definitions
  if (isset($_GET['lang']) && $_GET['lang'] != 'undefined' 
      && is_readable(joinPaths('lang',$_GET['lang'].'.php'))
      ) {
    require_once joinPaths('lang',$_GET['lang'].'.php');
  } else {
    require_once joinPaths('lang','en_EN.php');
  }
  
  $link="dialog.php?type=".$_GET['type']."&editor=";
	$link.=$_GET['editor'] ? $_GET['editor'] : 'mce_0';
	$link.="&popup=".$popup."&lang=";
	$link.=$_GET['lang'] ? $_GET['lang'] : 'en_EN';
	$link.="&field_id=";
	$link.=$_GET['field_id'] ? $_GET['field_id'] : '';
	$link.="&fldr="; 


  ?>
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <meta name="robots" content="noindex,nofollow">
      <title>FileManager</title>
      <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
      <link href="css/bootstrap-lightbox.min.css" rel="stylesheet" type="text/css" />
      <link href="css/style.css" rel="stylesheet" type="text/css" />
      <link href="css/dropzone.css" type="text/css" rel="stylesheet" />
      <!--[if lt IE 8]><style>
      .img-container span {
          display: inline-block;
          height: 100%;
      }
      </style><![endif]-->
      <script type="text/javascript" src="js/jquery.1.9.1.min.js"></script>
      <script type="text/javascript" src="js/bootstrap.min.js"></script>
      <script type="text/javascript" src="js/bootstrap-lightbox.min.js"></script>
      <script type="text/javascript" src="js/dropzone.min.js"></script>
      <script type="text/javascript" src="js/jquery.touchSwipe.min.js"></script>
      <script src="js/modernizr.custom.js"></script>
      <script>
        var ext_img=new Array('<?=implode("','", $ext_img)?>');
        var allowed_ext=new Array('<?=implode("','", $ext)?>');

        //dropzone config
        Dropzone.options.myAwesomeDropzone = {
          dictInvalidFileType: "<?= lang_Error_extension; ?>",
          dictFileTooBig: "<?= lang_Error_Upload;  ?>",
          dictResponseError: "SERVER ERROR",
          paramName: "file", // The name that will be used to transfer the file
          maxFilesize: <?=$MaxSizeUpload; ?>, // MB
          url: "upload.php",
          accept: function(file, done) {
          var extension=file.name.split('.').pop().toLowerCase();
            if ($.inArray(extension, allowed_ext) > -1) {
              done();
            }
            else { done("<?= lang_Error_extension; ?>"); }
          }
        };
      </script>
      <script type="text/javascript" src="js/include.js"></script>
    </head>
    <body>
      <input type="hidden" id="popup" value="<?=$popup; ?>" />
      <input type="hidden" id="track" value="<?=$_GET['editor']; ?>" />
      <input type="hidden" id="insert_folder_name" value="<?= lang_Insert_Folder_Name;  ?>" />
      <input type="hidden" id="new_folder" value="<?= lang_New_Folder;  ?>" />
      <input type="hidden" id="cur_dir" value="<?=$cur_upload_url;?>"/>
      <input type="hidden" id="sub_dir" value="<?=$subdir;?>"/>
      
      <?php 
      if($upload_files) { ?>
        <!-- uploader div start -->
        <div class="uploader">    
          <form action="dialog.php" method="post" enctype="multipart/form-data" id="myAwesomeDropzone" class="dropzone">
            <input type="hidden" name="path" value="<?=$subdir?>"/>
            <div class="fallback">
              <?= lang_Upload_file ?>:<br/>
              <input name="file" type="file" />
              <input type="hidden" name="fldr" value="<?=$_GET['fldr']?>"/>
              <input type="hidden" name="type" value="<?=$_GET['type']?>"/>
              <input type="hidden" name="field_id" value="<?=$_GET['field_id']?>"/>
              <input type="hidden" name="popup" value="<?=$popup; ?>"/>
              <input type="hidden" name="editor" value="<?=$_GET['editor']?>"/>
              <input type="hidden" name="lang" value="<?=$_GET['lang']?>"/>
              <input type="submit" name="submit" value="OK" />
            </div>
          </form>
          <center><button class="btn btn-large btn-inverse close-uploader"><i class="icon-backward icon-white"></i> <?= lang_Return_Files_List ?></button></center>
          <div class="space10"></div><div class="space10"></div>
        </div>
        <!-- uploader div end -->
      <?php 
      } ?>		
      <div class="container-fluid">
        <!-- header div start -->
        <div class="filters">
          <div class="row-fluid">
            <div class="span4">
              <?php 
              if($upload_files) { 
                    echo '<button class="btn btn-inverse upload-btn" style="margin-left:5px;"><i class="icon-upload icon-white"></i>'. lang_Upload_file .'</button>';
              }  
              if($create_folder) { 
                    echo '<button class="btn new-folder" style="margin-left:5px;"><i class="icon-folder-open"></i>'. lang_New_Folder .'</button>';
              }
              ?>
            </div>
            <div class="span8 pull-right">
              <?php 
              if($_GET['type']==2 || $_GET['type']==0) { ?>
                <div class="pull-right"><?= lang_Filter;  ?> : 
                    <input id="select-type-all" name="radio-sort" type="radio" data-item="ff-item-type-all" class="hide" />
                    <label id="ff-item-type-all" for="select-type-all" class="btn btn-inverse ff-label-type-all"><?= lang_All;  ?></label>
                    <input id="select-type-1" name="radio-sort" type="radio" data-item="ff-item-type-1" checked="checked"  class="hide"  />
                    <label id="ff-item-type-1" for="select-type-1" class="btn ff-label-type-1"><?= lang_Files;  ?></label>
                    <input id="select-type-2" name="radio-sort" type="radio" data-item="ff-item-type-2" class="hide"  />
                    <label id="ff-item-type-2" for="select-type-2" class="btn ff-label-type-2"><?= lang_Images;  ?></label>
                    <input id="select-type-3" name="radio-sort" type="radio" data-item="ff-item-type-3" class="hide"  />
                    <label id="ff-item-type-3" for="select-type-3" class="btn ff-label-type-3"><?= lang_Archives;  ?></label>
                    <input id="select-type-4" name="radio-sort" type="radio" data-item="ff-item-type-4" class="hide"  />
                    <label id="ff-item-type-4" for="select-type-4" class="btn ff-label-type-4"><?= lang_Videos;  ?></label>
                    <input id="select-type-5" name="radio-sort" type="radio" data-item="ff-item-type-5" class="hide"  />
                    <label id="ff-item-type-5" for="select-type-5" class="btn ff-label-type-5"><?= lang_Music;  ?></label>
                </div>
              <?php 
              } ?>
            </div>
          </div>
        </div>
        <!-- header div end -->
        <!-- breadcrumb div start -->
        <div class="row-fluid">
          <ul class="breadcrumb">
            <li class="pull-left"><a href="<?=$link?>"><i class="icon-home"></i></a></li><li><span class="divider">/</span></li>
            <?php
              $bc=explode('/',$subdir);
              $tmp_path='';
              if(!empty($bc)) {
                foreach($bc as $k=>$b) { 
                  $tmp_path.=$b."/";
                  if($k==count($bc)-2) {
                    echo '<li class="active">'.$b.'</li>';
                  } elseif($b!="") {
                    echo '<li><a href="'.$link.$tmp_path.'">'.$b.'</a></li><li><span class="divider">/</span></li>';
                  }
                } 
              }
            ?>
            <li class="pull-right"><a id="refresh" href="<?=$link.$subdir.'&'.uniqid()?>"><i class="icon-refresh"></i></a></li>
          </ul>
        </div>
        <!-- breadcrumb div end -->


        <div class="row-fluid ff-container">
          <div class="span12">
          <?php 
          if(@opendir($cur_upload_path)===FALSE){ ?>
            <br/>
            <div class="alert alert-error">There is an error! The root folder does not exist. </div> 
            <?php 
          } else { ?>
            <h4 id="help">Swipe the name of file/folder to show options</h4>
            <!--ul class="thumbnails ff-items"-->
            <ul class="grid cs-style-2">
            <?php
            $class_ext = '';
            $src = '';
            $dir = opendir($cur_upload_path);
            $i = 0;
            $k=0;
            $start=false;
            $end=false;
            if ($_GET['type']==1) {
              $apply = 'apply_img';
            } elseif($_GET['type']==2) {
              $apply = 'apply_link';
            } elseif($_GET['type']==0 && $_GET['field_id']=='') {
              $apply = 'apply_none';
            } elseif($_GET['type']==3 || $_GET['type']==4 || $_GET['type']==5) {
              $apply = 'apply_video';
            } else {
              $apply = 'apply';
            }
            $files = scandir($cur_upload_path);
            //List all the folders first
            foreach ($files as $folder) {
              $folder_path = joinPaths($cur_upload_path,$folder);
              $folder_relative_path = joinPaths($subdir,$folder);
              $folder_thumb_path = joinPaths($cur_thumbs_path,$folder);
              if (is_dir($folder_path)
                  && $folder[0] != '.'              
                  && ($folder != '.' 
                  && !($folder == '..' 
                  && $subdir=='')) 
                  ) {
                //add in thumbs folder if not exist 
                if (!file_exists($folder_path)) {
                  create_folder(false,$folder_path);
                }
                $class_ext = 3;			
                if($folder=='..' 
                   && trim($subdir) != '' 
                   ){
                    $src = explode("/",$subdir);
                    unset($src[count($src)-2]); //Remove the last entry?
                    $src=implode("/",$src);
                } elseif ($folder!='..') {
                  $src = $subdir . $folder."/";
                }
                ?>
                <li>
                  <figure>
                      <a title="<?= lang_Open ?>" href="<?=$link.$src.'&'.uniqid() ?>">
                        <?php 
                        if($folder=="..") { ?>
                          <div class="img-precontainer">
                            <div class="img-container directory"><span></span>
                              <img class="directory-img"  src="ico/folder<?php if($folder=='..') echo "_return"?>.png" alt="folder" />
                            </div>
                          </div>
                        </a>
                        <?php 
                        } else { ?>
                            <div class="img-precontainer">
                              <div class="img-container directory"><span></span>
                                <img class="directory-img"  src="ico/folder<?php if($folder=='..') echo "_return"?>.png" alt="folder" />
                              </div>
                            </div>
                          </a>
                          <div class="box">
                            <h4><?=$folder ?></h4>
                          </div>
                          <figcaption>
                            <a href="javascript:void('')" class="erase-button" <?php if($delete_folder){ ?>onclick="if(confirm('<?= lang_Confirm_Folder_del;  ?>')){ delete_folder('<?=$folder_relative_path; ?>'); $(this).parent().parent().parent().hide(200); return false;}"<?php } ?> title="<?= lang_Erase ?>">
                              <i class="icon-trash <?php if(!$delete_folder) echo 'icon-white'; ?>"></i>
                            </a>
                          </figcaption>
                          <?php 
                        } ?>
                  </figure>
                </li>
                <?php
                $k++;
              } // if (isDir)
            } // foreach (Folders)
            //List Files after all folders have been listed
            foreach ($files as $nu=>$file) {
              //define convenience variables for this file
              $file_path = joinPaths($cur_upload_path,$file);
              $file_relative_path = joinPaths($subdir,$file);
              $file_thumb_path = joinPaths($cur_thumbs_path,$file);
              $file_url = joinURL($cur_upload_url,$file);
              $file_thumb_url = joinURL($cur_thumbs_url,$file);
              
              if ($file != '.' 
                  && $file != '..' 
                  && $file[0] != '.'
                  && !is_dir($file_path)
                  ) {
                $is_img=false;
                $is_video=false;
                $show_original=false;
                $file_ext = substr(strrchr($file,'.'),1);
                if(in_array($file_ext, $ext)) {
                  if(in_array($file_ext, $ext_img)) {
                    $src = $file_url;
                    $src_thumb = $file_thumb_url;
                    //add to thumbs folder if not already 
                    if(!file_exists($file_thumb_path)) {
                      create_img_gd($file_path, $file_thumb_path, $thumbnail_width, $thumbnail_height);
                    }
                    $is_img=true;
                    //check if is smaller than the thumb
                    $info=getimagesize($file_path);
                    if($info[0]<$thumbnail_width && $info[2]<$thumbnail_height) {
                        $src_thumb=$file_url;
                        $show_original=true;
                    }
                  } elseif(file_exists('ico/'.strtoupper($file_ext).".png")) {
                    $src = $src_thumb ='ico/'.strtoupper($file_ext).".png";
                  } else {
                    $src = $src_thumb = "ico/Default.png";
                  }
                  if (in_array($file_ext, $ext_video)) {
                    $class_ext = 4;
                    $is_video=true;
                  } elseif (in_array($file_ext, $ext_img)) {
                    $class_ext = 2;
                  } elseif (in_array($file_ext, $ext_music)) {
                    $class_ext = 5;
                  } elseif (in_array($file_ext, $ext_misc)) {
                    $class_ext = 3;
                  } else {
                    $class_ext = 1;
                  }
                  if((!($_GET['type']==1 
                     && !$is_img) 
                     && !($_GET['type']>=3 
                     && !$is_video))
                     ) { ?>
                    <li class="ff-item-type-<?=$class_ext; ?>">
                      <figure>
                        <a href="javascript:void('');" title="<?= lang_Select ?>" onclick="<?=$apply."('".rawurlencode($file)."',".$_GET['type'].",'".$_GET['field_id']."');"; ?>">
                          <div class="img-precontainer">
                            <div class="img-container"><span></span>
                              <?='<img data-src="holder.js/'.$thumbnail_width.'x'.$thumbnail_height.'" alt="image"'. ($show_original ? 'class="original"' : '') .' src="'.$src_thumb.'">'?>
                            </div>
                          </div>
                        </a>	
                        <div class="box">				
                          <h4><?=substr($file, 0, '-' . (strlen($file_ext) + 1)); ?></h4>
                        </div>
                        <figcaption>
                          <form action="force_download.php" method="post" class="download-form" id="form<?=$nu; ?>">
                            <input type="hidden" name="path" value="<?=$file_relative_path;?>"/>
                            <input type="hidden" name="name" value="<?=$file;?>"/>
                            <a title="<?= lang_Download ?>" class="" href="javascript:void('');" onclick="$('#form<?=$nu; ?>').submit();"><i class="icon-download"></i></a>
                            <?php 
                            if($is_img){
                              echo '<a class="preview" title="'. lang_Preview .'" data-url="'. $src .'" data-toggle="lightbox" href="#previewLightbox"><i class=" icon-eye-open"></i></a>';
                            } else { 
                              echo '<a class="preview disabled"><i class="icon-eye-open icon-white"></i></a>';
                            } ?>
                            <a href="javascript:void('');" class="erase-button" <?php if($delete_file){ ?>onclick=" if(confirm('<?= lang_Confirm_del;  ?>')){ delete_file('<?=$file_relative_path; ?>'); $(this).parent().parent().parent().parent().hide(200); return false;}"<?php } ?> title="<?= lang_Erase ?>"><i class="icon-trash <?php if(!$delete_file) echo 'icon-white'; ?>"></i></a>
                          </form>
                        </figcaption>
                      </figure>
            
                    </li>
                    <?php
                    $i++;
                  }
                } //if (allowed extention)
              } // if (!isDir)
            } // foreach (Files)
            ?>
            </div>
            <?php closedir($dir);?>
          </ul>
          <?php 
          } ?>
        </div>
      </div>
    </div>
      
      <!-- lightbox div start -->    
      <div id="previewLightbox" class="lightbox hide fade"  tabindex="-1" role="dialog" aria-hidden="true">
        <div class='lightbox-content'>
          <img id="full-img" src="">
        </div>    
      </div>
      <!-- lightbox div end -->

      <!-- loading div start -->  
      <div id="loading_container" style="display:none;">
        <div id="loading" style="background-color:#000; position:fixed; width:100%; height:100%; top:0px; left:0px;z-index:100000"></div>
        <img id="loading_animation" src="img/storing_animation.gif" alt="loading" style="z-index:10001; margin-left:-32px; margin-top:-32px; position:fixed; left:50%; top:50%"/>
      </div>
      <!-- loading div end -->
      
  </body>
</html>
<?php 
} ?>
