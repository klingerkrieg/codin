<?php

if(!function_exists('template_url')){
  function template_url() {
    return base_url()."assets/template/";
  }

}


if(!function_exists('_v')){
  function _v($arr, $val) {

    if (isset($arr[$val])){
      return $arr[$val];
    } else {
      return false;
    }
    
  }
}


if(!function_exists('only')){
  function only($arr, $only) {
    $ret = array();
    foreach($only as $item){
      $ret[$item] = _v($arr,$item);
    }
    return $ret;
  }
}

if(!function_exists('date_br_to_mysql')){
  function date_br_to_mysql($date) {
    $dt = explode("/",$date);
    return "{$dt[2]}-{$dt[1]}-{$dt[0]}";

  }
}

if(!function_exists('date_mysql_to_br')){
  function date_mysql_to_br($date) {
    $dt = explode("-",$date);
    return "{$dt[2]}/{$dt[1]}/{$dt[0]}";

  }
}

if(!function_exists('getEnds')){
  function getEnds($str,$token) {
    return substr($str,strrpos($str,$token)+1);
  }
}

if(!function_exists('deleteFolder')){
  function deleteFolder($path){
    $s = opendir($path);
    while ($f = readdir($s)){

      if ($f == "." || $f == ".."){
        continue;
      }
      
      if (is_file($path."/".$f)){
        unlink($path."/".$f);
      } else {
        //deleta subpastas
        deleteFolder($path."/".$f);
        
        rmdir($path."/".$f);
        sleep(10);
      }
    }

    rmdir($path);
    
  }
}

    

if(!function_exists('miniImage')){
  function miniImage($path, $w, $h, $crop = false){
    
    list($width, $height) = getimagesize($path);
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newWidth = $w;
        $newHeight = $h;
    } else {
        if ($w/$h > $r) {
            $newWidth = $h*$r;
            $newHeight = $h;
        } else {
            $newHeight = $w/$r;
            $newWidth = $w;
        }
    }

    $type = strtolower(getEnds($path,"."));
    if ($type == "png"){
      $src = imagecreatefrompng($path);
    } else {
      $src = imagecreatefromjpeg($path);
    }

    $dst = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    $quality = 0;
    if ($type == "png"){
      imagepng($dst, $path, $quality);
    } else {
      imagejpeg($dst, $path, $quality);
    }
  }
}

if(!function_exists('saveUploadFile')){
  function saveUploadFile($folder,$inputName,$override=false){
    
    #cria o diretório
    if (!file_exists( "$folder/")){
      $allPath =  "$folder/";
      $allPath = str_replace("\\","/",$allPath);
      $parts = explode("/",$allPath);
      $complemento = "";
      foreach($parts as $partPath){
        $complemento .= $partPath . "/";
        if (!file_exists($complemento)){
          mkdir($complemento);
        }
      }
    }
    
    $uploadfile =  "$folder/" . basename($_FILES[$inputName]['name']);
    
    if (!$override){
      $fname = basename($_FILES[$inputName]['name']);
      $try = 1;
      #enquanto existir um arquivo com aquele nome
      while (file_exists($uploadfile)){
        $name_part = substr($fname,0,strrpos($fname,"."));
        $ext       = substr($fname,strrpos($fname,"."));
        $uploadfile = "$folder/" . $name_part . "($try)" . $ext ;
        $try++;
      }
    }
    
    if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $uploadfile)) {
      return $uploadfile;
    } else {
      return false;
    }
  }
}


if(!function_exists('unzip')){
  function unzip($file){
    $files = array();

    if ($file == ""){
      return $files;
    }

    $path = pathinfo(realpath($file), PATHINFO_DIRNAME);
    $path = str_replace("\\","/",$path);

    #adiciona o proprio arquivo
    $file = str_replace("\\","/",realpath($file));
    array_push($files, $file);

    if ( strtolower(getEnds($file,".")) == "zip") {
      $zip = new ZipArchive;
      $res = $zip->open($file);
      if ($res === TRUE) {
        // extract it to the path we determined above
        $zip->extractTo($path);
        //lista os arquivos presentes no zip
        for($i = 0; $i < $zip->numFiles; $i++) {
          array_push($files, $path."/".$zip->getNameIndex($i));
        }
        $zip->close();
        return $files;
      }
    }
    
    
    return $files;
  }
}

if(!function_exists('readFiles')){
  function readFiles($path){
    $files = array();
    if ($handle = opendir($path)) {
      while (false !== ($file = readdir($handle))) {
        echo "$file\n";
      }
    }
  }
}

global $badCode;
$badCode = [];
/*$badCode = ['fopen','tmpfile','bzopen','gzopen','chgrp','chmod','chown','copy','file_put_contents','lchgrp','lchown','link','mkdir','move_uploaded_file','rename','rmdir','symlink','tempnam','touch','unlink','imagepng','imagewbmp','image2wbmp ','imagejpeg','imagexbm','imagegif','imagegd','imagegd2','iptcembed','ftp_get','ftp_nb_get','file_exists','file_get_contents','file','fileatime','filectime','filegroup','fileinode','filemtime','fileowner','fileperms','filesize','filetype','glob','is_dir','is_executable','is_file','is_link','is_readable','is_uploaded_file','is_writable','is_writeable','linkinfo','lstat','parse_ini_file','pathinfo','readfile','readlink','realpath','stat','gzfile','readgzfile','getimagesize','imagecreatefromgif','imagecreatefromjpeg','imagecreatefrompng','imagecreatefromwbmp','imagecreatefromxbm','imagecreatefromxpm','ftp_put','ftp_nb_put','exif_read_data','read_exif_data','exif_thumbnail','exif_imagetype','hash_file','hash_hmac_file','hash_update_file','md5_file','sha1_file','highlight_file','show_source','php_strip_whitespace','get_meta_tags',
            'extract','parse_str','putenv','ini_set','mail(','proc_nice','proc_terminate','proc_close','pfsockopen','fsockopen','apache_child_terminate','posix_kill','posix_mkfifo','posix_setpgid','posix_setsid','posix_setuid',
            'phpinfo','posix_mkfifo','posix_getlogin','posix_ttyname','getenv','get_current_user','proc_get_status','get_cfg_var','disk_free_space','disk_total_space','diskfreespace','getcwd','getlastmo','getmygid','getmyinode','getmypid','getmyuid',
            'dl','exec','shell_exec','system','passthru','popen','pclose','proc_open','proc_nice','proc_terminate','proc_get_status','proc_close','pfsockopen','leak','apache_child_terminate','posix_kill','posix_mkfifo','posix_setpgid','posix_setsid','posix_setuid'];*/
