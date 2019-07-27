<?php /*a:2:{s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/indent/index.html";i:1557399130;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
/* 修改文件夹图标：*/
/** 未展开 */
.treeTable-icon .layui-icon-layer:before {content: "\e672";}
/** 展开 */
.treeTable-icon.open .layui-icon-layer:before {content: "\e672";}
/*修改文件图标：*/
.treeTable-icon .layui-icon-file:before {content: "\e621";}
.tanceng{width: 100%;text-align: center; line-height: 30px;}
.layui-form-label{width:40px}
.layui-form-item{margin:20px 0}
#edt-search{
            height: 33px;
            line-height: 33px;
            padding: 0 7px;
            border: 1px solid #ccc;
            border-radius: 2px;
            margin-bottom: -2px;
            outline: none;
        }

#edt-search:focus {
    border-color: #009E94;
}
#frameid{width: 299px;padding: 10px;border: 1px solid #D2D2D2;}
#frameid option{}
</style>
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-form">
            <blockquote class="layui-elem-quote news_search">
                <div class="layui-btn-group">
                    <button class="layui-btn" id="btn-expand">全部展开</button>
                    <button class="layui-btn" id="btn-fold">全部折叠</button>
                    <button class="layui-btn" id="btn-refresh">刷新表格</button>
                </div>
                <input id="edt-search" type="text" placeholder="输入关键词" style="width: 120px;"/>&nbsp;&nbsp;
                <button class="layui-btn" id="btn-search">&nbsp;&nbsp;搜索&nbsp;&nbsp;</button> 
            </blockquote>
            <!-- <?php dump($data);?>  -->
            <table id="table1" class="layui-table" lay-filter="table1"></table>
        </div>
    </div>
</div>

    
<script type="text/javascript" src="/static/admin/js/common.js"></script>
<script type="text/html" id="oper-col">
    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="add">添加</a>
    <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">修改</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="editstatu">禁用</a>
