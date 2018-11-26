<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-04-04 10:06:50
 * Description : [Description]
 */

namespace his;
// use by\sdk\his\HisHelper;
use by\sdk\his\Util;
use by\component\jinpu\logic\HospUserLogic;
use by\component\jinpu\logic\HospSectLogic;
use by\component\jinpu\logic\HospDoctLogic;
use by\component\jinpu\logic\HospSectRelationLogic;
use by\component\user\logic\UserProfileLogic;

class HisAction {
  private $his;
  private $hosp;
  private $user;
  public function __construct() {
    $this->his  = new HisHelper;
    $this->user = 7;
    $this->hosp = '1322'; // test 123456 ; online 1322
  }

  // HIS test
  // return $hisReturn
  // throw  \Exception
  function test($p) {
    $req = [
      'HOS_ID' =>$this->hosp,
      'IP'     =>get_client_ip(),
    ];
    if($p == 'curl'){
      $ret = $this->his->curl([1001,'nettest'],$this->user,$req);
    }elseif($p=='soap'){
      $ret = $this->his->soap([1001,'nettest'],$this->user,$req);
    }else{
      throws('非法参数'.$p);
    }
    // SYSDATE
    return $ret;
  }

  // HIS 用户注册并保存
  // return $pid
  // throw  \Exception
  /**$req = [
    'HOS_ID'     => '123456',
    'PARENT_TYPE'=> 1,//INT,1:成年人（必填证件信息） 2:儿童（可不填证件信息，但监护人信息必填）
    // 'CARD_TYPE'=>'', //int,用户卡
    // 'CARD_NO'=>'',
    'NAME'     => '周舟',
    'SEX'      => 1,//int
    'BIRTHDAY' => '1990-08-09',//datetime
    'MOBILE'   => '17681876087',
    // 'ADDRESS'=>'',
    'PASSWORD' => substr($id_no,-6),
    'ID_TYPE'  => 1, //int,用户证件
    'ID_NO'    => $id_no, //用户证件号码
    // 'ID_ISSUE_DATE'  =>'',
    // 'ID_EFFECT_DATE' =>'',
    // 'PARENT_ID_TYPE' =>'', // int
    // 'PARENT_ID_CARD' =>'',
    // 'PARENT_MOBILE'  =>'',
    // 'PARENT_ADRESS'  =>'',
    // 'PARENT_NAME'    =>'',
  ]; */
  function userAdd($uid,Array $req=[]) {
    $id_no = $req['ID_NO'];
    $req['HOS_ID']      = $this->hosp;
    $req['PARENT_TYPE'] = 1;
    $req['ID_TYPE']     = 1;
    $req['PASSWORD']    = substr($id_no,-6);

    $ret = $this->his->curl([1002,'createpat'],$this->user,$req);
    // SUC : HOSP_PATIENT_ID
    $pid = $ret['HOSP_PATIENT_ID'];
    // $pid = 'L00289807';
    // 保存HIS用户
    $save = Util::arr2Low($req);
    $save['hosp_parient_id'] = $pid;
    $this->addHospUser($uid,$save);
    return $pid;
  }
  // 添加医院用户 存在则覆盖
  private function addHospUser($uid,$save) {
    $l    = new HospUserLogic;
    $info = $l->getInfo(['uid'=>$uid]);
    if($info){
      $id = $info['id'];
      return ['type'=>'save','count'=>$l->save(['id'=>$id],$save)];
    }else{
      $save['uid'] = $uid;
      return ['type'=>'add','id'=>$l->add($save)];
    }
  }


  // 查询用户信息
  // 医院根据APP传入的参数返回用户在院内的信息。如果存在一个证件对应对个用户档案的情况，医院根据实际请求筛选一个档案返回给APP。
  /**$req = [
      'HOS_ID'=>'123456',
      'HOSP_PATIENT_ID'=>$pid,
      // 'ID_TYPE'=>'', // int
      // 'ID_NO' =>'',
      'NAME'=>'周舟',
      'SEX' =>1, // int
      'BIRTHDAY'=>'1990-08-09', // datetime
      // 'PARENT_ID_TYPE'=>'',  // int
      // 'PARENT_ID_CARD'=>'',
    ];*/
  function userInfo($req) {
    $req['HOS_ID'] = $this->hosp;
    $ret = $this->his->curl([1003,'getpatientinfo'],$this->user,$req);
    // SUC : CREATE_TIME HOSP_PATIENT_ID CARD_INFO[CARD_NO,(i)CARD_TYPE,(i)CARD_STATUS,CARD_TIME,LAST_TIME] ADDRESS MOBILE
    return $ret;
  }

