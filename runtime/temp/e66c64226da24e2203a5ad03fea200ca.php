<?php /*a:2:{s:75:"/www/wwwroot/www.hxpw1998.com/application/admin/view/offerlist/history.html";i:1563960961;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
.form-control{display: inline-block;padding: 5px;}
/*.text-limit{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;max-width: 110px;}*/
	.text-center{text-align: center !important;}
</style>

  <!-- 导出完成 -->
<div class="layui-card">
    <div class="layui-card-body">
        <div class="">
            <blockquote class="layui-elem-quote news_search">
                <form class="layui-inline">
					<a class="layui-btn layui-btn-sm" id="export" href = "<?php echo url('admin/offerlist/excel_export',['customerid'=>input('customerid'),'report_id'=>input('report_id')]); ?>">导出报表</a>
              <button class="layui-btn layui-btn-sm" type="button" data-fun="print" id="btn_export">打印</button>
                    <a class="layui-btn layui-btn-sm" href="<?php echo url('Offerlist/edit',['id'=>$edit_id]); ?>">新建报表</a>
											 
					<select class="form-control" id="changestatus" data-id="<?php echo htmlentities($data['id']); ?>" data-customerid="<?php echo htmlentities($data['customerid']); ?>">
						<option>选择标签</option>
						<option value="0">未报价</option>
						<option value="1">已报价</option>
						<option value="2">预算价</option>
						<option value="3">合同价</option>
						<option value="4">结算价</option>
					</select>
					<?php switch($data['status']): case "0": ?><a class="layui-btn layui-btn-xs layui-btn-danger">未报价</a><?php break; case "1": ?> <a class="layui-btn layui-btn-xs layui-btn-danger">已报价</a><?php break; case "2": ?><a class="layui-btn layui-btn-xs layui-btn-danger">预算价</a><?php break; case "3": ?> <a class="layui-btn layui-btn-xs layui-btn-danger">合同价</a><?php break; case "4": ?> <a class="layui-btn layui-btn-xs layui-btn-danger">结算价</a><?php break; default: ?>
						<a class="layui-btn layui-btn-xs layui-btn-danger">未报价</a>
					<?php endswitch; ?>
				</form>
            </blockquote>
			<div id="export-table">
				<?php if(!(empty($data) || (($data instanceof \think\Collection || $data instanceof \think\Paginator ) && $data->isEmpty()))): ?>
				<table class="layui-table">
					<thead>
						<tr>
							<th rowspan="2" colspan="2"><img style="max-width:200px;" src="/static/imgs/logo.png"></th>
							<th class="text-center text-large" colspan="5"><h3>住宅装饰工程造价预算书</h3></th>
							<th rowspan="2" colspan="2"></th>
					    </tr>
					    <tr>
							<th class="text-center" colspan="5">全国统一24小时客服热线：400-6281-968</th>
					    </tr>
						<tr>
							<th style="text-align:center;" colspan="9">单位：<?php echo htmlentities($data['unit']); ?></th>        
						</tr>
						<tr>
							<th colspan="3">工程名称：<?php echo htmlentities($data['address']); ?></th>       
							<th colspan="3">客户姓名：<?php echo htmlentities($data['customer_name']); ?></th>       
							<th colspan="2">设计师姓名：<?php echo htmlentities($data['designer_name']); ?></th>       
							<th colspan="2">报价师姓名：<?php echo htmlentities($data['quoter_name']); ?></th>       
						</tr>
						<tr>      
                            <th rowspan="2" colspan="1">序号</th>
							<th class="text-center" rowspan="2">工程项目名称</th>         
							<th class="text-center" rowspan="2">工程量</th>       
							<th class="text-center" rowspan="2">单位</th>
							<th class="text-center" colspan="2">辅材费</th> 
							<th class="text-center" colspan="2">人工费</th>    
							<th class="text-center" rowspan="2">施工工艺及材料说明</th> 
						</tr>
						<tr>   
							<th class="text-center" colspan="1">辅材基价</th>       
							<th class="text-center">辅材合价</th>       
							<th class="text-center">人工基价</th>       
							<th class="text-center">人工合价</th> 
						  </tr>
					</thead>
					<tbody> 
						<?php if(!(empty($data['details']) || (($data['details'] instanceof \think\Collection || $data['details'] instanceof \think\Paginator ) && $data['details']->isEmpty()))): if(is_array($data['details']) || $data['details'] instanceof \think\Collection || $data['details'] instanceof \think\Paginator): if( count($data['details'])==0 ) : echo "" ;else: foreach($data['details'] as $key=>$vos): ?>
							<tr data-cate="tr<?php echo htmlentities($key); ?>">
								<td><?php echo htmlentities($vos['conditions_no']); ?></td>
								<td colspan="2"><?php echo htmlentities($vos['conditionsname']); ?></td>
								<td colspan="7"></td>
							</tr>
							<?php if(is_array($vos['son']) || $vos['son'] instanceof \think\Collection || $vos['son'] instanceof \think\Paginator): if( count($vos['son'])==0 ) : echo "" ;else: foreach($vos['son'] as $k=>$v): ?>
								<tr id="tr<?php echo htmlentities($k); ?>">
                                    <td><?php echo htmlentities($v['room_no']); ?></td>
									<td class="text-center" colspan="9"><?php echo htmlentities($v['roomname']); ?></td>
								</tr>
								<?php if(is_array($v['item']) || $v['item'] instanceof \think\Collection || $v['item'] instanceof \think\Paginator): if( count($v['item'])==0 ) : echo "" ;else: foreach($v['item'] as $kk=>$vv): ?>
									<tr class="tr<?php echo htmlentities($key); ?><?php echo htmlentities($k); ?>">
                                      	<td><?php echo htmlentities($kk * 1 + 1); ?></td>
										<td ><?php echo htmlentities($vv['project']); ?></td>
										<td><?php echo htmlentities($vv['gcl']); ?></td>
										<td><?php echo htmlentities($vv['company']); ?></td>
										<td><?php echo htmlentities($vv['quota']); ?></td>
										<td><?php echo htmlentities($vv['quota'] * $vv['gcl']); ?></td>
										<td><?php echo htmlentities($vv['craft_show']); ?></td>
										<td><?php echo htmlentities($vv['craft_show'] * $vv['gcl']); ?></td>
										<td class="text-limit"><?php echo htmlentities($vv['material']); ?></td>
										<input type="hidden" form="myform" name="quote[<?php echo htmlentities($key); ?>][<?php echo htmlentities($k); ?>][]" value="<?php echo htmlentities($vv['id']); ?>">
									</tr>
								<?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
									<tr class="tr<?php echo htmlentities($key); ?>total">
										<td class="text-center" colspan="2">小计</td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
						<?php endforeach; endif; else: echo "" ;endif; endif; ?>
					</tbody>
				</table>
				<?php endif; ?>
			</div>
        </div>
    </div>
