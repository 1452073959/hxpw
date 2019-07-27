<?php /*a:2:{s:74:"/www/wwwroot/www.hxpw1998.com/application/admin/view/artificial/index.html";i:1563786664;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>报价后台管理系统</title>
    <meta name="author" content="YZNCMS">
    <link rel="stylesheet" href="/static/layui/css/layui.css">
    <link rel="stylesheet" href="/static/admin/css/admin.css">
    <link rel="stylesheet" href="/static/admin/font/iconfont.css">
    <script src="/static/layui/layui.js"></script>
</head>

<body class="childrenBody">
    
<style type="text/css">
/*分页样式*/
.pagination{text-align:center;margin-top:20px;margin-bottom: 20px;}
.pagination li{margin:0px 10px; border:1px solid #e6e6e6;padding: 3px 8px;display: inline-block;}
.pagination .active{background-color: #dd1a20;color: #fff;}
.pagination .disabled{color:#aaa;}
.layui-form-select{float:left;margin-right:10px}
.onfile {
    position: relative;
    display: inline-block;
    background: #D0EEFF;
    border: 1px solid #99D3F5;
    border-radius: 4px;
    padding: 4px 12px;
    overflow: hidden;
    color: #1E88C7;
    text-decoration: none;
    text-indent: 0;
    line-height: 20px;
}
.onfile input {
    position: absolute;
    font-size: 20px;
    right: 0;
    top: 0;
    opacity: 0;
}
.onfile:hover {
  opacity:0.8;
  color: #1E88C7
   /* background: #AADFFD;
    border-color: #78C3F3;
    color: #fff;
    text-decoration: none;*/
}
.daoru{position:absolute;top:1px;right:-90px;}
.daorus{position:absolute;top:1px;right:-50px;height:35px}
.bigbox{padding:10px}
.bigbox input{margin-bottom:10px}
#imgs{position:relative;float:left;}
.batchs span{cursor:pointer;}
</style>

  <!-- 导出完成 -->
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-form">

            <blockquote class="layui-elem-quote news_search">
                <div class="layui-inline">
                    <!-- <a class="layui-btn layui-btn-sm" href="<?php echo url('Artificial/add'); ?>">添加辅材{/static}</a> -->
                   <!--  <form method="post" action="<?php echo url('Artificial/ImportExcel'); ?>" class="form-signin" enctype="multipart/form-data" style="float: right;margin:0 18px;position:relative;">
                        <select name="frameid" lay-verify="required"  lay-filter="business">
                              <option value=""></option>
                              <?php foreach($frame as $key=>$vo): ?>
                                 <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['name']); ?></option>
                              <?php endforeach; ?>
                        </select>
                        <a href="javascript:;" class="onfile">选择文件
                          <input name="excel" type="file" class="" id="imgInput">
                        </a>
                        <div id="imgs"></div>
                          <input type="submit" value="导入Excel" class="layui-btn layui-btn-sm daoru">
                    </form> -->
                </div>     
                 <div class="layui-input-inline" style="margin-left: 100px;">
                   <form method="post" action="<?php echo url('Artificial/index'); ?>" class="form-signin" enctype="multipart/form-data">
                    <input type="text" name="search" placeholder="请按项目编号搜索" autocomplete="off" class="layui-input">
                    <input type="submit" value="搜索" class="layui-btn layui-btn-sm daorus">
                  </form>
                 </div>         
                <!-- <a class="layui-btn layui-btn-sm" title="辅材空模板.xls" style="float:right;margin-left:10px" href="/Template/人工工费空模板 .xls">下载空模板</a> -->
                <a class="layui-btn layui-btn-sm" style="float:right;" onclick="exportExcel()">导出excel</a>
                <!-- <a id="tishi" title="点我查看" style="float:right;" > <i class="layui-icon">&#xe702;</i></a> -->
            </blockquote>
            <script type="text/html" id="toolbarDemo">
              <div class="layui-btn-container">
                <button class="layui-btn layui-btn-sm" lay-event="getCheckData" id="batch">批量修改数据</button>
<!--                 <button class="layui-btn layui-btn-sm" lay-event="getCheckLength">导出</button> -->
                <!-- <button class="layui-btn layui-btn-sm" lay-event="isAll">验证是否全选</button> -->
              </div>
            </script>
            <table class="layui-table" lay-filter="test3"  lay-data="{id: '#test3', toolbar: '#toolbarDemo',defaultToolbar:['filter', 'print'],limit:20}">
                <colgroup>
                    <col width="80">
                    <col width="80">
                    <col width="160">
                </colgroup>
                <thead >
                    <tr>
                        <th class="taleft" lay-data="{fixed:'left', type: 'checkbox'}">选择框</th>
                        <th class="taleft" lay-data="{field:'id',}">ID</th>
                        <th class="taleft" lay-data="{field:'item_number',}">项目编号</th>
                        <th class="taleft" lay-data="{field:'frameid',}">所属公司</th>
                        <th class="taleft" lay-data="{field:'type_of_work',}">工种</th>
                        <th class="taleft" lay-data="{field:'project', }">项目名称</th>
                        <th class="taleft" lay-data="{field:'company',}">单位</th>
                        <th class="taleft" lay-data="{field:'labor_cost',edit: 'text'}">人工成本</th>                   
                        <th width="150" lay-data="{field:'edit',event: 'setSign',width:100}">修改操作</th>
                        <th width="150" lay-data="{field:'del',event: 'setdel',width:100}">操作删除</th>
                    </tr>
                </thead>
                <tbody> 
                  <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): if( count($data)==0 ) : echo "" ;else: foreach($data as $key=>$vo): ?>
                    <tr>
                      <td class="taleft"><?php echo htmlentities($vo['id']); ?></td>    
                      <td class="taleft"><?php echo htmlentities($vo['id']); ?></td>    
                      <td class="taleft"><?php echo htmlentities($vo['item_number']); ?></td>    
                      <td class="taleft"><?php echo getcid($vo['frameid']);?></td>    
                      <td class="taleft"><?php echo htmlentities($vo['type_of_work']); ?></td>    
                      <td class="taleft"><?php echo htmlentities($vo['project']); ?></td>    
                      <td class="taleft"><?php echo htmlentities($vo['company']); ?></td>    
                      <td class="taleft"><?php echo htmlentities($vo['labor_cost']); ?></td>                                        
                      <td>
                      <a class="layui-btn layui-btn-xs layui-btn-normal">编辑</a>
                      </td>
                      <td><a class="layui-btn layui-btn-danger layui-btn-xs ajax-get confirm" >删除</a></td>
                    </tr>       
                 <?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
                
       <?php echo $data->render(); ?>
        </div>
    </div>
</div>

    
<script type="text/javascript" src="/static/admin/js/common.js"></script>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
    layui.use('table', function(){
        var table = layui.table;
        table.on('exportExcel(test3)', function(obj){
            console.log(obj)
        });
});
</script>
<script type="text/javascript">
layui.use('table', function(){
  var table = layui.table;

   //工具栏事件
  table.on('toolbar(test3)', function(obj){
    var checkStatus = table.checkStatus(obj.config.id);
    var data = checkStatus.data;
    // var shujuzu = JSON.stringify(data);//json数据组
    // layer.alert(JSON.stringify(data));return false;  
    if (obj.event == 'getCheckData'){
      if(data.length == 0){
          layer.msg('请勾√选数据');
       }else{
         var jsonObj = JSON.parse(JSON.stringify(data));//转换为json对象
         // alert(JSON.stringify(data));return false;
         var hezi = "";
         for (var i = 0; i < data.length; i++) {
           hezi += jsonObj[i]['id']+',';
         }
         hezi = hezi.substring(0, hezi.length - 1);//移除最后一个逗号
        // layer.alert(hezi);return false;  
          var box = '<div class="batchs"><span data-fieldval="type_of_work">工种</span><br /><span data-fieldval="project">项目名称</span><br /><span data-fieldval="company">单位</span><br /><span data-fieldval="labor_cost">人工成本</span></div>';

        //自定页
        layer.tips(box, '#batch', {
            tips: [2, '#3595CC'],
            area:["auto","auto"],
            time: 5000
          });

        $('.batchs span').click(function(){
            var fieldval = $(this).data('fieldval');
            var fieldtext = $(this).text();
            var newcon = '<div class="zidauns"><input type="text" style="text-align:center;margin:10px;width:96%" name="valname" autocomplete="off" value="" class="layui-input"><button id="edit_batch" style="margin-left:190px" class="layui-btn ajax-post">立即提交</button></div>';
            //页面层
            layer.open({
              type: 1,
              fixed: false,    //取消固定定位，因为固定定位是相对body的
              offset: ['20%', '30%'],   //相对定位
              skin: 'layui-layer-rim', //加上边框
              area: ['500px', '150px'], //宽高
              title: '输入修改<span style="color:green">'+fieldtext+'</span>字段内容',
              content: newcon
            }); 
            $("#edit_batch").click(function(){
                var xiugais = $(this).parents(".zidauns").find("input[name='valname']").val();
                if (!xiugais){layer.msg('字段不能为空');return false}
                // alert(fieldval+'--'+xiugais+'-----'+hezi);return false;
                $.ajax({
                  type : 'post',
                  url  :  '<?php echo url("Artificial/batchedit"); ?>',
                  dataType : 'json',
                  data : {batchname:fieldval,value:xiugais,idarray:hezi},
                  success : function(re){
                    if(re.status == 0){
                       layer.msg(re.msg);
                       window.location.reload();

                    }else{
                      layer.msg(re.msg);return false;
                    }
                  }
                 });
            });

        });
 
       }
    }

  });



  // 监听单元格编辑
  table.on('edit(test3)', function(obj){
    var value = obj.value //得到修改后的值
    ,data = obj.data //得到所在行所有键值

    ,field = obj.field; //得到字段
    // layer.msg('[ID: '+ data.id +'] ' + field + ' 字段更改为：'+ value);
    // alert('[ID: '+ data.id +'] ' + field + ' 字段更改为：'+ value);
    $.ajax({
        type : 'post',
        url  :  '<?php echo url("Artificial/singlefield_edit"); ?>',
        dataType : 'json',
        data : {id:data.id,field:field,value:value},
        success : function(re){
          if(re.status == 1){
             layer.msg(re.msg);return false;
          }
          layer.msg(re.msg);
          window.location.reload();
        }
       });
  });

    //监听单元格事件
  table.on('tool(test3)', function(obj){
    var data = obj.data;
    if(obj.event === 'setSign'){
      // alert(data.id+'---'+data.item_number+'---'+data.project);
      var inputs = '<div class="bigbox"><input type="text" name="type_of_work" autocomplete="off" value="'+data.type_of_work+'" class="layui-input"><input type="text" name="project" autocomplete="off" value="'+data.project+'" class="layui-input"><input type="text" name="company" autocomplete="off" value="'+data.company+'" class="layui-input"><input type="text" name="labor_cost" autocomplete="off" value="'+data.labor_cost+'" class="layui-input"><button id="edit_ajax" style="margin-left:190px" class="layui-btn ajax-post">立即提交</button></div>';
      //页面层
      layer.open({
        type: 1,
        fixed: false,    //取消固定定位，因为固定定位是相对body的
        offset: ['20%', '30%'],   //相对定位
        skin: 'layui-layer-rim', //加上边框
        area: ['500px', '450px'], //宽高
        title: '修改',
        content: inputs
      });

      //修改数据ajax;
         $("#edit_ajax").click(function() {
            var type_of_work = $(this).parents(".bigbox").find("input[name='type_of_work']").val();
            var project = $(this).parents(".bigbox").find("input[name='project']").val();
            var company = $(this).parents(".bigbox").find("input[name='company']").val();
            var labor_cost = $(this).parents(".bigbox").find("input[name='labor_cost']").val();
           
            if (!type_of_work || !project || !company || !labor_cost) {layer.msg('信息不能为空');return false;}
            // alert(data.id+''+type_of_work+'--'+project+'--'+company+'--'+cost_value+'--'+quota+'--'+craft_show+'--'+material);return false;
            $.ajax({
              type : 'post',
              url  :  '<?php echo url("Artificial/ajaxedits"); ?>',
              dataType : 'json',
              data : {type_of_work:type_of_work,project:project,company:company,labor_cost:labor_cost,id:data.id},
              success : function(re){
                if(re.status == 0){
                   layer.msg(re.msg);
                   window.location.reload();
                }else{
                  layer.msg(re.msg);return false;
                }
              }
             });
          });
      
    }else if (obj.event === 'setdel') {
       // alert(data.id);return false;
       layer.confirm('确认要删除吗,删除后不可恢复？', {
            btn : [ '确定', '取消' ]//按钮
            ,icon: 3,
        }, function(index) {
            // layer.msg($id);
            $.ajax({
                type : 'post',
                url  :  '<?php echo url("Artificial/delete"); ?>',
                dataType : 'json',
                data : {id:data.id},
                success : function(re){
                  if(re.status == 0){
                     layer.msg(re.msg);window.location.reload();
                  }else{
                    layer.msg(re.msg);return false;
                  }
                }
               });
            
        }); 
    }
  });









});
</script>

</body>

</html>