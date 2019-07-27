<?php /*a:1:{s:68:"/www/wwwroot/www.hxpw1998.com/application/admin/view/main/index.html";i:1557193490;}*/ ?>

<link rel="stylesheet" href="/static/12/admin/css/layui.css" media="all"/>
<link rel="stylesheet" href="/static/12/admin/css/font-awesome.min.css" media="all"/>
<script src="/static/12/admin/js/jquery-2.0.0.js"></script>
<script src="/static/layui/layui.js"></script>
<link rel="stylesheet" href="/static/12/admin/css/index.css"/>
<script src="/static/12/admin/js/highcharts.js" type="text/javascript" charset="utf-8"></script>
<script src="/static/12/admin/js/exporting.js" type="text/javascript" charset="utf-8"></script>
<script src="/static/12/admin/js/highcharts-zh_cn.js" type="text/javascript" charset="utf-8"></script>
<style>
        .layui-breadcrumb div {
            height: 38px;
            line-height: 38px;
            float: left;
        }
        .layui-btn {
            width: 70%;
            padding: 0;
            list-style-type: none;
        }
        #newlist {
            width: 70%;
            overflow: hidden;
        }
        .gg_class {
            margin: 10px;
            padding: 0 10px;
            width: 70%;
            background: yellow;
            color: #333;
            overflow: hidden;
        }
        .gg_class .news{
            white-space: nowrap;
            cursor: pointer;
        }
        .gg_class .news span{
            font-weight: bold;
        }
        .newsstyle {
            color: #0b0b0b;
        }
        .balce_row {
            width: 100%;
            padding: 10px 0;
            box-sizing: border-box;
        }
        .bot .left_row, .bot .right_row {
            height: 50%;
        }
        .bot .left_row {
            border-bottom: 1px solid #e6e6e6;
        }
        .countInfo .inforight .bot {
            padding: 0;
            height: 67%;
        }
        .total-notice{
            margin: 10px 0;
            float: left;
            width: 128px;
            height: 40px;
            background: url(images/sign.png) no-repeat;
            background-position: center;
            background-size: 100%;
            color: #fff;
            text-align: center;
            cursor: pointer;
        }
        .total-notice a{
            display: block;
            width: 100%;
            height: 100%;
            color: #fff!important;
        }
        .total-notice a:hover{
            color: #fff!important;
        }
        .tips{
            opacity: 0;
        }
        .xinxi{float:left;width:50%;text-align:center;}
        .xinxi .index-p{padding:0 25px}
    </style>
</head>
        <!-- body -->
        <div class="layui-body" id="admin-body" style="left:0">
            <div class="layui-tab" lay-allowClose="true" lay-filter="tabMain">
                <div class="layui-tab-content" id="main">
                    <div class="layui-tab-item layui-show">
                        <div class="m_box">
                            <div class="content clearfix">
                                <!-- 数据汇总 -->
                                <style type="text/css">
                                    .layui-breadcrumb {
                                  display: block;
                                 width: 100%;
                                padding: 0 1%;
                                background: #fff;
                                 height: 60px;
                                line-height: 60px;
                                box-sizing: border-box;
                                }
                            .layui-breadcrumb div {
                                    height: 38px;
                                     line-height: 38px;
                                    float: left;
                                }
                            .layui-breadcrumb a:last-child cite {
                                     color: #5FB878;
                                }
                                .layui-breadcrumb div {
                             height: 38px;
                             line-height: 38px;
                             float: left;
                                }
                                .gg_class {
                            margin: 10px;
                             padding: 0 10px;
                                 width: 70%;
                            background: yellow;
                             color: #333;
                                 overflow: hidden;
                                    }
                                .gg_class .news {white-space: nowrap;cursor: pointer;}
                                </style>
                               <div class="layui-breadcrumb" lay-separator=">" style="visibility: visible;">
                                <div style="margin: 10px 0;height: 38px">您当前所在位置：<a><cite>首页</cite></a></div>
                               
                                        </div>
                                        <div class="background"></div>
