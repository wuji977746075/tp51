<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-19
 * Time: 18:39
 */

namespace app\admin\controller;

use app\src\admin\api\UserApi;
use app\src\admin\helper\AdminFunctionHelper;
use app\src\base\enum\StatusEnum;
use app\src\base\facade\AccountFacade;
use app\src\goods\logic\ProductLogic;
use app\src\log\logic\OperateLogLogic;
use app\src\powersystem\logic\AuthGroupAccessLogic;
use app\src\powersystem\logic\AuthGroupLogic;
use app\src\repairerApply\logic\RepairerApplyLogicV2;
use app\src\session\logic\LoginSessionLogic;
use app\src\user\action\UserLogoutAction;
use app\src\user\facade\DefaultUserFacade;
use app\src\user\logic\MemberConfigLogic;
use app\src\user\logic\MemberLogic;
use app\src\user\logic\UcenterMemberLogic;
use app\src\user\logic\VUserInfoLogic;
use app\src\user\action\RegisterAction;
use app\src\user\logic\DriverLogicV2;
use app\src\user\logic\WorkerLogicV2;
use app\src\wallet\logic\WalletHisLogicV2;
use app\src\wallet\logic\WalletLogic;
use app\src\wallet\model\WalletHis;
use think\exception\DbException;
use think\Request;
use app\src\ewt\logic\UserBookLogicV2;

class Member extends Admin
{

    public function index() {

        $mobile   = $this->_param('mobile', '');
        $nickname = $this->_param('nickname', '');
        $u_group  = $this->_param('u_group', '');
        $is_login = $this->_param('is_login','');
        $province = $this->_param('province','');
        $city = $this->_param('city','');
        $area = $this->_param('area','');

        $params = [];
        $map    = null;
        $now = time();
        if(!empty($is_login)){
            $params = ['is_login' => $is_login];
            if($is_login==1){
                $map['expire_time'] = array('EGT', $now);
            }else
                $map['expire_time'] =array(array('elt', $now),array('EXP','IS NULL'),'or');
        }
        if(!empty($mobile)){
            $map['mobile|email'] = array('like', "%" . $mobile  . "%");
            $params['mobile'] = $mobile;
        }
        if(!empty($nickname)){
            $map['nickname'] = array('like', "%" . $nickname  . "%");
            $params['nickname'] = $nickname;
        }
        if(!empty($province)){
            $map['loc_area'] = array('like', "%".$province."%");;
            $params['loc_area'] = $province;
        }
        if(!empty($city)){
            $map['loc_area'] = array('like', "%".$city."%");;
            $params['loc_area'] = $city;
        }
        if(!empty($area)){
            $map['loc_area'] = array('like', "%".$area."%");;
            $params['loc_area'] = $area;
        }
        if(!empty($u_group)){
            $map['u_group'] = $u_group;
            $params['u_group'] = $u_group;
        }
        $p = $this->_param('p',0);
        $page = array('curpage' => $p, 'size' => 10);
        $order = " reg_time desc ";
        $result = (new VUserInfoLogic())->queryWithPagingHtml($map, $page, $order,$params);


        if(!empty($result['info']['list'])) {
            foreach($result['info']['list'] as $v){
                $login_info[$v['id']]=$v['expire_time']>$now?'在线':'下线';
            }

            $this->assign('login_info', $login_info);
        }
        if ($result['status']) {

            $params['p'] = $p;
            $this -> assign('province',$province);
            $this -> assign('city',$city);
            $this -> assign('area',$area);
            $this -> assign('params',$params);
            $this -> assign('is_login',$is_login);
            $this -> assign('mobile',$mobile);
            $this -> assign("u_group",$u_group);
            $this -> assign("nickname",$nickname);
            $this -> assign("show", $result['info']['show']);
            $this -> assign("list", $result['info']['list']);
            $result = (new AuthGroupLogic())->queryNoPaging();
            if($result['status']){
                $this->assign('user_group',$result['info']);
            }

            return $this -> boye_display();
        } else {
            $this -> error($result['info']);
        }
    }

