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
  function saveUploadFile($folder,$inputName = "arquivo"){
    $uploaddir = './uploads/';
    
    #cria o diretório
    if (!file_exists($uploaddir . "/$folder/")){
      $allPath = $uploaddir . "/$folder/";
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
    
    $uploadfile = $uploaddir . "/$folder/" . basename($_FILES[$inputName]['name']);
    
    $fname = basename($_FILES[$inputName]['name']);
    $try = 1;
    #enquanto existir um arquivo com aquele nome
    while (file_exists($uploadfile)){
      $name_part = substr($fname,0,strrpos($fname,"."));
      $ext       = substr($fname,strrpos($fname,"."));
      $uploadfile = $uploaddir . "/$folder/" . $name_part . "($try)" . $ext ;
      $try++;
    }

    if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $uploadfile)) {
      return $uploadfile;
    } else {
      return false;
    }
  }
}