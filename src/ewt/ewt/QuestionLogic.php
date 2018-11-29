<?php
/**
 * @author rainbow 2017-03-25 17:03:07
 */
namespace src\ewt\ewt;

use src\base\BaseLogic;
// use app\src\ewt\model\Question;
// use app\src\ewt\logic\AnswerLogicV2;

class QuestionLogic extends BaseLogic{

  //解析content
  public function parseContent($content='',$dt_type=0){
    $content = str_replace('\n', '', strip_tags(trim($content)));
    $con_type = $this->getContentType($dt_type);
    $ret = [];
    // if(empty($content)) return returnSuc([]);
    if($con_type == 'img'){
      $content = rtrim($content,',');
      $content = explode(',', $content);
      $ppp = [];
      foreach ($content as $v) {
        $ppp[] = ['img',intval($v)];
      }
      $ret[] = $ppp;
    }elseif($con_type == 'str'){ //tr td co br
      $str_type = $this->getStrType($dt_type);
      if($str_type == 'table'){ //表格

        // $content = str_replace('{{br}}', '', $content);
        $content = str_replace('{{br}}', '', preg_replace('/^{{tr}}|{{tr}}$/','',$content));
        $content = explode('{{tr}}', $content);
        foreach ($content as $v) { //对于每一段
          $tmp = explode('{{td}}', $v);
          $ppp = [];
          foreach ($tmp as $vv) { //对于每个单元格
            if(trim($vv) == '{{co}}'){
              //整个为控制符
              $ppp[] = ['con',''];
            }else{
              //对于每个分割 - 暂当做str
              $ppp[] = ['str',trim($vv)];
            }
          }
          $ret[] = $ppp;
        }
      }else{ //非表格

        // $content = explode('{{tr}}', $content);
        $content = explode('{{tr}}', preg_replace('/^{{tr}}|{{tr}}$/','',$content));
        foreach ($content as $v) { //对于每一段
          $v   = trim($v);
          $tmp = preg_split('/\{\{(td|co|br)\}\}/',$v);
// echo $v;
// dump($tmp);
          $len = count($tmp);
          $char_at = 0;
          $ppp = [];
          for ($i=0; $i < $len; $i++) {
            $item = $tmp[$i];
            if($i == 0){ //首 无type
              // echo 'item1:'.$item.'==';
              $item && $ppp[] = ['str',$item];
              $char_at += mb_strlen($item); // xx
              if($len >1) $char_at += 2; // xx+'{{'

            }else{ //非首 有type
              // echo 'item2:'.$item.'=';
              // 查前面的类型标识符
              $type = mb_substr($v, $char_at,2);
              // echo $char_at.',2='.$type.':';

              $char_at += 4;  // 'xx}}'+xx
              $char_at += mb_strlen($item); //xx
              if($i<$len-1){ //非首非末
                $char_at += 2; // xx+'{{'
              }
              if($type == 'td'){
                // 仅做分割
                // $ppp[] = ['con','|'];
              }elseif($type == 'co'){
                $ppp[] = ['con',''];
              }elseif($type == 'br'){
                $ppp[] = ['con','nl'];
              }else{
                return returnErr('内容解析失败');
              }
              $item && $ppp[] = ['str',$item];
            }
          }
          //去除对话的:
          if($this->getStrType($dt_type) == 'dialog') $ppp[0][1] = rtrim(trim($ppp[0][1]),':');
          $ret[] = $ppp;
        }
      }
    }
    return returnSuc($ret);
  }
  //获取题目 - 小题列表 题号(1)
  public function getChildren(array $map,$order=false){
    $ret = $this->queryNoPaging($map,$order);
    if(!$ret) return returnErr('empty');
    $i = 0;
    foreach ($ret as &$v) {
      $i++;
      $v['title'] = "($i)";
       //题面重制
      if($v['content']){
        $r = $this->parseContent($v['content'],$v['dt_type'],$v['id']);
        if($r['status']) return $r;
        $v['content'] = $r['info'];
      }
      //附加答案
      $v = array_merge($v,$this->getAnswers($v['id']));
      //? 小题 - 不查下一级
      $v['child'] = [];
    }
    return returnSuc($ret);
  }

