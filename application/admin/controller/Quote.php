<?php

// +----------------------------------------------------------------------
// | 报表管理
// +----------------------------------------------------------------------
namespace app\admin\controller; 

use app\common\controller\Adminbase;
use think\Db;
use think\Request;

class Quote extends Adminbase
{
	public $upper = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	public $lower = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
    //获取模板列表
    public function ajax_get_tmp_list(){
        $userinfo = $this->_userinfo;
        $tmp_list = Db::name('tmp')->where(['f_id'=>$userinfo['companyid']])->field('tmp_id,tmp_name,remark,update_time')->group('tmp_id')->select();
        foreach($tmp_list as $k=>$v){
            $tmp_list[$k]['update_time'] = date('Y-m-d H:i',$v['update_time']);
        }
        echo json_encode(array('code'=>1,'datas'=>$tmp_list));
    }
    //获取模板详情
    public function ajax_get_tmp_info(){
        $userinfo = $this->_userinfo;
        $tmp_id = input('tmp_id');
        if(!$tmp_id){
            echo json_encode(array('code'=>0,'msg'=>'参数错误'));
        }
        $tmp_list = Db::name('tmp')->where(['tmp_id'=>$tmp_id,'f_id'=>$userinfo['companyid']])->select();

        //=============验证模板是否有效
        $offer_type_list = Db::name('offer_type')->where(['companyid'=>$userinfo['companyid'],'status'=>1])->select();
        $offer_type = [1=>[],2=>[]];
        foreach($offer_type_list as $k=>$v){
            $offer_type[$v['type']][] = $v['name'];
        }
        foreach($tmp_list as $k=>$v){
            if(!in_array($v['work_type'],$offer_type[1])){
                echo json_encode(array('code'=>0,'msg'=>'工种：'.$v['work_type'].' 不存在，模板失效'));die;
            }
            if(!in_array($v['space'], $offer_type[2])){
                echo json_encode(array('code'=>0,'msg'=>'空间：'.$v['space'].' 不存在，模板失效'));die;
            }
        }
        //=============验证模板是否有效 end

        $item_number = array_column($tmp_list, 'item_number');
        $offerquota = Db::name('offerquota')->where(['item_number'=>$item_number,'frameid'=>$userinfo['companyid']])->select();
        if(count($item_number) != count($offerquota)){
            //=============验证模板是否有效 end
            echo json_encode(array('code'=>0,'msg'=>'模板部分项目不全，模板失效'));die;
        }
        $offerquota = array_column($offerquota, null,'item_number');
        echo json_encode(array('code'=>1,'datas'=>$tmp_list,'offerquota'=>$offerquota));
    }
	//报价模板首页
	public function index(){
	    $userinfo = $this->_userinfo;
		$res = Db::name('tmp')->where('f_id','=',$userinfo['companyid'])->group('tmp_id')->select();
		$this->assign([ 'data'=>$res ]);
		return $this->fetch();
	}
	//报价模板查看
	public function checkmould(){
		$tmp_id = input('tmp_id');//模板id
        $userinfo = $this->_userinfo;
        $item_number = [];//所有项目集合
		// $type = input('type');//模板预览还是修改
		$tmp_list = Db::name('tmp')->where('tmp_id','=',$tmp_id)->select();
		$tmp_name = $tmp_list[0]['tmp_name'];
        $data = [];
		//工种
		$offer_type_list = Db::name('offer_type')->where(['companyid'=>$userinfo['companyid'],'status'=>1])->select();
        $offer_type = [1=>[],2=>[]];
        foreach($offer_type_list as $k=>$v){
            $offer_type[$v['type']][] = $v['name'];
        }
        foreach($tmp_list as $k=>$v){
            if(!in_array($v['work_type'],$offer_type[1])){
                $this->error('工种：'.$v['work_type'].' 不存在，模板失效');
            }
            if(!in_array($v['space'], $offer_type[2])){
                $this->error('空间：'.$v['space'].' 不存在，模板失效');
            }
            $data[$v['work_type']][$v['space']][$v['item_number']] = $v['num'];
            $item_number[] = $v['item_number'];
        }
        $item_number = array_unique($item_number);
        $item_number_num = count($item_number);
        $offerquota = Db::name('offerquota')->where(['item_number'=>$item_number,'frameid'=>$userinfo['companyid']])->select();
        if($item_number_num != count($offerquota)){
            $this->error('模板部分项目不全，模板失效');
        }
        $offerquota = array_column($offerquota, null,'item_number');
        $this->assign([ 
                'data'=>$data,
                'offerquota'=>$offerquota,
             ]);
		return $this->fetch();
	}
	//新建模板
	public function addmould(){
        $userinfo = $this->_userinfo;
        $offer_type_list = Db::name('offer_type')->where(['companyid'=>$userinfo['companyid'],'status'=>1])->select();
        $offer_type = [1=>[],2=>[]];//用于添加选择工种/空间
        $offer_type_check = [1=>[],2=>[]];//用于检测工种/空间是否还有效
        foreach($offer_type_list as $k=>$v){
            $offer_type[$v['type']][] = $v;
            $offer_type_check[$v['type']][] = $v['name'];
        }
        if(input('tmp_id')){
            $tmp_list = Db::name('tmp')->where('tmp_id','=',input('tmp_id'))->select();
            $tmp_name = $tmp_list[0]['tmp_name'];
            $data = [];
            foreach($tmp_list as $k=>$v){
                if(!in_array($v['work_type'],$offer_type_check[1])){
                    $this->error('工种：'.$v['work_type'].' 不存在，模板失效');
                }
                if(!in_array($v['space'], $offer_type_check[2])){
                    $this->error('空间：'.$v['space'].' 不存在，模板失效');
                }
                $data[$v['work_type']][$v['space']][$v['item_number']] = $v['num'];
                $item_number[] = $v['item_number'];
            }
            $item_number = array_unique($item_number);
            $item_number_num = count($item_number);
            $offerquota = Db::name('offerquota')->where(['item_number'=>$item_number,'frameid'=>$userinfo['companyid']])->select();
            if($item_number_num != count($offerquota)){
                $this->error('模板部分项目不全，模板失效');
            }
            $offerquota = array_column($offerquota, null,'item_number');
            $this->assign([ 
                'data'=>$data,
                'offerquota'=>$offerquota,
                'tmp_name'=>$tmp_name,
                'tmp_id'=>input('tmp_id')
             ]);
        }
        $this->assign([
            'offer_type'=>$offer_type,
        ]);
        return $this->fetch();
	}

