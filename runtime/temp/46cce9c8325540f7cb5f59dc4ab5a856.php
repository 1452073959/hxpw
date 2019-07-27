<?php /*a:2:{s:72:"/www/wwwroot/www.hxpw1998.com/application/admin/view/offerquota/add.html";i:1563787648;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
    <div class="layui-card-header">添加</div>
    <div class="layui-card-body">
        <form class="layui-form form-horizontal" action="<?php echo url('admin/offerquota/add'); ?>" method="post">
            <div class="layui-form-item">
                <label class="layui-form-label">选择分公司</label>
                <div class="layui-input-inline w300">
                  <select name="typeid" lay-verify="required"  lay-filter="business">
                    <option value=""></option>
                  <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['name']); ?></option>
                  <?php endforeach; endif; else: echo "" ;endif; ?>
                  </select>
                </div>
            </div> 
            <div class="layui-form-item">
                <label class="layui-form-label">项目编号</label>
                <div class="layui-input-inline w300">
                    <input type="text" name="item_number" autocomplete="off" placeholder="项目编号" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">工种 </label>
                <div class="layui-input-inline w300">
                    <input type="text" name="type_of_work" autocomplete="off" placeholder="工种" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">项目名称</label>
                <div class="layui-input-inline w300">
                    <input type="text" name="project" autocomplete="off" placeholder="工程项目" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">单位</label>
                <div class="layui-input-inline w300">
                    <input type="text" name="company" autocomplete="off" placeholder="单位" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">辅材单价</label>
                <div class="layui-input-inline w300">
                    <input type="text" name="quota" autocomplete="off" placeholder="辅材单价" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">人工单价</label>
                <div class="layui-input-inline w300">
                    <input type="text" name="craft_show" autocomplete="off" placeholder="人工单价" class="layui-input">
                </div>
            </div>
             <div class="layui-form-item">
                <label class="layui-form-label">综合价</label>
                <div class="layui-input-inline w300">
                    <input type="text" name="cost_value" autocomplete="off" placeholder="成本价值" class="layui-input">
                </div>
            </div>
             
             <div class="layui-form-item">
                <label class="layui-form-label">施工工艺与材料说明</label>
                <div class="layui-input-inline w600">
                    <!-- <input type="text" name="material" autocomplete="off" placeholder="辅材说明" class="layui-input"> -->
                    <textarea name="material" style="padding:10px;border:1px solid #D2D2D2;width:100%;height:200px; resize:none;"></textarea>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn ajax-post" lay-submit="" lay-filter="*" target-form="form-horizontal">立即提交</button>
                    <a class="layui-btn layui-btn-normal" href="<?php echo url('admin/offerquota/index'); ?>">返回</a>
                </div>
            </div>
        </form>
    </div>
</div>

    
<script type="text/javascript" src="/static/admin/js/common.js"></script>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script>
layui.use(['layedit','form'], function(){
  var layedit = layui.layedit;
  layedit.build('demo'); //建立编辑器

//   var form = layui.form, layer = layui.layer;

// form.on('select(business)', function(data){

//     alert($('select option:selected').val());

// })

});


</script>

</body>

</html>