  //题目的答案描述
  public function getAnswers($q_id){
    $l = new AnswerLogicV2;
    $answer = [];
    $answer_true = [];
    $ret = [];
    //被选答案
    $r = $l->queryNoPaging(['q_id'=>$q_id],'sort desc','title,content,type');
    foreach ($r as  $v) {
      $answer[] = [
        $v['title'],$v['type'],$v['content']
      ];
    }
    //正确答案
    if($r){
      $r = $l->queryNoPaging(['q_id'=>$q_id,'is_real'=>1],'real_sort desc','title');
      foreach ($r as $v) {
        $answer_true[] = $v['title'];
      }
    }
    $ret['answer'] = $answer;
    $ret['answer_true'] = $answer_true;
    $ret['answer_number'] = count($answer_true);
    return $ret;
  }
  //检查 答案类型是否符合题目
  public function checkAnswerType($id,$type=''){
    if(!$type)  return false;
    return $this->getAnswerType($id) == $type;
  }

  public function getStrType($dt_type=0){
    $dt_type = intval($dt_type);
    if($dt_type == 6231){
      return 'table';
    }elseif($dt_type == 6232){
      return 'dialog';
    }else{
      return '';
    }
  }
  //题目内容类型
  public function getContentType($dt_type=0){
    $dt_type = (int) $dt_type;
    if($dt_type){
      if(in_array($dt_type,[6228])){
        return 'img';
      }else{
        return 'str';
      }
    }else{
      return '';
    }
  }

  //答案是否为单词 - 无内容
  public function isWordAnswer($dt_type){
    $dt_type = (int) $dt_type;
    return in_array($dt_type,[6229,6233]);
  }
  //答案是否为字母 - 无内容
  public function isAlphaAnswer($dt_type){
    $dt_type = (int) $dt_type;
    return in_array($dt_type,[6233]);
  }
  //答案是否为选词填空 - 无内容
  public function isStrAnswer($dt_type){
    $dt_type = (int) $dt_type;
    return in_array($dt_type,[6231,6232,6202]);
  }
  //答案是否为 key-value 形式 A:xx
  public function isKvAnswer($dt_type){
    $dt_type = (int) $dt_type;
    return in_array($dt_type,[6226]);
  }
  //答案是否为 真假值
  public function isBoolAnswer($dt_type){
    $dt_type = (int) $dt_type;
    return in_array($dt_type,[6228,6230]);
  }

  //是否有小题
  public function hasChild($dt_type){
    $dt_type = (int) $dt_type;
    return in_array($dt_type,[6227]);
  }

  //是否为多段结构
  public function isMulti($dt_type){
    $dt_type = (int) $dt_type;
    return in_array($dt_type,[6227,6231,6232]);
  }

  //
  public function getAnswerTypeByDtree($id){
    switch ($id) {
      case 6202://选词填空
        return 'str';
        break;
      case 6203://选择图片
        return 'img';
        break;
      case 6226://单项选择
        return 'str';
        break;
      case 6227://完型填空
        return '';
        break;
      case 6228://图片判断
        return 'bool';
        break;
      case 6229://单词 - 听写
        return 'str';
        break;
      case 6230://判断
        return 'bool';
        break;
      case 6231://填表格
        return 'str';
        break;
      case 6232: //情景对话
        return 'str';
        break;
      case 6233: //单词 - 拼写
        return 'str';
        break;
      default:
        return '';
        break;
    }
    return '';
  }
  //题目答案类型
  public function getAnswerType($id){
    if($id){
      $r = $this->getField(['id'=>$id],'dt_type');
      return $r ? $this->getAnswerTypeByDtree(intval($r)) : '';
    }
    return '';
  }
}