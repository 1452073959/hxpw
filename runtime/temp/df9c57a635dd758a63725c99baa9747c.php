<?php /*a:2:{s:66:"/www/wwwroot/www.hxpw1998.com/application/admin/view/menu/add.html";i:1551760424;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
    <div class="layui-card-header">添加后台菜单</div>
    <div class="layui-card-body">
        <form class="layui-form form-horizontal" action="<?php echo url('admin/menu/add'); ?>" method="post">
            <div class="layui-form-item">
                <label class="layui-form-label">上级菜单</label>
                <div class="layui-input-inline w300">
                    <select name="parentid">
                        <option value="0">作为一级菜单</option>
                        <?php echo $select_categorys; ?>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">如果选择上级分类，那么新增的分类则为被选择上级分类的子分类</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">名称 </label>
                <div class="layui-input-inline w300">
                    <input type="text" name="title" autocomplete="off" placeholder="名称" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">模块</label>
                <div class="layui-input-inline w300">
                    <input type="text" name="app" autocomplete="off" placeholder="模块" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">控制器</label>
                <div class="layui-input-inline w300">
                    <input type="text" name="controller" autocomplete="off" placeholder="控制器" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">方法</label>
                <div class="layui-input-inline w300">
                    <input type="text" name="action" autocomplete="off" placeholder="方法" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">附加参数</label>
                <div class="layui-input-inline w300">
                    <input type="text" name="parameter" autocomplete="off" placeholder="附加参数" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">是否隐藏</label>
                <div class="layui-input-inline w300">
                    <input type="checkbox" name="status" lay-skin="switch" lay-text="否|是" value="1" checked="">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn ajax-post" lay-submit="" lay-filter="*" target-form="form-horizontal">立即提交</button>
                    <a class="layui-btn layui-btn-normal" href="<?php echo url('admin/menu/index'); ?>">返回</a>
                </div>
            </div>
        </form>
    </div>
</div>

    
<script type="text/javascript" src="/static/admin/js/common.js"></script>

</body>

</html>