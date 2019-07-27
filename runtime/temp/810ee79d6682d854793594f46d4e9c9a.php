<?php /*a:2:{s:72:"/www/wwwroot/www.hxpw1998.com/application/admin/view/offertype/edit.html";i:1561534714;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
    <div class="layui-card-header">编辑</div>
    <div class="layui-card-body">
        <form class="layui-form form-horizontal" id="myform" action="<?php echo url('admin/Offertype/edit'); ?>" method="post">
            <div class="area layui-form-item">
                <div class="layui-input-inline w300">
                    <input type="text" name="name" autocomplete="off" required="" value="<?php echo htmlentities($data['name']); ?>" class="layui-input">
                    <input type="text" name="other" autocomplete="off" class="layui-input" value="<?php echo htmlentities($data['other']); ?>">
										<input type="hidden" name="id" value="<?php echo input('id'); ?>">
                </div>
            </div>

            <div class="layui-form-item w300">
                <div class="layui-input-block">
                    <button class="layui-btn ajax-post" lay-submit="" lay-filter="*" target-form="form-horizontal">保存</button>
                    <a class="layui-btn layui-btn-normal" href="<?php echo url('admin/Offertype/index'); ?>">返回</a>
                </div>
            </div>
        </form>
    </div>
</div>

    
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#myform').submit(function(){
            var url = $(this).attr('action'),data = $(this).serialize();
            $.ajax({
                url:url,
                type:"post",
                data:data,
                success:function(result){
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