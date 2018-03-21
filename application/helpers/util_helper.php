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
