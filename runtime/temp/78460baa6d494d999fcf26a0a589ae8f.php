<?php /*a:2:{s:73:"/www/wwwroot/www.hxpw1998.com/application/admin/view/auxiliary/index.html";i:1564021994;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
                    <!-- <a class="layui-btn layui-btn-sm" href="<?php echo url('Auxiliary/add'); ?>">新建客户</a> -->
                  <!--   <form method="post" action="<?php echo url('Auxiliary/ImportExcel'); ?>" class="form-signin" enctype="multipart/form-data" style="float: right;margin:0 18px;position:relative;">
                        <a href="javascript:;" class="onfile">选择文件
                          <input name="excel" type="file" class="" id="imgInput">
                        </a>
                        <div id="imgs"></div>
                          <input type="submit" value="导入Excel" class="layui-btn layui-btn-sm daoru">
                    </form> -->
                </div>     
                 <div class="layui-input-inline" style="margin-left: 100px;">
                   <form method="post" action="<?php echo url('Auxiliary/index'); ?>" class="form-signin" enctype="multipart/form-data">
                    <input type="text" name="search" placeholder="请按客户姓名搜索" autocomplete="off" class="layui-input">
                    <input type="submit" value="搜索" class="layui-btn layui-btn-sm daorus">
                  </form>
                 </div>         
                <!-- <a class="layui-btn layui-btn-sm" style="float:right;margin-left:10px" href="/Template/报价空模板.xls">下载空模板</a> -->
         <!--  <a class="layui-btn layui-btn-sm" style="float:right;" onclick="exportExcel()">导出excel</a> -->
                <a id="tishi" title="点我查看" style="float:right;" > <i class="layui-icon">&#xe702;</i></a>
            </blockquote>
            <script type="text/html" id="toolbarDemo">
              <div class="layui-btn-container">
                <!-- <button class="layui-btn layui-btn-sm" lay-event="getCheckData" id="batch">批量修改数据</button> -->
                <!-- <button class="layui-btn layui-btn-sm" lay-event="getCheckLength">获取选中数目</button> -->
                <!-- <button class="layui-btn layui-btn-sm" lay-event="isAll">验证是否全选</button> -->
              </div>
            </script>
            <table class="layui-table" lay-filter="test3"  lay-data="{id: '#test3',toolbar:'#',limit:20}">
                <colgroup>
                    <col width="80">
                    <col width="80">
                    <col width="160">
                </colgroup>
                <thead >
                    <tr>
                        <!-- <th class="taleft" lay-data="{type: 'checkbox', fixed: 'left'}">ID</th> -->
                        <th class="taleft" lay-data="{field:'id', width:80,}">ID</th>
                        <th class="taleft" lay-data="{field:'customer_name',width:100}">客户姓名</th>
                        <th class="taleft" lay-data="{field:'address',width:250}">工程地址</th>
                        <th class="taleft" lay-data="{field:'quoter_name',event: 'setSign',}">报价师姓名</th>
                        <th class="taleft" lay-data="{field:'designer_name', }">设计师姓名</th>
                        <!-- <th class="taleft" lay-data="{field:'proquant',width:100}">工程报价</th> -->
                       <!--  <th class="taleft" lay-data="{field:'direct_cost',width:100 }">工程直接费</th>
                        <th class="taleft" lay-data="{field:'matquant',width:100 }">辅材报价</th>
                        <th class="taleft" lay-data="{field:'manual_quota',width:100 }">人工报价</th>
                        <th class="taleft" lay-data="{field:'tubemoney',width:100,edit:'text' }">管理费</th>
                        <th class="taleft" lay-data="{field:'taxes',width:100,edit:'text' }">税金</th>
                        <th class="taleft" lay-data="{field:'discount',width:100,edit:'text' }">优惠</th>
                        <th class="taleft" lay-data="{field:'gross_profit',width:100 }">工程毛利</th>
                        <th class="taleft" lay-data="{field:'entrytime',width:180}">录入时间</th> -->
                        <th class="taleft" lay-data="{field:'history',width:150}">操作</th>
                        <!-- <th width="150" lay-data="{field:'edit',width:150}">操作</th> -->
                    </tr>
                </thead>
                <tbody> 
                  
                  <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): if( count($data)==0 ) : echo "" ;else: foreach($data as $key=>$vo): ?>
                    <tr>
                      <!-- <td class="taleft"><?php echo htmlentities($vo['id']); ?></td>     -->
                      <td class="taleft"><?php echo htmlentities($vo['id']); ?></td>    
                      <td class="taleft"><?php echo htmlentities($vo['customer_name']); ?></td>                          
                      <td class="taleft"><?php echo htmlentities($vo['address']); ?></td>    
                      <td class="taleft"><?php echo htmlentities($vo['quoter_name']); ?></td>
                      <td class="taleft"><?php echo htmlentities($vo['designer_name']); ?></td>       
                      <!-- <td class="taleft"><?php echo htmlentities($vo['proquant']); ?></td>    -->
                     <!--  <td class="taleft"><?php echo htmlentities($vo['direct_cost']); ?></td>   
                      <td class="taleft"><?php echo htmlentities($vo['matquant']); ?></td>   
                      <td class="taleft"><?php echo htmlentities($vo['manual_quota']); ?></td>   
                      <td class="taleft"><?php echo htmlentities($vo['tubemoney']); ?></td>   
                      <td class="taleft"><?php echo htmlentities($vo['taxes']); ?></td>   
                      <td class="taleft"><?php echo htmlentities($vo['discount']); ?></td>   
                      <td class="taleft"><?php echo htmlentities($vo['gross_profit']); ?></td>   
                      <td class="taleft"><?php echo htmlentities(date("Y-m-d H:s:i",!is_numeric($vo['entrytime'])? strtotime($vo['entrytime']) : $vo['entrytime'])); ?></td>  -->  
                      <td class="taleft">
                        <a class="layui-btn layui-btn-xs layui-btn-warm" href="<?php echo url('admin/Auxiliary/history',['id'=>$vo['id']]); ?>">点击查看</a></td>   
                     <!--  <td>
                       <a class="layui-btn layui-btn-xs" href="<?php echo url('admin/Auxiliary/edit',['id'=>$vo['id']]); ?>">进入管理</a>
                      </td> -->
                    </tr>       
                 <?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
              
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
          var box = '<div class="batchs"><span data-fieldval="type_of_work">工种</span><br /><span data-fieldval="project">工程项目</span><br /><span data-fieldval="company">单位</span><br /><span data-fieldval="cost_value">成本价值</span><br /><span data-fieldval="quota">报价定额</span><br /><span data-fieldval="craft_show">工艺说明</span><br /><span data-fieldval="material">工艺说明辅材明细</span></div>';

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
                  url  :  '<?php echo url("Auxiliary/batchedit"); ?>',
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
        url  :  '<?php echo url("Auxiliary/singlefield_edit"); ?>',
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

  //监听单元格事件
  table.on('tool(test3)', function(obj){
    var data = obj.data;
    if(obj.event === 'setSign'){
      layer.prompt({
        formType: 2
        ,title: '修改 ID 为 ['+ data.id +'] 的条目'
        ,value: data.type_of_work
      }, function(value, index){
        layer.close(index);
        
        //这里一般是发送修改的Ajax请求
        
        //同步更新表格和缓存对应的值
        obj.update({
          type_of_work: value
        });
      });
    }
  });


});
</script>

</body>

</html>