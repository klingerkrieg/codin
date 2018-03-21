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


if(!function_exists('saveFile')){
  function saveUploadFile($folder){
    $uploaddir = './uploads/';
    
    #cria o diretório
    if (!file_exists($uploaddir . "/$folder/")){
      mkdir($uploaddir . "/$folder/");
    }
    
    $uploadfile = $uploaddir . "/$folder/" . basename($_FILES['arquivo']['name']);
    
    $fname = basename($_FILES['arquivo']['name']);
    $try = 1;
    #enquanto existir um arquivo com aquele nome
    while (file_exists($uploadfile)){
      $name_part = substr($fname,0,strrpos($fname,"."));
      $ext       = substr($fname,strrpos($fname,"."));
      $uploadfile = $uploaddir . "/$folder/" . $name_part . "($try)" . $ext ;
      $try++;
    }

    if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $uploadfile)) {
      return $uploadfile;
    } else {
      return false;
    }
  }
}