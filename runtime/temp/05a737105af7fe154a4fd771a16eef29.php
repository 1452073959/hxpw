<?php /*a:2:{s:77:"/www/wwwroot/www.hxpw1998.com/application/admin/view/offerlist/user_edit.html";i:1563588809;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
    .fenname{padding:10px;padding-right:8px;line-height:30px}
    #sougs{line-height:35px;background-color:#009688;color:#fff;padding:5px;border-radius:2px;cursor:pointer;}
</style>
<div class="layui-card">
    <div class="layui-card-header">添加</div>
    <div class="layui-card-body">
        <form class="layui-form form-horizontal" action="<?php echo url('admin/Offerlist/user_edit'); ?>" method="post">
            
            <div class="layui-form-item">
                <label class="layui-form-label">客户姓名 </label>
                <div class="layui-input-inline w300">
                    <input type="text" name="customer_name" autocomplete="off" required="" placeholder="客户姓名" class="layui-input" value="<?php echo htmlentities((isset($data['customer_name']) && ($data['customer_name'] !== '')?$data['customer_name']:'')); ?>">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">商务经理</label>
                <div class="layui-input-inline w300">
                    <input type="text" name="manager_name" autocomplete="off" placeholder="商务经理" class="layui-input" value="<?php echo htmlentities((isset($data['manager_name']) && ($data['manager_name'] !== '')?$data['manager_name']:'')); ?>">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">工程地址</label>
                <div class="layui-input-inline w300">
                    <input type="text" name="address" autocomplete="off" placeholder="工程地址" class="layui-input" value="<?php echo htmlentities((isset($data['address']) && ($data['address'] !== '')?$data['address']:'')); ?>">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">报价师姓名</label>
                <div class="layui-input-inline w300">
                    <input type="text" name="quoter_name" autocomplete="off" placeholder="报价师姓名" class="layui-input" value="<?php echo htmlentities((isset($data['quoter_name']) && ($data['quoter_name'] !== '')?$data['quoter_name']:'')); ?>">
                </div>
            </div>
             <div class="layui-form-item">
                <label class="layui-form-label">设计师姓名</label>
                <div class="layui-input-inline w300">
                    <input type="text" name="designer_name" autocomplete="off" placeholder="设计师姓名" class="layui-input" value="<?php echo htmlentities((isset($data['designer_name']) && ($data['designer_name'] !== '')?$data['designer_name']:'')); ?>">
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="id" value="<?php echo input('id'); ?>">
                    <button class="layui-btn ajax-post" lay-submit="" lay-filter="*" target-form="form-horizontal">立即提交</button>
                    <a class="layui-btn layui-btn-normal" href="<?php echo url('admin/Offerlist/index'); ?>">返回</a>
                </div>
            </div>
        </form>
    </div>
</div>

    
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>


</body>

</html>