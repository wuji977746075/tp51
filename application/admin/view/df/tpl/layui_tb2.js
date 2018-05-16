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
    tb2.reload({
      initSort: obj
      ,where: {
        field: obj.field
        ,order: obj.type
        ,parent: parent
      }
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
</script>