</div>

    
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<!-- 打印 -->
<script src="/static/bootstrap/js/table/printThis.min.js"></script>
<script>
  $(document).ready(function(){
    
    $('tr').each(function(){
    	var cate = $(this).attr('data-cate');
        if(cate){
          var fd_total = 0,rd_total = 0;
             $('tr[class*='+cate+']').each(function(){
             	var fd = $(this).find('td').eq(4).html() * 1;//辅材合价
             	var rd = $(this).find('td').eq(6).html() * 1;//人工合价
               fd_total += fd;
               rd_total += rd;
             });
          $('.'+cate+'total').find('td').eq(4).html(Math.round(fd_total * 100)/100);
          $('.'+cate+'total').find('td').eq(6).html(Math.round(rd_total * 100)/100);
         }
    });
    
     //打印
      $(document).on('click','[data-fun="print"]',function(){
          $("table").printThis({
             debug: false,
             importCSS: true,
             importStyle: true,
             printContainer: true,
    //       loadCSS: "/Content/Themes/Default/style.css",
             pageTitle: "姓名查询汇总表",
             removeInline: false,
             printDelay: 333,
             header: null,
             formValues: false
           });
      })
		//改变报表标签
    $(document).on('change','#changestatus',function(){
      var id= $(this).attr('data-id'),customerid = $(this).attr('data-customerid'),status = $(this).val();
      // mystatus(id,customerid);
      $.ajax({
          type : 'post',
          url  :  '<?php echo url("offerlist/status"); ?>',
          dataType : 'json',
          data : {id:id,customerid:customerid,status:status},
          success : function(re){
            console.log(re);
            // if(re.status == 0){
               alert(re.msg);
               window.location.reload();
            // }else{
            //   alert(re.msg);return false;
            // }
          }
        });
    });
		
		// $('#export').click(function(){
		// 	window.location.href = "<?php echo url('admin/offerlist/excel_export',['customerid'=>input('customerid'),'report_id'=>input('report_id')]); ?>";
		// });
    
  });
</script>

</body>

</html>