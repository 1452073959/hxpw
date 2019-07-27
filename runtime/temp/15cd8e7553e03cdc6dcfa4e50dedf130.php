<?php /*a:2:{s:75:"/www/wwwroot/www.hxpw1998.com/application/admin/view/offertype/addroom.html";i:1561711924;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
    
<div class="layui-card">
    <div class="layui-card-header">添加空间类型</div>
    <div class="layui-card-body">
        <form class="layui-form form-horizontal" id="myform" action="<?php echo url('admin/offertype/addroom'); ?>" method="post">
            <div class="area layui-form-item">
                <div class="layui-input-inline w300">
                    <span class="operation">
                        <a title="添加"class="addone">
                          <i class="layui-icon"></i>
                        </a>
                        <a title="删除">
                          <i class="layui-icon layui-icon-delete"></i>
                        </a>
                      </span>
                    <input type="text" name="name[]" autocomplete="off" placeholder="空间类型名称" required="" class="layui-input">
                    <input type="text" name="other[]" autocomplete="off" placeholder="其他备注" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item w300">
				<select name="pid" style="display: block;padding:7px;" required="">
					<option value="">选择工种</option>
					<?php if(is_array($conditions) || $conditions instanceof \think\Collection || $conditions instanceof \think\Paginator): $i = 0; $__LIST__ = $conditions;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
						<option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['name']); ?></option>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
                <div class="layui-input-block">
                    <button class="layui-btn ajax-post" lay-submit="" lay-filter="*" target-form="form-horizontal">添加</button>
                    <a class="layui-btn layui-btn-normal" href="<?php echo url('admin/Offertype/index'); ?>">返回</a>
                </div>
            </div>
        </form>
    </div>
</div>

    
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click','a.addone',function(){
            var html='<div class="layui-input-inline w300"><span class="operation"><a title="添加"class="addone"><i class="layui-icon"></i></a><a class="deleteone" title="删除"><i class="layui-icon layui-icon-delete"></i></a></span><input type="text" name="name[]" required="" autocomplete="off" placeholder="空间类型名称" class="layui-input"><input type="text" name="other[]" autocomplete="off" placeholder="其他备注" class="layui-input"></div>';
            $('.area').append(html);
         });

        $(document).on('click','a.deleteone',function(){
            $(this).parent().parent().remove();
        });
        $('#myform').submit(function(){
            var url = $(this).attr('action'),data = $(this).serialize();
            $.ajax({
                url:url,
                type:"post",
                data:data,
                success:function(result){
					console.log(result);
                    if(confirm(result.msg)){
                        if(result.code == 1){
                            window.location.href=result.url;
                        }
                    }
                }
            });
            return false;
        });


    });
</script>


</body>

</html>