  //医院信息
  function hospInfo() {
    $req = [
      'HOS_ID'  => $this->hosp,
    ];
    $ret = $this->his->curl([1004,'gethosinfo'],$this->user,$req);
    // SUC : HOS_ID,NAME,SHORT_NAME,ADDRESS,TEL,WEBSITE,WEIBO,(i)LEVEL,DESC,SPECTAL,LONGITUDE,LATITUDE,(i)MAX_REG_DAYS,START_REG_TIME,END_REG_TIME,STOP_BOOK_TIMEA,STOP_BOOK_TIMEP
    return $ret;
  }

  //科室同步
  // APP可通过该接口获取医院科室信息更新，查询有排班科室。当科室ID (DEPT_ID) 为-1时查询所有科室信息，为0时查询所有一级科室信息，为其他的时查本科室以及所有子科室信息。
  /**$req = [
      'HOS_ID' =>$hid,
      'DEPT_ID'=>$cid, //-1/0/string
    ]; */
  function deptSync($dept_id='-1',$sync=0) {
    $req = [
      'HOS_ID'  => $this->hosp,
      'DEPT_ID' => $dept_id,
    ];
    $ret = $this->his->curl([2001,'getdeptinfo'],$this->user,$req);
    !isset($ret['DEPT_LIST']) && throws('HIS信息异常');
    // SUC : HOS_ID,DEPT_LIST[DEPT_ID,DEPT_NAME,DARENT_ID,DESC,EXPERTISE,(i)LEVEL,ADDRESS,(int)STATUS]
    $list = isset($ret['DEPT_LIST']) ? $ret['DEPT_LIST'] : [];
    //查询全部同步到本地 - 慢
    if($sync && $dept_id=='-1'){
      $ids  = [];$adds = [];
      foreach ($list as $v) {
        $sect = new HospSectLogic;
        $temp = $v['DEPT_ID'];
        $where = [
          'dept_id'=>$temp
        ];
        $save = [];
        $save['dept_name'] = $v['DEPT_NAME'];
        $save['parent_id'] = $v['PARENT_ID'];
        $save['desc']      = $v['DESC'];
        $save['expertise'] = $v['EXPERTISE'];
        $save['level']     = (int) $v['LEVEL'];
        $save['address']   = $v['ADDRESS'];
        $r = $sect->getInfo($where,false,'id');
        if($r){// 修改
          // $id = $r['id'];
          $sect->save($where,$save);
          // if($temp=='67'){
          //   dump($save);
          //   dump(\think\Db::getLastSql());
          // }
        }else{// 添加
          $temp = (int) $v['STATUS']; // 1:正常,2:注销
          $save['status'] = in_array($temp,[0,1]) ? 1 :0; //1:开放,0:关闭
          $adds[] = array_merge($save,$where);
        }
        $ids[] = $temp;
      }

      $sect = new HospSectLogic;
      $adds && $sect->addAll($adds);
      // 删除其他
      if($ids){
        $sect->getModel()->where('dept_id','not in',$ids)->delete();
        // 删除科室关联
        // (new HospSectRelationLogic)->where('dept_id','not in',$ids)->delete();
      }else{
        $sect->getModel()->delete();
        // 删除科室关联
        // (new HospSectRelationLogic)->delete();
      }
    }
    return $list;
  }

