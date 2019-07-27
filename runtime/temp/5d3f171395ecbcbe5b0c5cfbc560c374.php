<?php /*a:2:{s:69:"/www/wwwroot/www.hxpw1998.com/application/admin/view/quote/index.html";i:1563248597;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
.text-center{text-align: center !important;}
.text-large{font-size:24px!important;font-weight:900!important;}
</style>
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-form">
            <blockquote class="layui-elem-quote news_search">
                <div class="layui-inline">
                    <a class="layui-btn layui-btn-sm" href="<?php echo url('admin/quote/addmould'); ?>">新建模板</a>
                </div>
            </blockquote>
            <table class="layui-table">
                <thead>
                    <tr>
                        <th class="taleft">模板名称</th>
                        <th class="taleft">上一次修改时间</th>
                        <th class="taleft">操作</th>
                    </tr>
                </thead>
                <tbody> 
                    <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <tr>   
                       <td class="taleft"><?php echo htmlentities($vo['mouldname']); ?></td>
                       <td class="taleft"><?php echo date('Y-m-d H:i:s',$vo['addtime']); ?></td>    
					    <td>
							<?php if(!(empty(input('customerid')) || ((input('customerid') instanceof \think\Collection || input('customerid') instanceof \think\Paginator ) && input('customerid')->isEmpty()))): ?>
							<a href="<?php echo url('admin/offerlist/edit',['id'=>input('customerid'),'mouldid'=>$vo['id']]); ?>" class="checkmould layui-btn layui-btn-sm">选择该模板</a>
							<?php endif; ?>
							<a href="<?php echo url('admin/quote/checkmould',['id'=>$vo['id'],'type'=>'preview']); ?>" class="checkmould layui-btn layui-btn-sm" data-id="<?php echo htmlentities($vo['id']); ?>">查看</a>
						    <a href="<?php echo url('admin/quote/checkmould',['id'=>$vo['id']]); ?>" class="checkmould layui-btn layui-btn-sm" data-id="<?php echo htmlentities($vo['id']); ?>">编辑</a>
						    <a class="deletemould layui-btn layui-btn-sm layui-btn-danger" href="<?php echo url('admin/quote/deletemould',['id'=>$vo['id']]); ?>" data-id="<?php echo htmlentities($vo['id']); ?>">删除</a>
					    </td>
                    </tr>   
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


    
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		//关闭弹窗
		$('#close').click(function(){
		    $('#offerlist').toggle();
		});
		$(document).on('click','.deletemould',function(){
			var id = $(this).attr('data-id');
			if(!id){
				alert('删除有误');
			}
			if(!confirm('确定要删除该模板吗？')){return false;}
			$.ajax({
				url:"<?php echo url('admin/quote/deletemould'); ?>",
				type:"post",
				data:{id:id},
				success:function(result){
					alert(result.msg);
					if(result.code == 1){
						window.location.href = result.url;
					}
				}
			});
			return false;
		});
	});
</script>

</body>

</html>