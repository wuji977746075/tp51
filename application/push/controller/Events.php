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
    * 发送 msg type(tip ping error chat)
    * @param int $client 连接id
    * @param mixed $src 具体消息
    */
    public static function onMessage($client, $src) {
      global $db;
      try{
        $data = json_decode($src,true);
        // uid type(chat/login/ping) msg
        if(!is_array($data)){
          self::pushClient($client,'非法操作','error');
        }else{
          $uid  = (int) $data['uid'];
          $msg  = $data['msg'];
          $type = (string) $data['type'];
          if($type == 'chat'){
            // $msg 可考虑保存到数据库
            // .mine( avatar: content: id: mine: username )
            //  content:face[标志] a(地址)[文本] img[地址] file(地址)[文本] audio[地址] video[地址]
            // .to (avatar: id: name: type: friend/group/kefu sign:username)
            if($uid){ //用户推送
              if(Gateway::isUidOnline($uid)){
                self::pushUid($uid,$msg,'chat');
                self::pushClient($client,'发送成功 ...','tip');
              }else{
                self::pushUid($_SESSION['uid'],$uid.'不在线 ...','tip');
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
       'msg'  =>self::getSendMsg($msg,$type),
       'type' =>$type,
      ];
      Gateway::sendToClient($client,json_encode($data));
    }
    // private static function retMsg($data,$msg='',$code=0){
    //   return ["code"=>$code,"msg"=>$msg,"data"=>$data];
    // }
    private static function pushAll($msg,$type='tip') {
      $data = [
       'msg'  =>self::getSendMsg($msg,$type),
       'type' =>$type,
      ];
      Gateway::sendToAll(json_encode($data));
    }
    private static function pushUid($uid,$msg,$type='chat'){
      // tip msg
      $data = [
        'msg' =>self::getSendMsg($msg,$type),
        'type'=>$type
      ];
      Gateway::sendToUid($uid,json_encode($data));
    }
    private static function getSendMsg($msg,$type){
      if($type == 'chat'){   //用户聊天
        $msg = [
          'username' =>$msg['mine']['username'], //来源用户名
          'avatar'   =>$msg['mine']['avatar'], //来源用户头像
          'id'       =>$msg['mine']['id'],//来源ID（私聊用户id/群聊群组id）
          'type'     =>$msg['to']['type'],
          'content'  =>$msg['mine']['content'],//消息内容
          // 'cid'=>0,//消息id，可不传。除非你要对消息进行一些操作（如撤回）
          'mine'=> false, //true显示在右方
          'fromid'=>$msg['mine']['id'],//消息的发送者id（比如群组中的某个消息发送者），可用于自动解决浏览器多窗口时的一些问题
          'timestamp'=> time()*1000 //服务端时间戳毫秒数。
        ];
      }else{ // 系统消息 tip error ping
        if(is_string($msg)){
          $msg = [
           'system' =>true,
           'id'     =>$_SESSION['uid'],
           'type'   =>'friend',
           'content'=>$msg,
          ];
        }else{
          $msg = [
           'system' =>true,
           'id'     =>$msg['mine']['id'],
           'type'   =>$msg['to']['type'],
           'content'=>$msg['mine']['content'],
          ];
        }
      }
      return $msg;
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
     * 当连接断开时触发的回调函数 : 经观察不断在关闭连接
     * 客户端与Gateway进程的连接断开时触发。不管是客户端主动断开还是服务端主动断开，都会触发这个回调。一般在这里做数据清理。
     * 注意：onClose回调里无法使用Gateway::getSession来获得当前用户的session数据，但是仍然可以使用$_SESSION变量获得
     * 客户端js: readyState 3
     * @param $connection
     */
    public static function onClose($client) {
      $uid = intval($_SESSION['uid']);
      if($uid){ // 不管其他
        global $db;
        $clients = Gateway::getClientIdByUid($uid);
        if(!$clients){ // 全设备下线
          echo $uid.'_all_offline : ';
          $_SESSION['uid'] = null;
          // 用户在线 => 离线
          $db->update('f_user_extra')->cols(['im_status'=>0])->where('im_status=1 and id='.$uid)->query();
          // 删除全部 clent
          $row_count = $db->delete('f_im_user')->where("uid=".$uid)->query();
          self::pushAll($uid,'logout');
        }else{ // 一设备下线
          echo $uid.'_'.$client.'_offline : ';
          // 删除 clent
          $row_count = $db->delete('f_im_user')->where("uid=".$uid." and client='".$client."'")->query();
        }
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
      }else{
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