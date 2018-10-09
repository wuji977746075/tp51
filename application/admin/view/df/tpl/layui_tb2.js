<!-- script -->
<script>
var parent = parseInt({$parent|default=1});
// page init
layui.use(['rb','table2','element','form'], function(){
  var layer = layui.layer
  ,table = layui.table2
  ,form = layui.form


  ,rb = layui.rb
  ,$ = layui.$;
  rb.log('page','init');
  // form init
    //表单初始赋值
    //formTest 即 class="layui-form" 所在元素对应的 lay-filter="" 对应的值
  form.val("formTest", {
    "username": "贤心" // "name": "value"
    ,"sex": "女"
    ,"auth": 3
    ,"check[write]": true
    ,"open": false
    ,"desc": "我爱layui"
  })
  // table init
    // 默认查询条件
    // table的排序显示需要设 initObj
    // table的页码显示需要设 page:{ curr: 1 }
  rb.where = {
    start:"{$start ?: ''}"
    ,end:"{$end ?: ''}"
    ,kword:"{$kword ?: ''}"
    ,field: 'id'
    ,order: 'desc'
  };
    // 设置查询条件 每次点击搜索/排序时
  rb.setWhere = function (){
    // function setWhere(){
    rb.where.kword = $('#jsf-kword').val();
    rb.where.start = $('#jsf-start').val();
    rb.where.end   = $('#jsf-end').val();
  }
  // table render
  rb.table = table.render($.extend(rb.table_config, {
    url: '{:url(CONTROLLER_NAME."/ajax")}'
    ,cols:  [[
      {type: 'checkbox'} //,LAY_CHECKED: true,fixed: 'left'[高度bug]
      ,{field: 'id', title: 'ID', width: 80,sort:true }
      ,{field: 'name', title: '推广位 .',edit: 'text'}
      ,{field: 'pid', title: 'PID',templet: '#pidTpl'}
      ,{type: 'space', title: '推广码',width: 96,templet: '#inviteQrTpl'}
      ,{field: 'invite_url', title: '长链接',templet: '#inviteUrlTpl'}
      ,{type: 'space', title: '推广码',width: 96,templet: '#inviteSmQrTpl'}
      ,{field: 'invite_url_sm', title: '短链接',templet: '#inviteUrlSmTpl'}
      ,{field: 'token', title: '淘口令'}
      ,{field: 'create_time', title: '导入时间',templet:'#timeTpl',sort:true}
      // ,{field: 'to_uid', title: '分配', width: 80}
      ,{align:'left',title: '操作',toolbar: '#barDemo'} //,fixed: 'right'[行过高错位bug]
    ]]
    ,done: function(res, page, count){
      // 转换数据
      //异步 res为接口返回,直接赋值 res为：{data: [], count: 99}
      // console.log(res);
      // res.data[0].username = 'done-edit';
      // console.log(page,count);
      // res.count = 100;
    }
  });
  // 单元格自定义编辑
  table.on('tool(fDemo)', function(obj){
    console.log(obj);
    var data = obj.data;
    if(obj.event === 'go'){
      rb.goTo(obj.tr.find('a').attr('href'));
    }
  });

  // 单元格排序
  table.on('sort(fDemo)', function(obj){
    //this 当前排序的 th 对象
    //desc（降序）、asc（升序）、null（空对象，默认排序）
    setWhere();
    rb.where.field = obj.field;
    rb.where.order = obj.type;
    rb.table.reload({
      where:rb.where
      ,initSort: obj
    });
  });
  // 单元格编辑
  table.on('edit(fDemo)', function(obj){
    // console.log(obj); // value , field ,data
    $.post('{:url(CONTROLLER_NAME."/editOne")}', { field: obj.field,val: obj.value,id:obj.data.id }, function(data){
      rb.ajaxTip(data);
    });
  });
  // 单选
  form.on('switch', function(data){
    // console.log(data); //elem elem.checked value othis
    var el = data.elem;
    var id = parseInt(el.getAttribute('data-id'));
    rb.post(el,'{:url(CONTROLLER_NAME."/editOne")}',{ field: 'hide',val: el.checked ? 1:0,id:id },true);
  });
  // 搜索点击
  $('body').on('click', '#js-search', function(event) {
    setWhere();
    rb.table.reload({
      where: rb.where
      ,page: { curr : 1 }
    });
    event.preventDefault();
    return false;
  });
  // 自动 select选择查询
  form.on('select(search)', function(){
    $('#js-search').trigger('click');
  });
  // 手动 下拉选择查询
  form.on('select(jsf-send)', function(data){
    rb.where.send = data.value;
    rb.table.reload();
  });
  // {include file="df/tpl/index_extra" /}
  // bar错位 init bug
  $('#js-search').click();
  // bar错位 init bug
  rb.table.reload({
    where: rb.where
    ,page: {
      curr: 1
    }
  });
</script>