  // 医生同步
  // todo : 注册医生 和 baichuanid
    // APP可通过该接口获取医生信息更新，查询排班的医生。
    //   当科室ID（DEPT_ID）为-1时查询所有科室下的医生；
    //   当医生ID（DOCTOR_ID）为-1，科室ID（DEPT_ID）不为-1时查询该科室下所有医生；
    //   当医生ID（DOCTOR_ID）不为-1时，查询该医生详细信息。
    // $req = [
    //   'HOS_ID'    =>$hid,
    //   'DEPT_ID'   =>$cid,
    //   'DOCTOR_ID' =>$did
    // ];
  function doctSync($dept_id='-1',$doctor_id=-1,$sync=0) {
    // $req['HOS_ID'] = $this->hosp;
    $req = [
      'HOS_ID'    =>$this->hosp,
      'DEPT_ID'   =>$dept_id,
      'DOCTOR_ID' =>$doctor_id,
    ];
    $ret = $this->his->curl([2002,'getdoctorinfo'],$this->user,$req);
    // SUC :HOS_ID,DOCTOR_LIST[DEPT_ID,DOCTOR_ID,NAME,IDCARD,DESC,SPECIAL,JOB_TITLE,(i)REG_FEE,(i)STATUS,(i)SEX,BIRTHDAY,MOBILE,TEL]
    $list = isset($ret['DOCTOR_LIST']) ? $ret['DOCTOR_LIST'] : [];

    //同步到本地 - 慢
    if($sync && $dept_id=='-1' && $doctor_id=='-1'){
      $ids  = [];
      foreach ($list as $v) {
        $sect = new HospDoctLogic;
        $where = [
          'dept_id'  =>$v['DEPT_ID'],
          'doctor_id'=>$v['DOCTOR_ID'],
        ];
        $save = [];
        $save['name']      = $v['NAME'];
        $save['idcard']    = $v['IDCARD'];
        $save['reg_fee']   = (int) $v['REG_FEE'];
        $save['status']    = (int) $v['STATUS'];
        $save['sex']       = (int) $v['SEX'];
        $save['birthday']  = $v['BIRTHDAY'];
        $save['mobile']    = $v['MOBILE'];
        $save['tel']       = $v['TEL'];

        if($v['DESC'] ){
          $save['desc']      = $v['DESC'];
        }
        if($v['SPECIAL']){
          $save['special']   = $v['SPECIAL'];
        }
        if($v['JOB_TITLE']){
          $save['job_title'] = $v['JOB_TITLE'];
        }
        $r = $sect->getInfo($where,false,'*');
        if($r){// 修改
          $id = $r['id'];
          $sect->save(['id'=>$id],$save);
          // ? 事先已设置了本系统用户
          $temp_uid = (int) $r['uid'];
          if($temp_uid){ // 更新用户实名/实名认证状态
            // 系统用户信息
            $uinfo = (new UserProfileLogic)->getInfo(['uid'=>$temp_uid]);
            $uinfo = $uinfo ? $uinfo->toArray() : [];
            $temp = [];
            if($save['name'] && $save['name'] != $uinfo['realname']){
              $temp['realname'] = $save['name'];
            }
            if($save['name'] && !$uinfo['identity_validate']){
              $temp['identity_validate'] = 1;
            }
            // dump($temp);
            // dump($uinfo);
            $temp && (new UserProfileLogic)->save(['uid'=>$temp_uid],$temp);
          }
        }else{// 添加
          $add = array_merge($save,$where);
          $add['uid'] = 0;
          $id = $sect ->add($add);
          // todo : 注册医生 + 百川id
          // set user_id
        }
        $ids[] = $id;
      }
      $sect = new HospDoctLogic;
      // 删除其他
      if($ids){
        $sect->getModel()->where('id','not in',$ids)->delete();
      }else{
        $sect->getModel()->delete();
      }
      // ? 删除医院关联doctor_id=>did 见下方科室同步
    }
    return $ret;
  }

  // 医生一段时间的排班
  // $req = [
  //   'DEPT_ID'    =>$cid,
  //   'DOCTOR_ID'  =>$did,
  //   'START_DATE' =>$start,//datetime
  //   'END_DATE'   =>$end,//datetime
  // ];
  function regInfo($req){
    $req['HOS_ID'] = $this->hosp;
    $ret = $this->his->curl([2003,'getreginfo'],$this->user,$req);
    // SUC :HOS_ID,DEPT_ID,REG_DOCTOR_LIST[DOCTOR_ID,NAME,JOB_TITLE,REG_LIST[REG_DATE,REG_WEEKDAY,REG_TIME_LIST[REG_ID,TIME_FLAG,REG_STATUS,TOTAL,OVER_COUNT,REG_LEVEL,REG_FEE,TREAT_FEE,ISTIME]]]
    return $ret;
  }