	//保存模板
	public function savemould(){
		if($this->request->isPost() && input('data') && input('tmp_name')){
			$userinfo = $this->_userinfo;
			$f_id = $userinfo['companyid'];
			$input = input();
			$datas = [];
			$time = time();
			//生成订单唯一id md5
			$tmp_id = input('tmp_id')?input('tmp_id'):md5(input('tmp_name').rand(1,999999).microtime(true));
			foreach(input('data') as $k1=>$v1){
				$type_word_name = $k1;//工种名称
				foreach($v1 as $k2=>$v2){
					$space = $k2;//工种名称
					foreach($v2 as $k3=>$v3){
						$datas[] = [
							'tmp_id'=>$tmp_id,
							'tmp_name'=>input('tmp_name'),
							'f_id'=>$f_id,
							'work_type'=>$type_word_name,
							'space'=>$space,
							'item_number'=>$k3,
							'num'=>$v3,
							'update_time'=>$time,
						];
					}
				}
			}
			Db::startTrans();
			try {
				if(input('tmp_id')){
					Db::name('tmp')->where('tmp_id',input('tmp_id'))->delete();
				}
			    $re = Db::name('tmp')->insertAll($datas);
			    // 提交事务
			    Db::commit();
			} catch (\Exception $e) {
			    // 回滚事务
			    Db::rollback();
			    $this->error('模板保存失败');
			}
			
			if($re){
				$this->success('模板保存成功','admin/quote/index');
			}else{
				$this->error('模板保存失败');
			}
		}
	}
	//删除模板
	public function deletemould(){
		$userinfo = $this->_userinfo;
		$id = input('id');
		if(empty($id)){
			$this->error('删除有误');
		}
		$re = Db::name('tmp')->where([ 'tmp_id'=>$id,'f_id'=>$userinfo['companyid'] ])->delete();
		if($re){
			$this->success('删除成功','admin/quote/index');
		}else{
			$this->error('删除失败');
		}
	}
	//根据工种获取空间类型
	public function getroom(){
		if($this->request->isPost()){
			$id = input('id');
			$result = Db::name('offer_type')->where('pid','=',$id)->select();
			if($result){
				$html = '';
				foreach($result as $key=>$value){
					$html .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
				}
				return json([ "msg"=>'success', "data"=>$html ]);
			}
			return json([ "msg"=>'fail' ]);
		}
	}
	//获取指定工种下定额数据
	public function getdata(){
		if($this->request->isPost()){
			$userinfo = $this->_userinfo; 
			$companyid = $userinfo['companyid'];
			$conditionsid = input('conditionsid');
			$conditionsname = Db::name('offer_type')->where('id','=',$conditionsid)->value('name');
            //搜索
            if(input('type') == 'search'){
                $project = input('project');
                $item_number = input('item_number');
                $where = [];
                if(!empty($project)){
                 $where[] = ['project','like',"%{$project}%"];
                }
                if(!empty($item_number)){
                    $where[] = ['item_number','like',"%{$item_number}%"];
                }
            }

            $where[] = ['type_of_work','=',$conditionsname];
            // return $where;
            if($companyid != 1){
                //非管理员获取指定公司
                $where[] = ['frameid','=',$companyid];
            }
            // var_dump($where);die;
            $result = Db::name('offerquota')->where($where)->select();
			if($result){
				//数据处理
				$html = '';
				if($result){
				    foreach ($result as $key => $value) {
				      $html .= '<tr>
				              <td><input type="checkbox" name="check" data-id="'.$value['item_number'].'"></td>
				              <td>'.$value['item_number'].'</td>     
				              <td>'.getcid($value['frameid']).'</td>    
				              <td>'.$value['type_of_work'].'</td>                                     
				              <td>'.$value['project'].'</td>                                     
				              <td>'.$value['company'].'</td>                                     
				              <td>'.$value['quota'].'</td>                                     
				              <td>'.$value['craft_show'].'</td> 
				              <td>'.$value['cost_value'].'</td>                              
				            </tr> ';
				   }
				}
				return json([ "msg"=>'success', "data"=>$html ]);
			}
			return json([ "msg"=>'fail','data'=>'查无数据' ]);
		}
	}
	//将选中的定额条目添加到模板
	public function returndata(){
		if($this->request->isPost()){
		    $data = input('data');//项目编码数组
		    $conditionsid = input('conditionsid');
		    $roomid = input('roomid');
		    $re = Db::name('offerquota')->where('item_number','in',$data)->select();
		    if($re){
		        $roomname = Db::name('offer_type')->where('id',$roomid)->value('name');//空间类型名称
		        $conditions = Db::name('offer_type')->where('id',$conditionsid)->value('name');//工种名称
				//拼接空间类型
				$html = '';
				$head = '';
				if(input('type') != "addnewquote"){
					$head = '<tr id="tr'.$roomid.'"><td></td><td class="text-center" colspan="8">'.$roomname.
                      '<a class="layui-icon layui-icon-add-1 addnewquote" data-conditionsid="'.$conditionsid.'" data-roomid="'.$roomid.'"></a>
                      <a class="layui-icon layui-icon-delete deleteroom" data-cate="tr'.$conditionsid.'" data-son="tr'.$conditionsid.$roomid.'" data-length="'.count($re).'"></a>'.
                      '<!--<a class="layui-btn layui-btn-sm addnewquote" data-conditionsid="'.$conditionsid.'" data-roomid="'.$roomid.'">新增</a>
                      <a class="layui-btn layui-btn-sm deleteroom" data-cate="tr'.$conditionsid.'" data-son="tr'.$conditionsid.$roomid.'" data-length="'.count($re).'">删除</a>--></td></tr>';
				}
				//拼接改空间下的定额数据
				$item_number = [];
				foreach ($re as $key => $value) {
					$item_number[] = $value['item_number'];
					$html .= '<tr class="tr'.$conditionsid.$roomid.'">
                    			<td></td>
								<td colspan="">' . $value['project'] . '<a class="layui-icon layui-icon-delete deletequote" data-cate="tr'.$conditionsid.'" data-parent="tr'.$conditionsid.$roomid.'"></a><!--<a class="layui-btn layui-btn-sm deletequote" data-cate="tr'.$conditionsid.'" data-parent="tr'.$conditionsid.$roomid.'">删除</a>--></td>
								<td><input class="myinput" type="text" name="gcl['.$conditionsid.']['.$roomid.']['.$value['item_number'].']"></td>
								<td>' . $value['company'] . '</td>
								<td>' . $value['quota'] . '</td>
								<td></td>
								<td>' . $value['craft_show'] . '</td>
								<td></td>
								<td class="text-limit">' . $value['material'] . '</td>
							</tr>';
				}
		        return json([ 'msg'=>'success','data'=>$html,'head'=>$head,'length'=>count($re),'input'=>input() ]);
		    }else{
		        return json([ 'msg'=>'fail' ]);
		    }
		}
	}

