<?php /*a:2:{s:78:"/www/wwwroot/www.hxpw1998.com/application/admin/view/bcontrast/singlelist.html";i:1562556579;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
.floatleft{float:left;}
#cunnei{overflow:auto;}
.lis{float:left;border-radius: 33px;background:#7c7cd2;padding: 5px 15px;color: #fff;}
.daoru{position:absolute;top:1px;right:-90px;}
.daorus{position:absolute;top:1px;right:-50px;height:35px}
.bigbox{padding:10px}
.bigbox input{margin-bottom:10px}
#imgs{position:relative;}
.batchs span{cursor:pointer;}
.form-control{display: inline-block;padding: 5px;}
</style>

  <!-- 导出完成 -->
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-form">

            <blockquote class="layui-elem-quote news_search">
                <div class="layui-inline">
  
                </div>     
             
								 <div class="layui-input-inline" style="margin-left: 100px;">
			
						</div>
                <a id="tishi" title="点我查看" style="float:right;" > <i class="layui-icon">&#xe702;</i></a>
            </blockquote>
            <script type="text/html" id="toolbarDemo">
              <div class="layui-btn-container">
                <button class="layui-btn layui-btn-sm" lay-event="getCheckData" id="batch">确认比较</button>
                <!-- <button class="layui-btn layui-btn-sm" lay-event="getCheckLength">获取选中数目</button>
                <button class="layui-btn layui-btn-sm" lay-event="isAll">验证是否全选</button> -->
              </div>
            </script>
            <table class="layui-table" lay-filter="test3"  lay-data="{id: '#test3' ,toolbar: '#toolbarDemo',limit:20}">
                <colgroup>
                    <col width="80">
                    <col width="80">
                    <col width="160">
                </colgroup>
                <thead >
                    <tr>
                        <th class="taleft" lay-data="{type: 'checkbox',field:'id', fixed: 'left'}">ID</th>
                        <!-- <th class="taleft" lay-data="{field:'id', width:80,event: 'setSign',}">ID</th> -->
                        <th class="taleft" lay-data="{field:'customer_name',width:100}">客户姓名</th>
												<th class="taleft" lay-data="{field:'status',width:100}">状态</th>
                        <th class="taleft" lay-data="{field:'address',width:250}">工程地址</th>
                        <th class="taleft" lay-data="{field:'manager_name',width:100}">客户经理</th>
                        <th class="taleft" lay-data="{field:'quoter_name',}">报价师姓名</th>
                        <th class="taleft" lay-data="{field:'designer_name',}">设计师姓名</th>
                        <!-- <th class="taleft" lay-data="{field:'proquant',width:100}">工程报价</th>
                        <th class="taleft" lay-data="{field:'direct_cost',width:100 }">工程直接费</th>
                        <th class="taleft" lay-data="{field:'matquant',width:100 }">辅材报价</th>
                        <th class="taleft" lay-data="{field:'manual_quota',width:100 }">人工报价</th>
                        <th class="taleft" lay-data="{field:'tubemoney',width:100,edit:'text' }">管理费</th>
                        <th class="taleft" lay-data="{field:'taxes',width:100,edit:'text' }">税金</th>
                        <th class="taleft" lay-data="{field:'discount',width:100,edit:'text' }">优惠</th>
                        <th class="taleft" lay-data="{field:'gross_profit',width:100 }">工程毛利</th>
                        <th class="taleft" lay-data="{field:'pro_apartment_type',edit:'text'}">工程户型</th> 
                        <th class="taleft" lay-data="{field:'area',edit:'text'}">面积</th>       
                        <th class="taleft" lay-data="{field:'entrytime',width:180}">录入时间</th> -->
                        <!-- <th width="150" lay-data="{field:'edit',width:150}">操作</th> -->
                    </tr>
                </thead>
                <tbody> 
                  
                  <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): if( count($data)==0 ) : echo "" ;else: foreach($data as $key=>$vo): ?>
                    <tr>   
                      <td class="taleft"><?php echo htmlentities($vo['id']); ?></td>    
                      <td class="taleft"><?php echo htmlentities($vo['customer_name']); ?></td>

											<td>
												<?php switch($vo['status']): case "0": ?><a class="layui-btn layui-btn-xs layui-btn-disabled">未报价</a><?php break; case "1": ?> <a class="layui-btn layui-btn-xs layui-btn-primary">已报价</a><?php break; case "2": ?><a class="layui-btn layui-btn-xs layui-btn-normal">预算价</a><?php break; case "3": ?> <a class="layui-btn layui-btn-xs layui-btn-warm">合同价</a><?php break; case "4": ?> <a class="layui-btn layui-btn-xs">结算价</a><?php break; default: ?>
														<a class="layui-btn layui-btn-xs layui-btn-danger">未报价</a>
												<?php endswitch; ?>
											</td>
                      <td class="taleft"><?php echo htmlentities($vo['address']); ?></td>   
                      <td class="taleft"><?php echo htmlentities($vo['manager_name']); ?></td> 
                      <td class="taleft"><?php echo htmlentities($vo['quoter_name']); ?></td>
                      <td class="taleft"><?php echo htmlentities($vo['designer_name']); ?></td>       
                    <!--   <td class="taleft"><?php echo htmlentities($vo['proquant']); ?></td>   
                      <td class="taleft"><?php echo htmlentities($vo['direct_cost']); ?></td>   
                      <td class="taleft"><?php echo htmlentities($vo['matquant']); ?></td>   
                      <td class="taleft"><?php echo htmlentities($vo['manual_quota']); ?></td>   
                      <td class="taleft"><?php echo htmlentities($vo['tubemoney']); ?></td>   
                      <td class="taleft"><?php echo htmlentities($vo['taxes']); ?></td>   
                      <td class="taleft"><?php echo htmlentities($vo['discount']); ?></td>   
                      <td class="taleft"><?php echo htmlentities($vo['gross_profit']); ?></td>   
                      <td class="taleft"><?php echo htmlentities((isset($vo['pro_apartment_type']) && ($vo['pro_apartment_type'] !== '')?$vo['pro_apartment_type']:'')); ?></td>
                      <td class="taleft"><?php echo htmlentities((isset($vo['area']) && ($vo['area'] !== '')?$vo['area']:'')); ?></td>
                      <td class="taleft"><?php echo htmlentities(date("Y-m-d H:s:i",!is_numeric($vo['entrytime'])? strtotime($vo['entrytime']) : $vo['entrytime'])); ?></td>    -->
                     <!-- <td class="taleft">
                        <a class="layui-btn layui-btn-xs layui-btn-warm" href="<?php echo url('admin/offerlist/history',['customerid'=>$vo['customerid']]); ?>">点击查看</a></td>  --> 
                      <!-- <td>
                       <a class="layui-btn layui-btn-xs" href="<?php echo url('admin/offerlist/edit',['id'=>$vo['id']]); ?>">进入管理</a>
                      </td> -->
                    </tr>       
                 <?php endforeach; endif; else: echo "" ;endif; ?>

                </tbody>
            </table>
            <div>
              <!-- <a class="layui-btn " href="">对比</a> -->
              <ul id="cunnei" class="" style="padding: 10px">
                 <!-- <li class="lis">1</li> -->
                 <!-- <li class="lis">2</li> -->
              </ul>
            </div>
        </div>
    </div>
</div>

    
<script type="text/javascript" src="/static/admin/js/common.js"></script>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>

<script type="text/javascript">
layui.use('table', function(){
  var table = layui.table;
  
  // //监听单元格事件
  // table.on('tool(test3)', function(obj){
  //   var data = obj.data;
  //   if(obj.event === 'setSign'){
  //       layer.msg(data.id);
  //       if ($('#cunnei li').length > 1){
  //          $('#cunnei li:first').remove();
  //          $("#cunnei").append('<li class="lis">'+data.id+'</li>');
  //       }else{
  //         $("#cunnei").append('<li class="lis">'+data.id+'</li>');
  //       }

        

  //   }
  // });



    //头工具栏事件
  table.on('toolbar(test3)', function(obj){
    var checkStatus = table.checkStatus(obj.config.id);
    switch(obj.event){
      case 'getCheckData':
        var data = checkStatus.data;
        if(data.length != 2){alert('请选择两个进行对比');return false;}
        var josnzu = JSON.parse(JSON.stringify(data));
        var _ht = '';
        for (var i = 0; i < 2; i++) {
          _ht += josnzu[i]['id']+',';
        }
        _ht = _ht.substring(0,_ht.length-1);

        // layer.alert(_ht);
        // 
        window.location.replace("<?php echo url('admin/Bcontrast/singleone'); ?>"+'?id='+_ht);




      break;
      case 'getCheckLength':
        var data = checkStatus.data;
        layer.msg('选中了：'+ data.length + ' 个');
      break;
      case 'isAll':
        layer.msg(checkStatus.isAll ? '全选': '未全选');
      break;
    };
  });






  // 监听单元格编辑
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
  //       data : {id:data.id,field:field,value:value,pro_apartment_type:data.pro_apartment_type,area:data.area},
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




});
</script>

</body>

</html>