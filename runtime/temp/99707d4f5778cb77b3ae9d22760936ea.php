<?php /*a:2:{s:73:"/www/wwwroot/www.hxpw1998.com/application/admin/view/offertype/index.html";i:1561711846;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
</style>
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-form">
            <blockquote class="layui-elem-quote news_search">
                <div class="layui-inline">
                    <a class="layui-btn layui-btn-sm" href="<?php echo url('Offertype/addroom'); ?>">添加空间类型</a>
                    <a class="layui-btn layui-btn-sm" href="<?php echo url('Offertype/addconditions'); ?>">添加工种类型</a>
                </div>
            </blockquote>
			<small>温馨提示：如果删除工种，该工种下对应的空间类型会全部删除！</small>
            <table class="layui-table">
                <colgroup>
                    <col width="80">
                    <col width="80">
                    <col width="160">
                </colgroup>
                <thead>
                    <tr>
                        <th class="taleft">工种</th>
                        <th class="taleft">空间类型</th>
                    </tr>
                </thead>
                <tbody> 
                  <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): if( count($data)==0 ) : echo "" ;else: foreach($data as $key=>$vo): ?>
                    <tr>   
                      <td class="taleft" <?php if(empty($vo['son']) || (($vo['son'] instanceof \think\Collection || $vo['son'] instanceof \think\Paginator ) && $vo['son']->isEmpty())): ?>colspan="2"<?php else: ?>rowspan="<?php echo count($vo['son']) * 1 + 1; ?>"<?php endif; ?>>
						  <span><?php echo htmlentities($vo['name']); ?></span>
						  <a href="<?php echo url('admin/offertype/edit',['id'=>$vo['id']]); ?>" class="edit layui-btn layui-btn-sm" data-id="<?php echo htmlentities($vo['id']); ?>">编辑</a>
						  <a class="delete layui-btn layui-btn-sm layui-btn-danger" data-id="<?php echo htmlentities($vo['id']); ?>">删除</a>
					  </td>     
                    </tr>   
					<?php if(!(empty($vo['son']) || (($vo['son'] instanceof \think\Collection || $vo['son'] instanceof \think\Paginator ) && $vo['son']->isEmpty()))): if(is_array($vo['son']) || $vo['son'] instanceof \think\Collection || $vo['son'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['son'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vos): $mod = ($i % 2 );++$i;?>
							<tr>
								<td>
									<span><?php echo htmlentities($vos['name']); ?></span>
									<a href="<?php echo url('admin/offertype/edit',['id'=>$vos['id']]); ?>" class="edit layui-btn layui-btn-sm" data-id="<?php echo htmlentities($vo['id']); ?>">编辑</a>
									<a class="delete layui-btn layui-btn-sm layui-btn-danger" data-id="<?php echo htmlentities($vos['id']); ?>">删除</a>
								</td>
							</tr>
						<?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
       
        </div>
    </div>
</div>

    
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		//删除
		$('a.delete').click(function(){
			var id = $(this).attr('data-id');
			if(!confirm('确认删除？')){return false;}
			$.ajax({
				url:"<?php echo url('admin/offertype/delete'); ?>",
				method:'post',
				data:{id:id},
				success:function(result){
					// console.log(result);
					alert(result.msg);
					if(result.code==1){
						window.location.href = result.url;
					}
				}
			});
		});
		//修改
		// $('a.edit').click(function(){
		// 	var val = $(this).parent().find('span').html(),id = $(this).attr('data-id');
		// 	$(this).parent().find('span').html('<input class="editinput" type="text" value="'+val+'" data-id="'+id+'">')
		// });
		// $(document).on('change','click',function(){
		// 	var id = $(this).attr('data-id'),val = $(this).val();
		// 	$.ajax({
		// 		url:"<?php echo url('admin/offertype/edit'); ?>",
		// 		method:'post',
		// 		data:{id:id}
		// 	});
		// });
	});
</script>

</body>

</html>