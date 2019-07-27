<?php /*a:2:{s:71:"/www/wwwroot/www.hxpw1998.com/application/admin/view/quote/preview.html";i:1563960556;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
.p_tex{line-height:38px;color:#666;}
.layui-form-label{width: 120px}
.layui-table-view .layui-table{width:100%}
.layui-form-label{width:40px}
.layui-form-item{margin:20px 0}
#frameid{    width: 250px;
    padding: 10px;}
.text-center{text-align: center !important;}
.bolder{font-weight: bolder;}
.text-large{font-size:24px!important;font-weight:900!important;}
.text-right{text-align: right !important;}
.text-left{text-align: left !important;}
.form-control{display: inline-block;padding: 5px;}
input[type="checkbox"]{
  display: inline-block!important;
}
.myinput{border:none;display: inline-block;background-color: #eeeeee;padding:5px;}
.text-limit{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;max-width: 110px;}
</style>
<div class="layui-card">
    <div class="layui-card-header">
        <a class="layui-btn" id="export" href="<?php echo url('admin/quote/excel_export',['id'=>input('id')]); ?>">导出模板</a>
        <a class="layui-btn" href="<?php echo url('admin/quote/index'); ?>">查看模板</a>
	</div>
    <div class="layui-card-body">
			<h2 class="text-center"><?php echo htmlentities($data['mouldname']); ?></h2>
            <table class="layui-table mould"> 
                <thead>
                    <tr>
						<th rowspan="2" colspan="2"><img style="max-width:200px;" src="/static/imgs/logo.png"></th>
                        <th class="text-center text-large" colspan="6"><h3>住宅装饰工程造价预算书</h3></th>
						<th rowspan="2" colspan="2"></th>
                    </tr>
					<tr>
						<th class="text-center" colspan="6">全国统一24小时客服热线：400-6281-968</th>
					</tr>
                    <tr>
                        <th style="text-align:center;" colspan="10">
                          单位：
                        </th>        
                    </tr>
                    <tr>
                        <th colspan="3">工程名称：</th>       
                        <th colspan="3">客户姓名：</th>       
                        <th colspan="2">设计师姓名：</th>       
                        <th colspan="2">报价师姓名：</th>       
                    </tr>
                    <tr>
						<th class="text-left" style="width:20px;" rowspan="2">序号</th> 
                        <th class="text-center" rowspan="2" colspan="2">工程项目名称</th>         
                        <th class="text-center" rowspan="2">工程量</th>       
                        <th class="text-center" rowspan="2">单位</th>
                        <th class="text-center" colspan="2">辅材费</th> 
                        <th class="text-center" colspan="2">人工费</th>    
                        <th class="text-center" rowspan="2">施工工艺及材料说明</th> 
                    </tr>
                    <tr>   
                        <th class="text-center">辅材基价</th>       
                        <th class="text-center">辅材合价</th>       
                        <th class="text-center">人工基价</th>       
                        <th class="text-center">人工合价</th> 
                      </tr>
                </thead>

                <tbody> 
					<?php if(is_array($data['details']) || $data['details'] instanceof \think\Collection || $data['details'] instanceof \think\Paginator): if( count($data['details'])==0 ) : echo "" ;else: foreach($data['details'] as $key=>$vos): ?>
						<tr data-cate="">
							<td class="text-left" ><?php echo htmlentities($vos['conditions_no']); ?></td>
							<td colspan="2" class="text-center bolder"><?php echo htmlentities($vos['conditionsname']); ?></td>
							<td colspan="7"></td>
						</tr>
						<?php if(is_array($vos['son']) || $vos['son'] instanceof \think\Collection || $vos['son'] instanceof \think\Paginator): if( count($vos['son'])==0 ) : echo "" ;else: foreach($vos['son'] as $k=>$v): ?>
							<tr id="tr<?php echo htmlentities($k); ?>">
								<td ><?php echo htmlentities($v['room_no']); ?></td>
								<td colspan="9"><?php echo htmlentities($v['roomname']); ?></td>
							</tr>
							<?php if(is_array($v['item']) || $v['item'] instanceof \think\Collection || $v['item'] instanceof \think\Paginator): if( count($v['item'])==0 ) : echo "" ;else: foreach($v['item'] as $kk=>$vv): ?>
								<tr class="tr'.$conditionsid.$roomid.'">
									<td><?php echo htmlentities($kk*1+1); ?></td>
									<td colspan="2"><?php echo htmlentities($vv['project']); ?></td>
									<td></td>
									<td><?php echo htmlentities($vv['company']); ?></td>
									<td><?php echo htmlentities($vv['quota']); ?></td>
									<td></td>
									<td><?php echo htmlentities($vv['craft_show']); ?></td>
									<td></td>
									<td class="limit text-limit" title="点击查看详情"><?php echo htmlentities($vv['material']); ?></td>
								</tr>
							<?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
			<input type="hidden" name="html" value="" />
		</div>
</div>

<div>

</div>


    
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script>
	$(document).ready(function(){
		$(document).on('mouseover','.limit',function(){
			$(this).css({'cursor':'pointer'});
		});
		$(document).on('click','.limit',function(){
			if($(this).hasClass('text-limit')){
				$(this).removeClass('text-limit');
			}else{
				$(this).addClass('text-limit');
			}
		})
		$('#export').click(function(){
			window.location.href = "<?php echo url('admin/quote/excel_export',['id'=>input('id')]); ?>";
		});
	});
</script>

</body>

</html>