	public function excel_tmp(Request $request){
		if ($_FILES['excel']['error'] == 4) {
			$this->error('没有文件被上传');die;
		}

        $userinfo = $this->_userinfo;
        if(!$userinfo) {
            $this->error('无法获取当前操作人员');die;
        }
        // //生成空间类型数据 用于判断空间类型是否有效
        $offer_type_list = Db::name('offer_type')->where(['companyid'=>$userinfo['companyid'],'status'=>1])->select();
        $offer_type = [1=>[],2=>[]];
        foreach($offer_type_list as $k=>$v){
            $offer_type[$v['type']][] = $v;
        }

        require'../extend/PHPExcel/PHPExcel.php';
        $file = $request->file();        // dump($file);
        if($file){
            foreach ($file as $files) {
             // dump($files);
              $info = $files->validate(['size'=>10485760,'ext'=>'xls,xlsx'])->move(ROOT_PATH . 'public/'. 'excel');
            }
            if (!$info) {
               // Result(1,'上传文件格式不正确'); 
                $this->error('上传文件格式不正确');
            }else{
               // Result(0,'上传成功'); 
               //获取上传到后台的文件名
                $fileName = $info->getSaveName();
                //获取文件路径
                $filePath = ROOT_PATH . 'public/'. 'excel/'.$fileName;
                //获取文件后缀
                $suffix = $info->getExtension();

        		$tmp_name = explode('.', $info->getInfo()['name'])[0];

                // 判断哪种类型
                if($suffix=="xlsx"){
                    $reader = \PHPExcel_IOFactory::createReader('Excel2007');
                }else{
                    $reader = \PHPExcel_IOFactory::createReader('Excel5');
                }
            }
            //处理表格数据
            //载入excel文件
            $excel = $reader->load("$filePath",$encode = 'utf-8');
            //读取第一张表
            $sheet = $excel->getSheet(0);
            //获取总行数
            $row_num = $sheet->getHighestRow();
            //获取总列数
            $col_num = $sheet->getHighestColumn();
            $data = []; //数组形式获取表格数据 
              // dump($col_num);
            if($col_num != 'D') {
               $this->error('文件数据字段不匹配，请重新选择');die;
            } 
            $time = time();
            $tmp_id = md5($fileName.rand(1,999999).microtime(true));
            for ($i = 2; $i <= $row_num; $i ++) {
            	if(empty($sheet->getCell("A".$i)->getValue()) || empty($sheet->getCell("B".$i)->getValue()) || empty($sheet->getCell("C".$i)->getValue())){
            		$this->error('字段不能为空');die;
            	}
            	$work_type = trim($sheet->getCell("A".$i)->getValue());
            	$space = trim($sheet->getCell("B".$i)->getValue());
            	if(!in_array($work_type, $offer_type[1])){
            		$this->error('工种：'.$work_type.'，不存在');
            	}
                if(!in_array($space, $offer_type[2])){
                    $this->error('空间类型'.$space.'，不存在');
                }
                $data[$i]['tmp_id']  = $tmp_id;
                $data[$i]['tmp_name']  = $tmp_name;
                $data[$i]['f_id']  = $userinfo['companyid'];
                $data[$i]['work_type']  = $work_type;
                $data[$i]['space']  = $space;
                $data[$i]['item_number']  = trim($sheet->getCell("C".$i)->getValue());
                $data[$i]['num']  = $sheet->getCell("D".$i)->getValue() ? trim($sheet->getCell("D".$i)->getValue()): '';
                $data[$i]['update_time']  = $time;
            }
            // $project_name = array_unique(array_column($data, 'project_name'));
            // $offerquota = array_unique(Db::name('offerquota')->where(['project'=>$project_name,'frameid'=>$userinfo['companyid']])->field('item_number,project')->select(),null,'project');
            // foreach($data as $k=>$v){
            //     if($offerquota[$v['project_name']]['item_number']){
            //         $data[$i]['item_number'] = $offerquota[$v['project_name']]['item_number'];
            //     }else{
            //         $this->error('项目：'.$v['project_name'].'不存在，导入失败');
            //     }
            // }

            //将数据保存到数据库
            if ($data) {
                //把获取到的二维数组遍历进数据库
	           	Db::startTrans();
				try {
					foreach ($data as $key => $value) {
						$ishas = Db::name('offerquota')->where('item_number',$value['item_number'])->where('frameid',$value['f_id'])->find();
						if(!$ishas){
							exception('项目编号：'.$value['item_number'].'不存在');
						}
					    $res = Db::name('tmp')->insert($value);
					}
				    // 提交事务
				    Db::commit();
				} catch (\Exception $e) {
				    // 回滚事务
				    Db::rollback();
				    $this->error($e->getMessage());
				}
                
               $this->success('导入成功');
            }else{
              $this->error('获取导入文件数据失败');
            }
        }else{
        $this->error('请选择上传文件');
        }
	}
	
