<?php /*a:2:{s:69:"/www/wwwroot/www.hxpw1998.com/application/admin/view/manager/add.html";i:1556252982;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
	<div class="layui-card-header">添加管理员</div>
    <div class="layui-card-body">
        <form class="layui-form form-horizontal" action="<?php echo url('admin/manager/add'); ?>" method="post">
            <div class="layui-form-item">
                <label class="layui-form-label">用户名</label>
                <div class="layui-input-inline">
                    <input type="text" name="username" lay-verify="username" autocomplete="off" placeholder="用户名" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">3-15位字符，可由字母和数字，下划线"_"及破折号"-"组成。</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">密码</label>
                <div class="layui-input-inline">
                    <input type="password" name="password" lay-verify="password" autocomplete="off" placeholder="密码" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">6-20位字符，可由英文、数字及标点符号组成</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">确认密码</label>
                <div class="layui-input-inline">
                    <input type="password" name="password_confirm" lay-verify="password_confirm" autocomplete="off" placeholder="确认密码" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">请再次输入您的密码</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">E-mail</label>
                <div class="layui-input-inline">
                    <input type="text" name="email" lay-verify="email" autocomplete="off" placeholder="E-mail" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">填写完整邮箱，如 yzncms@163.com</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">真实姓名</label>
                <div class="layui-input-inline">
                    <input type="text" name="name" lay-verify="name" autocomplete="off" placeholder="真实姓名" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">手机号</label>
                <div class="layui-input-inline">
                    <input type="text" name="phone" lay-verify="phone" autocomplete="off" placeholder="手机号" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">选择公司</label>
                <div class="layui-input-inline" id="xiaxian">
                    <input type="text" name="companyid" lay-verify="companyid" autocomplete="off" placeholder="所属公司" class="layui-input">
                    <input type="hidden" name="hidecid" lay-verify="hidecid" autocomplete="off" placeholder="所属公司id" class="layui-input">
                </div>
                <span id="sougs"  onclick="return myquery()">搜索公司</span>
            </div>
            <!-- <div class="layui-form-item">
                <label class="layui-form-label">所属公司</label>
                <div class="layui-input-inline">
                    <select name="companyid" lay-filter="companyid">
                        
                    </select>
                    <span id="xuanze">选择</span>
                </div>
            </div> -->
            <div class="layui-form-item">
                <label class="layui-form-label">权限组</label>
                <div class="layui-input-inline" id="tipsc">
                    <select name="roleid" lay-filter="roleid">
                        <?php if(is_array($roles) || $roles instanceof \think\Collection || $roles instanceof \think\Paginator): $i = 0; $__LIST__ = $roles;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['title']); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>                  
                </div>                 
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn ajax-post" lay-submit="" lay-filter="*" target-form="form-horizontal">立即提交</button>
                    <!-- <button class="layui-btn layui-btn-normal" onClick="javascript :history.back(-1);">返回</button> -->
                    <a class="layui-btn layui-btn-normal" href="<?php echo url('admin/Manager/index'); ?>">返回</a>
                </div>
            </div>
        </form>
    </div>
</div>

    
<script type="text/javascript" src="/static/admin/js/common.js"></script>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
// layui.use('form', function(){
//     $("#xuanze").mouseover(function () {
//         // alert(111)

//         var jsonar = "";
//         for (var i = 0; i < content.length; i++) {
//            jsonar += '<span class="fenname" onClick="myfun('+content[i]['id']+')">'+content[i]['name']+'</span>';
//         }
//        layer.open({
//           type: 1,
//           area:["1000px","620px"],
//           title: false,
//           closeBtn: 0,
//           shadeClose: true,
//           skin: 'yourclass',
//           content: jsonar
//         });
//     });  
// });

//    function myfun(id="") {
//        alert("选中公司的id："+id);
//    }

   function myquery(){
    var getval = $('input[name="companyid"]').val();
     if(!getval){layer.msg("请输入要选择公司搜索");return false;}
        // alert(getval);
        $.ajax({
          type : 'post',
          url  :  '<?php echo url("Manager/ajaxquery"); ?>',
          dataType : 'json',
          data : {value:getval},
          success : function(re){
               if(re.status!=0){
                  layer.msg(re.msg);return false;
               }else{
                  // layer.msg(re.msg);
                  var rehui = re.data;
                  var _html = "";
                  for (var i = 0; i <rehui.length; i++) {
                     _html += '<span class="fenname" data-nid="'+rehui[i]['id']+'">'+rehui[i]['name']+'</span>';
                  }
                   // layer.open({
                   //    type: 1,
                   //    area:["500px","300px"],
                   //    title: false,
                   //    closeBtn: 0,
                   //    shadeClose: true,
                   //    skin: 'yourclass',
                   //    content: _html
                   //  });
                   layer.tips(_html, '#xiaxian', {
                      tips: [3, '#3595CC'],
                       area: ['auto', 'auto'],
                      time: 5000
                    });  
                  $(".fenname").click(function(){
                      var nid = $(this).data('nid');
                      var text = $(this).text();
                      $('input[name="companyid"]').val(text);
                      $('input[name="hidecid"]').val(nid);
                      // alert(nid+'--'+text);
                  });
               }
          }
        })
   }
   // function newmyfun(val="") {
   //     // alert("选中公司的id："+val);
   //     alert($(this).text());
   //      $('input[name="companyid"]').val(val);

   // }

   // $(function(){
    
   // })
</script>

</body>

</html>