  // his 预挂号
  /** 用户通过APP挂号，生成订单，调用HIS接口将订单同步到HIS。
   *   对于给本人挂号的，必须要填写患者身份证号码信息，取号的时候需要出示患者的身份证；对于给子女挂号的，患者身份证件号码允许为空（此种情况适用于患者是孩子还没有身份证件），取号的时候出示大人(挂号人)或者子女(被监护人)的身份证，都允许取号。
   *   对于给本人及他人挂号的，必须要填写患者姓名、身份证号码、手机号码信息，取号的时候需要出示患者的身份证；
   *   对于给没有身份证小孩挂号的，患者身份证件号码允许为空，必须填写患者姓名、手机号码及监护人身份证号码，取号的时候出示本人(挂号人)或者子女(被监护人)的身份证，都允许取号。
   *   挂号接口增加传入医院内部用户ID号，如果APP方传空，则表示医院在挂号成功后，必须返回此医院内部用户ID号（没有查到用户的需要注册用户后返回）；如果APP方不为空，则医院根据传入的用户ID去挂号。*/
   /**$req = [
      'ORDER_ID'            =>'',
      // 'HOSP_PATINENT_ID' =>'',
      'CHANNEL_ID'          =>'',
      'IS_REG'              =>'', //int,1-当天挂号 2-预约挂号（直接挂号） 3-预约挂号（锁号挂号）
      'REG_ID'              =>'',
      // 'REG_LEVEL'        =>'', //int,1-普通 2-专家 3-急诊
      // 'DEPT_ID'          =>'',
      // 'DOCTOR_ID'        =>'',
      // 'REG_DATE'         =>'', //datetime
      // 'TIME_FLAG'        =>'',//int
      // 'BEGIN_TIME'       =>'',
      // 'END_TIME'         =>'',
      'REG_FEE'             =>'',//int
      'TREAT_FEE'           =>'', //int
      'REG_TYPE'            =>'',//int
      // 'IDCARD_TYPE'      =>'',//int
      // 'IDCARD_NO'        =>'',
      // 'CARD_TYPE'        =>'',//int
      // 'CARD_NO'          =>'',
      'NAME'                =>'',
      'SEX'                 =>'',//int
      'BIRTHDAY'            =>'',//datetime
      // 'ADDRESS'          =>'',
      // 'MOBILE'           =>'',
      'OPER_IDCARD_TYPE'    =>'',//int
      'OPER_IDCARD_NO'      =>'',
      'OPER_NAME'           =>'',
      'OPER_MOBILE'         =>'',
      // 'AGENT_ID'         =>'',
      'ORDER_TIME'          =>'',//datetime
    ];
   */
  function orderReg($req){
    $req['HOS_ID'] = $this->hosp;
    $ret = $this->his->curl([2007,'orderreg'],$this->user,$req);
    // SUC :HOSP_ORDER_ID,HOSP_PATIENT_ID,HOSP_SERIAL_NUM,HOSP_MEDICAL_NUM,HOSP_GETREG_DATE,HOSP_SEE_DECT_ADDR,HOSP_CARD_NO,HOSP_REMARK,IS_CONCESSIONS,CONCESSIONS[CONCESSIONS_FEE,REAL_REG_FEE,REAL_TREAT_FEE,REAL_TOTAL_FEE,CONCESSIONS_TYPE]
    return $ret;
  }

  // 取消挂号
  /** 在用户取消挂号时，调用该接口。未付费挂号成功后需要取消时调用。
   *   此种情况适用于用户已挂号成功但未支付的情况，对于挂号成功且30分钟内没有支付的情况，APP自动取消挂号。
   *   如果在APP调用医院取消接口，医院订单已经取消成功的情况下，医院应该直接返回取消成功。
   */
  /** $req = [
      'ORDER_ID'=>'',
      'CANCEL_REMARK'=>'不想开挂',
    ];*/
  function cancelReg($req) {
    $req['HOS_ID']      = $this->hosp;
    $req['CANCEL_DATE'] = date('Y-m-d H:i:s');
    $ret = $this->his->curl([2009,'cancelreg'],$this->user,$req);
    // SUC :
    return $ret;
  }

