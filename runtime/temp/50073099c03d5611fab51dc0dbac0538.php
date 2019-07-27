<?php /*a:2:{s:77:"/www/wwwroot/www.hxpw1998.com/application/admin/view/settlement/contrast.html";i:1561194636;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
.ulall{overflow:auto;}
.ulall li{float:left;border: 1px solid #ccc;padding: 5px;margin-right: 20px;}
.layui-input{width:100px;}
</style>

  <!-- 导出完成 -->
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-form">

            <blockquote class="layui-elem-quote news_search">
                <div class="layui-inline">
                   <ul class="ulall">
                      <li>客户姓名：<?php echo htmlentities($data['0']['customer_name']); ?></li>
                      <li>工程地址：<?php echo htmlentities($data['0']['address']); ?></li>
                      <li>联系电话：</li>
                      <li>报价师姓名：<?php echo htmlentities($data['0']['quoter_name']); ?></li>
                      <li>设计师姓名：<?php echo htmlentities($data['0']['designer_name']); ?></li>
                   </ul>
                </div>     
              
            </blockquote>
            <script type="text/html" id="toolbarDemo">
              <div class="layui-btn-container">
                <!-- <button class="layui-btn layui-btn-sm" lay-event="getCheckData" id="batch">批量修改数据</button> -->
                <!-- <button class="layui-btn layui-btn-sm" lay-event="getCheckLength">获取选中数目</button> -->
                <!-- <button class="layui-btn layui-btn-sm" lay-event="isAll">验证是否全选</button> -->
              </div>
            </script>
            <form id="myform">
              <table class="layui-table">
                <colgroup>
                    <col width="80">
                    <col width="80">
                    <col width="160">
                </colgroup>
                <thead >
                    <tr>
                        <th class="taleft" >数据来源</th>
                        <th class="taleft" >工程报价</th>
                        <th class="taleft" >工程直接费</th>
                        <th class="taleft" >辅材报价</th>
                        <th class="taleft" >辅材成本</th>
                        <th class="taleft" >人工报价</th>
                        <th class="taleft" >人工成本</th>
                        <th class="taleft" >管理费</th>
                        <th class="taleft" >税金</th>
                        <th class="taleft" >优惠</th>
                        <th class="taleft" >工程毛利</th>
                        <th class="taleft" >工程毛利率</th>
                        <th class="taleft" >实际工程量</th>
                        <th class="taleft" >录入时间</th>

                    </tr>
                </thead>
                <tbody> 
                  <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "暂无数据" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                      <tr>
                        <td>预算</td>
                        <td class="taleft"><?php echo htmlentities($vo['proquant']); ?></td>
                        <td class="taleft"><?php echo htmlentities($vo['direct_cost']); ?></td>
                        <td class="taleft"><?php echo htmlentities($vo['matquant']); ?></td>
                        <td class="taleft"><?php echo htmlentities($vo['fc_cost']); ?></td>
                        <td class="taleft"><?php echo htmlentities($vo['manual_quota']); ?></td>
                        <td class="taleft"><?php echo htmlentities($vo['labor_cost']); ?></td>
                        <td class="taleft"><?php echo htmlentities($vo['tubemoney']); ?></td>
                        <td class="taleft"><?php echo htmlentities($vo['taxes']); ?></td>
                        <td class="taleft"><?php echo htmlentities($vo['discount']); ?></td>
                        <td class="taleft"><?php echo htmlentities($vo['gross_profit']); ?></td>
                        <td class="taleft"><?php echo round($vo['gross_profit'] / $vo['proquant'] * 100,2); ?>%</td>
                        <td class="taleft"></td>
                        <td class="taleft"><?php echo date("Y/m/d",$vo['entrytime']); ?></td>
                      </tr>
                 <?php endforeach; endif; else: echo "暂无数据" ;endif; ?>
                  <tr>
                      <td class="taleft">结算</td>
                      <td class="taleft">
                        <input class="layui-input" type="text" name="proquant" value="<?php echo htmlentities((isset($budget['proquant']) && ($budget['proquant'] !== '')?$budget['proquant']:'')); ?>">
                      </td>
                      <td class="taleft">
                        <input type="text" name="direct_cost" class="layui-input" value="<?php echo htmlentities((isset($budget['direct_cost']) && ($budget['direct_cost'] !== '')?$budget['direct_cost']:'')); ?>">
                      </td>
                      <td class="taleft">
                        <input type="text" name="matquant" class="layui-input" value="<?php echo htmlentities((isset($budget['matquant']) && ($budget['matquant'] !== '')?$budget['matquant']:'')); ?>">
                      </td>
                      <td class="taleft">
                        <input type="text" name="fc_cost" class="layui-input" value="<?php echo htmlentities((isset($budget['fc_cost']) && ($budget['fc_cost'] !== '')?$budget['fc_cost']:'')); ?>">
                      </td>
                      <td class="taleft">
                        <input type="text" name="manual_quota" class="layui-input" value="<?php echo htmlentities((isset($budget['manual_quota']) && ($budget['manual_quota'] !== '')?$budget['manual_quota']:'')); ?>">
                      </td>
                      <td class="taleft">
                        <input type="text" name="labor_cost" class="layui-input" value="<?php echo htmlentities((isset($budget['labor_cost']) && ($budget['labor_cost'] !== '')?$budget['labor_cost']:'')); ?>">
                      </td>
                      <td class="taleft">
                        <input type="text" name="tubemoney" class="layui-input" value="<?php echo htmlentities((isset($budget['tubemoney']) && ($budget['tubemoney'] !== '')?$budget['tubemoney']:'')); ?>">
                      </td>
                      <td class="taleft">
                        <input type="text" name="taxes" class="layui-input" value="<?php echo htmlentities((isset($budget['taxes']) && ($budget['taxes'] !== '')?$budget['taxes']:'')); ?>">
                      </td>
                      <td class="taleft">
                        <input type="text" name="discount" class="layui-input" value="<?php echo htmlentities((isset($budget['discount']) && ($budget['discount'] !== '')?$budget['discount']:'')); ?>">
                      </td>
                      <td class="taleft">
                        <input type="text" name="gross_profit" class="layui-input" value="<?php echo htmlentities((isset($budget['gross_profit']) && ($budget['gross_profit'] !== '')?$budget['gross_profit']:'')); ?>">
                      </td>
                      <td class="taleft">
                        <input type="text" name="gross_profit" class="layui-input" value="<?php echo htmlentities((isset($budget['gross_profit']) && ($budget['gross_profit'] !== '')?$budget['gross_profit']:'')); ?>">
                      </td>
                      <td class="taleft">
                        <input type="text" name="fact_gcl" class="layui-input" value="<?php echo htmlentities((isset($budget['fact_gcl']) && ($budget['fact_gcl'] !== '')?$budget['fact_gcl']:'')); ?>">
                      </td>
                      <td class="taleft"><?php if(!(empty($budget) || (($budget instanceof \think\Collection || $budget instanceof \think\Paginator ) && $budget->isEmpty()))): ?><?php echo date("Y-m-d h:i:s",$budget['savetime']); endif; ?></td>
                  </tr>
                </tbody>
             </table>
             <input type="hidden" name="offerlist_id" value="<?php echo htmlentities($offerlist_id); ?>">
             <div class="layui-inline"><button type="submit" class="layui-btn">保存</button></div>
           </form>
        </div>
    </div>
</div>

    
<script type="text/javascript" src="/static/admin/js/common.js"></script>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>

<script type="text/javascript">
//保存结算数据
$(document).ready(function(){
  $('#myform').submit(function(){
    $.ajax({
      url:"<?php echo url('admin/Settlement/savebudget'); ?>",
      type:"post",
      data:$('#myform').serialize(),
      success:function(result){
        console.log(result);
      }
    });
  });

});   

</script>

</body>

</html>