<div class="layui-fluid main" style="padding-bottom:15px">

    <div class="layui-row">
        <div class="information-box" >
            <p class="index-title">帐户信息<a href="<?php echo url('main/edit'); ?>"><span style="margin-left:30px;color:green;display:none;">信息修改</span></a></p></p>
             <?php if(app('session')->get('admin_user_auth.pyid') != '0'): ?>
            <div class="countInfo">
                <div class="infoleft">
                    <div class="tu">
                        <img src="/static/12/admin/images/user.png" alt=""/>
                    </div>
                    <div class="mess" style="float:left;">
                        <h3 class="" style="font-weight: bold;padding-top: 10px;"><?php echo htmlentities(app('session')->get('admin_user_auth.name')); ?></h3>
                        <!--<p class="index-p">邮箱:<span>--</span></p>-->
                        <p class="index-p">手机:<span><?php echo htmlentities(app('session')->get('admin_user_auth.phone')); ?></span></p>
                        <p class="index-p">上次登录时间:<span><?php echo htmlentities(date('Y-m-d H:i:s',!is_numeric(app('session')->get('admin_user_auth.last_login_time'))? strtotime(app('session')->get('admin_user_auth.last_login_time')) : app('session')->get('admin_user_auth.last_login_time'))); ?></span></p>
                        <!--<p class="index-p">上次登录IP:<span>113.77.146.30</span></p>-->
                        <p class="index-p">上次登录地址:<span class="userct"><?php echo htmlentities(app('session')->get('admin_user_auth.last_login_address')); ?></span></p>
                    </div>
                     <div class="xinxi">
                         <h2 style="text-align: center;line-height:52px"><?php echo htmlentities($rerole['title']); ?></h2>
                         <p class="index-p">所属公司：<?php echo htmlentities($rerole['name']); ?></p>
                         <p class="index-p"><?php echo htmlentities($rerole['description']); ?></p>
                     </div>   
                </div>
                 <!-- <?php dump($rerole)?> -->
            </div>
            <?php else: ?>
            <h1 style="text-align: center;line-height: 10rem;">超级管理员</h1>

            <?php endif; ?>

        </div>

    <!--     <div class="information-box">
            <?php if($re['roleid'] == '1' or $re['roleid'] == 7): if(app('session')->get('admin_user_auth.pyid') != '0'): ?>
            <div class="information1">
                    <p class="index-title">
                        <span style="font-weight: 800;">账户余额 （元）</span>
                        <i class="question question1">
                            <span class="wen-icon">?</span>
                            <span class="tips tips1"><b></b>账户余额为充值消费后剩余金额（我的余额）</span>
                        </i>
                        <span class="layui-badge">平台</span>
                    </p>
                <ul class="info-list">
                    <li>
                        <p style="line-height: 35px;font-size: 32px;" id="user_balance"><?php echo htmlentities($py['money']); ?></p>
                    </li>
                </ul>
            </div>
            <?php endif; endif; ?>

            <div class="information1">
                <p class="index-title">
                    <span style="font-weight: 800;">回款总额（元）</span>
                    <i class="question question5">
                        <span class="wen-icon">?</span>
                        <span class="tips tips5"><b></b>平台所有用户回款金额总和</span>
                    </i>
                    <span class="layui-badge" style="">平台</span>
                </p>
                <ul class="info-list">
                    <li>
                        <p style="line-height: 35px;font-size: 32px;" id="receivable">0</p>
                    </li>
                </ul>
            </div>

        </div>

        <div class="information-box">
            <div class="information1">
                <p class="index-title">
                    <span style="font-weight: 800;">待放总额（元）</span>
                    <i class="question question4">
                        <span class="wen-icon">?</span>
                        <span class="tips tips4"><b></b>今天审核通过后未放款金额总和</span>
                    </i>
                    <span class="layui-badge" style="">今天</span>
                </p>
                <ul class="info-list">
                    <li>
                        <p style="line-height: 35px;font-size: 32px;" id="stay_put_money"><?php echo htmlentities((isset($await['money']) && ($await['money'] !== '')?$await['money']:0)); ?></p>
                    </li>
                </ul>
            </div>
            <div class="information1">
                <p class="index-title">
                    <span style="font-weight: 800;">未审人数（个）</span>
                    <i class="question question2">
                        <span class="wen-icon">?</span>
                        <span class="tips tips2"><b></b>未审核的用户总数</span>
                    </i>
                    <span class="layui-badge">平台</span>
                </p>
                <ul class="info-list">
                    <li>
                        <p style="line-height: 35px;font-size: 32px;" id="user_count">0</p>
                    </li>
                </ul>
            </div>
        </div>
        <div class="information-box">
            <div class="information1">
                <p class="index-title">
                    <span style="font-weight: 800;">已放金额（元）  </span>
                    <i class="question question6">
                        <span class="wen-icon">?</span>
                        <span class="tips tips6"><b></b>今天放款用户金额总和</span>
                    </i>
                    <span class="layui-badge" style="">今天</span>
                </p>
                <ul class="info-list">
                    <li>
                        <p style="line-height: 35px;font-size: 32px;" id="has_put_money"><?php echo htmlentities((isset($already['money']) && ($already['money'] !== '')?$already['money']:0)); ?></p>
                    </li>

                </ul>
            </div>
            <div class="information1"  id="shouyi-tab" style="display: none">
                <p class="index-title">
                    <span style="font-weight: 800;">收益金额 （元）</span>
                    <i class="question question3">
                        <span class="wen-icon">?</span>
                        <span class="tips tips3"><b></b>收益金额=还款总额（本金+利息+违约金+展期费用）-用户实际到账金额</span>
                    </i>
                    <span class="layui-badge">平台</span>
                </p>
                <ul class="info-list">
                    <li>
                        <p style="line-height: 35px;font-size: 32px;" id="shouyi"></p>
                    </li>

                </ul>
            </div>
        </div> -->
    </div>
    <div class="layui-row">
            <div class="layui-col-xs12 layui-col-sm12 inf-box" style="margin-top: 20px;">
              <!--   <p class="index-title">数据统计
                    <span style="float: right;margin-right: 15px;font-size: 16px;"></span>
                </p> -->
                <div style="width: 100%;height: auto;">
                    <div id="container1" style="width: 49%;height:420px;float: left;">


                       <!-- <div id="container" style="height: 100%"></div> -->

                    </div>
                    <div id="container2" style="width: 49%;height:420px;float: right;">
                    </div>
                </div>

            </div>
        </div>  <div id="myModal" style="display: none;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="form-inline" name="amountForm" method="post">
                    <div class="modal-body">
                        <div class="" style="margin-bottom: 9px">
                            <!--<span id="ctitle"></span>--><span class="ctime"></span>
                        </div>
                        <div id="ccontent" style="text-indent: 2em;font-size:20px;">ghgfkslkghj</div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- <script type="text/javascript">
    function arrayToJson(formArray){
        var dataArray = {};
        $.each(formArray,function(){
            if(dataArray[this.name]){
                if(!dataArray[this.name].push){
                    dataArray[this.name] = [dataArray[this.name]];
                }
                dataArray[this.name].push(this.value || '');
            }else{
                dataArray[this.name] = this.value || '';
            }
        });
        return JSON.stringify(dataArray);
    }
    function aa(e){
        if(e.keyCode==116){
            location.reload();
            return false;
        }
    }
    document.onkeydown = aa;

    //  判断如果是顶层页面禁止全屏展示按钮出现
    if (window == top) {
        $("#feedback").hide();
    } else {
        var url = window.location.href;
        $("#feedback").attr("href", url);
    }

    //ipad端禁止缩放屏幕
    window.onload=function () {
        document.addEventListener('touchstart',function (event) {
            if(event.touches.length>1){
                event.preventDefault();
            }
        })
        var lastTouchEnd=0;
        document.addEventListener('touchend',function (event) {
            var now=(new Date()).getTime();
            if(now-lastTouchEnd<=300){
                event.preventDefault();
            }
            lastTouchEnd=now;
        },false)
    }