  // 挂号支付
  /** 挂号成功后调用。 医院his根据APP订单号（挂号接口有传入）进行挂号订单的支付操作。
   *   用户在支付时，APP将支付结果同步到APP（支付仅限30分钟内提交的订单）。
   */
  /**$req = [
      'ORDER_ID'        =>'',
      'SERIAL_NUM'      =>'',
      'PAY_CHANNEL_ID'  =>'',
      'PAY_TOTAL_FEE'   =>'',//int
      'PAY_COPE_FEE'    =>'', //int
      'PAY_FEE'         =>'',      //int
      // 'PAY_RES_CODE' =>'',
      // 'PAY_RES_DESC' =>'',
      // 'MERCHANT_ID'  =>'',
      // 'TERMINAL_ID'  =>'',
      // 'BANK_NO'      =>'',
      // 'PAY_ACCOUNT'  =>'',
    ];*/
  function payReg($req){
    $req['HOS_ID']   = $this->hosp;
    !isset($req['PAY_DATE']) && $req['PAY_DATE'] = date('Y-m-d');
    !isset($req['PAY_TIME']) && $req['PAY_TIME'] = date('H:i:s');
    $ret = $this->his->curl([2008,'payreg'],$this->user,$req);
    // SUC :HOSP_PAY_ID,HOSP_SERIAL_NUM,HOSP_MEDICAL_NUM,HOSP_GETREG_DATE,HOSP_SEE_DOCT_ADDR,HOSP_REMARK
    return $reg;
  }

  /**
   * his 挂号取消且退款
   * 2010 refund
   * 用户挂号并支付成功后需要取消挂号并退款时，调用该接口
   *   如果在APP调用医院退款接口，医院订单已经退款成功的情况下，医院应该直接返回退款成功。
   */   /**$req = [
      'ORDER_ID'          =>'',
      'HOSP_ORDER_ID'     =>'',
      'REFUND_ID'         =>'',
      'REFUND_SERIAL_NUM' =>'',
      'TOTAL_FEE'         =>'',  //int
      'REFUND_FEE'        =>'', //int
      'REFUND_DATE'       =>'',
      'REFUND_TIME'       =>'',
      'REFUND_RES_CODE'   =>'',
      'REFUND_RES_DESC'   =>'',
      'REFUND_REMARK'     =>'',
    ];*/
  function refund($req){
    $req['HOS_ID'] = $this->hosp;
    $ret = $this->his->curl([2010,'refund'],$this->user,$req);
    // SUC :HOSP_REFUND_ID,REFUND_FLAG(0/失败,1/成功我方执行退款,2/院内已退款)
    return $reg;
  }
  /** his 挂号记录
   * 挂号记录查询接口，同步订单状态，医院返回相应的订单结果。起止时间为挂号的就诊日期。
   *   该接口适用于当订单在医院发生取号、取消、退款等操作时，APP能确保和医院的状态保持一致。*/
  // $req = [
  //   'ORDER_ID'=>'',
  //   'BEGIN_DATE'=>'',//DATETIME
  //   'END_DATE'=>'',//DATETIME
  //   'PAGE_CURRENT'=>'',//INT
  //   'PAGE_SIZE'=>'',//INT
  // ];
  function regList($req) {
    $req['HOS_ID'] = $this->hosp;
    $ret = $this->his->curl([2012,'queryRegRecord'],$this->user,$req);
    // SUC : COUNT,ORDER_LIST[ORDER_ID,ORDER_STAUS,HOSP_SERIAL_NUM,GET_REGNO_DATE,CANCEL_DATE]
    return $ret;
  }

  /** his 检查列表查询
   * 证件号码或卡号获取用户的检验报告记录
   *   如果用证件号码查询，医院有多个账户，医院需要根据传入的用户信息刷选出最适合的用户返回。
   *   开始和结束查询时间为1-3个月。
   * $req = [
      // 'HOSP_PATIENT_ID'=>''
      // 'PATIENT_IDCARD_TYPE'=>'',//INT
      // 'PATIENT_IDCARD_NO'=>'',
      // 'PATIENT_CARD_TYPE'=>'',//INT
      // 'PATIENT_CARD_NO'=>'',
      'PATIENT_NAME'=>'',
      'PATIENT_SEX'=>'', // INT
      'PATIENT_AGE'=>'', // INT
      'BEGIN_DATE'=>'',//datetime
      'END_DATE'=>'',//DATETIME
    ];
   */
  function getCheckList(){
    $req['HOS_ID'] = $this->hosp;
    $ret = $this->his->curl([8001,'getcheckoutreportlist'],$this->user,$req);
    // SUC : HOS_ID,HOSP_PATIENT_ID,(i)PATIENT_IDCARD_TYPE,PATIENT_IDCARD_NO,PATIENT_CARD_TYPE,PATIENT_CARD_NO,PATIENT_NAME,(i)PATIENT_SEX,(i)PATIENT_AGE,(i)VISIT_NUMBER,MEDICAL_INSURANNCE_TYPE,REPORT_LIST[REPORT_INFO[REPORT_ID,DIAGNOSIS,ITEM_NAME,SPECIMEN_NAME,SPECIMEN_ID,REPORT_TIME,DEPT_NAME,DOCTOR_NAME,(i)REPORT_TYPE,REMARK]]
    // REPORT_TYPE[0普通检验/1药敏检验/2检查报告]
    return $ret;
  }


