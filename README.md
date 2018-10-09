#rainbowPHP
===============
- author : rainbow
- email : 977746075@qq.com

#require
===============
- ThinkPHP5.1+ (PHP5.6+,..)
- uediter (admin)
- layui (admin: 模块css合并到screen,skin为风格相关)
- avalonjs (test)

#note
===============
#api-doc 路由需要修改(helper.php)

<!-- function alter(id) {
    var str = "{:url('Index/detail',['id'=>'pid'])}";
    var url=str.replace("pid",id);

    layer.open({
        title:"新增百度风云榜采集器(其它地址请勿添加)",
        type: 2,
        area: ['700px', '450px'],
        fixed: false, //不固定
        maxmin: true,
        content: url
    });
} -->

#TODO
===============
##other
- 安装参考 dolphinPhp的 install 模块
- db bak
- 文章采集
- ueditor添加图片删除

##admin
- tpl/ base_frame/set_form
- edit/add/return => layer
- user auth-wheretime
- throws 由index外放
- 顶部用户hover下拉z-index
- 全模板 no-refresh fresh-table
- query count=0 直接返回
- iframe内部登陆问题
- 主题切换
- 用户登陆权限 菜单权限 控制
- jsf:time-time选择
- 一对一关联保存获取
- dtpicker css延迟问题
- cmspost checkbox无改变