</script>
 -->
<script type="text/javascript">
     // 公告横向滚动
   /* var notContainer = $(".gg_class");
    var notText = $(".news");
    var marginLeft = notContainer.width();
    notText.css({"margin-left": marginLeft + 'px'});  //定义文字的初始位置
    var move = function(){
            //移动效果
            notText.animate({"margin-left": -marginLeft  + 'px'}, 20000, 'linear', function() {
                notText.stop(true,true);
                notText.css({"margin-left": marginLeft + 'px'});
                move();
            })
    };
    move();
    //  定义容器的暂停、恢复效果
    notContainer.on("mouseenter", function () {notText.stop(true)}).on('mouseleave', function () {move()})
*/
    // $.getScript('//int.dpool.sina.com.cn/iplookup/iplookup.php?format=js', function (remote_ip_info) {
        // console.log(remote_ip_info);
  /*   function LastIp() {
         $.get('/admin/index.php/home/index/getLastIp.html', function (data) {
             $(".userct").html(data.info.lastad);
         })
     }
     LastIp();*/
        // $(".isp").html(remote_ip_info.isp);
    // });

    //设置字段值
  /*  function set_field() {
        $.get('/admin/index.php/home/api/get_home_field.html', '', function (data) {
            $('#user_balance').html(data.user_balance);
            $('#expense').html(data.expense);
            if(data.earnings > 0){
                $('#shouyi-tab').show();
            }
            $('#shouyi').html(data.earnings);
            $('#user_count').html(data.user_count);
            $('#stay_put_money').html( data.stay_put_money);
            $('#success_month').html( data.lend);
            $('#has_put_money').html(data.has_put_money);
            $('#receivable').html(data.receivable);
        });
        setTimeout(function () {
            set_field();
        }, '300000')
    }

    set_field();
*/

    layui.use('jquery', function () {
        var $ = layui.jquery;
        $('#test').on('click', function () {
         /*   parent.message.show({
                skin : 'cyan'
            });*/
        });
    });
    layui.use('table', function () {
        var table = layui.table;
    });
    layui.use('layer', function () { //独立版的layer无需执行这一句
        var $ = layui.jquery, layer = layui.layer; //独立版的layer无需执行这一句
        var gonggao=function($this){
          /*  var lindex = layer.load();
            var id     = $this.attr('data-id');*/
          /*  $.post('/admin/index.php/system/notice/get_news.html', {id : id}, function (data) {
                $('#ctitle').html(data.info.title);
                $('#ccontent').html(data.info.content);
                $('.ctime').html(data.info.create_time);
                layer.close(lindex);
                layer.open({
                    type         : 1,
                    title        : data.info.title,
                    id           : 'layerDemoes'  //防止重复弹出
                    , btnAlign   : 'c' //按钮居中
                    , shade      : 0.8
                    , shadeClose : true
                    , area       : '600px'
                    , btn        : '确定'
                    , content    : $("#myModal")
                });
            }, 'json');*/
        };
        //自动弹出公告
/*        var auto_notice=function(nid){
            var lindex = layer.load();
            $.post('/admin/index.php/system/notice/get_news.html', {id : nid}, function (data) {
                $('#ctitle').html(data.info.title);
                $('#ccontent').html(data.info.content);
                $('.ctime').html(data.info.create_time);
                layer.close(lindex);
                layer.open({
                    type         : 1,
                    title        : data.info.title,
                    id           : 'layerDemoes'  //防止重复弹出
                    , btnAlign   : 'c' //按钮居中
                    , shade      : 0.8
                    , shadeClose : true
                    , area       : '600px'
                    , btn        : '确定'
                    , content    : $("#myModal")
                });
            }, 'json');
        }*/
/*
        $.getJSON('/admin/index.php/system/notice/getAlertNoticeId.html',function(alert_id){
            if(alert_id && alert_id > 0){
                auto_notice(alert_id);
            }
        });*/

        //----------------------------

        var tips;
        $(".question0").hover(function(data){
            tips= layer.tips('所有商户余额的总和', this, {
                tips: [2, '#333'],
                time: 0
            });
        },function(){
            layer.close(tips);
        });
        $(".question1").hover(function(data){
            tips= layer.tips('账户余额为充值消费后剩余金额（我的余额）', this, {
                tips: [2, '#333'],
                time: 0
            });
        },function(){
            layer.close(tips);
        });
        $(".question2").hover(function(data){
            tips= layer.tips('未审核的用户总数', this, {
                tips: [2, '#333'],
                time: 0
            });
        },function(){
            layer.close(tips);
        });
        $(".question3").hover(function(data){
            tips= layer.tips('收益金额=还款总额（本金+利息+违约金+展期费用）-用户实际到账金额', this, {
                tips: [2, '#333'],
                time: 0
            });
        },function(){
            layer.close(tips);
        });
        $(".question4").hover(function(data){
            tips= layer.tips('今天审核通过后未放款金额总和', this, {
                tips: [2, '#333'],
                time: 0
            });
        },function(){
            layer.close(tips);
        });
          $(".question5").hover(function(data){
            tips= layer.tips('平台所有用户回款金额总和', this, {
                tips: [2, '#333'],
                time: 0
            });
        },function(){
            layer.close(tips);
        });
          $(".question6").hover(function(data){
            tips= layer.tips('今天放款用户金额总和', this, {
                tips: [2, '#333'],
                time: 0
            });
        },function(){
            layer.close(tips);
        });

        $(document).on('touchstart', '.news', function () {
            gonggao($(this));
        }).on('click', '.news', function () {
            gonggao($(this));
        });
    });

    // $(document).on('click', '.notice-list', function () {


