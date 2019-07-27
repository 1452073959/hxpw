<?php /*a:2:{s:72:"/www/wwwroot/www.hxpw1998.com/application/admin/view/usedlist/index.html";i:1563518780;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
.dingw{padding: 10px;}
/*.pdingw{position:absolute;top:22px;left:0}*/
</style>

  <!-- 导出完成 -->
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-form">

            <blockquote class="layui-elem-quote news_search">
                <div class="layui-inline">
                  
                </div>     
                 <div class="layui-input-inline" style="margin-left: 100px;">
                   <form method="post" action="<?php echo url('usedlist/index'); ?>" class="form-signin" enctype="multipart/form-data">
                    <input type="text" name="search" placeholder="请按项目编号搜索" autocomplete="off" class="layui-input">
                    <input type="submit" value="搜索" class="layui-btn layui-btn-sm daorus">
                  </form>
                 </div>         
                <!-- <a class="layui-btn layui-btn-sm" title="辅材空模板.xls" style="float:right;margin-left:10px" href="/Template/人工工费空模板 .xls">下载空模板</a> -->
<!--                 <a class="layui-btn layui-btn-sm" style="float:right;" onclick="exportExcel()">导出excel</a> -->
                <!-- <a id="tishi" title="点我查看" style="float:right;" > <i class="layui-icon">&#xe702;</i></a> -->
            </blockquote>
            <script type="text/html" id="toolbarDemo">
              <div class="layui-btn-container">
                <!-- <button class="layui-btn layui-btn-sm" lay-event="getCheckData" id="batch">批量修改数据</button> -->
                <!-- <button class="layui-btn layui-btn-sm" lay-event="getCheckLength">获取选中数目</button> -->
                <!-- <button class="layui-btn layui-btn-sm" lay-event="isAll">验证是否全选</button> -->
              </div>
            </script>
            <table class="layui-table" lay-filter="test3"  lay-data="{id: '#test3', toolbar: '#toolbarDemo',limit:20,page: true}">
                <colgroup>
                    <col width="80">
                    <col width="80">
                    <col width="160">
                </colgroup>
                <thead >
                    <tr>
                        <!-- <th class="taleft" lay-data="{fixed:'left', type: 'checkbox'}">选择框</th> -->
                        <th class="taleft" lay-data="{field:'id',width:100}">项目编号</th>
                        <th class="taleft" lay-data="{field:'itemcode',width:100}">项目编号</th>
                        <th class="taleft" lay-data="{field:'frameid',width:100}">项目名称</th>
                       <?php $__FOR_START_1943090012__=1;$__FOR_END_1943090012__=21;for($i=$__FOR_START_1943090012__;$i < $__FOR_END_1943090012__;$i+=1){ ?>  
                        <th class="taleft" lay-data="{field:'worktype<?php echo htmlentities($i); ?>',width:110}">辅材名称<?php echo htmlentities($i); ?></th>
                        <th class="taleft" lay-data="{field:'title<?php echo htmlentities($i); ?>',width:110 }">辅材基数<?php echo htmlentities($i); ?></th>
                       <?php } ?>
                        <!-- <th class="taleft" lay-data="{field:'company',}">单位</th>
                        <th class="taleft" lay-data="{field:'labor_cost',}">人工成本</th>   -->                 
                        <th width="150" lay-data="{fixed:'right',field:'edit',width:100,event: 'setSign'}">操作</th>
                    </tr>
                </thead>
                <tbody> 
                  <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): if( count($data)==0 ) : echo "" ;else: foreach($data as $key=>$vo): ?>
                    <tr>
                     <td><?php echo htmlentities($vo['id']); ?></td> 
                     <td><?php echo htmlentities($vo['item_number']); ?></td> 
                     <td><?php echo htmlentities($vo['project']); ?></td> 
                     <?php if(is_array($vo['content']) || $vo['content'] instanceof \think\Collection || $vo['content'] instanceof \think\Paginator): if( count($vo['content'])==0 ) : echo "" ;else: foreach($vo['content'] as $key=>$v): ?>
                       <td><?php echo htmlentities($v[0]); ?></td> 
                       <td><?php echo htmlentities($v[1]); ?></td> 
                     <?php endforeach; endif; else: echo "" ;endif; ?>
                     <td><a class="layui-btn layui-btn-xs layui-btn-normal">编辑</a></td> 
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
          var box = '<div class="batchs"><span data-fieldval="name">辅材名称</span><br /><span data-fieldval="units">单位</span><br /><span data-fieldval="phr">辅材用量</span><br /><span data-fieldval="price">辅材用量价格</span><br /><span data-fieldval="remarks">辅材备注</span><br /></div>';

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




  //监听单元格编辑
  // table.on('edit(test3)', function(obj){
  //   var value = obj.value //得到修改后的值
  //   ,data = obj.data //得到所在行所有键值

  //   ,field = obj.field; //得到字段
  //   // layer.msg('[ID: '+ data.id +'] ' + field + ' 字段更改为：'+ value);
  //   // alert('[ID: '+ data.id +'] ' + field + ' 字段更改为：'+ value);
  //   $.ajax({
  //       type : 'post',
  //       url  :  '<?php echo url("offerlist/singlefield_edit"); ?>',
  //       dataType : 'json',
  //       data : {id:data.id,field:field,value:value},
  //       success : function(re){
  //         if(re.status == 1){
  //            layer.msg(re.msg);return false;
  //         }
  //         layer.msg(re.msg);
  //         // window.location.reload();
  //       }
  //      });
  // });



  //监听单元格修改事件
  table.on('tool(test3)', function(obj){
    var data = obj.data;
    if(obj.event === 'setSign'){
      // alert(data.id+'---'+data.item_number+'---'+data.project);
      var inputval = '';
       for (var i = 1; i < 21; i++) {
         inputval +='<div class="layui-form-item"><label class="layui-form-label">辅材名称'+i+'</label><div class="layui-input-inline w300"><input class="dingw" type="text" name="" autocomplete="off" value="'+data['worktype'+i]+'" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">辅材基数'+i+'</label><div class="layui-input-inline w300"><input class="dingw" type="text" name="" autocomplete="off" value="'+data['title'+i]+'" class="layui-input"></div></div>';
       }
        


      var inputs = '<div class="bigbox">'+inputval+'<button id="edit_ajax" style="margin-left:190px" class="layui-btn ajax-post">立即提交</button></div>';

     
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
            var bigbox = '';
            $(".bigbox input").each(function(){
                 bigbox += $(this).val()+',';
              });
            bigbox = bigbox.substring(0,bigbox.length-1);
            // alert(bigbox);return false;

            $.ajax({
              type : 'post',
              url  :  '<?php echo url("usedlist/ajaxedits"); ?>',
              dataType : 'json',
              data : {id:data.id,bigbox:bigbox},
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
      
    }
  });


});
</script>

</body>

</html>