  /**
   * his 检验报告查询(普通报告)
   * 检验单号，获取用户的普通检验报告记录
   */
  /*$req = [
    'REPORT_ID'=>'',
    // 'HOSP_PATIENT_ID'=>''
  ];*/
  function getCheckNormal($req){
    $req['HOS_ID'] = $this->hosp;
    $ret = $this->his->curl([8002,'getnormalreportinfo'],$this->user,$req);
    // SUC : HOS_ID,REPORT_INFO[HOSP_PATIENT_ID,(i)PATIENT_IDCARD_TYPE,PATIENT_IDCARD_NO,PATIENT_CARD_TYPE,PATIENT_CARD_NO,PATIENT_NAME,(i)PATIENT_SEX,(i)PATIENT_AGE,(i)VISIT_NUMBER,MEDICAL_INSURANNCE_TYPE,DIAGNOSIS,ITEM_NAME,SPECIMEN_NAME,SPECIMEN_ID,REPORT_TIME,DEPT_NAME,DOCTOR_NAME,REVIEW_NAME,REVIEW_TIME,REMARK],CHECK_LIST[DETAIL[CHECK_NAME,RESULT,UNIT,NORMAL_FLAG,REFERENCE_VALUE,DESC]]
    return $ret;
  }
  /**
   * his 检验报告查询(药敏检验)
   * 检验单号，获取用户的药物过敏检验报告记录
   */
  /*$req = [
    'REPORT_ID'=>'',
    // 'HOSP_PATIENT_ID'=>''
  ];*/
  function getCheckDrug($req){
    $req['HOS_ID'] = $this->hosp;
    $ret = $this->his->curl([8003,'getdurgreportinfo'],$this->user,$req);
    // SUC : HOS_ID,REPORT_INFO[HOSP_PATIENT_ID,(i)PATIENT_IDCARD_TYPE,PATIENT_IDCARD_NO,PATIENT_CARD_TYPE,PATIENT_CARD_NO,PATIENT_NAME,(i)PATIENT_SEX,(i)PATIENT_AGE,(i)VISIT_NUMBER,MEDICAL_INSURANNCE_TYPE,DIAGNOSIS,ITEM_NAME,SPECIMEN_NAME,SPECIMEN_ID,REPORT_TIME,DEPT_NAME,DOCTOR_NAME,REVIEW_NAME,REVIEW_TIME,REMARK],REPORT_DETAIL[CHECK_NAME,DRUG_LIST[DRUG_INFO[DRUG_NAME,DRUG_ENGLIST_NAME,MIC,SENSITIVITY,DESC]]]
    return $ret;
  }
  /**
   * his 检查报告查询
   * 检查单号，获取用户的检查报告记录
   */
  /*$req = [
    'REPORT_ID'=>'',
    // 'HOSP_PATIENT_ID'=>''
  ];*/
  function getCheckInfo($req){
    $req['HOS_ID'] = $this->hosp;
    $ret = $this->his->curl([8004,'getcheckreportinfo'],$this->user,$req);
    // SUC : HOS_ID,HOSP_PATIENT_ID,(i)PATIENT_IDCARD_TYPE,PATIENT_IDCARD_NO,(i)PATIENT_CARD_TYPE,PATIENT_CARD_NO,PATIENT_NAME,PATIENT_SEX,(i)PATIENT_AGE,(i)VISIT_NUMBER,MEDICAL_INSURANNCE_TYPE,SPECIMEN_NAME,SPECIMEN_ID,ITEM_NAME,COMPLAINT,DIAGNOSIS,SEEN,CONTENT,REPORT_TIME,DEPT_NAME,DOCTOR_NAME,REVIEW_NAME,REVIEW_TIME,REMARK
    return $ret;
  }
}