    /**
     * 实名审核
     */
    public function realname(){
        $uid = $this->_param('uid',0);
        $this->assign('uid',$uid);
        if($uid >0 ){
            $r = (new MemberLogic())->getInfo(['uid'=>$uid]);

            if(!$r['status']) $this->error($r['info']);
            if(empty($r['info'])) $this->error('uid错误');
            $nickname = $r['info']['nickname'];
        }else{
            $nickname = '';
        }
        $this->assign('nickname',$nickname);
        $page = array('curpage' => $this->_param('p'), 'size' => 15);
        $map  = array('identity_validate'=>2);
        $result = (new MemberConfigLogic())->queryWithPagingHtml($map,$page);
        $this->assign('member',$result['info']['list']);
        $this->assign('show',$result['info']['show']);

        return $this->boye_display();
    }

    /**
     * 审核通过
     */
    public function pass(){

        $map = array('uid' => $this->_param('id',0));
        $entity = array('identity_validate'=>1);
        $result = (new MemberConfigLogic())->save($map,$entity);
        if($result['status']){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }



    /**
     * 审核失败
     */
    public function fail(){
        $map = array('uid'=>$this->_param('id',0));
        $entity = array('identity_validate'=>0);
        $result = (new MemberConfigLogic())->save($map,$entity);

        if($result['status']){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }
    /**
     * 删除用户
     * 假删除
     */
    public function delete(){
        $uid = $this->_param('uid',0);
        if(AdminFunctionHelper::isRoot($uid)){
            $this->error("禁止对超级管理员进行删除操作！");
        }
        if($uid > 0){
            $result = (new UcenterMemberLogic())->saveByID($uid,['status'=>StatusEnum::DELETE]);
            if(!$result['status']){
                $this->error($result['info']);
            }
        }

        $this->success("删除成功!");
    }

    /**
     * 启用
     */
    public function enable(){
        $id = $this->_param('id',0);
        if(AdminFunctionHelper::isRoot($id)){
            $this->error("禁止对超级管理员进行禁用操作！");
        }

        $result = (new UcenterMemberLogic())->saveByID($id,['status'=>StatusEnum::NORMAL]);
        if($result['status']){
            $this->success('启用成功',url('Admin/Member/index'));
        }else{
            $this->error('启用失败',url('Admin/Member/index'));
        }
    }

    /**
     * 禁用
     */
    public function disable(){

        $id = $this->_param('id',0);
        if(AdminFunctionHelper::isRoot($id)){
            $this->error("禁止对超级管理员进行禁用操作！");
        }

        $result = (new UcenterMemberLogic())->saveByID($id,['status'=>StatusEnum::DISABLED]);
        if($result['status']){
            $this->success('禁用成功',url('Admin/Member/index'));
        }else{
            $this->error('禁用失败',url('Admin/Member/index'));
        }
    }

    /**
     * add
     * @param string $username
     * @param string $password
     * @param string $repassword
     * @return mixed
     */
    public function add(){
        if(IS_POST){

            $mobile = $this->_param('mobile',0);
            $email = $this->_param('username',0);
            $password = $this->_param('password',0);
            $repassword = $this->_param('repassword',0);
            $nickname = $this->_param('nickname',0);
            if($password != $repassword){
                $this->error("密码和重复密码不一致！");
            }
            $entity = [
                'nickname'=>$nickname,
                'username' =>'user'.time(),
                'password' =>$password,
                'mobile'   =>$mobile,
                'email'    =>$email,
                'country'  =>'+86',
                'reg_from' =>2,
                'reg_type' =>2,
            ];

            $action = new RegisterAction();
            $r = $action->register($entity);
            if($r['status'] && intval($r['info']) > 0){
                $this->success('用户添加成功！',url('Member/index'));
            }
            if(is_string($r['info'])){
                $this->error($r['info']);
            }
            $this->error(lang("fail"));

        }else{

            return $this->boye_display();
        }
    }

    /**
     * 检测用户名是否已存在
     */
    public function check_username($username){
        $result = (new UcenterMemberLogic())->checkUsername($username);
        if($result['status']){
            echo "false";
        }else{
            echo "true";
        }
    }

    /**
     * ajax - member-select
     */
    public function select(){
        $q = strip_tags(trim($this->_param('q', '')));
        $map = [];
        if($q){
            $map['uid|nickname'] = ['like', "%" . $q  . "%"];
        }

        $page = ['curpage'=>0,'size'=>20];
        $order = "uid desc";
        $r = (new MemberLogic)->query($map,$page, $order,false,'uid,nickname,head');
        !$r['status'] && $this->error($r['info']);;
        $list = $r['info']['list'];
        foreach($list as $key=>$g){
            $list[$key]['id']   = $list[$key]['uid'];
            $list[$key]['head'] = getImgUrl($list[$key]['head'],60);
        }

        $this->success("读取成功","",$list);

    }

    /**
     * ajax - 选择销售人员
     */
    public function salerSelect(){
        $q = strip_tags(trim($this->_param('q', '')));
        $map = ['a.group_id'=>7];//,'m.status'=>['gt',0]];
        if($q){
            $map['m.uid|m.nickname'] = ['like', "%" . $q  . "%"];
        }

        $page = ['curpage'=>0,'size'=>20];
        // $order = "uid desc";
        $r = (new MemberLogic)->queryGroup($map,$page,false,false,'m.uid,m.nickname,m.head');
        !$r['status'] && $this->error($r['info']);;
        $list = $r['info']['list'];
        foreach($list as $key=>$g){
            $list[$key]['id']   = $list[$key]['uid'];
            $list[$key]['head'] = getImgUrl($list[$key]['head'],60);
        }

        $this->success("读取成功","",$list);

    }
    //用户已购书籍 - 有效期有效
    public function userBookExpire(){
        $uid = $this->_param('uid',0);
        $pid = $this->_param('pid',0);
        $add = (int) $this->_param('add',0);
        $day = (int) $this->_param('day_'.$pid,0);

        //检查
        if(!in_array($add,[-1,1])) $this->error('需要操作类型');//1(+)/-1(-)
        if($day<1) $this->error('需要设置天数');
        $time = $day*86400;
        //? 买过
        $ub = (new UserBookLogicV2);
        $r = $ub->getInfo(['uid'=>$uid,'book_id'=>$pid]);
        if(!$r) $this->error("用户\$uid并未购买书籍\$pid");

        if($add==1){ //增加有效期
            $r = $ub->save(['uid'=>$uid,'book_id'=>$pid],['expire_time'=>['exp','expire_time+'.$time]]);
            if(!$r) $this->error("操作失败");
        }elseif($add==-1){ //减少有效期
            $r = $ub->save(['uid'=>$uid,'book_id'=>$pid],['expire_time'=>['exp','expire_time-'.$time]]);
            if(!$r) $this->error("操作失败");
        }else{
            $this->error('操作类型错误');
        }
        $log = (new OperateLogLogic())->addLog($uid,OperateLogLogic::Editvalidity,'修改用户书籍有效期'.$add,UID,$pid);
        $this->success('操作成功');
    }
    public function userBookDel(){
        $uid = $this->_param('uid',0);
        $pid = $this->_param('pid',0);
        //? 买过
        $ub = new UserBookLogicV2;
        $map = ['uid'=>$uid,'book_id'=>$pid];
        $r = $ub->getInfo($map);
        if(!$r) $this->error("用户$uid并未购买书籍$pid");
        if(!$ub->delete($map)) $this->error('操作失败');
        //记录
        $log = (new OperateLogLogic())->addLog($uid,OperateLogLogic::Delbook,'删除用户书籍',UID,$pid);
        $this->success('操作成功');
    }
    public function view(){
        $id = $this->_param('id',0);
        $p  = $this->_param('p',1);

        $result = (new VUserInfoLogic())->getInfo(array("id"=>$id));
        if(!$result['status']){
            $this->error($result['info']);
        }

        $this->assign("userinfo",$result['info']);

        $result = (new AuthGroupAccessLogic())->queryGroupInfo($id);
        if(!$result['status']){
            $this->error($result['info']);
        }

        $this->assign("userroles",$result['info']);

        $result = (new LoginSessionLogic())->getInfo(['uid'=>$id]);
        if(!$result['status']){
            $this->error($result['info']);
        }
        $this->assign("login_session",$result['info']);

        //用户已购书籍
        $field = 'b.expire_time,b.buy_time,img.img_id as img_main';
        $field .= ',p.name,p.uid as author,p.secondary_headlines,p.cate_id,p.onshelf,p.synopsis,p.id as p_id';
        $r = (new UserBookLogicV2)->getUserBooks(['b.uid'=>$id],['curpage'=>$p,'size'=>10],'b.update_time desc',['id'=>$id,'active'=>'book'],$field);
        !$r['status'] && $this->error($r['info']);
        $this->assign('books',$r['info']['list']);
        $this->assign('books_show',$r['info']['show']);

        //在架图书
        $book_map = ['onshelf'=>1];
        $books = (new ProductLogic())->queryNoPaging($book_map);
        $this->assign('allbooks',$books['info']);

        $active = $this->_param('active','');//base login role book
        $this->assign('active',$active);
        return $this->boye_display();
    }

    /**
     * 单个用户角色管理
     */
    public function user_role(){

        $id = $this->_param('id','','缺失id');
        if(IS_GET){

            $result = (new AuthGroupAccessLogic())->queryGroupInfo($id);
            $role_ids = "";
            foreach ($result['info'] as $vo){
                $role_ids .= $vo['id'].',';
            }
            $this->assign('role_ids',$role_ids);
            $result = (new AuthGroupLogic())->queryNoPaging();
            $this->assign('roles',$result['info']);
            $this->assign('id',$id);
            return $this->boye_display();
        }else{
            $roles = $this->_param('roles/a',[]);

            $result = (new AuthGroupAccessLogic())->delete(['uid'=>$id]);
            $data = [];
            foreach ($roles as $vo){
                $data[] = ['uid'=>$id,'group_id'=>$vo,'is_auth'=>0];
            }

            $result = (new AuthGroupAccessLogic())->saveAll($data);
            if($result['status']){
                $this->success('保存成功',url("Member/user_role",array('id'=>$id)));
            }else{
                $this->error('保存失败',url("Member/user_role",array('id'=>$id)));
            }
        }

    }

    /**
     * 强制踢下线
     *
     */
    public function force_logout(){
        $id = $this->_param('id','');
        $result = (new UserLogoutAction())->logout($id,"force_logout");

        if($result['status']){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }


    /**
     * 用户编辑
     * @return mixed
     */
    public function edit(){
        $id = $this->_param('id',0);
        if(IS_GET){
            $result = (new VUserInfoLogic())->getInfo(['id'=>$id]);
            $this->assign('user',$result['info']);
            return $this->boye_display();
        }
        $params = Request::instance()->param();
        $params['s_id'] = "itboye";
        $params['uid'] = $id;
        $result = (new UserApi())->update($params);

        if($result['status']){
            $this->success('操作成功',url('Member/edit',array('id'=>$id)));
        }else{
            $this->error('操作失败-'.$result['info'],url('Member/edit',array('id'=>$id)));
        }
    }

    /**
     * 余额充值
     */
    public function addMoney(){
        $id = $this->_param('id',0);
        if(IS_GET){

            $result = (new WalletLogic())->getInfoIfNotExistThenAdd($id);
            $this->assign('wallet',$result['info']);
            return $this->boye_display();
        }

        $money = $this->_param('money',0);
        $money = floatval($money) * 100.0;

        if($money > 0) {
            $result = (new WalletLogic())->plusMoney($id, $money, "[后台人工充值" . $money / 100.0 . " 金额]");
            if($result['status']){
                $this->success('操作成功');
            }

            $this->error($result['info']);
        }

        $this->error('操作失败');
    }

    //给用户添加书籍
    public function addbook(){
        $uid = $this->_param('uid','');
        $book_id = $this->_param('bookid','');
        $time = $this->_param('time','');
        if(empty($uid)||empty($book_id)||empty($time)) $this->error('添加失败');
        $entity = [
            'uid'=>$uid,
            'book_id'=>$book_id,
            'unit'=>0,
            'buy_time'=>time(),
            'expire_time'=>time()+3600*24*30*$time,
            'create_time'=>time(),
            'update_time'=>time()
        ];

        $book = (new UserBookLogicV2())->getInfo(['uid'=>$uid,'book_id'=>$book_id]);

        if(!empty($book)){
            $result = (new UserBookLogicV2())->save(['uid'=>$uid,'book_id'=>$book_id],['expire_time'=>$book['expire_time']+3600*24*30]);
        }else{
            $result = (new UserBookLogicV2())->add($entity,'id');
        }
        $books = (new UserBookLogicV2())->queryNoPaging(['uid'=>$uid]);
        $log = (new OperateLogLogic())->addLog($uid,OperateLogLogic::Addbook,'添加用户书籍',UID,$book_id);
        if($result){
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }

}