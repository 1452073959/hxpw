<?php /*a:1:{s:69:"/www/wwwroot/www.hxpw1998.com/application/admin/view/login/index.html";i:1562406977;}*/ ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>登录</title>
    <link rel="shortcut icon" href="/static/admin/images/logo.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css" />
    <link rel="stylesheet" type="text/css" href="/static/admin/css/style.css" />
    <script src="http://www.jq22.com/jquery/jquery-1.10.2.js"></script>
</head>

<body>
    <style>
        *{margin:0;padding: 0;box-sizing: border-box;-webkit-touch-callout: none;-webkit-user-select: none;-khtml-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;}
        .login-main{width: auto;/*background-color: rgba(183, 171, 171, 0.09);*/padding: 20px 64px;left: 46%;height: auto;}
        body {width:100%;overflow: hidden;background: url("/static/admin/images/lb.png") no-repeat center top;background-size: cover;}
        .outer{position: relative;margin:20px auto;height: 40px;line-height: 40px;border:1px solid #ccc;border-radius: 2px;}
        .outer span,.filter-box,.inner{position: absolute;top: 0;left: 0;}
        .outer span{display: block;padding:0  0 0 36px;width: 100%;height: 100%;color: #fff;text-align: center;}
        .filter-box{width: 0;height: 100%;background: green;z-index: 9;}
        .outer.act span{padding:0 36px 0 0;}
        .inner{width: 40px;height: 40px;text-align: center;background: url(/admins/images/logo.ico) no-repeat;background-size: 100% 100%;cursor: pointer;font-family: "宋体";z-index: 10;font-weight: bold;color: #929292;left: -1px;}
        .outer.act .inner{color: green;}
        .outer.act span{z-index: 99;}
        .login-main form .layui-input-inline {margin-bottom: 17px; margin-right: 10px;}
        .layui-btn{background-color:#383e3e;}
        h3.tab-h:nth-child(1) {color: #009688 !important;}
        .tab-h{width:87%;float: left;text-align: center;margin-left:20px;margin-top: 0;margin-bottom: 30px;font-size: 24px;font-weight: 500;padding: 0;line-height: 30px;color: #ccc;}
        .outer1{position: relative;margin:20px auto;height: 40px;line-height: 40px;border:1px solid #ccc;border-radius: 2px;}
        .outer1 span,.filter-box1,.inner1{position: absolute;top: 0;left: 0;}
        .outer1 span{display: block;padding:0  0 0 36px;width: 100%;height: 100%;color: #ccc;text-align: center;}
        .filter-box1{width: 0;height: 100%;background: green;z-index: 9;}
        .outer1.act span{padding:0 36px 0 0;}
        .inner1{width: 36px;height: 38px;text-align: center;background: #fff;cursor: pointer;font-family: "宋体";z-index: 10;font-weight: bold;color: #929292;}
        .outer1.act .inner1{color: green;}
        .outer1.act span{z-index: 99;}
    </style>
    <div id="mydiv">
        <div class="login-main">
        <!-- <h1 class="layui-elip" style="color: white;position: absolute;top: -120px;font-size: 38px;font-family: cursive;">后台管理系统</h1> -->
            <p><img style="width:420px" src="/static/admin/images/logo.png"></p>
            <h3 class="tab-h" style="color: #f37020"> 账号登陆 </h3>
            
            <form class="layui-form" action="<?php echo url('admin/login/index'); ?>" method="post">
                <div class="layui-form-item">
                    <div class="layui-input-inline input-item">
                        <label for="username"></label>
                        <input type="text" name="username" lay-verify="required" autocomplete="off" placeholder="账号" class="layui-input">
                    </div>
                    <div class="layui-input-inline input-item">
                        <label for="password"></label>
                        <input type="password" name="password" lay-verify="required" autocomplete="off" placeholder="密码" class="layui-input">
                    </div>
                   
                    <div class="layui-input-inline login-btn">
                        <button class="layui-btn" lay-filter="login" >登录</button>
                    </div>
                </div>
            </form>
           
        </div>
    </div>
    <script type="text/javascript" src="/static/layui/layui.js"></script>
   
       
</body>

</html>