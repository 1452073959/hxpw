<?php /*a:2:{s:72:"/www/wwwroot/www.hxpw1998.com/application/admin/view/quote/addmould.html";i:1563328694;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
.p_tex{line-height:38px;color:#666;}
.layui-form-label{width: 120px}
.layui-table-view .layui-table{width:100%}
.layui-form-label{width:40px}
.layui-form-item{margin:20px 0}
#frameid{    width: 250px;
    padding: 10px;}
.text-center{text-align: center !important;}
.text-large{font-size:24px!important;font-weight:900!important;}
.form-control{display: inline-block;padding: 5px;}
input[type="checkbox"]{
  display: inline-block!important;
}
.myinput{border:none;display: inline-block;background-color: #eeeeee;padding:5px;}
.text-limit{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;max-width: 110px;}
</style>
<div class="layui-card">
    <div class="layui-card-header">
		<a class="layui-btn layui-btn-sm" data-selector="addconditions">增加工种</a>
	</div>
    <div class="layui-card-body">
        <form id="myform" class="form-horizontal" action="<?php echo url('admin/Offerlist/edit'); ?>" method="post">
          <table class="layui-table mould"> 
                <thead>
                    <tr>
						<th rowspan="2" colspan="1"><img style="max-width:200px;" src="/static/imgs/logo.png"></th>
                        <th class="text-center text-large" colspan="6"><h3>住宅装饰工程造价预算书</h3></th>
						<th rowspan="2" colspan="3"></th>
                    </tr>
					<tr>
						<th class="text-center" colspan="6">全国统一24小时客服热线：400-6281-968</th>
					</tr>
                    <tr>
                        <th style="text-align:center;" colspan="10">
                          单位：
                        </th>        
                    </tr>
                    <tr>
                        <th colspan="3">工程名称：</th>       
                        <th colspan="3">客户姓名：</th>       
                        <th colspan="2">设计师姓名：</th>       
                        <th colspan="2">报价师姓名：</th>       
                    </tr>
                    <tr>
                        <!-- <th class="text-center" rowspan="2">序号</th>          -->
                        <th class="text-center" rowspan="2" colspan="2">工程项目名称</th>         
                        <th class="text-center" rowspan="2">工程量</th>       
                        <th class="text-center" rowspan="2">单位</th>
                        <th class="text-center" colspan="2">辅材费</th> 
                        <th class="text-center" colspan="2">人工费</th>    
                        <th class="text-center" rowspan="2">施工工艺及材料说明</th> 
                    </tr>
                    <tr>   
                        <th class="text-center">辅材基价</th>       
                        <th class="text-center">辅材合价</th>       
                        <th class="text-center">人工基价</th>       
                        <th class="text-center">人工合价</th> 
                      </tr>
                </thead>

                <tbody> 
                    <tr data-cate="">
						<!-- <td>1</td> -->
						<td colspan="2">
							<select class="form-control" data-selector="conditions">
								<option value="">选择工种</option>
								<?php if(is_array($tree) || $tree instanceof \think\Collection || $tree instanceof \think\Paginator): $i = 0; $__LIST__ = $tree;if( count($__LIST__)==0 ) : echo "暂无数据" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
									<option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['name']); ?></option>
								<?php endforeach; endif; else: echo "暂无数据" ;endif; ?>
							</select>
                            <a></a>
                          	<a class="layui-icon layui-icon-delete" data-selector="delete"></a>
							<!--<a class="layui-btn layui-btn-sm" data-selector="delete">删除</a>-->
						</td>
						<td colspan="7">
							<select class="form-control" data-selector="room">
								<option value="">选择空间类型</option>
							</select>
							<a title="添加新的空间类型" class="addone" data-conditionsid="" data-status="on">
							  <i class="layui-icon"></i>
							</a>
							<a class="layui-btn layui-btn-xs getofferlist" data-selector="getdata" data-conditionsid="" data-roomid="">获取数据</a>
						</td>
                    </tr>
					<!--<tr>
						<td class="text-center" colspan="2">小计</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>-->
                </tbody>
            </table>
			<a class="layui-btn layui-btn-sm" data-selector="addconditions">增加工种</a>
            
            <div class="layui-form-item">
                <div class="layui-input-block">
					<input class="form-control" type="text" placeholder="请输入模板名称" required="" name="mouldname" />
                    <button class="layui-btn ajax-post" target-form="form-horizontal">保存模板</button>
                </div>
            </div>
        </form>
		</div>
</div>

<div>

</div>
<!-- 弹窗 -->
<div class="layui-card" style="height:100vh;position:fixed;top:0;display: none;opacity: 0.95;padding: 10px;background-color:#e2e2e2 " id="offerlist">
    <div class="layui-card-body" style="overflow-y: scroll;height: 90vh;">
        <div class="layui-form">
              <div class="layui-btn-container" style="position: fixed;top:0;z-index: 9999;">
                <input id="addlist" class="layui-btn layui-btn-sm" type="submit" form="offerlistform" value="添加报价条目">
                <button class="layui-btn layui-btn-sm" id="close">关闭</button>
              </div>
          
               <form id="searchform" style="margin:30px 0;position: fixed;top:0;z-index: 9999">
                <input class="" type="search" name="project" style="padding: 5px;" placeholder="输入项目名称">
                <input class="" type="search" name="item_number" style="padding: 5px;" placeholder="输入项目编号">
                <input type="hidden" name="type" value="search" >
                <button type="submit" class="layui-btn">搜索</button>
              </form>
              <!-- <form id="searchform" style="margin-top:30px;">
                <input class="" type="search" name="project" style="padding: 5px;" placeholder="输入项目名称">
                <input class="" type="search" name="item_number" style="padding: 5px;" placeholder="输入项目编号">
                <input type="hidden" name="searchtype" >
                <button type="submit" class="layui-btn">搜索</button>
              </form> -->
              <form id="offerlistform" style="margin-top:50px;">
                <table class="layui-table">
                  <thead >
                      <th><input type="checkbox" id="checkall" name="checkall"></th>
                      <th>ID</th>
                      <th>项目编号</th>
                      <th>所属公司</th>
                      <th>工种</th>
                      <th>项目名称</th>
                      <th>单位</th>                   
                      <th>辅材单价</th>
                      <th>人工单价</th>
                      <th>综合价</th>
                      <!-- <th>施工工艺与材料说明</th> -->
                  </thead>
                  <tbody> 
                  </tbody>
              </table>
            </form>
        </div>
    </div>
</div>
<form style="display:inline-block;" id="addone"></form>

    
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
          //搜索
         $('#searchform').submit(function(){
          var conditionsid = $('#searchform').attr('data-conditionsid'),roomid = $('#searchform').attr('data-roomid');
           console.log(roomid);
          var project = $('input[name="project"]').val(),item_number = $('input[name="item_number"]').val();
           $.ajax({
            url:"<?php echo url('admin/quote/getdata'); ?>",
            type:"post",
            data:{conditionsid:conditionsid,roomid:roomid,type:'search',project:project,item_number:item_number},
            success:function(result){
             console.log(result);
             $('#offerlistform tbody').html(result.data);
             $('#addlist').attr({'data-conditionsid':conditionsid,'data-roomid':roomid});
             // that.parent().parent().attr('data-cate','tr'+conditionsid)

            }
           });
          return false;
         });
		//增加工种
		$('a[data-selector="addconditions"]').click(function(){
			var html = '<tr data-cate=""><td colspan="2"><select class="form-control" data-selector="conditions"><option value="">选择工种</option><?php if(is_array($tree) || $tree instanceof \think\Collection || $tree instanceof \think\Paginator): $i = 0; $__LIST__ = $tree;if( count($__LIST__)==0 ) : echo "暂无数据" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['name']); ?></option><?php endforeach; endif; else: echo "暂无数据" ;endif; ?></select><a class="layui-icon layui-icon-delete" data-selector="delete"></a><!--<a class="layui-btn layui-btn-sm" data-selector="delete">删除</a>--></td><td colspan="7"><select class="form-control" data-selector="room"><option value="">选择空间类型</option></select><a title="添加新的空间类型" class="addone" data-conditionsid="" data-status="on"><i class="layui-icon"></i></a><a class="layui-btn layui-btn-xs getofferlist" data-selector="getdata" data-conditionsid="" data-roomid="">获取数据</a></td></tr><!--<tr><td class="text-center" colspan="2">小计</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>-->';
			$('table.mould').append(html);
		});
        //按钮开关
        $('#newentries').click(function(){
          $('a.getofferlist').toggle().siblings('select').toggle();
          $('a[data-name="delete"]').toggle();
        });
		//删除工种
		$(document).on('click','[data-selector="delete"]',function(){
			$(this).parent().parent().remove()
		});
		//根据工种获取空间类型
		$(document).on('change','[data-selector="conditions"]',function(){
			var that = $(this),conditionsid = that.val();
			if(conditionsid){
				$.ajax({
					url:"<?php echo url('admin/quote/getroom'); ?>",
					type:"post",
					data:{id:conditionsid},
					success:function(result){
						that.parent().parent().find('[data-selector="getdata"]').attr('data-conditionsid',conditionsid);
                        $('#searchform').attr('data-conditionsid',conditionsid);//搜索
						that.parent().parent().find('.addone').attr('data-conditionsid',conditionsid);
						if(result.msg == 'fail'){
							that.parent().parent().find('[data-selector="room"]').html('<option value="">选择空间类型</option>');
							return false;
						}
						that.parent().parent().find('[data-selector="room"]').html('<option value="">选择空间类型</option>'+result.data);
					}
				});
			}
		});
		//选择空间类型
		$(document).on('change','[data-selector="room"]',function(){
			var roomid = $(this).val();
			$(this).parent().find('[data-selector="getdata"]').attr('data-roomid',roomid);
			
		});
		//弹窗显示获取定额数据
		$(document).on('click','[data-selector="getdata"]',function(){
			var that = $(this),conditionsid = that.attr('data-conditionsid'),roomid = that.attr('data-roomid');
			if(conditionsid == '' || roomid == ''){alert('工种或者空间类型未选择');return false;}
			$('#offerlist').show();
			$.ajax({
				url:"<?php echo url('admin/quote/getdata'); ?>",
				type:"post",
				data:{conditionsid:conditionsid,roomid:roomid},
				success:function(result){
					$('#offerlistform tbody').html(result.data);
					$('#addlist').attr({'data-conditionsid':conditionsid,'data-roomid':roomid});
					that.parent().parent().attr('data-cate','tr'+conditionsid)
					
				}
			});
		});
		//关闭弹窗
		$('#close').click(function(){
		    $('#offerlist').toggle();
		});
		//全选/全不选
		var i=0;
		$('#checkall').click(function(){
		   if(i==0){
				//把所有复选框选中
				$("#offerlist td :checkbox").prop("checked", true);
				i=1;
			}else{
				$("#offerlist td :checkbox").prop("checked", false);
				i=0;
			}
		});
		//选择定额数据
		$('#offerlistform').on('mouseover','tbody tr',function(){$(this).css({cursor:'pointer'})});
		$('#offerlistform').on('click','tr',function(){
			if($(this).find(':checkbox').prop("checked")){
				$(this).find(':checkbox').prop('checked',false);
			}else{
				$(this).find(':checkbox').prop('checked',true);
			}
		});
		//添加选定的定额数据到模板
		$('#addlist').click(function(){
			var that = $(this);
		  var conditionsid = $(this).attr('data-conditionsid'),roomid = $(this).attr('data-roomid');
		  var data = [];//条目id
		  var select = $(this).attr('data-select');
		    $(':checked[name="check"]').each(function(){
		        data.push($(this).attr('data-id'));
		    });
		    if(data.length<1){alert('未选择条目');return false;}
		    $.ajax({
		        url:"<?php echo url('admin/quote/returndata'); ?>",
		        type:"post",
		        data:{conditionsid:conditionsid,roomid:roomid,data:data,type:that.attr("data-type")},
		        success:function(result){
					if(result.msg == 'fail'){
					    alert('添加失败');
					}else{
						var curtr = $('tr[data-cate="tr'+conditionsid+'"]'), currowspan = curtr.find('td:first').attr('rowspan');
						currowspan = currowspan ? currowspan * 1: 1;
						if(that.attr("data-type") == "addnewquote"){
							$('#tr'+roomid).after(result.data);
						}else{
							curtr.after(result.data);
						}
						that.attr("data-type",'');
						
						// curtr.find('td:first').attr('rowspan',currowspan*1+result.length*1 + 1);
					    $("#offerlist td :checkbox").prop("checked", false);//添加成功取消勾选
					    $('#close').click();
					}
		        }
		    });
		    return false;
		});
		//新增指定空间类型下的定额条目
		$(document).on('click','a.addnewquote',function(){
			var that = $(this),roomid = that.attr("data-roomid"),conditionsid = that.attr('data-conditionsid');
			$('#offerlist').show();
			$.ajax({
				url:"<?php echo url('admin/quote/getdata'); ?>",
				type:"post",
				data:{conditionsid:conditionsid,roomid:roomid},
				success:function(result){
					$('#offerlistform tbody').html(result.data);
					$('#addlist').attr({'data-conditionsid':conditionsid,'data-roomid':roomid,'data-type':"addnewquote"});
					//that.parent().parent().attr('data-cate','tr'+conditionsid)
					
				}
			});
			
		});
		//删除空间类型以及下面的定额条目
		$(document).on('click','a.deleteroom',function(){
			if(!confirm('将删除该类型下的已添加的条目')){return false;}
			$('tr.'+$(this).attr('data-son')).remove();
			$(this).parent().parent().remove();
			// var td = $('tr[data-cate="'+$(this).attr('data-cate')+'"]').find('td:first');//序号所在单元格
			// var tdrowspan = td.attr('rowspan');
			// td.attr('rowspan',tdrowspan - $(this).attr('data-length') - 1);
		});
		//删除条目
		$(document).on('click','a.deletequote',function(){
			$(this).parent().parent().remove();
			// var td = $('tr[data-cate="'+$(this).attr('data-cate')+'"]').find('td:first');//序号所在单元格
			// var tdrowspan = td.attr('rowspan');
			// td.attr('rowspan',tdrowspan - 1);
			var roomtd = $('a[data-son="'+$(this).attr("data-parent")+'"]');//对应的空间类型
			var length = roomtd.attr('data-length');
			roomtd.attr('data-length',length - 1);
		});
        //新增自定义空间类型
        $(document).on('click','a.addone',function(){
			if($(this).attr("data-conditionsid") == ''){alert('未选择工种');return false;}
            var html = '<input form="addone" type="text" class="form-control" placeholder="新定义空间类型" name="name"><input form="addone" type="text" class="form-control" placeholder="备注" name="other"><input type="hidden" name="pid" form="addone"><input type="submit" form="addone" class="layui-btn layui-btn-sm" id="addnewtype" value="新增">';
            if($(this).attr('data-status') == "on"){
				$('input[form="addone"]').remove();
                $(this).after(html);
				$(this).siblings('input[name=pid]').val($(this).attr("data-conditionsid"));
				$('a.addone').attr('data-status','on');
				$(this).attr('data-status','off');
            }else{
                $(this).siblings('input').remove();
				$('a.addone').attr('data-status','off');
				$(this).attr('data-status','on');
            }
        });
        //保存空间类型
		$('#addone').submit(function(){
          var data = $(this).serialize();
            $.ajax({
                url:"<?php echo url('admin/Offertype/addoneroom'); ?>",
                data:data,
                type:"post",
                success:function(result){
					alert(result.msg);
					if(result.code==1){
						$('#addnewtype').siblings('a[data-selector="getdata"]').attr('data-roomid',result.data.id);
						var html = '',selected = '',res = result.data.res;
						for(var i=0;i<res.length;i++){
							selected = (result.data.id == res[i]['id']) ? 'selected=""' : '';
							html += '<option '+selected+' value="'+res[i]['id']+'">'+res[i]['name']+'</option>'
						}
						$('#addnewtype').siblings('select').html(html);//更新空间类型
						$('a.addone').attr('data-status','on').siblings('input').remove();
					}
                }
            });
            return false;
        });
        //计算辅材合价和人工合价
        $(document).on('change','input[name*="gcl"]',function(){
            var gcl = parseInt( $(this).val() );
            var fd = $(this).parent().parent().find('td').eq(3).html();//辅材基价
            var rd = $(this).parent().parent().find('td').eq(5).html();//人工基价
            $(this).parent().parent().find('td').eq(4).html(gcl*fd);
            $(this).parent().parent().find('td').eq(6).html(gcl*rd);
        });
        //保存报表
        $('#myform').submit(function(){
            $.ajax({
                url:"<?php echo url('admin/quote/savemould'); ?>",
                type:"post",
                data:$('#myform').serialize(),
                success:function(result){
                    alert(result.msg)
					if(result.code == 1){
						window.location.href = result.url;
					}
					
               }
            });
            return false;
        });

    });
</script>

</body>

</html>