<?php
/**
 * Created by PhpStorm.
 * User: boye
 * Date: 2017/3/22
 * Time: 14:59
 */
namespace app\weixin\controller;
use app\src\user\logic\MemberLogic;
use app\src\user\logic\UserMemberLogic;

class Webview extends Home{
    public function app(){
        $this->assignTitle('APP下载');
        return $this->fetch();
    }
    public function shequn(){
        $this->assignTitle('社群服务');
        return $this->fetch();
    }
    public function customer(){
        $this->assignTitle('联系客服');
        return $this->fetch();
    }
    public function recharge(){
        $this->assignTitle('如何充值');
        return $this->fetch();
    }

    public function spread_code(){
        $uid=input('uid');
        if(empty($uid)) $this->error('分享页面信息错误');
        $memberinfo=(new MemberLogic())->getInfo(['uid'=>$uid]);
        $memberinfo=$memberinfo['info'];
        $roles_info=(new UserMemberLogic())->getInfo(['uid'=>$uid]);
        $memberinfo['roles_info']['group_info']=$roles_info['info'];
        $this->assignTitle('推荐人');
        $this->assign('user_Info',$memberinfo);
        switch ($memberinfo['roles_info']['group_info']['group_id'])
        {
            case 1:
                $level = '健康天使';
                break;
            case 2:
                $level = '健康精英';
                break;
            case 3:
                $level = '健康领袖';
                break;
            case 4:
                $level = '注册会员';
                break;
            default:
                $level = '非注册会员';
        }
        $this->assign('level',$level);

        $code='http://dehong.8raw.com/weixin.php/weixin/user/recommend/uid/'.$uid;
        $this->assign('code',$code);
        return $this->fetch();
    }

}



///**
// * Created by PhpStorm.
// * User: 64
// * Date: 2017/3/21 0021
// * Time: 14:46
// */
//
//
//        {
//            "button":[
//            {
//          "name":"关于德弘",
//          "type":"view",
//           "url":"http://mp.weixin.qq.com/s?__biz=MzI0MjA2NDUyMg==&mid=312184517&idx=1&sn=6f8012711fc9ef6c11da7e1bacb8bfac&chksm=7e6d2320491aaa364de6061710939612f5fd957ada047cd54aadbec503cd0d514e24c98996d4&mpshare=1&scene=1&srcid=0322wCH2cDf4t39Lo1UBBz8q#rd"
//       },
//      {
//          "name":"服务中心",
//           "sub_button":[
//           {
//               "type":"view",
//               "name":"APP下载",
//               "url":"http://dehong.8raw.com/weixin.php/weixin/webview/app"
//            },
//            {
//                "type":"view",
//               "name":"社群服务",
//               "url":"http://dehong.8raw.com/weixin.php/weixin/webview/shequn"
//            },
//          {
//              "type":"view",
//               "name":"如何充值",
//               "url":"http://dehong.8raw.com/weixin.php/weixin/webview/recharge"
//            },
//            {
//                "type":"view",
//               "name":"联系客服",
//               "url":"http://dehong.8raw.com/weixin.php/weixin/webview/customer"
//
//            }
//          ]
//       },
//      {
//          "type":"view",
//           "name":"移动商城",
//            "url":"http://dehong.8raw.com/weixin.php/weixin/index/index"
//       }
//
//]
// }
//
//
