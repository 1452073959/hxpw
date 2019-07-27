<?php /*a:2:{s:68:"/www/wwwroot/www.hxpw1998.com/application/admin/view/menu/index.html";i:1557193526;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
    <div class="layui-card-body">
        <div class="layui-form">
            <blockquote class="layui-elem-quote news_search">
                <div class="layui-inline">
                    <a class="layui-btn layui-btn-sm" href="<?php echo url('admin/menu/add'); ?>">新增后台菜单</a>
                </div>
            </blockquote>
            <table class="layui-table">
                <colgroup>
                    <col width="80">
                    <col width="80">
                    <col width="160">
                </colgroup>
                <thead>
                    <tr>
                        <th>排序</th>
                        <th>ID</th>
                        <th>操作</th>
                        <th>菜单名称</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $categorys; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    
<script type="text/javascript" src="/static/admin/js/common.js"></script>

</body>

</html>