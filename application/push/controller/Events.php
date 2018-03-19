<?php
namespace app\push\controller;
use GatewayWorker\Lib\Gateway;
use Workerman\MySQL\Connection as Db;
/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 * uid,msg,type
 * uid:发送者uid
 * msg:内容,不同类型可能不一样
 * 客户端发送 login(msg为登陆用户uid) chat logout
 * 服务器发送 chat logout login tip error list
 */
class Events{
    /**
    * 当客户端发来消息时触发 - 变更需要重启server3
    * @param int $client 连接id
    * @param mixed $src 具体消息
    */
    public static function onMessage($client, $src) {
      global $db;
      try{
        $data = json_decode($src,true);
        if(!is_array($data)){
          self::pushClient($client,'非法操作','error');
        }else{
          $uid  = (int) $data['uid'];
          $msg  = (string) $data['msg'];
          $type = (string) $data['type'];
          if($type == 'list'){
            $ret  = [];
            // 查询该用户信息
            $ret["mine"] = [
              "username" => "纸飞机" //我的昵称
              ,"id"      => "100000" //我的ID
              ,"status"  => "online" //在线状态 online：在线、hide：隐身
              ,"sign"    => "在深邃的编码世界，做一枚轻盈的纸飞机" //我的签名
              ,"avatar"  => "a.jpg" //我的头像
            ];
            // 查询好友列表
            $ret["friend"] =[];
            $ret['friend'][] = [
              "groupname"=> "我的好友" //好友分组名
              ,"id"=> 1 //分组ID
              ,"list"=> [[ //分组下的好友列表
                "username" => "贤心" //好友昵称
                ,"id"      => "100001" //好友ID
                ,"avatar"  => "a.jpg" //好友头像
                ,"sign"    => "这些都是测试数据，实际使用请严格按照该格式返回" //好友签名
                ,"status"  => "online" //若值为offline代表离线，online或者不填为在线
              ]]
            ];
            // 查询群组列表
            $ret["group"] = [];
            $ret["group"][]= [
              "groupname" => "前端群" //群组名
              ,"id"       => "101" //群组ID
              ,"avatar"   => "a.jpg" //群组头像
            ];
            self::pushClient($client,$ret,'list');
          }else if($type == 'chat'){
            if($uid){ //用户推送
              if(Gateway::isUidOnline($uid)){
                self::pushUid($_SESSION['uid'],$uid,$msg);
                self::pushUid(0,$_SESSION['uid'],'发送成功 ...','tip');
              }else{
                self::pushUid(0,$_SESSION['uid'],$uid.'不在线 ...');
              }
            }else{ //用户咨询
                self::pushClient($client,'感谢您的反馈 ...');
            }
          }else if($type == 'ping'){
            self::login($uid,$client);
            self::pushClient($client,'ok','ping');
          }else if($type == 'logout'){
            self::onClose($client);
          }else{
            self::pushClient($client,'非法操作','error');
          }
        }

      }catch(\Exception $e){
        self::pushClient($client,$e->getMessage(),'error');
      }
    }
    private static function pushClient($client,$msg,$type="tip") {
      $data = [
       'uid'  =>0,
       'msg'  =>$msg,
       'type' =>$type,
      ];
      Gateway::sendToClient($client,json_encode($data));
    }
    // private static function retMsg($data,$msg='',$code=0){
    //   return ["code"=>$code,"msg"=>$msg,"data"=>$data];
    // }
    private static function pushAll($msg,$type='tip') {
      $data = [
       'uid'  =>0,
       'msg'  =>$msg,
       'type' =>$type,
      ];
      Gateway::sendToAll(json_encode($data));
    }
    private static function pushUid($from,$to,$msg,$type='chat'){
      // tip msg
      $data = [
        'uid' =>$from,
        'msg' =>$msg,
        'type'=>$type
      ];
      Gateway::sendToUid($to,json_encode($data));
    }
    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 客户端js: readyState 1
     * @param int $client 连接id
     */
    public static function onConnect($client) {
    }
    /**
     * 当连接断开时触发的回调函数
     * 客户端js: readyState 3
     * @param $connection
     */
    public static function onClose($client) {
      Gateway::closeClient($client);
      global $db;
      $uid = intval($_SESSION['uid']);
      $clients = Gateway::getClientIdByUid($uid);
      if(!$clients){ // 全设备下线
        echo $uid.':all offline<br/>';
        $_SESSION['uid'] = null;
        // 用户在线 => 离线
        $db->update('f_user_extra')->cols(['im_status'=>0])->where('im_status=1 and id='.$uid)->query();
        // 删除全部 clent
        $row_count = $db->delete('f_im_user')->where("uid=".$uid)->query();
        self::pushAll($uid,'logout');
      }else{ // 一设备下线
        echo $uid.': offline'.$client.'<br/>';
        // 删除 clent
        $row_count = $db->delete('f_im_user')->where("uid=".$uid." and client='".$client."'")->query();
      }
    }
    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public static function onError($client, $code, $msg) {
        echo "error $code $msg\n";
        self::pushClient($client,$code.':'.$msg,'error');
    }
    /**
     * 每个进程启动
     * @param $worker
     */
    public static function onWorkerStart($worker) {
      // 将db实例存储在全局变量中(也可以存储在某类的静态成员中)
      global $db;
      $db = new Db('127.0.0.1', '3306', 'root', '1', 'fly');
      // 通过全局变量获得db实例
      // $u_info = $db->row("SELECT * FROM `f_user` WHERE id=1");
      // 获取所有数据
      // $db->select('ID,Sex')->from('Persons')->where('sex= :sex')->bindValues(array('sex'=>'M'))->query();
      //等价于
      // $db->select('ID,Sex')->from('Persons')->where("sex= 'F' ")->query();
      //等价于
      // $db->query("SELECT ID,Sex FROM `Persons` WHERE sex='M'");

      // 获取一行数据
      // $db->select('ID,Sex')->from('Persons')->where('sex= :sex')->bindValues(array('sex'=>'M'))->row();
      // //等价于
      // $db->select('ID,Sex')->from('Persons')->where("sex= 'F' ")->row();
      // //等价于
      // $db->row("SELECT ID,Sex FROM `Persons` WHERE sex='M'");

      // // 获取一列数据
      // $db->select('ID')->from('Persons')->where('sex= :sex')->bindValues(array('sex'=>'M'))->column();
      // //等价于
      // $db->select('ID')->from('Persons')->where("sex= 'F' ")->column();
      // //等价于
      // $db->column("SELECT `ID` FROM `Persons` WHERE sex='M'");

      // // 获取单个值
      // $db->select('ID,Sex')->from('Persons')->where('sex= :sex')->bindValues(array('sex'=>'M'))->single();
      // //等价于
      // $db->select('ID,Sex')->from('Persons')->where("sex= 'F' ")->single();
      // //等价于
      // $db->single("SELECT ID,Sex FROM `Persons` WHERE sex='M'");

      // // 复杂查询
      // $db->select('*')->from('table1')->innerJoin('table2','table1.uid = table2.uid')->where('age > :age')->groupBy(array('aid'))->having('foo="foo"')->orderByASCorderByDESC(array('did'))
      // ->limit(10)->offset(20)->bindValues(array('age' => 13));
      // // 等价于
      // $db->query("SELECT * FROM `table1` INNER JOIN `table2` ON `table1`.`uid` = `table2`.`uid` WHERE age > 13 GROUP BY aid HAVING foo='foo' ORDER BY did LIMIT 10 OFFSET 20");

      // // 插入
      // $insert_id = $db->insert('Persons')->cols(array(
      //     'Firstname'=>'abc',
      //     'Lastname'=>'efg',
      //     'Sex'=>'M',
      //     'Age'=>13))->query();
      // // 等价于
      // $insert_id = $db->query("INSERT INTO `Persons` ( `Firstname`,`Lastname`,`Sex`,`Age`) VALUES ( 'abc', 'efg', 'M', 13)");

      // // 更新
      // $row_count = $db->update('Persons')->cols(array('sex'))->where('ID=1')
      // ->bindValue('sex', 'F')->query();
      // // 等价于
      // $row_count = $db->update('Persons')->cols(array('sex'=>'F'))->where('ID=1')->query();
      // // 等价于
      // $row_count = $db->query("UPDATE `Persons` SET `sex` = 'F' WHERE ID=1");

      // // 删除
      // $row_count = $db->delete('Persons')->where('ID=9')->query();
      // // 等价于
      // $row_count = $db->query("DELETE FROM `Persons` WHERE ID=9");
      // 事务
      // $db->beginTrans();
      // ....
      // $db->commitTrans(); // or $db->rollBackTrans();
    }

