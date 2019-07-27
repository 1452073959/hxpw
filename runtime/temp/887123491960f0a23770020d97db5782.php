<?php /*a:2:{s:73:"/www/wwwroot/www.hxpw1998.com/application/admin/view/artificial/gcfx.html";i:1563525608;s:70:"/www/wwwroot/www.hxpw1998.com/application/admin/view/index_layout.html";i:1556248450;}*/ ?>
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
.layui-form-select{float:left;margin-right:10px}
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
#imgs{position:relative;float:left;}
.batchs span{cursor:pointer;}
.myinput{padding: 2px;border-color:#eeeeee;width:100%;border:none;padding:5px;}
.bg1{background-color: #f2f2f2;}
textarea{padding:4px;}
.form-control{display: inline-block;padding: 5px;}
</style>

  <!-- 导出完成 -->
<div class="layui-card">
    <div class="layui-card-body">
        <div>

            <blockquote class="layui-elem-quote news_search">
                <div class="layui-inline">
                  <form id="myform" target="<?php echo url('admin/settlement/gcfx'); ?>" method="post" valu>
                    <select class="form-control" name="false" required="">
                      <option value="">请选择预算报价</option>
                      <?php if(is_array($res) || $res instanceof \think\Collection || $res instanceof \think\Paginator): $i = 0; $__LIST__ = $res;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['unit']); ?>——<?php echo htmlentities($vo['status']); ?></option>
                      <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                    <select class="form-control" name="true" required="">
                      <option value="">请选择结算报价</option>
                      <?php if(is_array($res) || $res instanceof \think\Collection || $res instanceof \think\Paginator): $i = 0; $__LIST__ = $res;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['unit']); ?>——<?php echo htmlentities($vo['status']); ?></option>
                      <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select> 
                    <input class="layui-btn layui-btn-sm" type="submit" value="确认">
                  </form>  
                </div>       
                <p class="layui-btn" id="daochu" style="float:right;margin-right:10px">导出excel</p>
              <button class="layui-btn layui-btn-sm" type="button" data-fun="print" id="btn_export">打印</button>
            </blockquote>
            <script type="text/html" id="toolbarDemo">
            </script>
          <!--startprint-->  
            <?php if(!(empty($true) || (($true instanceof \think\Collection || $true instanceof \think\Paginator ) && $true->isEmpty()))): ?>
            <table class="layui-table table2excel"   id="test_table">
                <colgroup>
                    <col width="80">
                    <col width="80">
                    <col width="160">
                </colgroup>
                <thead >
                   <tr><th colspan="14" style="text-align: center;">工程成本分析表</th></tr>
                </thead>
                <tbody> 
                  <tr>
                   <td colspan="2" class="bg1">档案编号：</td>
                   <td colspan="1"><input type="text" name="file_no" class="myinput" value="" placeholder="输入"></td>
                   <td class="bg1">监理：</td>
                   <td colspan="3"><input type="text" name="jianli" class="myinput" value="" placeholder="输入"></td>
                   <td class="bg1">报价：</td>
                   <td colspan="3"><?php echo htmlentities((isset($true['proquant']) && ($true['proquant'] !== '')?$true['proquant']:0)); ?></td>
                   <td class="bg1">填表日期：</td>
                   <td colspan="3"><?php echo date('Y/m/d h:i:s',time()); ?></td>
                 </tr>
                 <tr>
                   <td colspan="3" class="bg1">工程地址及业主姓名</td>
                   <td colspan="6"><?php echo htmlentities($true['address']); ?></td>
                   <td class="bg1">商务经理</td>
                   <td><?php echo htmlentities($true['manager_name']); ?></td>
                   <td class="bg1">设计师</td>
                   <td colspan="2"><?php echo htmlentities($true['designer_name']); ?></td>
                 </tr>
                 <tr>
                   <td colspan="14" style="background-color: #f2f2f2;text-align: center;">成本分析（所有数据以优惠折扣前数据为准）</td>
                 </tr>
                 <tr>
                   <td colspan="2" class="bg1">施工项目</td>
                   <td colspan="2" class="bg1">预算报价</td>
                   <td colspan="2" class="bg1">结算单</td>
                   <td colspan="2" class="bg1">发生成本</td>
                   <td colspan="3" class="bg1">成本利润分析</td>
                   <td class="bg1">工程户型</td>
                   <td class="bg1"><?php echo htmlentities($true['pro_apartment_type']); ?></td>
                   <td>面积： <?php echo htmlentities($true['area']); ?> m2</td>
                 </tr>
                 <tr>
                   <td class="bg1">序号</td>
                   <td class="bg1">项目内容</td>
                   <td class="bg1">材料直接费</td>
                   <td class="bg1">人工直接费</td>
                   <td class="bg1">材料直接费</td>
                   <td class="bg1">人工直接费</td>
                   <td class="bg1">材料成本</td>
                   <td class="bg1">工人工资</td>
                   <td class="bg1">材料占比</td>
                   <td class="bg1">人工占比</td>
                   <td class="bg1">综合利润率</td>
                   <td class="bg1">材料结算价超预算比</td>
                   <td class="bg1">人工结算价超预算比</td>
                   <td class="bg1">备注信息</td>
                 </tr>
                 <?php if(is_array($true['content']) || $true['content'] instanceof \think\Collection || $true['content'] instanceof \think\Paginator): $k = 0; $__LIST__ = $true['content'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?>
                 <tr class="save">
                  <td><?php echo htmlentities($k + 1); ?></td>
                  <td><?php echo htmlentities((isset($vo['type_of_work']) && ($vo['type_of_work'] !== '')?$vo['type_of_work']:"未定义")); ?></td>
                  <td class="total-item a" data-class="a"><?php echo htmlentities((isset($vo['pre_quotaall']) && ($vo['pre_quotaall'] !== '')?$vo['pre_quotaall']:0)); ?></td>
                  <td class="total-item b" data-class="b"><?php echo htmlentities((isset($vo['pre_craft_showall']) && ($vo['pre_craft_showall'] !== '')?$vo['pre_craft_showall']:0)); ?></td>
                  <td class="total-item c" data-class="c"><?php echo htmlentities((isset($vo['quotaall']) && ($vo['quotaall'] !== '')?$vo['quotaall']:0)); ?></td>
                  <td class="total-item d" data-class="d"><?php echo htmlentities((isset($vo['craft_showall']) && ($vo['craft_showall'] !== '')?$vo['craft_showall']:0)); ?></td>
                  <td class="total-item e edit" data-class="e">0</td>
                  <td class="total-item f edit" data-class="f">0</td>
                  <td class="total-item g" data-class="g">0</td>
                  <td class="total-item h" data-class="h">0</td>
                  <td class="total-item i" data-class="i">0</td>
                  <td class="total-item j" data-class="j"><?php if($vo['pre_quotaall'] != '0'): ?><?php echo round($vo['quotaall'] / $vo['pre_quotaall'],4) * 100; ?>%<?php endif; ?></td>
                  <td class="total-item k" data-class="k"><?php if($vo['pre_quotaall'] != '0'): ?><?php echo round($vo['craft_showall'] / $vo['pre_craft_showall'],4) * 100; ?>%<?php endif; ?></td>
                  <?php if($k == '1'): ?>
                   <td rowspan="<?php echo count($true['content']) + 1; ?>"><textarea style="border:none;width:100%;"></textarea><!-- <img src="http://www.hxpw1998.com/static/imgs/logo.png"> --></td>
                  <?php endif; ?>
                 </tr>
                 <?php endforeach; endif; else: echo "" ;endif; ?>
                 <tr>
                   <td colspan="2" class="bg1">直接费小计</td>
                   <td class="totala"></td>
                   <td class="totalb"></td>
                   <td class="totalc"></td>
                   <td class="totald"></td>
                   <td class="totale"></td>
                   <td class="totalf"></td>
                   <td class="totalg"></td>
                   <td class="totalh"></td>
                   <td class="totali"></td>
                   <td class="totalj"></td>
                   <td class="totalk"></td>
                 </tr>
                 <tr>
                   <td rowspan="7" class="bg1">其他项目</td>
                   <td class="bg1">项目内容</td>
                   <td class="bg1">预算金额</td>
                   <td class="bg1">结算金额</td>
                   <td colspan="3" class="bg1">报价与结算</td>
                   <td colspan="7" rowspan="3"><textarea style="border:none;width:100%;" placeholder="优惠折扣、工程收方、增减变更项目记录与说明："></textarea></td>
                 </tr>
                 <tr>
                   <td class="bg1">搬运费</td>
                   <td><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                   <td><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                   <td colspan="2" style="background-color: #f2f2f2;">折前总报价</td>
                   <td><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                 </tr>
                 <tr>
                   <td class="bg1">清洁费</td>
                   <td><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                   <td><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                   <td colspan="2" class="bg1">折后总报价</td>
                   <td><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                 </tr>
                 <tr>
                   <td class="bg1">工程意外险</td>
                   <td><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                   <td><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                   <td colspan="2" class="bg1">实际结算额</td>
                   <td><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                   <td colspan="7" rowspan="4"><textarea style="border:none;width:100%;" placeholder="结算说明："></textarea></td>
                 </tr>
                 <tr>
                   <td style="background-color: #f2f2f2;">管理费</td>
                   <td><?php echo htmlentities((isset($true['tubemoney']) && ($true['tubemoney'] !== '')?$true['tubemoney']:"0")); ?></td>
                   <td><!--<input type="text" name="fc_cost" class="myinput" value="" placeholder="输入">--><?php echo htmlentities((isset($true['tubemoney']) && ($true['tubemoney'] !== '')?$true['tubemoney']:"0")); ?></td>
                   <td colspan="2"><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                   <td><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                 </tr>
                 <tr>
                   <td style="background-color: #f2f2f2;">税金</td>
                   <td><?php echo htmlentities((isset($true['taxes']) && ($true['taxes'] !== '')?$true['taxes']:"0")); ?></td>
                   <td><!--<input type="text" name="fc_cost" class="myinput" value="" placeholder="输入">--><?php echo htmlentities((isset($true['taxes']) && ($true['taxes'] !== '')?$true['taxes']:"0")); ?></td>
                   <td colspan="2"><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                   <td><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                 </tr>
                 <tr>
                   <td class="bg1">远程费</td>
                   <td><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                   <td><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                   <td colspan="2"><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                   <td><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                 </tr>
                 <tr>
                   <td>工程部质检：</td>
                   <td colspan="3"><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                   <td>工程部经理：</td>
                   <td colspan="4"><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                   <td>总经理：</td>
                   <td colspan="4"><input type="text" name="fc_cost" class="myinput" value="" placeholder="输入"></td>
                 </tr>
                </tbody>
            </table>
            <?php endif; ?>
            <!--endprint-->
        </div>
    </div>
</div>

    
<!-- <script type="text/javascript" src="/static/admin/js/common.js"></script> -->
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript" src="/static/jquery-table2excel-master/dist/jquery.table2excel.min.js"></script>

<!-- 打印 -->
<script src="/static/bootstrap/js/table/printThis.min.js"></script>
<script type="text/javascript">
     //打印
      $(document).on('click','[data-fun="print"]',function(){
          $("table").printThis({
             debug: false,
             importCSS: true,
             importStyle: true,
             printContainer: true,
    //       loadCSS: "/Content/Themes/Default/style.css",
             pageTitle: "姓名查询汇总表",
             removeInline: false,
             printDelay: 333,
             header: null,
             formValues: false
           });
      })


 //filename 导出的excel文件名
//方法可带filename参数，亦可不带参数直接定义
// function table2Excel(filename='111'){
 $("#daochu").click(function(){
     $(".table2excel").table2excel({
      exclude: ".noExl",//class="noExl"的列不导出
      name: "Excel Document Name",
      filename: 'filename',//文件名称
      fileext: ".xls",//文件后缀名
      exclude_img: false,//导出图片
      exclude_links: false,//导出超链接
      exclude_inputs: false//导出输入框内容
    });

 }); 
  
// }

//导出
      // $('#tableexport').click(function(){
      //   var tableHTML = document.querySelector("table").outerHTML;
      //   var xlsContent= `<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel"xmlns="http://www.w3.org/TR/REC-html40">
      //         <head>
      //           <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      //           <meta name="ProgId" content="Excel.Sheet" /> 
      //           <style>
      //               table,td,th{border:1px solid #000000;}
      //           </style>
      //         </head>
      //         <body>${tableHTML}</body>
      //         </html>`; 
      //   var blob = new Blob([xlsContent], { type: "application/vnd.ms-excel" });
      //   var a = document.getElementById("tableexport");
      //   a.href = URL.createObjectURL(blob);
      //   a.classList.add('layui-btn');
      //   a.download = "岗位绩效考核表.xls";
        //a.innerHTML = "导出"
        //document.body.appendChild(a); 

      // });

</script>
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
<script type="text/javascript">
    // layui.use('table', function(){
    //     var table = layui.table;
    //     table.on('exportExcel(test3)', function(obj){
    //         console.log(obj)
    //     });
    // });
    $(document).ready(function(){

      function autototal(){
        var percent = ['g','h','i','j','k'];
        $('td.total-item').each(function(){
            var total = 0;
            var name= $(this).attr('data-class');
            $('td.' + name).each(function(){
              var val = $(this).html()=='' ? 0 : $(this).html();
                if(percent.indexOf(name) == -1){
                  total += val * 1;
                }else{
                  if(val != 0){val=val.substring(0,val.length-1);}
                  total += val * 100;
                }
                
            });

            if(percent.indexOf(name) == -1){
              $('td.total'+name).html(total);
            }else{
              $('td.total'+name).html(total / 100 + '%');
            }
        });
      }
      autototal();

        $('td.edit').click(function(){
          $(this).html('<input type="text" class="myinput">');
          $(this).find('input').focus();
        });
        $(document).on('blur','td.edit input',function(){
          var val = $(this).val();
          var attr = $(this).parent('td.edit').attr('data-class');

            c_val = $(this).parent().siblings('td.c').html();
            d_val = $(this).parent().siblings('td.d').html();
          if( attr== 'e'){
            if(c_val != 0 && c_val != ''){
              $(this).parent().siblings('td.g').html(Math.floor(val/c_val*100)/100 *100 +'%');
            }
          }
          if(attr == 'f'){
            if(d_val != 0 && d_val != ''){
              $(this).parent().siblings('td.h').html(Math.floor(val/d_val*100)/100*100 +'%');
            }
          }
            if($(this).parent().hasClass('e')){
              e_val = val;
            f_val = $(this).parent().siblings('td.f').html();
            }else{
              e_val = $(this).parent().siblings('td.e').html();
              f_val = val;
            }
            if(c_val!=0 && d_val!=0 && e_val!=0 && f_val!=0){
              var temp = 1-(e_val *1 + f_val*1)/(c_val*1 + d_val*1);
                  $(this).parent().siblings('td.i').html(Math.floor(temp*100)/100*100+'%');
            }
          $(this).parent('td.edit').html(val==''?0:val);
          autototal();
        });





    });
</script>

</body>

</html>