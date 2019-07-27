<?php /*a:2:{s:75:"/www/wwwroot/www.hxpw1998.com/application/admin/view/auxiliary/history.html";i:1564023050;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
.ulall{overflow:auto;}
.ulall li{float:left;border: 1px solid #ccc;padding: 5px;margin-right: 20px;}
</style>

  <!-- 导出完成 -->
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-form">

            <blockquote class="layui-elem-quote news_search">
                <div class="layui-inline">
                   <ul class="ulall">
                      <li>客户姓名：<?php echo htmlentities($data['customer_name']); ?></li>
                      <li>工程地址：<?php echo htmlentities($data['address']); ?></li>
                      <li>联系电话：</li>
                      <li>报价师姓名：<?php echo htmlentities($data['quoter_name']); ?></li>
                      <li>设计师姓名：<?php echo htmlentities($data['designer_name']); ?></li>
                   </ul>
                </div>     
              
            </blockquote>
            <script type="text/html" id="toolbarDemo">
              <div class="layui-btn-container">
                <!-- <button class="layui-btn layui-btn-sm" lay-event="getCheckData" id="batch">批量修改数据</button> -->
                <!-- <button class="layui-btn layui-btn-sm" lay-event="getCheckLength">获取选中数目</button> -->
                <!-- <button class="layui-btn layui-btn-sm" lay-event="isAll">验证是否全选</button> -->
              </div>
            </script>
            <table class="layui-table" lay-filter="test3"  lay-data="{id: '#test3',toolbar:'#',limit:20,page:true}">
                <colgroup>
                    <col width="80">
                    <col width="80">
                    <col width="160">
                </colgroup>
                <thead >
                    <tr>
                        <!-- <th class="taleft" lay-data="{type: 'checkbox', fixed: 'left'}">ID</th> -->
                        <!-- <th class="taleft" lay-data="{field:'id', width:80,}">ID</th> -->
                        <th class="taleft" lay-data="{field:'customer_name',width:180}">辅材编码</th>
                        <th class="taleft" lay-data="{field:'address',width:250}">辅材名称</th>
                        <th class="taleft" lay-data="{field:'quoter_name',event: 'setSign',}">图片</th>
                        <th class="taleft" lay-data="{field:'designer_name', }">单位</th>
                        <th class="taleft" lay-data="{field:'proquant', }">数量</th>
                        <th class="taleft" lay-data="{field:'direct_cost', }">单价</th>
                        <th class="taleft" lay-data="{field:'matquant', }">总价</th>

                    </tr>
                </thead>
                <tbody> 
                  <?php if(is_array($arrs) || $arrs instanceof \think\Collection || $arrs instanceof \think\Paginator): $i = 0; $__LIST__ = $arrs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$kkk): $mod = ($i % 2 );++$i;?> 
                    <tr>
                        <td class="taleft"><?php echo htmlentities($kkk['amcode']); ?></td>
                        <td class="taleft"><?php echo htmlentities($kkk[0]); ?></td>
                        <td class="taleft">暂无<?php echo count($arrs); ?></td>
                        <td class="taleft"><?php echo htmlentities($kkk['units']); ?></td>
                        <td class="taleft"><?php echo htmlentities($kkk['number']); ?></td>
                        <td class="taleft"><?php echo htmlentities($kkk['price']); ?></td>
                        <td class="number taleft"><?php echo htmlentities($kkk['total']); ?></td>
                    </tr>
                  <?php if($key * 1 + 1 == count($arrs)): ?>
                  <tr>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td class="taleft">领料总价</td>
                      <td class='taleft total'></td>
                  </tr>
                  <?php endif; endforeach; endif; else: echo "" ;endif; ?>  

                <!--  <?php if(is_array($newarr) || $newarr instanceof \think\Collection || $newarr instanceof \think\Paginator): if( count($newarr)==0 ) : echo "" ;else: foreach($newarr as $key=>$vo): if(is_array($vo['content']) || $vo['content'] instanceof \think\Collection || $vo['content'] instanceof \think\Paginator): if( count($vo['content'])==0 ) : echo "" ;else: foreach($vo['content'] as $key=>$kkk): ?> 
                    <tr>
                        <td class="taleft"><?php echo htmlentities($kkk['amcode']); ?></td>
                        <td class="taleft"><?php echo htmlentities($kkk[0]); ?></td>
                        <td class="taleft">暂无</td>
                        <td class="taleft"><?php echo htmlentities($kkk['units']); ?></td>
                        <td class="taleft"><?php echo htmlentities($kkk['number']); ?></td>
                        <td class="taleft"><?php echo htmlentities($kkk['price']); ?></td>
                        <td class="taleft"><?php echo htmlentities($kkk['total']); ?></td>
                    </tr>
                   <?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>  -->
                </tbody>
            </table>
              
        </div>
    </div>
</div>

    
<script type="text/javascript" src="/static/admin/js/common.js"></script>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>

<script type="text/javascript">
      $(function(){
            var _total = 0;
      		$('td.number').each(function(){
            	_total += $(this).html() * 1;
            });
            $('td.total').html(_total);
      });
//是否报价
  function mystatus($id,$customerid){
     layer.confirm('确认选择该版本？', {
            btn : [ '确定', '取消' ]//按钮
            ,icon: 3,
        }, function(index) {
            // layer.msg($id);return false;
            $.ajax({
                type : 'post',
                url  :  '<?php echo url("offerlist/status"); ?>',
                dataType : 'json',
                data : {id:$id,customerid:$customerid},
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

   };
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
                  url  :  '<?php echo url("offerlist/batchedit"); ?>',
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