//      parent.tab.close('member');
//         parent.tab.tabAdd({
//             url   : '/admin/index.php/system/notice/index.html?', //地址
//             icon  : '&#xe651;',
//             title : '系统公告',
         // id    : 'member'
//         });
    // });
    $(".close").click(function () {
        $(".layui-layer-pages").hide();
    })

</script>
<!-- <script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/echarts.min.js"></script>
<script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts-gl/echarts-gl.min.js"></script>
<script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts-stat/ecStat.min.js"></script>
<script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/extension/dataTool.min.js"></script>
<script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/map/js/china.js"></script>
<script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/map/js/world.js"></script>
<script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/extension/bmap.min.js"></script>
<script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/simplex.js"></script>
<script type="text/javascript">
  var dom = document.getElementById("container");
  
  var countall1 = "<?php echo htmlentities($countall[0]); ?>";
  var countall2 = "<?php echo htmlentities($countall[1]); ?>";
  var countall3 = "<?php echo htmlentities($countall[2]); ?>";

  var myChart = echarts.init(dom);
  var app = {};
  option = null;
  option = {
      legend: {},
      tooltip: {},
      dataset: {
          source: [
              ['product', '报价量', '营业额', '成本'],
              ['2018.03', countall1, countall2,countall3],
              ['2018.04', 83.1, 73.4, 55.1],
              ['2018.05', 86.4, 65.2, 82.5],
              ['2018.06', 72.4, 53.9, 39.1]
          ]
      },
      xAxis: {type: 'category'},
      yAxis: {},
      // Declare several bar series, each will be mapped
      // to a column of dataset.source by default.
      series: [
          {type: 'bar'},
          {type: 'bar'},
          {type: 'bar'}
      ]
  };
  ;
  if (option && typeof option === "object") {
      myChart.setOption(option, true);
  }
