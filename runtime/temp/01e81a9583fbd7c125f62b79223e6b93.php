<?php /*a:2:{s:76:"/www/wwwroot/www.hxpw1998.com/application/admin/view/offerlist/userlist.html";i:1563589804;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
</style>

  <!-- 导出完成 -->
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-form">

            <blockquote class="layui-elem-quote news_search">
                <div class="layui-inline">
                    <a class="layui-btn layui-btn-sm" href="<?php echo url('Offerlist/add'); ?>">新建客户</a>
                  <!--   <form method="post" action="<?php echo url('Offerlist/ImportExcel'); ?>" class="form-signin" enctype="multipart/form-data" style="float: right;margin:0 18px;position:relative;">
                        <a href="javascript:;" class="onfile">选择文件
                          <input name="excel" type="file" class="" id="imgInput">
                        </a>
                        <div id="imgs"></div>
                          <input type="submit" value="导入Excel" class="layui-btn layui-btn-sm daoru">
                    </form> -->
                </div>     
                 <!-- <div class="layui-input-inline" style="margin-left: 100px;">
                   <form method="post" action="<?php echo url('Offerlist/index'); ?>" class="layui-form form-signin" enctype="multipart/form-data">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                    <input type="text" name="search['customer_name']" placeholder="客户姓名" class="layui-input">
										</div>
										</div>
                    <input type="text" name="search['quoter_name']" placeholder="报价师" class="layui-input">
                    <input type="text" name="search['designer_name']" placeholder="设计师" class="layui-input">
                    <input type="text" name="search['address']" placeholder="工程地址" class="layui-input">
                    <input type="text" name="search['manager_name']" placeholder="客户经理" class="layui-input">
                    <input type="submit" value="搜索" class="layui-btn layui-btn-sm daorus">
                  </form>
                 </div> -->
								 <div class="layui-input-inline" style="margin-left: 100px;">
						<form class="layui-form" method="get" action="<?php echo url('Offerlist/userlist'); ?>" class="layui-form form-signin" enctype="multipart/form-data">
                    <div class="layui-inline">
												<input type="text" name="search['customer_name']" placeholder="客户姓名" class="layui-input">
                    </div>
                    <div class="layui-inline">
												<input type="text" name="search['quoter_name']" placeholder="报价师" class="layui-input">
                    </div>
                    <div class="layui-inline">
												<input type="text" name="search['designer_name']" placeholder="设计师" class="layui-input">
                    </div>
                    <div class="layui-inline">
												<input type="text" name="search['address']" placeholder="工程地址" class="layui-input">
                    </div>
                    <div class="layui-inline">
												<input type="text" name="search['manager_name']" placeholder="客户经理" class="layui-input">
                    </div>
                    <div class="layui-inline">
                         <button type="submit" class="layui-btn layui-btn-blue">搜索</button>
                    </div>
            </form>
						</div>
                <!-- <a class="layui-btn layui-btn-sm" style="float:right;margin-left:10px" href="/Template/报价空模板.xls">下载空模板</a> -->
         <!--  <a class="layui-btn layui-btn-sm" style="float:right;" onclick="exportExcel()">导出excel</a> -->
                <a id="tishi" title="点我查看" style="float:right;" > <i class="layui-icon">&#xe702;</i></a>
            </blockquote>
            <script type="text/html" id="toolbarDemo">
              <div class="layui-btn-container">
                <!-- <button class="layui-btn layui-btn-sm" lay-event="getCheckData" id="batch">批量修改数据</button> -->
                <!-- <button class="layui-btn layui-btn-sm" lay-event="getCheckLength">获取选中数目</button> -->
                <!-- <button class="layui-btn layui-btn-sm" lay-event="isAll">验证是否全选</button> -->
              </div>
            </script>
     
            <table class="layui-table">
                <colgroup>
                    <col width="80">
                    <col width="80">
                    <col width="160">
                </colgroup>
                <thead >
                    <tr>
                        <th>ID</th>
                        <th>客户姓名</th>
                        <th>报价师姓名</th>
                        <th>设计师姓名</th>
                        <th>商务经理</th>
                        <th>地址</th>
                        <th>报价历史</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody> 
                  
                  <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): if( count($data)==0 ) : echo "" ;else: foreach($data as $key=>$vo): ?>
                    <tr>   
                      <td ><?php echo htmlentities($vo['id']); ?>
                      </td> 
                      <td ><?php echo htmlentities($vo['customer_name']); ?></td> 
                      <td ><?php echo htmlentities($vo['quoter_name']); ?></td> 
                      <td ><?php echo htmlentities($vo['designer_name']); ?></td> 
                      <td ><?php echo htmlentities($vo['manager_name']); ?></td> 
                      <td ><?php echo htmlentities($vo['address']); ?></td> 
                      <td><a class="layui-btn layui-btn-sm layui-btn-warm" href="<?php echo url('admin/offerlist/index',['customer_id'=>$vo['id']]); ?>">查看历史</a></td>
                      <td >
                        <a class="layui-btn layui-btn-sm layui-btn-danger" href="<?php echo url('admin/offerlist/user_delete',['id'=>$vo['id']]); ?>">删除</a>
                        <a class="layui-btn layui-btn-sm" href="<?php echo url('admin/offerlist/user_edit',['id'=>$vo['id']]); ?>">编辑</a>
                      </td>    
                 <?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
              
        </div>
    </div>
</div>

    
<script type="text/javascript" src="/static/admin/js/common.js"></script>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>

<script type="text/javascript">

</script>
<script type="text/javascript">

</script>

</body>

</html>