</script>
<!-- <script src="http://code.jquery.com/jquery-2.1.4.min.js"></script> -->
<script type="text/javascript">

    layui.config({
        base: '/static/module/'
    }).extend({
        treetable: 'treetable-lay/treetable'
    }).use(['layer', 'table', 'treetable'], function () {
        var $ = layui.jquery;
        var table = layui.table;
        var layer = layui.layer;
        var treetable = layui.treetable;

        // 渲染表格
        var renderTable = function () {
            layer.load(2);
            treetable.render({
                // toolbar: '#', //设置打印
                treeColIndex: 1,
                treeSpid: -1,
                treeIdName: 'id',
                treePidName: 'pid',
                treeDefaultClose: true,
                treeLinkage: false,
                elem: '#table1',
                url: '<?php echo url("indent/TreeType"); ?>',
                // url: '/static/module/json/data.json',
                page: false,
                cols: [[
                    {type: 'numbers'},
                    {field: 'name', title: '名称'},
                    {field: 'id', title: 'id'},
                    {field: 'levelid', title: '级别'},
                    {field: 'other', title: '其他备注'},
                    // {field: 'pid', title: '父级id'},
                    {field: 'status', title: '状态0启用1禁用'},
                    {templet: '#oper-col', title: '操作'}
                ]],
                
                done: function () {
                    layer.closeAll('loading');
                }
            });
        };

        // 搜索显示
        $('#btn-search').click(function () {
            var keyword = $('#edt-search').val();
            var searchCount = 0;
            $('#table1').next('.treeTable').find('.layui-table-body tbody tr td').each(function () {
                $(this).css('background-color', 'transparent');
                var text = $(this).text();
                if (keyword != '' && text.indexOf(keyword) >= 0) {
                    $(this).css('background-color', 'rgba(250,230,160,0.5)');
                    if (searchCount == 0) {
                        treetable.expandAll('#table1');
                        $('html,body').stop(true);
                        $('html,body').animate({scrollTop: $(this).offset().top - 150}, 500);
                    }
                    searchCount++;
                }
            });
            if (keyword == '') {
                layer.msg("请输入搜索内容", {icon: 5});
            } else if (searchCount == 0) {
                layer.msg("没有匹配结果", {icon: 5});
            }
        });

         // treetable.foldAll('#table1');
        renderTable();

        $('#btn-expand').click(function () {
            treetable.expandAll('#table1');
        });

        $('#btn-fold').click(function () {
            treetable.foldAll('#table1');
        });

        $('#btn-refresh').click(function () {
            renderTable();
        });

        //监听工具条
        table.on('tool(table1)', function (obj) {
            var data = obj.data;
            var layEvent = obj.event;

            if (layEvent === 'del') {
              layer.confirm("确认要删除<font style='color:red'>"+data.name+"</font>删除后不能恢复", { title: "删除确认" }, function (index) {           
                 $.ajax({
                    type : 'post',
                    url  :  '<?php echo url("indent/dels"); ?>',
                    dataType : 'json',
                    data : {msg:'del',id:data.id},
                    success : function(re){
                      if(re.status == 1){
                         layer.msg(re.msg);return false;
                      }
                      layer.msg(re.msg);
                      window.location.reload();
                    }
                   });
              });              
            } else if (layEvent === 'edit') {
              //遍历扁插下拉上级
              if (data.levelid == 3) {
                var josnarr = JSON.parse(JSON.stringify(<?php echo json_encode($data);?>));
                // alert(josnarr[0]['name']);return false;
                var _nbox = '';
                for (var i = 0; i < josnarr.length; i++) {
                   
                   if(josnarr[i]['id']==data.pid){
                     _nbox += '<option selected value="'+josnarr[i]['id']+'">'+josnarr[i]['name']+'</option>';
                   }else{
                     _nbox += '<option value="'+josnarr[i]['id']+'">'+josnarr[i]['name']+'</option>';
                   }
                }
                var newselect = '<div class="layui-form-item"><label class="layui-form-label">上级</label><div class="layui-input-inline"><select name="frameid" id="frameid">'+_nbox+'</select></div></div>';//下拉框
                }else{
                  var newselect = '';
                }
                 //遍历扁插下拉上级end
                var inputs = '<div class="newbox">'+newselect+'<div class="layui-form-item"><label class="layui-form-label">名称 </label><div class="layui-input-inline w300"><input type="text" name="name" autocomplete="off" value="'+data.name+'" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">其他 </label><div class="layui-input-inline w300"><input type="text" name="other" autocomplete="off" value="'+data.other+'" class="layui-input"></div></div><div class="layui-input-inline w300"><button id="edit_ajax" style="margin-left:70px" class="layui-btn ajax-post">立即提交</button></div></div>';
                //页面层
                layer.open({
                  type: 1,
                  fixed: false,    //取消固定定位，因为固定定位是相对body的
                  offset: ['30%', '30%'],   //相对定位
                  skin: 'layui-layer-rim', //加上边框
                  area: ['420px', '280px'], //宽高
                  title:'修改信息',
                  content: inputs
                });
                $("#edit_ajax").click(function() {
                  var frameids = $("#frameid").val();
                  var names = $(this).parents(".newbox").find("input[name='name']").val();
                  var others = $(this).parents(".newbox").find("input[name='other']").val();
                  if (!names || !others) {layer.msg('信息不能为空');return false;}
                  if(frameids==data.pid){
                    frameids = 'current';//下拉是否为当前
                  }
                  // alert(frameids);
                  $.ajax({
                    type : 'post',
                    url  :  '<?php echo url("indent/edits"); ?>',
                    dataType : 'json',
                    data : {name:names,other:others,id:data.id,frameid:frameids},
                    success : function(re){
                      if(re.status == 0){
                         layer.msg(re.msg);window.location.reload();
                      }else{
                        layer.msg(re.msg);return false;
                      }
                    }
                   });
                });
            }else if (layEvent === 'add'){
                      var inputs = '<div class="newaddbox"><div class="layui-form-item"><label class="layui-form-label">名称 </label><div class="layui-input-inline w300"><input type="text" name="name" autocomplete="off" value="" class="layui-input"></div></div><div class="layui-form-item"><label class="layui-form-label">其他 </label><div class="layui-input-inline w300"><input type="text" name="other" autocomplete="off" value="" class="layui-input"></div></div><div class="layui-input-inline w300"><button id="add_ajax" style="margin-left:70px" class="layui-btn ajax-post">立即提交</button></div></div>';
                    //页面层
                    layer.open({
                      type: 1,
                      fixed: false,    //取消固定定位，因为固定定位是相对body的
                      offset: ['30%', '30%'],   //相对定位
                      skin: 'layui-layer-rim', //加上边框
                      area: ['420px', '240px'], //宽高
                      title: '添加<font style="color:red">'+data.name+'</font>下级',
                      content: inputs
                    });
                    $("#add_ajax").click(function() {
                      var names = $(this).parents(".newaddbox").find("input[name='name']").val();
                      var others = $(this).parents(".newaddbox").find("input[name='other']").val();
                      if (!names || !others) {layer.msg('信息不能为空');return false;}
                      $.ajax({
                        type : 'post',
                        url  :  '<?php echo url("indent/adds"); ?>',
                        dataType : 'json',
                        data : {name:names,other:others,pid:data.id,levelid:data.levelid},
                        success : function(re){
                          if(re.status == 0){
                             layer.msg(re.msg);window.location.reload();
                          }else{
                            layer.msg(re.msg);return false;
                          }
                        }
                       });
                    });      
            }else if (layEvent === 'editstatu'){
                      if(data.status == 0){
                        var newstatu = '禁用';
                        var colors = 'red';

                      }else{
                         var newstatu = '操作';
                         var colors = 'green';
                      }

                      layer.confirm("确认要<font style='color:"+colors+"'>"+newstatu+"</font>", { title: "确认" }, function (index) {         
                       $.ajax({
                          type : 'post',
                          url  :  '<?php echo url("indent/editstatu"); ?>',
                          dataType : 'json',
                          data : {id:data.id,status:data.status},
                          success : function(re){
                            if(re.status == 1){
                               layer.msg(re.msg);return false;
                            }
                            layer.msg(re.msg);
                            window.location.reload();
                          }
                         });
                    });      
            }
        });
    });

    // $(function(){
    //   $("table").on("click","tr td",function(){
    //     var _this = $(this).children();
    //     alert(_this.html())
    //   })
      
    // })

</script>

</body>

</html>