    private static function getUinfo($uid,$field='*'){
      $uid = (int) $uid;
      global $db;
      return $db->row("select ".$field." from f_user where id=".$uid);
    }
    private static function login($uid,$client=''){
      $uid = (int) $uid;
      echo $uid.':'.$client.':online<br/>';
      global $db;
      $now = time();
      // todo : 设备数 限制
      if(isset($_SESSION['uid']) && $_SESSION['uid']){
        $_SESSION['uid'] = $uid;
      }
      Gateway::bindUid($client, $uid);
      $clients = Gateway::getClientIdByUid($uid);
      // 删除uid 已离线设备
      if($clients){
        $count = $db->delete('f_im_user')->where("uid=".$uid." and client not in('".implode("','", $clients)."')")->query();
        // 用户离线 => 在线
        $db->update('f_user_extra')->cols(['im_status'=>1])->where('im_status=0 and id='.$uid)->query();
      }else{
        $count = $db->delete('f_im_user')->where("uid=".$uid)->query();
      }
      // 保存 client
      $info = $db->row("select id from f_im_user where uid=".$uid." and client='".$client."'");
      if($info){ // update
        $row_count = $db->update('f_im_user')->cols([
          'update_time' =>$now
        ])->where("id =".$info['id'])->query();
      }else{ // add
        $insert_id = $db->insert('f_im_user')->cols([
          'uid'         =>$uid,
          'client'      =>$client,
          'create_time' =>$now,
          'update_time' =>$now
        ])->query();
      }
      // self::pushAll($uid,'login');
      // 返回登陆的用户

    }
}