	// public function excel_export(){
	// 	$filename = "报表模板";
	// 	header("Content-type:application/octet-stream");
	// 	header("Accept-Ranges:bytes");
	// 	header("Content-type:application/vnd.ms-excel");
	// 	header("Content-Disposition:attachment;filename=".$filename.".xls");
	// 	header("Pragma: no-cache");
	// 	header("Expires: 0");
	// 	$id = input('id') ?: 2;
	// 	$mould = Db::name('mould')->where('id','=',$id)->find();
	// 	if($mould['content']){
	// 		$conditions_no = 0;$room_no = 0;
	// 		foreach(json_decode($mould['content'],true) as $key=>$value){
	// 			$conditionsname = Db::name('offer_type')->where('id','=',$key)->value('name');//工种名称
	// 			$mould['details'][$key]['conditionsname'] = $conditionsname;
	// 			$mould['details'][$key]['conditions_no'] = $this->upper[$conditions_no];
	// 			foreach($value as $ke=>$va){
	// 				$roomname = Db::name('offer_type')->where('id','=',$ke)->value('name');//空间类型名称
	// 				$mould['details'][$key]['son'][$ke]['roomname'] = $roomname;
	// 				$mould['details'][$key]['son'][$ke]['room_no'] = $this->lower[$room_no];
	// 				foreach($va as $k=>$v){
	// 					$item = Db::name('offerquota')->find($v);//定额条目
	// 					$mould['details'][$key]['son'][$ke]['item'][$k] = $item;
	// 				}
	// 				$room_no++;
	// 			}
	// 			$conditions_no++;
	// 		}
	// 	}
	// 	$str = '<table style="border:1px solid #000000;"><thead>
 //                    <tr>
	// 					<th style="border:1px solid #000000;" rowspan="2" colspan="2"></th>
 //                        <th style="border:1px solid #000000;" colspan="6"><h3>住宅装饰工程造价预算书</h3></th>
	// 					<th style="border:1px solid #000000;" rowspan="2" colspan="2"></th>
 //                    </tr>
	// 				<tr>
	// 					<th style="border:1px solid #000000;" colspan="6">全国统一24小时客服热线：400-6281-968</th>
	// 				</tr>
 //                    <tr>
 //                        <th style="border:1px solid #000000;" style="text-align:center;" colspan="10">
 //                          单位：
 //                        </th>        
 //                    </tr>
 //                    <tr>
 //                        <th style="border:1px solid #000000;" colspan="3">工程名称：</th>       
 //                        <th style="border:1px solid #000000;" colspan="3">客户姓名：</th>       
 //                        <th style="border:1px solid #000000;" colspan="2">设计师姓名：</th>       
 //                        <th style="border:1px solid #000000;" colspan="2">报价师姓名：</th>       
 //                    </tr>
 //                    <tr>
	// 					<th style="border:1px solid #000000;" rowspan="2">序号</th> 
 //                        <th style="border:1px solid #000000;" rowspan="2" colspan="2">工程项目名称</th>         
 //                        <th style="border:1px solid #000000;" rowspan="2">工程量</th>       
 //                        <th style="border:1px solid #000000;" rowspan="2">单位</th>
 //                        <th style="border:1px solid #000000;" colspan="2">辅材费</th> 
 //                        <th style="border:1px solid #000000;" colspan="2">人工费</th>    
 //                        <th style="border:1px solid #000000;" rowspan="2">施工工艺及材料说明</th> 
 //                    </tr>
 //                    <tr>   
 //                        <th style="border:1px solid #000000;">辅材基价</th>       
 //                        <th style="border:1px solid #000000;">辅材合价</th>       
 //                        <th style="border:1px solid #000000;">人工基价</th>       
 //                        <th style="border:1px solid #000000;">人工合价</th> 
 //                      </tr>
 //                </thead>';
	// 	foreach($mould['details'] as $key=>$vos){
	// 		$str .= '<tr>
	// 			<td style="border:1px solid #000000;">'.$vos['conditions_no'].'</td>
	// 			<td style="border:1px solid #000000;" colspan="2">'.$vos['conditionsname'].'</td>
	// 			<td style="border:1px solid #000000;" colspan="7"></td>
	// 		</tr>';
	// 		foreach($vos['son'] as $k=>$v){
	// 			$str .= '<tr">
	// 				<td style="border:1px solid #000000;">'.$v['room_no'].'</td>
	// 				<td style="border:1px solid #000000;" colspan="9">'.$v['roomname'].'</td>
	// 			</tr>';
	// 			foreach($v['item'] as $kk=>$vv){
	// 				$str .= '<tr>
	// 					<td style="border:1px solid #000000;">'.($kk*1+1).'</td>
	// 					<td style="border:1px solid #000000;" colspan="2">'.$vv['project'].'</td>
	// 					<td style="border:1px solid #000000;"></td>
	// 					<td style="border:1px solid #000000;">'.$vv['company'].'</td>
	// 					<td style="border:1px solid #000000;">'.$vv['quota'].'</td>
	// 					<td style="border:1px solid #000000;"></td>
	// 					<td style="border:1px solid #000000;">'.$vv['craft_show'].'</td>
	// 					<td style="border:1px solid #000000;"></td>
	// 					<td style="border:1px solid #000000;" title="点击查看详情">'.$vv['material'].'</td>
	// 				</tr>
	// 				<tr>
	// 					<td style="background-color:#999999;text-align:center;" colspan="2">小计</td>
	// 					<td style="background-color:#999999;"></td>
	// 					<td style="background-color:#999999;"></td>
	// 					<td style="background-color:#999999;"></td>
	// 					<td style="background-color:#999999;"></td>
	// 					<td style="background-color:#999999;"></td>
	// 					<td style="background-color:#999999;"></td>
	// 					<td style="background-color:#999999;"></td>
	// 				</tr>';
	// 			}
	// 		}
	// 	}
	// 	$str .= '</table>';		
	// 	echo($str);
	// }
	
	
}