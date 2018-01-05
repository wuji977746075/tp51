<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-03
 * Time: 17:24
 */

namespace app\domain;
use app\src\banners\logic\BannersLogic;
use app\src\banners\model\Banners;
// use app\src\base\helper\PageHelper;
// use app\src\base\helper\ValidateHelper;
// use app\src\index\action\IndexAction;
use app\src\bbs\logic\BbsPostLogicV2;
use app\src\goods\logic\ProductGroupLogic;
use app\src\bbs\logic\BbsAttachLogicV2;
use app\src\system\logic\ConfigLogic;
/**
 * app首页
 * Class IndexDomain
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\domain
 */
class IndexDomain extends BaseDomain
{
    /**
     * 首页商品接口
     * 101: 增加了收藏字段is_fav 有的时候为收藏id，没有为0 或 空字符串
     * 102: 商品数据也为数组
     * @author hebidu <email:346551990@qq.com>
     */
    public function index(){
        $this->checkVersion([100,101]);

        //返回数据
        $uid  = $this->_post('uid','');
        $size = $this->_post('size',10);
        $ret = [];

        //1. 查询商品分组 6204(首页推荐) max:4;
        $group = 6204;
        $r = (new ProductGroupLogic)->queryWithProduct(['g.g_id'=>$group,'p.status'=>1,'p.onshelf'=>1],['curpage'=>1,'size'=>$size],'g.display_order desc',[],'p.id,p.name,p.uid,p.cate_id,p.synopsis,image.img,sku.price');
        $this->exitWhenError($r);
        $ret['products'] = $r['info']['list'];
        //2. 查询置顶帖子
        $r = (new ConfigLogic)->getCacheConfig('APP_TOP_POST_NUM');
        $this->exitWhenError($r);
        $post_size = intval($r['info']);
        if($this->request_api_ver == '101'){
            $r = (new BbsPostLogicV2)->query(['status'=>1,'top'=>1],['curpage'=>1,'size'=>$post_size],'update_time desc',[],'id,title,content,uid,create_time,views');
            $list = $r['list'];
            foreach ($list as &$v) {
                $v['uname'] = get_nickname($v['uid']);
                $v['content'] = BbsPostLogicV2::subPureContent($v['content']);
                $r = (new BbsAttachLogicV2)->getInfo(['pid'=>$v['id'],'rid'=>0],false,'img');
                $v['img'] = $r ? $r['img'] : 0;
            } unset($v);
            $ret['post'] = $list;
        }else{
            $info = (new BbsPostLogicV2)->getInfo(['status'=>1,'top'=>1],'update_time desc','id,title,content,uid,create_time,views');
            if($info){
                $info['uname'] = get_nickname($info['uid']);
                $info['content'] = BbsPostLogicV2::subPureContent($info['content']);
                $r = (new BbsAttachLogicV2)->getInfo(['pid'=>$info['id'],'rid'=>0],false,'img');
                $info['img'] = $r ? $r['img'] : 0;
            }
            $ret['post'] = $info;
        }
        $this->apiReturnSuc($ret);
    }

    private function combine($info,$ads){
        $count = $info['count'];
        $list  = $info['list'];
        $total = count($ads) + count($list);
        $tmp   = [];
        $i     = 0;//ads index
        $j     = 0;//list index

        while($i + $j < $total){
            if(rand(0,10) < 5){
                if($j < count($list)){
                    array_push($tmp, ['type' => 'p', 'info' => [$list[$j]]]);
                    $j++;
                }elseif($i < count($ads)) {
                    array_push($tmp, ['type' => 'ad', 'info' => $ads[$i]]);
                    $i++;
                }
            }else{
                if($i < count($ads)) {
                    array_push($tmp, ['type' => 'ad', 'info' =>  $ads[$i]]);
                    $i++;
                }elseif($j < count($list)){
                    array_push($tmp, ['type' => 'p', 'info' => [$list[$j]]]);
                    $j++;
                }
            }
        }

        return ['count'=>$count,'list'=>$tmp];
    }

    /**
     * 向返回数据插入 广告条目
     * @author hebidu <email:346551990@qq.com>
     */
    private function queryAd(){
        //1. 随机获取首页广告 进行插入
        $logic = new BannersLogic();

        $rand = rand(0,2);
        $r = $logic->query(['position'=>Banners::APP_AD],['curpage'=>1,'size'=>$rand]);
        if(!empty($r['info']) && isset($r['info']['list'])){
            //TODO: 支持多图片返回
            $list = $r['info']['list'];
            $tmp = [];
            foreach ($list as $item){
                array_push($tmp,[$item]);
            }
            return $tmp;
        }
        return [];
    }
}