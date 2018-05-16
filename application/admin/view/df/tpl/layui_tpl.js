<script type="text/html" id="showTpl">
  <form class="layui-form">
  <input type="checkbox" data-id="{{ d.id }}"
  {{# if(d.show){ }} checked  {{#  } }}
  lay-skin="switch">
  </form>
</script>

<script type="text/html" id="barDemo">
<div class="layui-btn-group">
  <a href="{:url(CONTROLLER_NAME.'/set',[],'')}?id={{ d.id }}" class="layui-btn layui-btn-sm"><i class="layui-icon layui-icon-edit"></i>编辑</a>
  <a href="{:url(CONTROLLER_NAME.'/del',[],'')}?id={{ d.id }}" class="layui-btn layui-btn-sm layui-btn-danger ajax-get confirm no-alert"><i class="layui-icon layui-icon-delete"></i>删除</a>
</div>
</script>

<script type="text/html" id="urlTpl">
  <a href="{:url(CONTROLLER_NAME.'/index',[],'')}?parent={{ d.id }}" class="layui-table-link">{{ d.id }}</a>
</script>

<script type="text/html" id="timeTpl">
  {{# if(d.reg_time>0){ }}
  {{ new Date((d.reg_time)*1000).format('yyyy/MM/dd hh:mm:ss') }}
  {{# } }}
</script>

<script type="text/html" id="imgTpl">
  <img src="{:config('avatar_url')}?uid={{ d.id }}&size=60" layer-src="{:config('avatar_url')}?uid={{ d.id }}"  class="layui-nav-img js-view">  {{ d.name }}
</script>