</script> -->
<!-- <script type="text/javascript">
    var chart = null;

                 // 回款曲线图
    $.ajax({
        type: "post",
        url:  "/admin/main/repayment",
        contentType: "application/json",
        dataType: "json",
            success: function(data) {
                console.log(data);
                chart = Highcharts.chart('container1', {
                    chart       : {
                        type : 'area',
                        zoomType: 'x'
                    },
                    title       : {
                        text : '30天回款曲线'
                    },
                    colors: ['#009688'],
        //          subtitle: {
        //              text: document.ontouchstart === undefined ?
        //              '鼠标拖动可以进行缩放' : '手势操作进行缩放'
        //          },
                    xAxis       : {
                        categories : data.monthlist,
                    },
                    yAxis       : [{
                        title    : {
                            text : ''
                        },
                        min: 0,
                        opposite : false

                    }, {
                        title    : {
                            text : ''
                        },
                            min:0,
                        opposite : false
                    }],
                    legend      : {
                        layout          : 'vertical',
                        backgroundColor : '#FFFFFF',
                        floating        : false,
                        align           : 'center',
                        verticalAlign   : 'bottom',
                        y: 0,
                    },
                    tooltip     : {
                        formatter : function () {
                            return '<b>' + this.series.name + '</b><br/>' +
                                this.x + ': ' + this.y;
                        },dateTimeLabelFormats : {
                            millisecond : '%H:%M:%S.%L',
                            second      : '%H:%M:%S',
                            minute      : '%H:%M',
                            hour        : '%H:%M',
                            day         : '%Y-%m-%d',
                            week        : '%m-%d',
                            month       : '%Y-%m',
                            year        : '%Y'
                        },backgroundColor : 'rgba(0,0,0,0.8)'//提示框背景色
                        ,style: {
                            color:'#fff'
                        }
                        ,borderColor: 'rgba(0,0,0,0.8)'
                    },
                    exporting   : {enabled : false},//隐藏导出图片
                    plotOptions : {
                        area : {
                            fillColor : {
                                linearGradient : {
                                    x1 : 0,
                                    y1 : 0,
                                    x2 : 0,
                                    y2 : 1
                                },
                                stops          : [
                                    [0, Highcharts.getOptions().colors[7]],
                                    [1, Highcharts.Color(Highcharts.getOptions().colors[7]).setOpacity(0).get('rgba')]
                                ]
                            },
                            marker    : {
                                radius : 2
                            },
                            lineWidth : 1,
                            states    : {
                                hover : {
                                    lineWidth : 1
                                }
                            },
                            threshold : null
                        }
                    },
                    series      : [{
                        name  : "回款曲线",
                        // data  : data.monthlist,
                        data: data.countlist,
                        yAxis : 1,
                        marker: {
                            enabled: false //去掉线图中的实心点，鼠标移入显示
                        },
                        events: {
                            //控制图标的图例legend不允许切换
                            legendItemClick: function (event){
                                return false; //return  true 则表示允许切换
                            }
                        }
                    }]
                });
            }
        });
        //          新增用户
       $.ajax({
        type: "post",
        url:  "/admin/main/zhuceliang",
        contentType: "application/json",
        dataType: "json",
        success: function(data) {
            // console.log(data);
            var chart = Highcharts.chart('container2', {
                title : {
                    text: '新增用户'
                },
                chart : {
                    type : 'line',
                    zoomType: 'x'
                },
                title : {
                    text : '新增用户'
                },
                colors: ['#009688'],
    //          subtitle: {
    //              text: document.ontouchstart === undefined ?
    //              '鼠标拖动可以进行缩放' : '手势操作进行缩放'
    //          },
                xAxis : {
                    categories : data.monthlist

                },

                yAxis        : [{
                    title    : {
                        text : ''
                    },
                    opposite : false,

                }, {
                    title    : {
                        text : ''
                    },
                    opposite : false
                }],
                legend       : {
                    layout          : 'vertical',
                    backgroundColor : '#FFFFFF',
                    floating        : false,
                    align           : 'center',
                    verticalAlign   : 'bottom',
                    y:0,
                },
                tooltip      : {
                    /*formatter : function () {
                        return '<b>' + this.series.name + '</b><br/>' +
                            this.x + ': ' + this.y;
                    },*/
                    backgroundColor : 'rgba(0,0,0,0.8)'//提示框背景色
                    ,style: {
                        color:'#fff'
                    }
                    ,borderColor: 'rgba(0,0,0,0.8)'
                }, exporting : {
                    enabled : false
                }
                ,//隐藏导出图片
                plotOptions  : {}
                ,
                series       : [{
                    name  : "新增用户",
                    data  : data.countlist,
                    yAxis : 1,
                    marker: {
                        enabled: false
                    },
                    events: {
                        //控制图标的图例legend不允许切换
                        legendItemClick: function (event){
                            return false; //return  true 则表示允许切换
                        }
                    }
                }]
            })
        }
        });
</script>
                -->             </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>