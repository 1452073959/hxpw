<?php /*a:2:{s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/anbank/index.html";i:1564017394;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
#imgs{position:relative;}
.batchs span{cursor:pointer;}
</style>

  <!-- 导出完成 -->
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-form">

            <blockquote class="layui-elem-quote news_search">
                <div class="layui-inline">
                    <!-- <a class="layui-btn layui-btn-sm" href="<?php echo url('Anbank/add'); ?>">添加辅材{/static}</a> -->
                    <form method="post" action="<?php echo url('Anbank/ImportExcel'); ?>" class="form-signin" enctype="multipart/form-data" style="float: right;margin:0 18px;position:relative;">
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
                    </form>
                </div>     
                 <div class="layui-input-inline" style="margin-left: 100px;">
                   <form method="get" action="<?php echo url('Anbank/index'); ?>" class="form-signin" enctype="multipart/form-data">
                            


                    <select name="frameid" lay-verify="required"  lay-filter="business">
                                 <option value=""></option>
                              <?php foreach($frame as $key=>$vo): ?>
                                 <option value="<?php echo htmlentities($vo['id']); ?>" <?php if(input('frameid') == $vo['id']): ?>selected<?php endif; ?>><?php echo htmlentities($vo['name']); ?></option>
                              <?php endforeach; ?>
                    </select>
                    <input type="text" name="search" placeholder="请按辅材名称搜索" autocomplete="off" class="layui-input" value="<?php echo input('search'); ?>">
                    <input type="submit" value="搜索" class="layui-btn layui-btn-sm daorus">
                  </form>
                 </div>         
                <a class="layui-btn layui-btn-sm" title="辅材空模板.xls" style="float:right;margin-left:10px" href="/Template/辅材仓库空模板1.xls">下载空模板</a>
             <!-- <a class="layui-btn layui-btn-sm" style="float:right;" onclick="exportExcel()">导出excel</a> -->
                <a id="tishi" title="点我查看" style="float:right;" > <i class="layui-icon">&#xe702;</i></a>
            </blockquote>
            <script type="text/html" id="toolbarDemo">
              <div class="layui-btn-container">
                <button class="layui-btn layui-btn-sm" lay-event="getCheckData" id="batch">批量修改数据</button>
                <a class="layui-btn layui-btn-sm" href="<?php echo url('Anbank/add'); ?>">添加条目</a> 
             <!--  <button class="layui-btn layui-btn-sm" lay-event="getCheckLength">导出</button> -->
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
                        <th class="taleft" lay-data="{field:'id',fixed:'left', type: 'checkbox'}">选择框</th>
                        <th class="taleft" lay-data="{field:'amcode',}">辅材编码</th>
                        <th class="taleft" lay-data="{field:'frameid',}">所属公司</th>
                        <th class="taleft" lay-data="{field:'category',}">工种类别</th>
                        <th class="taleft" lay-data="{field:'fine', }">辅材细类</th>
                        <th class="taleft" lay-data="{field:'brand',}">品牌</th>
                        <th class="taleft" lay-data="{field:'place',}">产地</th>
                        <th class="taleft" lay-data="{field:'name',width:200}">辅材名称</th>
                        <th class="taleft" lay-data="{field:'img'}">图片</th>
                        <th class="taleft" lay-data="{field:'units'}">单位</th>
                        <th class="taleft" lay-data="{field:'price',edit:'text'}">单价</th>
                        <th class="taleft" lay-data="{field:'norms',}">规格</th>
                        <th class="taleft" lay-data="{field:'phr',width:180}">包装规格</th>
                        <th class="taleft" lay-data="{field:'remarks', }">供应来源</th>                        
                        <th width="100" lay-data="{field:'edit',event: 'setSign'}">修改操作</th>
                        <th width="100" lay-data="{field:'del',event: 'setdel'}">删除操作</th>
                    </tr>
                </thead>
                <tbody> 
                  <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): if( count($data)==0 ) : echo "" ;else: foreach($data as $key=>$vo): ?>
                    <tr>
                      <td class="taleft"><?php echo htmlentities($vo['id']); ?></td>    
                      <td class="taleft"><?php echo htmlentities($vo['amcode']); ?></td>    
                      <td class="taleft"><?php echo getcid($vo['frameid']);?></td>   
                      <td class="taleft"><?php echo htmlentities($vo['category']); ?></td>    
                      <td class="taleft"><?php echo htmlentities($vo['fine']); ?></td>    
                      <td class="taleft"><?php echo htmlentities($vo['brand']); ?></td>    
                      <td class="taleft"><?php echo htmlentities($vo['place']); ?></td>    
                      <td class="taleft"><?php echo htmlentities($vo['name']); ?></td>    
                      <td class="taleft"><?php echo htmlentities($vo['img']); ?></td>    
                      <td class="taleft"><?php echo htmlentities($vo['units']); ?></td>  
                      <td class="taleft"><?php echo htmlentities($vo['price']); ?></td>  
                      <td class="taleft"><?php echo htmlentities($vo['norms']); ?></td>  
                      <td class="taleft"><?php echo htmlentities($vo['phr']); ?></td>                                     
                      <td class="taleft"><?php echo htmlentities($vo['remarks']); ?></td>                                     
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
  //简单显示上传文件信息
     $("#imgInput").change(function(e){
       var fileMsg = e.currentTarget.files;
       var fileName = fileMsg[0].name;
       var fileSize = Math.floor(((fileMsg[0].size)/1024))+'kb';     
       var fileType = fileMsg[0].type;
       var _html = '<p>文件名：'+fileName+'</p><p>文件大小：'+fileSize+'</p><p>文件类型：'+fileType+'</p>'
         layer.tips(_html, '#imgs', {
          tips: [1, '#3595CC'],
          area:["auto","auto"],
          time: 5000
        });
       $("#imgs").html("<span style='color:green;position:absolute;top:-25px;right:-5px;font-size:20px'>✔</span>");
    });      
    //定义提示信息 
    $("#tishi").click(function(){
      var content = '<p>这是我定义的说明<br />只能导入excel文件。其他后缀名不识别<br />下载空模板可以使用添加数据导入</p>';
      //自定页
      layer.tips(content, '#tishi', {
          tips: [4, '#3595CC'],
          area:["auto","auto"],
          time: 5000
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
         var hezi = "";
         for (var i = 0; i < data.length; i++) {
           hezi += jsonObj[i]['id']+',';
         }
         hezi = hezi.substring(0, hezi.length - 1);//移除最后一个逗号
        // layer.alert(hezi);return false;  
          var box = '<div class="batchs"><span data-fieldval="category">工种类别</span><br /><span data-fieldval="fine">辅材细类</span><br /><span data-fieldval="brand">品牌</span><br /><span data-fieldval="place">产地</span><br /><span data-fieldval="name">辅材名称</span><br /><span data-fieldval="units">单位</span><br /><span data-fieldval="norms">规格</span><br /><span data-fieldval="phr">包装规格</span><br /><span data-fieldval="price">单价</span><br /><span data-fieldval="remarks">辅材备注</span><br /></div>';

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
                  url  :  '<?php echo url("Anbank/batchedit"); ?>',
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




  //监听单元格编辑
  table.on('edit(test3)', function(obj){
    var value = obj.value //得到修改后的值
    ,data = obj.data //得到所在行所有键值

    ,field = obj.field; //得到字段
    // layer.msg('[ID: '+ data.id +'] ' + field + ' 字段更改为：'+ value);
    // alert('[ID: '+ data.id +'] ' + field + ' 字段更改为：'+ value);
    $.ajax({
        type : 'post',
        url  :  '<?php echo url("Anbank/singlefield_edit"); ?>',
        dataType : 'json',
        data : {id:data.id,field:field,value:value},
        success : function(re){
          if(re.status == 1){
             layer.msg(re.msg);return false;
          }
          layer.msg(re.msg);
          // window.location.reload();
        }
       });
  });

  //监听行单击事件（单击事件为：rowDouble）
  // table.on('rowDouble(test3)', function(obj){
  //   var data = obj.data;
  //   var newdata = JSON.stringify(data);

  //   layer.alert(newdata, {
  //     title: '当前行数据：'
  //   });
    
  //   //标注选中样式
  //   obj.tr.addClass('layui-table-click').siblings().removeClass('layui-table-click');
  // });

  //监听单元格修改事件
  table.on('tool(test3)', function(obj){
    var data = obj.data;
    if(obj.event === 'setSign'){
      // alert(data.id+'---'+data.item_number+'---'+data.project);
      var inputs = '<div class="bigbox"><input type="text" name="category" autocomplete="off" value="'+data.category+'" class="layui-input"><input type="text" name="fine" autocomplete="off" value="'+data.fine+'" class="layui-input"><input type="text" name="brand" autocomplete="off" value="'+data.brand+'" class="layui-input"><input type="text" name="place" autocomplete="off" value="'+data.place+'" class="layui-input"><input type="text" name="name" autocomplete="off" value="'+data.name+'" class="layui-input"><input type="text" name="units" autocomplete="off" value="'+data.units+'" class="layui-input"><input type="text" name="price" autocomplete="off" value="'+data.price+'" class="layui-input"><input type="text" name="norms" autocomplete="off" value="'+data.norms+'" class="layui-input"><input type="text" name="phr" autocomplete="off" value="'+data.phr+'" class="layui-input"><input type="text" name="remarks" autocomplete="off" value="'+data.remarks+'" class="layui-input"><button id="edit_ajax" style="margin-left:190px" class="layui-btn ajax-post">立即提交</button></div>';
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
            var category = $(this).parents(".bigbox").find("input[name='category']").val();
            var fine = $(this).parents(".bigbox").find("input[name='fine']").val();
            var brand = $(this).parents(".bigbox").find("input[name='brand']").val();
            var place = $(this).parents(".bigbox").find("input[name='place']").val();
            var name = $(this).parents(".bigbox").find("input[name='name']").val();
            var units = $(this).parents(".bigbox").find("input[name='units']").val();
            var price = $(this).parents(".bigbox").find("input[name='price']").val();
            var norms = $(this).parents(".bigbox").find("input[name='norms']").val();
            var phr = $(this).parents(".bigbox").find("input[name='phr']").val();
            var remarks = $(this).parents(".bigbox").find("input[name='remarks']").val();
            if (!category || !fine || !brand || !name || !units || !price || !norms || !phr || !remarks) {layer.msg('信息不能为空');return false;}
            // alert(data.id+''+type_of_work+'--'+project+'--'+company+'--'+cost_value+'--'+quota+'--'+craft_show+'--'+material);return false;
            $.ajax({
              type : 'post',
              url  :  '<?php echo url("Anbank/ajaxedits"); ?>',
              dataType : 'json',
              data : {category:category,fine:fine,brand:brand,place:place,name:name,units:units,price:price,norms:norms,phr:phr,remarks:remarks,id:data.id},
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
                url  :  '<?php echo url("Anbank/delete"); ?>',
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