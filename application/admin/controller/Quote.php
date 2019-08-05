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
	//报价模板首页
	public function index(){
	    $userinfo = $this->_userinfo;
		$res = Db::name('mould')->where('userid','=',$userinfo['userid'])->select();
		$this->assign([ 'data'=>$res ]);
		return $this->fetch();
	}
	//报价模板编辑/查看
	public function checkmould(){
		$id = input('id');//模板id
		$type = input('type');//模板预览还是修改
		$mould = Db::name('mould')->where('id','=',$id)->find();
		if($mould['content']){
			$conditions_no = 0;
			foreach(json_decode($mould['content'],true) as $key=>$value){
				$conditionsname = Db::name('offer_type')->where('id','=',$key)->value('name');//工种名称
				$mould['details'][$key]['conditionsname'] = $conditionsname;
				$mould['details'][$key]['conditions_no'] = $this->upper[$conditions_no];
             	 $room_no = 0;
				foreach($value as $ke=>$va){
					$roomname = Db::name('offer_type')->where('id','=',$ke)->value('name');//空间类型名称
					$mould['details'][$key]['son'][$ke]['roomname'] = $roomname;
					$mould['details'][$key]['son'][$ke]['room_no'] = $this->lower[$room_no];
					foreach($va as $k=>$v){
						$item = Db::name('offerquota')->find($v);//定额条目
						$mould['details'][$key]['son'][$ke]['item'][$k] = $item;
					}
					$room_no++;
				}
				$conditions_no++;
			}
		}
		
		$res = Db::name('offer_type')->select();//工种和空间类型
		$tree = [];//树状数据
		foreach($res as $key =>$value){
			if($value['pid'] === 0){
				$tree[$key] = $value;
				unset($res[$key]);
				foreach($res as $k=>$v){
					if($v['pid'] == $value['id']){
						$tree[$key]['son'][] = $v;
					}
				}
			}
		}
		$this->assign([ 'data'=>$mould,'tree'=>$tree ]);
		if($type == 'preview'){
			return $this->fetch('preview');
		}else{
			return $this->fetch();
		}
	}
	//新建模板
	public function addmould(){
		$res = Db::name('offer_type')->select();//工种和空间类型
		$tree = [];//树状数据
		foreach($res as $key =>$value){
			if($value['pid'] === 0){
				$tree[$key] = $value;
				unset($res[$key]);
				foreach($res as $k=>$v){
					if($v['pid'] == $value['id']){
						$tree[$key]['son'][] = $v;
					}
				}
			}
		}
		$this->assign([
			'tree'=>$tree
		]);
        return $this->fetch();
	}
	//保存模板
	public function savemould(){
		if($this->request->isPost()){
			$input = input();
			$userinfo = $this->_userinfo;
			if($input['quote']){
				$insert['userid'] = $userinfo['userid'];
				$insert['addtime'] = time();
				$insert['mouldname'] = $input['mouldname'];
				$insert['content'] = json_encode($input['quote']);
				if(input('type') == 'save'){
          //return input();
					//更新模板
					$re = Db::name('mould')->where('id',input('id'))->update($insert);
				}else{
					//新增模板
					$re = Db::name('mould')->insert($insert);
				}
				if($re){
					$this->success('模板保存成功','admin/quote/index');
				}else{
					$this->error('模板保存失败');
				}
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
		$re = Db::name('mould')->where([ 'id'=>$id,'userid'=>$userinfo['userid'] ])->delete();
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
				              <td><input type="checkbox" name="check" data-id="'.$value['id'].'"></td>
				              <td>'.$value['id'].'</td>    
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
		    $data = input('data');
		    $conditionsid = input('conditionsid');
		    $roomid = input('roomid');
		    $idstr = implode(",", $data);//定额条目id拼接成字符串
		    $re = Db::name('offerquota')->where('id','in',$idstr)->select();
		    if($re){
		        $roomname = Db::name('offer_type')->where('id',$roomid)->value('name');//空间类型名称
		        $conditions = Db::name('offer_type')->where('id',$conditionsid)->value('name');//工种名称
				//拼接空间类型
				if(input('type') == "addnewquote"){
					$html = '';
				}else{
					$html = '<tr id="tr'.$roomid.'"><td></td><td class="text-center" colspan="8">'.$roomname.
                      '<a class="layui-icon layui-icon-add-1 addnewquote" data-conditionsid="'.$conditionsid.'" data-roomid="'.$roomid.'"></a>
                      <a class="layui-icon layui-icon-delete deleteroom" data-cate="tr'.$conditionsid.'" data-son="tr'.$conditionsid.$roomid.'" data-length="'.count($re).'"></a>'.
                      '<!--<a class="layui-btn layui-btn-sm addnewquote" data-conditionsid="'.$conditionsid.'" data-roomid="'.$roomid.'">新增</a>
                      <a class="layui-btn layui-btn-sm deleteroom" data-cate="tr'.$conditionsid.'" data-son="tr'.$conditionsid.$roomid.'" data-length="'.count($re).'">删除</a>--></td></tr>';
				}
				//拼接改空间下的定额数据
				foreach ($re as $key => $value) {
					$html .= '<tr class="tr'.$conditionsid.$roomid.'">
                    			<td></td>
								<td colspan="">' . $value['project'] . '<a class="layui-icon layui-icon-delete deletequote" data-cate="tr'.$conditionsid.'" data-parent="tr'.$conditionsid.$roomid.'"></a><!--<a class="layui-btn layui-btn-sm deletequote" data-cate="tr'.$conditionsid.'" data-parent="tr'.$conditionsid.$roomid.'">删除</a>--></td>
								<td><input class="myinput" type="text" name="gcl['.$conditionsid.']['.$roomid.']['.$value['id'].']"></td>
								<td>' . $value['company'] . '</td>
								<td>' . $value['quota'] . '</td>
								<td></td>
								<td>' . $value['craft_show'] . '</td>
								<td></td>
								<td class="text-limit">' . $value['material'] . '</td>
								<input type="hidden" form="myform" name="quote['.$conditionsid.']['.$roomid.'][]" value="'.$value['id'].'">
							</tr>';
				}
		        return json([ 'msg'=>'success','data'=>$html,'length'=>count($re),'input'=>input() ]);
		    }else{
		        return json([ 'msg'=>'fail' ]);
		    }
		}
	}
	
	public function excel_export(){
		$filename = "报表模板";
		header("Content-type:application/octet-stream");
		header("Accept-Ranges:bytes");
		header("Content-type:application/vnd.ms-excel");
		header("Content-Disposition:attachment;filename=".$filename.".xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		$id = input('id') ?: 2;
		$mould = Db::name('mould')->where('id','=',$id)->find();
		if($mould['content']){
			$conditions_no = 0;$room_no = 0;
			foreach(json_decode($mould['content'],true) as $key=>$value){
				$conditionsname = Db::name('offer_type')->where('id','=',$key)->value('name');//工种名称
				$mould['details'][$key]['conditionsname'] = $conditionsname;
				$mould['details'][$key]['conditions_no'] = $this->upper[$conditions_no];
				foreach($value as $ke=>$va){
					$roomname = Db::name('offer_type')->where('id','=',$ke)->value('name');//空间类型名称
					$mould['details'][$key]['son'][$ke]['roomname'] = $roomname;
					$mould['details'][$key]['son'][$ke]['room_no'] = $this->lower[$room_no];
					foreach($va as $k=>$v){
						$item = Db::name('offerquota')->find($v);//定额条目
						$mould['details'][$key]['son'][$ke]['item'][$k] = $item;
					}
					$room_no++;
				}
				$conditions_no++;
			}
		}
		$str = '<table style="border:1px solid #000000;"><thead>
                    <tr>
						<th style="border:1px solid #000000;" rowspan="2" colspan="2"></th>
                        <th style="border:1px solid #000000;" colspan="6"><h3>住宅装饰工程造价预算书</h3></th>
						<th style="border:1px solid #000000;" rowspan="2" colspan="2"></th>
                    </tr>
					<tr>
						<th style="border:1px solid #000000;" colspan="6">全国统一24小时客服热线：400-6281-968</th>
					</tr>
                    <tr>
                        <th style="border:1px solid #000000;" style="text-align:center;" colspan="10">
                          单位：
                        </th>        
                    </tr>
                    <tr>
                        <th style="border:1px solid #000000;" colspan="3">工程名称：</th>       
                        <th style="border:1px solid #000000;" colspan="3">客户姓名：</th>       
                        <th style="border:1px solid #000000;" colspan="2">设计师姓名：</th>       
                        <th style="border:1px solid #000000;" colspan="2">报价师姓名：</th>       
                    </tr>
                    <tr>
						<th style="border:1px solid #000000;" rowspan="2">序号</th> 
                        <th style="border:1px solid #000000;" rowspan="2" colspan="2">工程项目名称</th>         
                        <th style="border:1px solid #000000;" rowspan="2">工程量</th>       
                        <th style="border:1px solid #000000;" rowspan="2">单位</th>
                        <th style="border:1px solid #000000;" colspan="2">辅材费</th> 
                        <th style="border:1px solid #000000;" colspan="2">人工费</th>    
                        <th style="border:1px solid #000000;" rowspan="2">施工工艺及材料说明</th> 
                    </tr>
                    <tr>   
                        <th style="border:1px solid #000000;">辅材基价</th>       
                        <th style="border:1px solid #000000;">辅材合价</th>       
                        <th style="border:1px solid #000000;">人工基价</th>       
                        <th style="border:1px solid #000000;">人工合价</th> 
                      </tr>
                </thead>';
		foreach($mould['details'] as $key=>$vos){
			$str .= '<tr>
				<td style="border:1px solid #000000;">'.$vos['conditions_no'].'</td>
				<td style="border:1px solid #000000;" colspan="2">'.$vos['conditionsname'].'</td>
				<td style="border:1px solid #000000;" colspan="7"></td>
			</tr>';
			foreach($vos['son'] as $k=>$v){
				$str .= '<tr">
					<td style="border:1px solid #000000;">'.$v['room_no'].'</td>
					<td style="border:1px solid #000000;" colspan="9">'.$v['roomname'].'</td>
				</tr>';
				foreach($v['item'] as $kk=>$vv){
					$str .= '<tr>
						<td style="border:1px solid #000000;">'.($kk*1+1).'</td>
						<td style="border:1px solid #000000;" colspan="2">'.$vv['project'].'</td>
						<td style="border:1px solid #000000;"></td>
						<td style="border:1px solid #000000;">'.$vv['company'].'</td>
						<td style="border:1px solid #000000;">'.$vv['quota'].'</td>
						<td style="border:1px solid #000000;"></td>
						<td style="border:1px solid #000000;">'.$vv['craft_show'].'</td>
						<td style="border:1px solid #000000;"></td>
						<td style="border:1px solid #000000;" title="点击查看详情">'.$vv['material'].'</td>
					</tr>
					<tr>
						<td style="background-color:#999999;text-align:center;" colspan="2">小计</td>
						<td style="background-color:#999999;"></td>
						<td style="background-color:#999999;"></td>
						<td style="background-color:#999999;"></td>
						<td style="background-color:#999999;"></td>
						<td style="background-color:#999999;"></td>
						<td style="background-color:#999999;"></td>
						<td style="background-color:#999999;"></td>
					</tr>';
				}
			}
		}
		$str .= '</table>';		
		echo($str);
	}
	
	
}