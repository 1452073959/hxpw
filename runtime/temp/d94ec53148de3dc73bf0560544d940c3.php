<?php /*a:2:{s:71:"/www/wwwroot/www.hxpw1998.com/application/admin/view/manager/index.html";i:1557193610;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
    .daoru{position:absolute;top:1px;right:-50px;height:35px}
</style>
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-form">
            <blockquote class="layui-elem-quote news_search">
                <div class="layui-inline">
                    <a class="layui-btn layui-btn-sm" href="<?php echo url('admin/manager/add'); ?>">添加管理员</a>
                </div>
                <div class="layui-input-inline" style="margin-left:30px;">
                   <form method="post" action="<?php echo url('Manager/index'); ?>" class="form-signin" enctype="multipart/form-data">
                    <input type="text" name="search" placeholder="登陆名、手机号、分公司" autocomplete="off" class="layui-input">
                    <input type="submit" value="搜索" class="layui-btn layui-btn-sm daoru">
                  </form>
                 </div>  
                 <!-- 下拉框查询分公司 -->
            <!--     <div class="layui-input-inline" style="float: right;">
                    <select name="companyid" lay-filter="companyid">
                        <option value=""></option>
               
                    </select>
                </div>  -->
            </blockquote>
            <table class="layui-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>操作</th>
                        <th>登录名</th>
                        <th>手机号</th>
                        <th>所属角色</th>
                        <th>所属公司</th>
                        <!-- <th>最后登录地址</th> -->
                        <!-- <th>最后登录时间</th> -->
                        <th>E-mail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(is_array($Userlist) || $Userlist instanceof \think\Collection || $Userlist instanceof \think\Paginator): $i = 0; $__LIST__ = $Userlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <tr>
                        <td><?php echo htmlentities($vo['userid']); ?></td>
                        <td>
                            <a class="layui-btn layui-btn-xs" href="<?php echo url('admin/manager/edit',['id'=>$vo['userid']]); ?>">编辑</a>
                            <a class="layui-btn layui-btn-danger layui-btn-xs ajax-get confirm" url="<?php echo url('admin/manager/del',['id'=>$vo['userid']]); ?>">删除</a>
                            <a class="layui-btn layui-btn-<?php  echo ($vo['status']==1)?'danger':'disabled' ?> layui-btn-xs ajax-get confirm" url="<?php echo url('admin/manager/bankai',['id'=>$vo['userid'],'status'=>$vo['status']]); ?>"><?php  echo ($vo['status']==1)?'禁用':'已禁用' ?></a>
                        </td>
                        <td><?php echo htmlentities($vo['username']); ?></td>
                        <td><?php echo htmlentities($vo['phone']); ?></td>
                        <td><?php  echo model('admin/AuthGroup')->getRoleIdName($vo['roleid'])  ?></td>
                        <td><?php  echo $vo['companyid'] ? getcid($vo['companyid']) : '总公司'  ?></td>
                        <!-- <td><?php  echo $vo['last_login_address'] ? ($vo['last_login_address']) : '--'  ?></td> -->
                        <!-- <td><?php  echo $vo['last_login_time'] ? time_format($vo['last_login_time']) : '--'  ?></td> -->
                        <td><?php echo htmlentities($vo['email']); ?></td>

                    </tr>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    
<script type="text/javascript" src="/static/admin/js/common.js"></script>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
 //下拉选择分公司（第二种方案）   
 layui.use('form', function(){   
    var form = layui.form;
    form.on('select(companyid)', function(data){
      // alert(data.value); return false;
      if(!data.value){layer.msg('选项为空');return false;}
        $.ajax({
            type : 'post',
            url  : '<?php echo url("Manager/myquery"); ?>',
            dataType : 'json',
            data : {value:data.value},
            success : function(re){
                if(re.status != 0){
                    layer.msg(re.msg);
                }else{
                    var _html = '';
                    for (var i = 0; i < re.data.length; i++) {
                      _html += '<tr>';
                      _html += '<td>'+re.data[i]['userid']+'</td>';
                      _html += '<td><a class="layui-btn layui-btn-xs" href="/admin/manager/edit/id/'+re.data[i]['userid']+'">编辑</a><a class="layui-btn layui-btn-danger layui-btn-xs ajax-get confirm" url="/admin/manager/del/id/'+re.data[i]['userid']+'">删除</a></td>';
                      _html += '<td>'+re.data[i]['username']+'</td>';
                      _html += '<td>'+re.data[i]['phone']+'</td>';
                      _html += '<td>'+re.data[i]['roleid']+'</td>'
                      _html += '<td>'+re.data[i]['companyid']+'</td>'                
                      _html += '<td>'+re.data[i]['email']+'</td>';
                      _html += '</tr>'; 
                    }
                    $("tbody").html(_html);
                }     
            }
        });
    });
  });  
</script>

</body>

</html>