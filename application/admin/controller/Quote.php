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

    public function ajax_get_address(){
        $table = ['1'=>'cities','2'=>'areas'];
        $where = ['1'=>'provinceid','2'=>'cityid'];
        $type = input('type');
        $id = input('id');
        $lists = Db::name($table[$type])->where([$where[$type]=>$id])->order('id','asc')->select();
        if($lists){
            $this->success('success','',$lists);
        }else{
            $this->error('none');
        }
    }

    //模板管理页面
    public function tmp_cost(){
        $userinfo = $this->_userinfo;
        $res = Db::name('tmp_cost')->where(['f_id'=>$userinfo['companyid'],'status'=>1])->group('tmp_id')->order('id','desc')->select();
        $this->assign([ 'datas'=>$res ]);
        return $this->fetch();
        // echo 1;die;
    }

    // 获取某条模板详情
    public function get_tmp_cost_info(){
        $userinfo = $this->_userinfo;
        $tmp_list = Db::name('tmp_cost')->where(['f_id'=>$userinfo['companyid'],'tmp_id'=>input('tmp_id')])->select();
        foreach($tmp_list as $k=>$v){
            $tmp_list[$k]['add_time'] = date('Y-m-d H:i',$v['add_time']);
        }
        echo json_encode(array('code'=>1,'datas'=>$tmp_list));
    }

    //获取模板列表
    public function get_tmp_cost_list(){
        $userinfo = $this->_userinfo;
        $res = Db::name('tmp_cost')->where(['f_id'=>$userinfo['companyid'],'status'=>1])->group('tmp_id')->select();
        echo json_encode(array('code'=>1,'datas'=>$res));
    }

    //获取订单取费模板详情
    public function get_order_tmp_cost(){
        $o_id = input('id');
        $offerlist = Model('offerlist')->get_order_info($o_id);
        echo json_encode(array('code'=>1,'datas'=>$offerlist['order_cost'],'default_cost'=>$offerlist['default_cost'],'append_cost'=>$offerlist['append_cost']));
    }

    //订单附加取费模板
    public function apennd_tmp_cost(){
        $offerlist_info = Db::name('offerlist')->where(['id'=>input('o_id')])->find();
        if($offerlist_info['status'] >= 5){
            $this->error('结算状态禁止修改模板');
        }
        $datas = [];
        //array_filter是去空值
        if(input('name')){
            $name = array_filter(input('name'));
        }else{
            $name = [];
        }
        if(input('sign')){
            $sign = array_filter(input('sign'));
        }else{
            $sign = [];
        }
        if(input('formula')){
            $formula = array_filter(input('formula'));
        }else{
            $formula = [];
        }
        if(input('rate')){
            $rate = array_filter(input('rate'));
        }else{
            $rate = [];
        }
        if(input('content')){
            $content = array_filter(input('content'));
        }else{
            $content = [];
        }
        // $sign = array_filter(input('sign'));
        // $formula = array_filter(input('formula'));
        // $rate = array_filter(input('rate'));
        // $content = array_filter(input('content'));
        $count_name = count($name);
        $count_sign = count($sign);
        $count_formula = count($formula);
        $count_rate = count($rate);
        $count_content = count($content);
        if($count_name != $count_sign || $count_sign != $count_formula || $count_formula != $count_rate || $count_rate != $count_content){
            $this->error('新增项目不得留空');
        }
        $time = time();
        $userinfo = $this->_userinfo;
        foreach($name as $k=>$v){
            $info = [];
            $info['name'] = $v;
            $info['sign'] = $sign[$k];
            $info['formula'] = $formula[$k];
            $info['rate'] = $rate[$k];
            $info['content'] = $content[$k];
            $info['add_time'] = $time;
            $datas[] = $info;
        }
        $tmp_cost = Db::name('tmp_cost')->where(['tmp_id'=>input('tmp_id')])->select();
        $list = array_merge($tmp_cost,$datas);
        //判断是否有效
        $sign_data['A1'] = 100;//自定义一个直接费
        $sign_data['A2'] = 200;//自定义一个优惠
        $sign_data['A3'] = 300;//自定义一个材料直接费
        foreach($list as $k=>$v){
            $count_sign = count($sign_data);
            $num = 1;
            foreach($sign_data as $k2=>$v2){
                $v['formula'] = str_replace($k2,$v2,$v['formula']);
                if($count_sign == $num){
                    $str = $v['formula'];
                    if(@eval("return $str;") && @is_numeric(eval("return $str;"))){
                        $sign_data[$v['sign']] = round(eval("return $str;")*$v['rate']/100,2);
                    }else{
                         //模板错误
                        $this->error($v['name'].'计算方式有误，请检查后重新提交');
                    }
                }else{
                    $num++;
                }
            }
        }
        $datas = json_encode($datas);
        $res = Db::name('offerlist')->where(['id'=>input('o_id')])->update(['tmp_append_cost'=>$datas]);
        $this->success('保存成功');
        // if($res){
        //     $this->success('保存成功');
        // }else{
        //     $this->error('保存失败');
        // }

    }

    public function add_tmp_cost(){
        if(input('tmp_name') && input('name') && input('sign') && input('formula') && input('rate')){
            $datas = [];
            $tmp_id = md5(input('tmp_name').rand(1,999999).microtime(true));
            $tmp_name = input('tmp_name');
            $name = array_filter(input('name'));
            $sign = array_filter(input('sign'));
            $formula = array_filter(input('formula'));
            $rate = array_filter(input('rate'));
            $content = array_filter(input('content'));

            $count_name = count($name);
            $count_sign = count($sign);
            $count_formula = count($formula);
            $count_rate = count($rate);
            $count_content = count($content);
            if($count_name != $count_sign || $count_sign != $count_formula || $count_formula != $count_rate || $count_rate != $count_content){
                $this->error('新增项目不得留空');
            }
            if($count_name < 1){
                $this->error('未添加费用');
            }
            $time = time();
            $userinfo = $this->_userinfo;
            foreach($name as $k=>$v){
                $info = [];
                $info['tmp_id'] = $tmp_id;
                $info['f_id'] = $userinfo['companyid'];
                $info['tmp_name'] = $tmp_name;
                $info['name'] = $v;
                $info['sign'] = $sign[$k];
                $info['formula'] = $formula[$k];
                $info['rate'] = $rate[$k];
                $info['content'] = $content[$k];
                $info['add_time'] = $time;
                $datas[] = $info;
            }
            //==========判断模板是否有效
            //判断字段 标识符是否重复
            $name_count = count(array_column($datas, 'name'));
            $sign_count = array_column($datas, 'sign');
            if(count($datas) !== $name_count){
                $this->error('字段名称不得重复');
            }
            if(count($datas) !== count($sign_count) || in_array('A1', $sign_count)){
                $this->error('标识符不得重复');
            }

            //判断计算方式
            $sign_data['A1'] = 100;//自定义一个直接费
            $sign_data['A2'] = 200;//自定义一个优惠
            $sign_data['A3'] = 200;//自定义一个材料直接费
            foreach($datas as $k=>$v){
                $count_sign = count($sign_data);
                $num = 1;
                foreach($sign_data as $k2=>$v2){
                    $v['formula'] = str_replace($k2,$v2,$v['formula']);
                    if($count_sign == $num){
                        $str = $v['formula'];
                        if(@eval("return $str;") && @is_numeric(eval("return $str;"))){
                            $sign_data[$v['sign']] = round(eval("return $str;")*$v['rate']/100,2);
                        }else{
                             //模板错误
                            $this->error($v['name'].'计算方式有误，请检查后重新提交');
                        }
                    }else{
                        $num++;
                    }
                }
            }
            //=============判断结束
            Db::startTrans();
            try {
                // if(input('tmp_id')){
                //     Db::name('tmp_cost')->where('tmp_id',input('tmp_id'))->delete();
                // }
                $re = Db::name('tmp_cost')->insertAll($datas);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->error('模板保存失败');
            }
            
            if($re){
                $this->success('模板保存成功','/admin/quote/tmp_cost');
            }else{
                $this->error('模板保存失败');
            }
        }else{
            if(!input('tmp_name')){
                echo json_encode(array('code'=>0,'msg'=>'模板名称不能为空'));die;
            }else{
                echo json_encode(array('code'=>0,'msg'=>'参数错误'));die;
            }
            
        }
    }

    public function delete_tmp_cost(){
        if(input('tmp_id')){
            $res = Db::name('tmp_cost')->where(['tmp_id'=>input('tmp_id')])->update(['status'=>9]);
            if($res){
                echo json_encode(['code'=>1,'msg'=>'删除成功']);
            }else{
                echo json_encode(['code'=>0,'msg'=>'删除失败']);
            }
        }else{
            echo json_encode(array('code'=>0,'msg'=>'参数错误'));die;
        }
    }










    //获取模板列表
    public function ajax_get_tmp_list(){
        $userinfo = $this->_userinfo;
        $where = [];
        $where['f_id']  = $userinfo['companyid'];
        $where['adminid'] = [$userinfo['userid']];
        if(input('type')){
            $where['type'] = input('type');
        }else{
            $where['type'] = 1;
        }
        $fzids = array_column(Db::name('admin')->where(['companyid'=>$userinfo['companyid'],'roleid'=>[10,1]])->field('userid')->select() ,'userid');
        $where['adminid'] = array_merge($where['adminid'],$fzids);
        $tmp_list = Db::name('tmp')->where($where)->field('tmp_id,tmp_name,remark,update_time')->group('tmp_id')->select();
        foreach($tmp_list as $k=>$v){
            $tmp_list[$k]['update_time'] = date('Y-m-d H:i',$v['update_time']);
        }
        echo json_encode(array('code'=>1,'datas'=>$tmp_list));
    }

    public function get_f_tmp_info(){
        $userinfo = $this->_userinfo;
        $tmp_id = input('tmp_id');
        if(!$tmp_id){
            echo json_encode(array('code'=>0,'msg'=>'参数错误'));
        }
        $tmp_list = Db::name('tmp')->where(['tmp_id'=>$tmp_id,'f_id'=>$userinfo['companyid']])->order('id','desc')->select();
        $data = [];
        $item_number = [];
        foreach($tmp_list as $k=>$v){
            $item_number[] = $v['item_number'];
        }
        $furniture = Db::name('furniture')->where(['serial_number'=>$item_number,'frameid'=>$userinfo['companyid']])->select();
        $furniture = array_column($furniture,null, 'serial_number');
        foreach($tmp_list as $k=>$v){
            $v['name'] = $furniture[$v['item_number']]['name'];
            $v['price'] = $furniture[$v['item_number']]['price'];
            $v['unit'] = $furniture[$v['item_number']]['unit'];
            $v['classify'] = $furniture[$v['item_number']]['classify'];
            $v['type_name'] = $furniture[$v['item_number']]['type_name'];
            $v['content'] = $furniture[$v['item_number']]['content'];
            $data[] = $v;
        }
        $this->success('success','',$data);
    }

    //获取模板详情
    public function ajax_get_tmp_info(){
        $userinfo = $this->_userinfo;
        $tmp_id = input('tmp_id');
        if(!$tmp_id){
            echo json_encode(array('code'=>0,'msg'=>'参数错误'));
        }
        $tmp_list = Db::name('tmp')->where(['tmp_id'=>$tmp_id,'f_id'=>$userinfo['companyid']])->order('id','asc')->select();

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

        $item_number = array_unique(array_column($tmp_list, 'item_number'));
        $offerquota = Db::name('offerquota')->where(['item_number'=>$item_number,'frameid'=>$userinfo['companyid']])->select();
        if(count($item_number) != count($offerquota)){
            //=============验证模板是否有效 end
            echo json_encode(array('code'=>0,'msg'=>'模板部分项目不全，模板失效'));die;
        }
        $offerquota = array_column($offerquota, null,'item_number');
        
        echo json_encode(array('code'=>1,'datas'=>$tmp_list,'offerquota'=>$offerquota));
    }

    //主材模板首页
    public function furniture_tmp(){
        $userinfo = $this->_userinfo;
        $where['f_id'] = $userinfo['companyid'];
        if(input('type')){
            $where['type'] = input('type');
        }else{
            $this->error('参数错误');
        }
        $res = Db::name('tmp')->where($where)->group('tmp_id')->order('id','desc')->select();
        $this->assign([ 'data'=>$res ]);
        return $this->fetch();
    }

    //添加主材模板
    public function add_furniture_tmp(){
        $userinfo = $this->_userinfo;
        $type = [2=>'主材',3=>'智能、家电',4=>'软装'];
        if(!isset($type[input('type')])){
            $this->error('参数错误');
        }
        $where['type_name'] = $type[input('type')];//类型
        $where['frameid'] = $userinfo['companyid'];//公司
        $classify = Db::name('furniture')->where($where)->group('classify')->select();
        //编辑
        if(input('tmp_id')){
            $tmp_list = Db::name('tmp')->where(['tmp_id'=>input('tmp_id')])->select();
            $tmp_name = $tmp_list[0]['tmp_name'];
            $data = [];
            $item_number = [];
            foreach($tmp_list as $k=>$v){
                $item_number[] = $v['item_number'];
            }
            $furniture = Db::name('furniture')->where(['serial_number'=>$item_number,'frameid'=>$userinfo['companyid']])->select();
            $furniture = array_column($furniture,null, 'serial_number');
            foreach($tmp_list as $k=>$v){
                $v['name'] = $furniture[$v['item_number']]['name'];
                $v['price'] = $furniture[$v['item_number']]['price'];
                $v['unit'] = $furniture[$v['item_number']]['unit'];
                $data[$furniture[$v['item_number']]['classify']][] = $v;
            }
            $this->assign([
                'data'=>$data,
                'tmp_name'=>$tmp_name,
                'tmp_id'=>input('tmp_id')
            ]);
        }
        $this->assign([
            'classify'=>$classify,
            'type_name'=>$type[input('type')],
        ]);
        return $this->fetch();
    }
    public function add_ftmp_operation(){
        $userinfo = $this->_userinfo;
        $type = [2=>'主材',3=>'智能、家电',4=>'软装'];
        if(!isset($type[input('type')]) || !input('data') || !input('tmp_name')){
            $this->error('参数错误');
        }
        $tmp_id = input('tmp_id')?input('tmp_id'):md5(input('tmp_name').rand(1,999999).microtime(true));
        $datas = [];
        $time = time();
        foreach(input('data') as $k=>$v){
            $info = [];
            $info['tmp_id'] = $tmp_id;
            $info['tmp_name'] = input('tmp_name');
            $info['f_id'] = $userinfo['companyid'];
            $info['work_type'] = $type[input('type')];
            $info['item_number'] = $k;
            $info['num'] = $v['num'];
            $info['type'] = input('type');
            $info['update_time'] = $time;
            $info['adminid'] = $userinfo['userid'];
            $datas[] = $info;
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
            $this->success('模板保存成功','/admin/quote/furniture_tmp/type/'.input('type'));
        }else{
            $this->error('模板保存失败');
        }
        var_dump(input());
    }

    //查看主材模板
    public function furniture_tmp_info(){

    }

	//报价模板首页
	public function index(){
	    $userinfo = $this->_userinfo;
        $where = [];
        $where['f_id'] = $userinfo['companyid'];
        $where['type'] = 1;
        $where['adminid'] = [$userinfo['userid']];
        //获取分总的id
        $fzids = array_column(Db::name('admin')->where(['companyid'=>$userinfo['companyid'],'roleid'=>[10,1]])->field('userid')->select() ,'userid');
        $where['adminid'] = array_merge($where['adminid'],$fzids);
		$res = Db::name('tmp')->where($where)->group('tmp_id')->order('id','desc')->select();
		$this->assign([ 'data'=>$res ]);
		return $this->fetch();
	}
	//报价模板查看
	public function checkmould(){
		$tmp_id = input('tmp_id');//模板id
        $userinfo = $this->_userinfo;
        $item_number = [];//所有项目集合
		// $type = input('type');//模板预览还是修改
		$tmp_list = Db::name('tmp')->where('tmp_id','=',$tmp_id)->order('id','asc')->select();
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
            $data[$v['space']][$v['item_number']] = $v['num'];
            $item_number[] = $v['item_number'];
        }
        $item_number = array_unique($item_number);
        $item_number_num = count($item_number);
        $offerquota = Db::name('offerquota')->where(['item_number'=>$item_number,'frameid'=>$userinfo['companyid']])->select();
        if($item_number_num != count($offerquota)){
            $item_number1 = array_column($offerquota, 'item_number');
            $arr = array_diff($item_number, $item_number1);
            $this->error('模板部分项目不全，模板失效  '. implode(',', $arr));
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
        if(input('tmp_id')){ //编辑
            $tmp_list = Db::name('tmp')->where('tmp_id','=',input('tmp_id'))->order('id','asc')->select();
            if($userinfo['userid'] != $tmp_list[0]['adminid']){
                $this->error('禁止修改他人的模板');
            }
            $tmp_name = $tmp_list[0]['tmp_name'];
            $data = [];
            $item_number = [];
            foreach($tmp_list as $k=>$v){
                if(!in_array($v['work_type'],$offer_type_check[1])){
                    $this->error('工种：'.$v['work_type'].' 不存在，模板失效');
                }
                if(!in_array($v['space'], $offer_type_check[2])){
                    $this->error('空间：'.$v['space'].' 不存在，模板失效');
                }
                $data[$v['space']][$v['item_number']] = $v['num'];
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
            //获取所有编号
            $item_number = [];
            foreach(input('data') as $k1=>$v1){
                foreach($v1 as $k2=>$v2){
                    foreach($v2 as $k3=>$v3){
                        $item_number[] = $k3;
                    }
                }
            }
            $offerquota = Db::name('offerquota')->where(['item_number'=>$item_number,'frameid'=>$userinfo['companyid']])->select();
            $offerquota = array_column($offerquota, null,'item_number');
			foreach(input('data') as $k1=>$v1){
				$type_word_name = $k1;//工种名称
				foreach($v1 as $k2=>$v2){
					$space = $k2;//工种名称
					foreach($v2 as $k3=>$v3){
						$datas[] = [
							'tmp_id'=>$tmp_id,
							'tmp_name'=>input('tmp_name'),
							'f_id'=>$f_id,
							'work_type'=>$offerquota[$k3]['type_of_work'],
							'space'=>$space,
							'item_number'=>$k3,
							'num'=>$v3,
							'update_time'=>$time,
                            'adminid'=>$userinfo['userid']
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
				$this->success('模板保存成功','/admin/quote/index');
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
        $tmp = Db::name('tmp')->where(['tmp_id'=>$id])->find();
        if($tmp['adminid'] != $userinfo['userid']){
            $this->error('禁止删除他人模板');
        }
		$re = Db::name('tmp')->where([ 'tmp_id'=>$id,'f_id'=>$userinfo['companyid'] ])->delete();
		if($re){
			$this->success('删除成功');
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
            $offer_type[$v['type']][] = $v['name'];
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
                $this->error('上传文件格式不正确');die;
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
            if($col_num != 'E') {
               $this->error('文件数据字段不匹配，请重新选择');die;
            } 
            $time = time();
            $tmp_id = md5($fileName.rand(1,999999).microtime(true));
            for ($i = 2; $i <= $row_num; $i ++) {
            	if(empty($sheet->getCell("A".$i)->getValue()) || empty($sheet->getCell("B".$i)->getValue()) || empty($sheet->getCell("C".$i)->getValue()) || empty($sheet->getCell("D".$i)->getValue())){
            		$this->error('字段不能为空');die;
            	}
            	$work_type = trim($sheet->getCell("A".$i)->getValue());
            	$space = trim($sheet->getCell("B".$i)->getValue());
            	if(!in_array($work_type, $offer_type[1])){
            		$this->error('工种：'.$work_type.'，不存在');die;
            	}
                if(!in_array($space, $offer_type[2])){
                    $this->error('空间类型'.$space.'，不存在');die;
                }
                $data[$i]['tmp_id']  = $tmp_id;
                $data[$i]['tmp_name']  = $tmp_name;
                $data[$i]['f_id']  = $userinfo['companyid'];
                $data[$i]['work_type']  = $work_type;
                $data[$i]['space']  = $space;
                $data[$i]['item_number']  = trim($sheet->getCell("C".$i)->getValue());
                $data[$i]['num']  = $sheet->getCell("E".$i)->getValue() ? trim($sheet->getCell("E".$i)->getValue()): '';
                $data[$i]['update_time']  = $time;
            }
            //将数据保存到数据库
            if ($data) {
                krsort($data);
                //把获取到的二维数组遍历进数据库
	           	Db::startTrans();
				try {
					foreach ($data as $key => $value) {
						$ishas = Db::name('offerquota')->where('item_number',$value['item_number'])->where('frameid',$value['f_id'])->find();
						if(!$ishas){
							exception('项目编号：'.$value['item_number'].'不存在');
						}
					}
                    $res = Db::name('tmp')->insertAll($data);
				    // 提交事务
				    Db::commit();
				} catch (\Exception $e) {
				    // 回滚事务
				    Db::rollback();
				    $this->error($e->getMessage());
                    die;
				}
                
               $this->success('导入成功');
            }else{
              $this->error('获取导入文件数据失败');
            }
        }else{
        $this->error('请选择上传文件');
        }
	}
}