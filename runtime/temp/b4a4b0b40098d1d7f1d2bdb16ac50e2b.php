<?php /*a:2:{s:77:"/www/wwwroot/www.hxpw1998.com/application/admin/view/bcontrast/singleone.html";i:1562556579;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
.text-limit{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;max-width: 110px;}
	.text-center{text-align: center !important;}
</style>

  <!-- 导出完成 -->
<div class="layui-card">
    <div class="layui-card-body">
        <div class="">
            <blockquote class="layui-elem-quote news_search">
   
            </blockquote>
           <!--startprint-->

			<div id="export-table">
				
				<table class="layui-table"  lay-filter="test3"  lay-data="{id: '#test3', toolbar: '#',totalRow: true,limit:20}">
					<thead>
				      <tr>               
                  <th class="taleft" rowspan="2" lay-data="{field:'item_number',width:100, totalRowText: '汇总'}">项目编码</th>
                  <th class="taleft" rowspan="2" lay-data="{field:'project',width:100}">工程项目名称</th>
                  <th class="taleft" rowspan="2" lay-data="{field:'type_of_work',width:100}">工种</th>
                  <th class="taleft" rowspan="2" lay-data="{field:'kongjian',width:100}">空间类型</th>
                  <th class="taleft" rowspan="2" lay-data="{field:'company',}">单位</th>
                  <th class="taleft" colspan="5" lay-data="{field:'A',}" style="text-align:center;">A报表</th>
                  <th class="taleft" colspan="5" lay-data="{field:'B',}" style="text-align:center;">B报表</th>
                  <th class="taleft" colspan="5" lay-data="{field:'C',}" style="text-align:center;">差异</th>
              </tr>
              <tr>
                  <th class="taleft" lay-data="{field:'gclA',width:60,totalRow: true}">工程量</th>
                  <th class="taleft" lay-data="{field:'quotaA',totalRow: true }">辅材基价</th>
                  <th class="taleft" lay-data="{field:'quotaallA',totalRow: true }">辅材合价</th>
                  <th class="taleft" lay-data="{field:'craft_showA',totalRow: true }">人工基价</th>
                  <th class="taleft" lay-data="{field:'craft_showallA',totalRow: true }">人工合价</th>   
                  <th class="taleft" lay-data="{field:'gclB',width:60,totalRow: true}">工程量</th>
                  <th class="taleft" lay-data="{field:'quotaB',totalRow: true }">辅材基价</th>
                  <th class="taleft" lay-data="{field:'quotaallB',totalRow: true }">辅材合价</th>
                  <th class="taleft" lay-data="{field:'craft_showB',totalRow: true }">人工基价</th>
                  <th class="taleft" lay-data="{field:'craft_showallB',totalRow: true }">人工合价</th>   
                  <th class="taleft" lay-data="{field:'gclC',width:60,totalRow: true}">工程量</th>
                  <th class="taleft" lay-data="{field:'quotaC',totalRow: true }">辅材基价</th>
                  <th class="taleft" lay-data="{field:'quotaallC',totalRow: true }">辅材合价</th>
                  <th class="taleft" lay-data="{field:'craft_showC',totalRow: true }">人工基价</th>
                  <th class="taleft" lay-data="{field:'craft_showallC',totalRow: true }">人工合价</th>   
              </tr>
   
					</thead>
					<tbody> 
              <?php if(is_array($newlist) || $newlist instanceof \think\Collection || $newlist instanceof \think\Paginator): if( count($newlist)==0 ) : echo "" ;else: foreach($newlist as $key=>$vo): ?>
                 <tr>
                    <td><?php echo htmlentities($vo['item_number']); ?></td>
                    <td><?php echo htmlentities($vo['project']); ?></td>
                    <td><?php echo htmlentities($vo['type_of_work']); ?></td>
                    <td><?php echo htmlentities($vo['kongjian']); ?></td>
                    <td><?php echo htmlentities($vo['company']); ?></td>
                    <td><?php echo htmlentities($vo['gclA']); ?></td>
                    <td><?php echo htmlentities($vo['quotaA']); ?></td>
                    <td><?php echo htmlentities($vo['quotaallA']); ?></td>
                    <td><?php echo htmlentities($vo['craft_showA']); ?></td>
                    <td><?php echo htmlentities($vo['craft_showallA']); ?></td>
                    <td><?php echo htmlentities($vo['gclB']); ?></td>
                    <td><?php echo htmlentities($vo['quotaB']); ?></td>
                    <td><?php echo htmlentities($vo['quotaallB']); ?></td>
                    <td><?php echo htmlentities($vo['craft_showB']); ?></td>
                    <td><?php echo htmlentities($vo['craft_showallB']); ?></td>
                    <td><?php echo htmlentities($vo['gclC']); ?></td>
                    <td><?php echo htmlentities($vo['quotaC']); ?></td>
                    <td><?php echo htmlentities($vo['quotaallC']); ?></td>
                    <td><?php echo htmlentities($vo['craft_showC']); ?></td>
                    <td><?php echo htmlentities($vo['craft_showallC']); ?></td>
                  </tr>
              <?php endforeach; endif; else: echo "" ;endif; ?>
                  <!-- <tr>
                    <td>Djksa</td>
                    <td>拆房子</td>
                    <td>打拆工</td>
                    <td>大厅</td>
                    <td>m2</td>
                    <td>5</td>
                    <td>6</td>
                    <td>7</td>
                    <td>8</td>
                    <td>9</td>
                    <td>9</td>
                    <td>8</td>
                    <td>7</td>
                    <td>6</td>
                    <td>5</td>
                    <td>4</td>
                    <td>2</td>
                    <td>0</td>
                    <td>-2</td>
                    <td>-4</td>
                  </tr> -->
					</tbody>
                   
					
			
			</div>

          <!--endprint-->
        </div>
    </div>
</div>

    
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script language=javascript>
function doPrint() { 
		bdhtml=window.document.body.innerHTML; 
		sprnstr="<!--startprint-->"; 
		eprnstr="<!--endprint-->"; 
		prnhtml=bdhtml.substr(bdhtml.indexOf(sprnstr)+17); 
		prnhtml=prnhtml.substring(0,prnhtml.indexOf(eprnstr)); 
		window.document.body.innerHTML=prnhtml; 
		window.print(); 
}
</script>
<script>
  $(document).ready(function(){
		//改变报表标签
    $(document).on('change','#changestatus',function(){
      var id= $(this).attr('data-id'),customerid = $(this).attr('data-customerid'),status = $(this).val();
      // mystatus(id,customerid);
      $.ajax({
          type : 'post',
          url  :  '<?php echo url("offerlist/status"); ?>',
          dataType : 'json',
          data : {id:id,customerid:customerid,status:status},
          success : function(re){
            console.log(re);
            // if(re.status == 0){
               alert(re.msg);
               window.location.reload();
            // }else{
            //   alert(re.msg);return false;
            // }
          }
        });
    });
		
		// $('#export').click(function(){
		// 	window.location.href = "<?php echo url('admin/offerlist/excel_export',['customerid'=>input('customerid'),'report_id'=>input('report_id')]); ?>";
		// });
    
  });
</script>
<script type="text/javascript">
  layui.use('table', function(){
   var table = layui.table;
 });
</script>


</body>

</html>