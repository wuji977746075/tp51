<?php
/**
 * Created by PhpStorm.
 * User: Zhoujinda
 * Date: 2017/1/4
 * Time: 18:03
 */
namespace app\src\repairerApply\logic;

use app\src\base\helper\ResultHelper;
use app\src\base\logic\BaseLogicV2;
use app\src\extend\Page;
use app\src\repairerApply\model\RepairerApply;
use think\exception\DbException;

class RepairerApplyLogicV2 extends BaseLogicV2{

    //初始化
    protected function _init(){
        $this->setModel(new RepairerApply);
    }

    // 技工申请
    public function addApply($mobile,$invite_id=0){

        $RepairerApply = RepairerApply::get(['mobile' => $mobile, 'status' => 0]);
        if(is_null($RepairerApply)){
            $data = [
                'mobile'      => $mobile,
                'create_time' => time(),
                'invite_id'   => $invite_id,
            ];
            try{
                $RepairerApply = new RepairerApply;
                $RepairerApply->data($data)->save();
                return ResultHelper::success(lang('repairer_apply_success'));
            }catch (DbException $e){
                return ResultHelper::error(lang('fail'));
            }
        }else{
            return ResultHelper::error(lang('err_repairer_has_apply'));
        }

    }

    public function applyList($current_page = 1, $per_page = 15, $map = ['status' => 0], $params = false){

        try{
            $list = RepairerApply::where($map)->order('create_time desc')->paginate($per_page, false, ['page' => $current_page]);
            $list = $list->toArray();

            $count = $list['total'];
            // 查询满足要求的总记录数
            $Page = new Page($count, $per_page);

            //分页跳转的时候保证查询条件
            if ($params !== false) {
                foreach ($params as $key => $val) {
                    $Page -> parameter[$key] = urlencode($val);
                }
            }

            // 实例化分页类 传入总记录数和每页显示的记录数
            $show = $Page -> show();
            $list['show'] = $show;

            return ResultHelper::success(['list' => $list['data'], 'show' => $show]);
        }catch (DbException $e){
            return ResultHelper::error(lang('fail'));
        }

    }
}