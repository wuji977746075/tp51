<?php

namespace src\base\traits;
use \ErrorCode as EC;
// 请求参数处理
// require :
//  $this->data
//  trait Jump / $this->err
trait PostData {
    /**
   * @title 仅适用 index模块
   * 兼容/模式 2018-07-26 13:53:47
   * @param string $key
   * @param string $default
   * @param string $emptyErr 是否
   * @return mixed
   */
  public function _post($key, $default = '', $emptyErr = false) {
    $key = explode('/',$key);
    $key_type = isset($key[1]) ? $key[1] : 's'; // string
    $key = $key[0]; // key has change
    $key_data = "_data_" . $key ;
    $v = isset($this->data[$key_data]) ? trim($this->data[$key_data]) : '';
    if($key_type == 's'){
      $v = $v ? $this->escapeEmoji($v) : '';
      empty($v) && $emptyErr && $this->err(Llack($key), EC::LACK_PARA);
    }elseif($key_type == 'f'){
      $v = floatval( $v );
    }elseif($key_type == 'd'){
      $v = (int) $v;
    }
    return $v;
  }

  /**
   * @param string $key
   * @param string $default
   * @param string $emptyErrMsg 为空时的报错
   * @return mixed
   */
  public function _get($key, $default = '', $emptyErrMsg = '') {
    $this->_post($key,$default,$emptyErrMsg);
  }

  /**
   * 放到utils中，作为工具类
   * @brief 干掉emoji
   * @autho chenjinya@baidu.com
   * @param {String} $strText
   * @return {String} removeEmoji
   **/
  protected function escapeEmoji($strText, $bool = false) {
    $preg = '/\\\ud([8-9a-f][0-9a-z]{2})/i';
    if ($bool == true) {
      $boolPregRes = (preg_match($preg, json_encode($strText, true)));
      return $boolPregRes;
    } else {
      $strPregRes = (preg_replace($preg, '', json_encode($strText, true)));
      $strRet = json_decode($strPregRes, JSON_OBJECT_AS_ARRAY);

      if ( is_string($strRet) && strlen($strRet) == 0) {
        return "";
      }

      return $strRet;
    }
  }
  /**
   * 合并必选和可选post参数并返回
   * $str: 需要检查的post参数
   * $oth_str: 不需检查的post参数
   */
  protected function parsePost($str='',$oth_str=''){
    return array_merge($this->getPost($str,true),$this->getPost($oth_str,false));
  }
  /**
   * 获取原始数据
   * @return array
   */
  protected function getOriginData(){
    $tmp = [];
    foreach ($this->data as $key=>$vo){
      $k = str_replace("_data_","",$key);
      $tmp[$k] = $vo;
    }
    return $tmp;
  }
  /**
   * 获取post参数并返回
   * $need:是否必选
   * a|0|int   默认0
   * a         默认''
   * a|p       默认'p'
   * a||mulint 数字','链接字符串
   * a||float  小数
   * @DateTime 2016-12-13T10:25:17+0800
   * @param    string  $str  [description]
   * @param    boolean $need [description]
   * @return   array   [description]
   */
  protected function getPost($str,$need=false){
    if(empty($str) || !is_string($str)) return [];
    $r = [];
    $arr = explode(',', $str);
    $data = $this->data;
    foreach ($arr as $v) {
      //补全预定义
      $p = explode('|', $v);
      !isset($p[1]) && $p[1]='';   //默认值空字符串
      !isset($p[2]) && $p[2]='str';//默认类型字符串
      $key = '_data_'.$p[0];
      //_post number bug
      // if($need) $post = $this->_post($p[0],$p[1],Llack($p[0]));
      // else  $post = $this->_post($p[0],$p[1]);
      // fix bug
      !isset($data[$key]) && $data[$key]='';
      if($need && $data[$key] === ''){
        $this->err(Llack($p[0]), EC::LACK_PARA);
      }
      $post = $data[$key]==='' ? $p[1] : $data[$key];
      if($p[2] === 'int'){
        $post = intval($post);
      }elseif($p[2] === 'float'){
        $post = floatval($post);
      }elseif($p[2] === 'mulint'){
        $post = array_unique(explode(',', $post));
        $temp = [];
        foreach ($post as $v) {
          if(is_numeric($v)){
            $temp[] = $v;
          }else{
            $this->err(Linvalid($p[0]), EC::INVALID_PARA);
          }
        }
        $post = implode(',', $temp);
      }
      $r[$p[0]] = $post;
    }
    return $r;
  }
}