<?php

// +----------------------------------------------------------------------
// | 辅材基础数据 分公司提供
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\common\controller\Adminbase;
use think\Db;
use think\Paginator;
use think\Request;
use think\db\Where;
use think\Cache;

use PHPExcel;
use PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;



class BasisData extends Adminbase{

    //辅材报表
    public function material_report(){

        $where = [];
        if(input('type_of_work')){
            $where['b.type_of_work'] = explode(',', input('type_of_work'));
        }
        if(input('classify')){
            $where['b.classify'] = explode(',', input('classify'));
        }
        if(input('fine')){
            $where['b.fine'] = explode(',', input('fine'));
        }
        if(input('source')){
            $where['f.source'] = explode(',', input('source'));
        }
        if(input('frame')){
            $where['f.fid'] = explode(',', input('frame'));
        }

        if(!empty($where)){
            $data = Db::name('f_materials')->alias('f')->leftJoin('basis_materials b','f.p_amcode = b.amcode')->group('f.amcode')->where($where)->order('f.p_amcode','asc')->select();
            // var_dump($data);
        }else{
            $data = [];
        }

        $type_of_work = Db::name('basis_materials')->field('type_of_work')->group('type_of_work')->select();
        $classify = Db::name('basis_materials')->field('classify')->group('classify')->select();
        $fine = Db::name('basis_materials')->field('fine')->group('fine')->select();
        $source = Db::name('f_materials')->field('source')->group('source')->select();
        $frame = array_column(Db::name('frame')->where('levelid',3)->field('id,name')->select(), null,'id');
        $this->assign('type_of_work',$type_of_work);
        $this->assign('classify',$classify);
        $this->assign('fine',$fine);
        $this->assign('source',$source);
        $this->assign('frame',$frame);
        $this->assign('data',$data);
        return $this->fetch('');
    }

    public function material_report_by_f(){
        $field = ['brank'=>'品牌','place'=>'产地','price'=>'出库价','in_price'=>'入库价','pack'=>'包装数量','one_price'=>'出库计量单价','one_in_price'=>'入库计量单价','phr'=>'出库单位','source'=>'来源'];
        $frame = array_column(Db::name('frame')->where('levelid',3)->field('id,name')->select(), null,'id');
        $where = [];
        $condition = [];
        $f_materials = [];
        $has_f_materials = [];
        if(input('type_of_work')){
            $where['type_of_work'] = explode(',', input('type_of_work'));
        }
        if(input('classify')){
            $where['classify'] = explode(',', input('classify'));
        }
        if(input('fine')){
            $where['fine'] = explode(',', input('fine'));
        }
        if(input('frame')){
            $condition['fid'] = explode(',', input('frame'));
        }else{
            $condition['fid'] = array_keys($frame);
        }

        

        if(1){
            $basis_materials = Db::name('basis_materials')->group('amcode')->where($where)->order('amcode','asc')->select();
            $amcode = array_column($basis_materials, 'amcode');
            if(!empty($amcode)){
                $f_datas = Db::name('f_materials')->where($condition)->where(['p_amcode'=>$amcode])->select();
                foreach($f_datas as $k=>$v){
                    if(is_numeric($v['pack']) && $v['pack'] > 0){
                        $v['one_price']  = $v['price'] / $v['pack'];
                    }else{
                        $v['one_price'] = 0;
                    }
                    if(is_numeric($v['pack']) && $v['pack'] > 0){
                        $v['one_in_price']  = $v['in_price'] / $v['pack'];
                    }else{
                        $v['one_in_price'] = 0;
                    }
                    
                    $f_materials[$v['fid']][$v['p_amcode']][] = $v;
                    $has_f_materials[] = $v['p_amcode'];
                }
            }
        }else{
            $basis_materials = [];
        }
        $type_of_work = Db::name('basis_materials')->field('type_of_work')->group('type_of_work')->select();
        $classify = Db::name('basis_materials')->field('classify')->group('classify')->select();
        $fine = Db::name('basis_materials')->field('fine')->group('fine')->select();
        $frame = array_column(Db::name('frame')->where('levelid',3)->field('id,name')->select(), null,'id');
        $this->assign('type_of_work',$type_of_work);
        $this->assign('classify',$classify);
        $this->assign('fine',$fine);
        $this->assign('frame',$frame);
        $this->assign('basis_materials',$basis_materials);
        $this->assign('f_materials',$f_materials);
        $this->assign('has_f_materials',$has_f_materials);
        $this->assign('fid',$condition['fid']);
        $this->assign('field',$field);
        return $this->fetch();
    }

    //查看每间分公司录入情况报表
    public function get_entry_statistical(){
        $arr = [];
        $f_materials = array_column(Db::name('f_materials')->group('fid')->field('count(id) as f_materials,fid')->select(),null, 'fid') ;
        $f_project = array_column(Db::name('f_project')->group('fid')->field('count(id) as f_project,fid')->select(),null, 'fid');
        $apply_material1 = array_column(Db::name('apply_material')->where(['status'=>1])->group('fid')->field('count(id) as apply_material1,fid')->select(),null, 'fid');
        $apply_material2 = array_column(Db::name('apply_material')->where(['status'=>2])->group('fid')->field('count(id) as apply_material2,fid')->select(),null, 'fid');
        $apply_project1 = array_column(Db::name('apply_project')->where(['status'=>1])->group('fid')->field('count(id) as apply_project1,fid')->select(),null, 'fid');
        $apply_project2 = array_column(Db::name('apply_project')->where(['status'=>2])->group('fid')->field('count(id) as apply_project2,fid')->select(),null, 'fid');
        $personnel = array_column(Db::name('personnel')->group('fid')->field('count(id) as personnel,fid')->select(),null, 'fid');
        $department = array_column(Db::name('department')->group('fid')->field('count(id) as department,fid')->select(),null, 'fid');
        $frame = Db::name('frame')->where('levelid',3)->field('id,name')->select();
        foreach($frame as $k=>$v){
            if(isset($f_materials[$v['id']])){
                $arr[$v['name']]['f_materials'] = $f_materials[$v['id']]['f_materials'];
            }
            if(isset($f_project[$v['id']])){
                $arr[$v['name']]['f_project'] = $f_project[$v['id']]['f_project'];
            }
            if(isset($apply_material1[$v['id']])){
                $arr[$v['name']]['apply_material1'] = $apply_material1[$v['id']]['apply_material1'];
            }
            if(isset($apply_material2[$v['id']])){
                $arr[$v['name']]['apply_material2'] = $apply_material2[$v['id']]['apply_material2'];
            }
            if(isset($apply_project1[$v['id']])){
                $arr[$v['name']]['apply_project1'] = $apply_project1[$v['id']]['apply_project1'];
            }
            if(isset($apply_project2[$v['id']])){
                $arr[$v['name']]['apply_project2'] = $apply_project2[$v['id']]['apply_project2'];
            }
            if(isset($personnel[$v['id']])){
                $arr[$v['name']]['personnel'] = $personnel[$v['id']]['personnel'];
            }
            if(isset($department[$v['id']])){
                $arr[$v['name']]['department'] = $department[$v['id']]['department'];
            }
        }
        $this->assign('data',$arr);
        return $this->fetch();
    }

    //公共工种列表
    public function public_type_work(){
        $where = [];
        $res = Db::name('basis_type_work')->where($where)->order('id','asc')->paginate(20,false,['query'=>request()->param()]);
        $this->assign('data',$res);
        return $this->fetch();
    }

    public function add_public_type_work(){
        $data['name'] = input('name');
        $id = Db::name('basis_type_work')->where(['name'=>$data['name']])->value('id');
        if($id){
            $this->error('添加工种已存在');
        }
        $res = Db::name('basis_type_work')->insert($data);
        if($res){
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }

    //辅材公共基础库列表
    public function public_warehouse(){
        $where = [];
        if(input('samcode')){
            $where[] = ['amcode','like','%'.input('samcode').'%'];
        }
        if(input('stype_of_work')){
            $where[] = ['type_of_work','like','%'.input('stype_of_work').'%'];
        }
        if(input('sclassify')){
            $where[] = ['classify','like','%'.input('sclassify').'%'];
        }
        if(input('sfine')){
            $where[] = ['fine','like','%'.input('sfine').'%'];
        }
        if(input('sname')){
            $where[] = ['name','like','%'.input('sname').'%'];
        }
        $res = Db::name('basis_materials')->where($where)->order('id','asc')->paginate(20,false,['query'=>request()->param()])->each(function($item, $key){
            $item['count'] = Db::name('f_materials')->where(['p_amcode'=>$item['amcode']])->field('count(id) as count')->find()['count'];
            return $item;
        });

        $this->assign('data',$res);
        return $this->fetch();
    }

    //添加辅材公共基础库
    public function add_public_warehouse(){
        $data['amcode'] = input('amcode');
        $data['type_of_work'] = input('type_of_work');
        $data['classify'] = input('classify');
        $data['fine'] = input('fine');
        $data['name'] = input('name');
        $data['unit'] = input('unit');
        if(input('brank')){
            $data['brank'] = input('brank');
        }
        if(input('place')){
            $data['place'] = input('place');
        }
        $data['coefficient'] = input('coefficient');
        $data['important'] = input('important');
        if(!$data['amcode'] || !$data['type_of_work'] || !$data['fine'] || !$data['name'] || !$data['unit'] ){
            $this->error('参数不得为空');
        }
        if($data['coefficient'] < 0 || $data['coefficient'] > 100 || !is_numeric($data['coefficient']) || $data['coefficient']%1 != 0){
            $this->error('系数输入不规范');
        }
        if($data['important'] != '0' && $data['important'] != 1){
            $this->error('是否重要输入不规范');
        }

        $id = Db::name('basis_materials')->where(['amcode'=>$data['amcode']])->value('id');
        if($id){
            $this->error('编号已存在');
        }
        //判断细类单位是否一致
        $unit = Db::name('basis_materials')->where(['fine'=>$data['fine']])->value('unit');
        if($unit && $unit != $data['unit']){
            $this->error('分类单位错误，应为：'.$unit);
        }
        $res = Db::name('basis_materials')->insert($data);
        if($res){
            if(input('apply_material_id')){
                //分公司申请后 管理员添加的辅材 自动绑定的申请的辅材里面
                Db::name('apply_material')->where(['id'=>input('apply_material_id')])->update(['p_amcode'=>$data['amcode'],'status'=>2,'audittime'=>time()]);
            }
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }

    //修改辅材公共基础库
    public function edit_public_warehouse(){
        $data['amcode'] = input('amcode');
        $data['type_of_work'] = input('type_of_work');
        $data['classify'] = input('classify');
        $data['fine'] = input('fine');
        $data['name'] = input('name');
        $data['unit'] = input('unit');
        $data['brank'] = input('brank');
        $data['place'] = input('place');
        $data['coefficient'] = input('coefficient');
        $data['important'] = input('important');
        if(!$data['amcode'] || !$data['type_of_work'] || !$data['fine'] || !$data['name'] || !$data['unit'] ){
            $this->error('参数不得为空');
        }
        if($data['coefficient'] < 0 || $data['coefficient'] > 100 || !is_numeric($data['coefficient']) || $data['coefficient']%1 != 0){
            $this->error('系数输入不规范');
        }
        if($data['important'] != '0' && $data['important'] != 1){
            $this->error('是否重要输入不规范');
        }
        //判断细类单位是否一致
        
        $edit_unit = 0;
        Db::startTrans();
        try {
            $unit = Db::name('basis_materials')->where(['fine'=>$data['fine']])->value('unit');
            if($unit && $unit != $data['unit']){
                $edit_unit = Db::name('basis_materials')->where(['fine'=>$data['fine']])->update(['unit'=>$data['unit']]);
                unset($data['unit']);
            }
            $res = Db::name('basis_materials')->where(['amcode'=>$data['amcode']])->update($data);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if($edit_unit){
            $this->success('编辑成功，同时修改了'. ($edit_unit-1) .'个辅材单位');
        }else{
            $this->success('编辑成功');
        }
    }

    //项目公共基础库列表
    public function public_project(){
        $where = [];
        if(input('item_number')){
            $where[] = ['item_number','like','%'.input('item_number').'%'];
        }
        if(input('type_word_id')){
            $where[] = ['type_word_id','=',input('type_word_id')];
        }
        if(input('name')){
            $where[] = ['name','like','%'.input('name').'%'];
        }
        $res = Db::name('basis_project')->where($where)->order('id','asc')->paginate(20,false,['query'=>request()->param()])->each(function($item, $key){
            $item['count'] = Db::name('f_project')->where(['p_item_number'=>$item['item_number']])->field('count(id) as count')->find()['count'];
            return $item;
        });;
        //获取所有辅材细类
        $fines = Db::name('basis_materials')->field('fine,unit')->group('fine')->select();
        $type_work = array_column(Db::name('basis_type_work')->field('id,name')->select(),null,'id');
        $this->assign('data',$res);
        $this->assign('fines',$fines);
        $this->assign('type_work',$type_work);
        return $this->fetch();
    }

    public function add_public_project(){
        $datas = [];
        $datas['item_number'] = input('item_number');
        $datas['type_word_id'] = input('type_word_id');
        $datas['name'] = input('name');
        $datas['unit'] = input('unit');
        $datas['content'] = input('content');
        $find = input('find');
        $funit = input('funit');
        if ($find && count($find) != count(array_unique($find))) {   
            $this->error('细类不得重复');
        } 
        //判断编号是否有重复
        $has_project = Db::name('basis_project')->where(['item_number'=>$datas['item_number']])->value('id');
        if($has_project){
            $this->error('编号已存在');
        }
        if(count($find) != count($funit)){
            $this->error('辅材参数错误');
        }
        if($find){
            $materials = [];
            foreach($find as $k=>$v){
                $info = [];
                $info['fine'] = $v;
                $info['funit'] = $funit[$k];
                $materials[] = $info;
            }
            $datas['fine'] = json_encode($materials);
        }else{
            $datas['fine'] = '{}';
        }
        
        $res = Db::name('basis_project')->insert($datas);
        if($res){
            if(input('apply_project_id')){
                //分公司申请后 管理员添加的辅材 自动绑定的申请的辅材里面
                Db::name('apply_project')->where(['id'=>input('apply_project_id')])->update(['p_item_number'=>$datas['item_number'],'status'=>2,'audittime'=>time()]);
            }
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }

    //获取报价基础库所需辅材
    public function get_b_project_fine(){
        $fine = Db::name('basis_project')->where(['item_number'=>input('item_number')])->value('fine');
        if(!$fine){
            $this->success('success','',[]);
        }
        $data = json_decode($fine,true);
        if($fine){
            $this->success('success','',$data);
        }else{
            $this->success('success','',[]);
        }
    }

    public function edit_public_project(){
        $datas = [];
        $datas['item_number'] = input('item_number');
        $datas['type_word_id'] = input('type_word_id');
        $datas['name'] = input('name');
        $datas['unit'] = input('unit');
        $datas['content'] = input('content');
        $find = input('find');
        $funit = input('funit');
        if ($find && count($find) != count(array_unique($find))) {   
            $this->error('细类不得重复');
        }
        if(count($find) != count($funit)){
            $this->error('辅材参数错误');
        }
        if($find){
            $materials = [];
            foreach($find as $k=>$v){
                $info = [];
                $info['fine'] = $v;
                $info['funit'] = $funit[$k];
                $materials[] = $info;
            }
            $datas['fine'] = json_encode($materials);
        }else{
            $datas['fine'] = '{}';
        }
        Db::startTrans();
        try {
            $basis_project = Db::name('basis_project')->where(['item_number'=>$datas['item_number']])->find();
            $res = Db::name('basis_project')->where(['item_number'=>$datas['item_number']])->update($datas);
            if($basis_project['fine'] != $datas['fine']){
                //辅材修改了 需要分公司重新设置该报价
                $rs = Db::name('f_project')->where(['p_item_number'=>$basis_project['item_number']])->update(['status'=>2]);
                Db::name('offerquota')->where('item_number','like',$basis_project['item_number'].'%')->delete();
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
       
        if($res){
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }

    public function del_public_project(){
        $item_number = input('item_number');
        Db::startTrans();
        try {
            Db::name('basis_project')->where(['item_number'=>$item_number])->delete();
            Db::name('f_project')->where(['p_item_number'=>$item_number])->update(['status'=>3]);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success('删除成功');
    }

    //获取单个基础辅材库
    public function get_public_warehouse(){
        $amcode = input('amcode');
        $info = Db::name('basis_materials')->where(['amcode'=>$amcode])->find();
        if(!$info){
            $this->error('参数错误');
        }
        $this->success('success','',$info);
    }

    //获取单个基础报价项目库
    public function get_public_project(){
        $item_number = input('item_number');
        $info = Db::name('basis_project')->where(['item_number'=>$item_number])->find();
        if(!$info){
            $this->error('参数错误');
        }
        $this->success('success','',$info);
    }

    //=========================================================分公司

    //分公司添加页面 
    public function pwarehouse(){
        $where = [];
        if(input('samcode')){
            $where[] = ['amcode','like','%'.input('samcode').'%'];
        }
        if(input('type_of_work')){
            $where[] = ['type_of_work','like','%'.input('type_of_work').'%'];
        }
        if(input('fine')){
            $where[] = ['fine','like','%'.input('fine').'%'];
        }
        if(input('name')){
            $where[] = ['name','like','%'.input('name').'%'];
        }
        if(input('typeof')){
            $where[] = ['type_of_work','like','%'.input('typeof').'%'];
        }
        $neq=Db::table('fdz_basis_materials')->field('type_of_work')->group('type_of_work')->select();
        $res = Db::name('basis_materials')->where($where)->order('id','asc')->paginate(20,false,['query'=>request()->param()]);
        $p_amcode = array_column($res->items(), 'amcode');
        $p_amcode = array_column(Db::name('f_materials')->where(['p_amcode'=>$p_amcode,'fid'=>$this->_userinfo['companyid']])->field('p_amcode')->select(),'p_amcode');
        // var_dump($p_amcode);die;
        $this->assign('amcode',$p_amcode);
        $this->assign('data',$res);
        $this->assign('typeof',$neq);
        return $this->fetch();
    }

     //分公司添加页面 
    public function pproject(){
        $where = [];
        if(input('item_number')){
            $where[] = ['item_number','like','%'.input('item_number').'%'];
        }
        if(input('type_word_id')){
            $where[] = ['type_word_id','=',input('type_word_id')];
        }
        if(input('name')){
            $where[] = ['name','like','%'.input('name').'%'];
        }
        $res = Db::name('basis_project')->where($where)->order('id','asc')->paginate(20,false,['query'=>request()->param()])->each(function($item, $key){
            $fine = $item['fine'];
            $fine_info['has'] = [];
            $fine_info['nohas'] = [];
            if(!$fine || $fine == '{}'){
                $item['fine_info'] = $fine_info;
                return $item;
            }
            $fine = json_decode($fine);
            $fine = array_column($fine,'fine');
            $m_fine = Db::name('f_materials')->where(['fine'=>$fine,'fid'=>$this->_userinfo['companyid']])->group('fine')->select();
            $m_fine = array_column($m_fine,'fine');
            
            foreach($fine as $k=>$v){
                if(in_array($v, $m_fine)){
                    $fine_info['has'][] = $v;
                }else{
                    $fine_info['nohas'][] = $v;
                }
            }
            $item['fine_info'] = $fine_info;
            return $item;
        });
        //获取所有辅材细类
        $fines = Db::name('basis_materials')->field('fine,unit')->group('fine')->select();
        $type_work = array_column(Db::name('basis_type_work')->field('id,name')->select(),null,'id');

        //判断是否已添加
        $item_number = array_column($res->items(), 'item_number');
        $item_number = array_column(Db::name('f_project')->where(['p_item_number'=>$item_number,'fid'=>$this->_userinfo['companyid']])->field('p_item_number')->select(),'p_item_number');
        $this->assign('item_number',$item_number);
        $this->assign('data',$res);
        $this->assign('fines',$fines);
        $this->assign('type_work',$type_work);
        return $this->fetch();
    }

    //公司添加的仓库列表
    public function fwarehouse_list(){
        $where = [];
        $condition = [];
        if(input('amcode')){
            $where[] = ['amcode','like','%'.input('amcode').'%'];
        }
        if(input('fid')){
            $where[] = ['fid','=',input('fid')];
        }else{
            $where[] = ['fid','=',$this->_userinfo['companyid']];
        }
        if(input('name')){
            $name = Db::name('basis_materials')->where('name','like','%'.input('name').'%')->field('amcode')->select();
            if($name){
                $condition['p_amcode'] = array_column($name, 'amcode');
            }else{
                $condition['p_amcode'] = 0;
            }
        }
          
        $data = Db::name('f_materials')->where($condition)->where($where)->order('id','asc')->paginate(20,false,['query'=>request()->param()]);
        $p_amcode = array_unique(array_column($data->items(), 'p_amcode'));
        $basis_materials = array_column(Db::name('basis_materials')->where(['amcode'=>$p_amcode])->select(),null, 'amcode');
        $frame = Db::name('frame')->where('levelid',3)->field('id,name')->select();
        $user=$this->_userinfo;
        $this->assign('user',$user['roleid']);
        $this->assign('frame',$frame);
        $this->assign('admininfo',$this->_userinfo);
        $this->assign('basis_materials',$basis_materials);
        $this->assign('data',$data);
        return $this->fetch();
    }

    //分公司 添加辅材页面(忘记还有没有用了)
    public function add_fwarehouse(){
        $amcode = input('amcode');
        $info = Db::name('basis_materials')->where(['amcode'=>$amcode])->find();
        if(!$info){
            $this->error('参数错误');
        }
        $frame = Db::name('frame')->where('levelid',3)->field('id,name')->select();
        $this->assign('info',$info);
        $this->assign('frame',$frame);
        $this->assign('admininfo',$this->_userinfo);
        return $this->fetch();
    }
    //分公司 添加辅材操作
    public function add_fwarehouse_operation(){
        $datas = input();
        $datas['fid'] = $this->_userinfo['companyid'];
        $info = Db::name('basis_materials')->where(['amcode'=>$datas['p_amcode']])->find();
        if(!$info){
            $this->error('参数错误');
        }
        if($info['img']){
            $datas['img'] = $info['img'];
        }
        $datas['fine'] = $info['fine'];
        // var_dump($datas);die;

        Db::startTrans();
        try {
            $res = Db::name('f_materials')->insertGetId($datas);
            Db::name('f_materials')->where(['id'=>$res])->update(['amcode'=>$info['amcode'].'_'.$res]);
            $this->update_fwarehouse($info['amcode'].'_'.$res);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success('添加成功','fwarehouse_list');
    }

    //分公司 修改辅材操作
    public function edit_fwarehouse_operation(){
        $datas = input();
        Db::startTrans();
        try {
            $res = Db::name('f_materials')->where(['amcode'=>$datas['amcode']])->update($datas);
            $this->update_fwarehouse($datas['amcode']);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success('修改成功');
    }

    //分公司 删除辅材操作
    public function del_fwarehouse_operation(){
        $amcode = input('amcode');
        $info = Db::name('f_materials')->where(['amcode'=>$amcode])->find();
        Db::startTrans();
        try {
            // Db::name('f_project')->where('material','like','%'.$amcode.'%')->where(['fid'=>$info['fid']])->update(['status'=>2]);
            $this->update_fwarehouse($amcode,1);
            $res = Db::name('f_materials')->where(['amcode'=>$amcode])->delete();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if($res){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    //公司添加的报价项目
    public function fproject_list(){
        $where = [];
        $condition = [];
        // $where[] = ['status', 'IN', [1,2]];
        if(input('sitem_number')){
            $where[] = ['item_number','like','%'.input('sitem_number').'%'];
        } 
        if(input('sp_item_number')){
            $where[] = ['p_item_number','like','%'.input('sp_item_number').'%'];
        } 
        if(input('fid')){
            $where[] = ['fid','=',input('fid')];
        }else{
            $where[] = ['fid','=',$this->_userinfo['companyid']];
        }
        if(input('sname')){
            $name = Db::name('basis_project')->where('name','like','%'.input('sname').'%')->field('item_number')->select();
            if($name){
                $condition['p_item_number'] = array_column($name, 'item_number');
            }else{
                $condition['p_item_number'] = 0;
            }
        }
        $data = Db::name('f_project')->where($condition)->where($where)->order('status','desc')->order('id','asc')->paginate(20,false,['query'=>request()->param()]);
        $p_item_number = array_unique(array_column($data->items(), 'p_item_number'));
        $basis_project = array_column(Db::name('basis_project')->where(['item_number'=>$p_item_number])->select(),null, 'item_number');
        $frame = Db::name('frame')->where('levelid',3)->field('id,name')->select();

        $type_work = array_column(Db::name('basis_type_work')->field('id,name')->select(),null,'id');
        $this->assign('admininfo',$this->_userinfo);
        $this->assign('type_work',$type_work);
        $this->assign('frame',$frame);
        $this->assign('basis_project',$basis_project);
        $this->assign('data',$data);
        return $this->fetch();
    }

    //分公司 添加辅材页面(改ajax添加了 暂时不用了)
    public function add_fproject(){
        $item_number = input('item_number');
        $info = Db::name('basis_project')->where(['item_number'=>$item_number])->find();
        if(!$info){
            $this->error('参数错误');
        }
        $frame = Db::name('frame')->where('levelid',3)->field('id,name')->select();
        //获取所需辅材细类
        if($info['fine']){
            $fine = json_decode($info['fine'],true);
            if(!$fine || !is_array($fine)){
                $this->error('该项目辅材配置有误，请联系管理员处理');
            }
            $fine = array_column($fine, 'fine');
            $basis_materials = Db::name('f_materials')->where(['fine'=>$fine])->select();
            $fmaterials = [];
            foreach($basis_materials as $k=>$v){
                $fmaterials[$v['fine']][] = $v;
            }
            if(count($fine) != count($fmaterials)){
                $this->error('该项目辅材不全，请及时补充');
            }
        }else{
            $fmaterials = [];
        }
        $this->assign('info',$info);
        $this->assign('frame',$frame);
        $this->assign('fmaterials',$fmaterials);
        $this->assign('admininfo',$this->_userinfo);
        return $this->fetch();
    }

    //分公司 添加项目操作
    public function add_fproject_operation(){
        $datas = input();
        $datas['fid'] = $this->_userinfo['companyid'];
        $info = Db::name('basis_project')->where(['item_number'=>$datas['p_item_number']])->find();
        if(!$info){
            $this->error('参数错误');
        }
        $datas['cost_value'] = $datas['quota'] + $datas['craft_show'];
        if (isset($datas['material'])) {
            $datas['material'] = json_encode($datas['material']);
        }else{
            $datas['material'] = '';
        }

        Db::startTrans();
        try {
            $res = Db::name('f_project')->insertGetId($datas);
            Db::name('f_project')->where(['id'=>$res])->update(['item_number'=>$info['item_number'].'_'.$res]);
            $this->update_fproject($info['item_number'].'_'.$res);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success('添加成功','fproject_list');
    }

    //分公司 编辑项目操作
    public function edit_fproject_operation(){
        $datas = input();
        // $datas['fid'] = $this->_userinfo['companyid'];
        $info = Db::name('basis_project')->where(['item_number'=>$datas['p_item_number']])->find();

        $f_project = Db::name('f_project')->where(['id'=>$datas['id']])->find();
        if(!$info || !$f_project){
            $this->error('参数错误');
        }
        $datas['cost_value'] = $datas['quota'] + $datas['craft_show'];
        if (isset($datas['material'])) {
            $datas['material'] = json_encode($datas['material']);
        }else{
            $datas['material'] = '';
        }
        $datas['status'] = 1;
        Db::startTrans();
        try {
            $res = Db::name('f_project')->where(['id'=>$datas['id']])->update($datas);
            $this->update_fproject($f_project['item_number']);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success('修改成功','fproject_list');
    }

    //删除分公司报价
    public function del_fproject(){
        $id = input('id');
        $f_project = Db::name('f_project')->where(['id'=>$id])->find();
        if(!$f_project){
            $this->error('参数错误');
        }
        try {
            $this->update_fproject($f_project['item_number'],1);
            $res = Db::name('f_project')->where(['id'=>$id])->delete();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success('删除成功');
    }

    public function create_datas(){
        $admininfo = $this->_userinfo;
        if(!input('fid')){
            $this->error('参数错误');
        }
        $fid = input('fid');
        $time = time();
        Db::startTrans();
        try {
            // 先清空原来的
            Db::name('materials')->where(['frameid'=>$fid])->delete();
            Db::name('offerquota')->where(['frameid'=>$fid])->delete();

            $materials = $this->create_materials($fid);
            Db::name('materials')->insertAll($materials);

            foreach($materials as $k=>$v){
                $materials[$k]['time'] = $time;
            }
            Db::name('materials_fb')->insertAll($materials);

            $projects = $this->create_project($fid);
            Db::name('offerquota')->insertAll($projects);
            foreach($projects as $k=>$v){
                $projects[$k]['time'] = $time;
            }
            Db::name('offerquota_fb')->insertAll($projects);
           
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success('更新成功');
    }

    //生成 最终仓库数据 并保存
    public function create_materials($fid){
        $admininfo = $this->_userinfo;
        $materials = Db::name('f_materials')->where(['fid'=>$fid])->select();
        $p_amcode = array_unique(array_column($materials, 'p_amcode'));
        $basis_materials = array_column(Db::name('basis_materials')->where(['amcode'=>$p_amcode])->select(),null ,'amcode');
        $datas = [];
        foreach($materials as $k=>$v){
            if(!isset($basis_materials[$v['p_amcode']])){
                // $this->error('辅材编码'.$v['amcode'].'的基础库不存在');
                throw new \think\Exception('辅材编码'.$v['amcode'].'的基础库不存在', 10006);
            }
            $info = [];
            $info['frameid'] = $v['fid'];
            $info['userid'] = $admininfo['userid'];
            $info['amcode'] = $v['amcode'];
            $info['fine'] = $basis_materials[$v['p_amcode']]['classify'];
            $info['brand'] = $v['brank'];
            $info['place'] = $v['place'];
            $info['category'] = $basis_materials[$v['p_amcode']]['type_of_work'];
            $info['name'] = $basis_materials[$v['p_amcode']]['name'];
            //基础库还没有图片
            // if($v['img']){
            //     $info['img'] = $v['img'];
            // }else{
            //     $info['img'] = $basis_materials[$v['p_amcode']]['img'];
            // }
            $info['img'] = $v['img'];
            // $info['norms'] = $v['xxx'];
            $info['units'] = $v['phr'];
            $info['phr'] = $v['pack'].$basis_materials[$v['p_amcode']]['unit'].'/'.$v['phr'];
            $info['price'] = $v['price'];
            $info['in_price'] = $v['in_price'];
            $info['remarks'] = $v['source'];
            $info['coefficient'] = $basis_materials[$v['p_amcode']]['coefficient'];
            $info['important'] = $basis_materials[$v['p_amcode']]['important'];
            $datas[] = $info;
        }
        return $datas;
    }

    //生成 最终项目数据 并保存
    public function create_project($fid){
        $admininfo = $this->_userinfo;
        $project = Db::name('f_project')->where(['fid'=>$fid,'status'=>1])->select();
        $p_item_number = array_unique(array_column($project, 'p_item_number'));
        $basis_project = array_column(Db::name('basis_project')->where(['item_number'=>$p_item_number])->select(),null ,'item_number');
        $basis_type_work = array_column(Db::name('basis_type_work')->select(), 'name','id') ;
        $datas = [];
        foreach($project as $k=>$v){
            if(!isset($basis_project[$v['p_item_number']])){
                // $this->error('项目编号'.$v['item_number'].'找不到公共基础项目');
                throw new \think\Exception('项目编号'.$v['item_number'].'找不到公共基础项目', 10006);
            }
            $info = [];
            //计算辅材基数
            if($v['material']){
                $fine = json_decode($basis_project[$v['p_item_number']]['fine'],true);
                $fine = array_column($fine, 'funit','fine');//公式

                $material = json_decode($v['material'],true);
                $datas_material = [];
                foreach($material as $k1=>$v1){
                    // $fine[$k1] 需要的数量
                    $pack = Db::name('f_materials')->where(['amcode'=>$v1])->value('pack');//包装数量
                    if(!$pack){
                        // $this->error('项目编号'.$v['item_number'].'中,辅材编号'.$v1.'不存在');
                        throw new \think\Exception('项目编号'.$v['item_number'].'中,辅材编号'.$v1.'不存在', 10006);
                    }
                    //下面这个格式是按照之前的格式的 [对应辅材id,基数]
                    // v1 的id不对
                    $num = round($fine[$k1]/$pack,2);
                    if($num <= 0){
                        $num = 0.001;
                    }
                    $datas_material[] = [$v1,round($num,2)];
                }
                $info['content'] = json_encode($datas_material);
            }else{
                $info['content'] = '';
            }
            $info['frameid'] = $v['fid'];
            $info['userid'] = $admininfo['userid'];
            $info['item_number'] = $v['item_number'];
            $info['type_of_work'] = $basis_type_work[$basis_project[$v['p_item_number']]['type_word_id']];

            if($v['remark']){
                $info['project'] = $basis_project[$v['p_item_number']]['name'].'（'.$v['remark'].'）';
            }else{
                $info['project'] = $basis_project[$v['p_item_number']]['name'];
            }
            
            $info['company'] = $basis_project[$v['p_item_number']]['unit'];
            $info['cost_value'] = $v['cost_value'];
            $info['quota'] = $v['quota'];
            $info['craft_show'] = $v['craft_show'];
            $info['labor_cost'] = $v['labor_cost'];
            $info['material'] = $basis_project[$v['p_item_number']]['content'];
            $datas[] = $info;
        }
        return $datas;
    }

    //获取公司项目使用了什么辅材
    public function get_project_material(){
        $item_number = input('item_number');
        $material = Db::name('f_project')->where(['item_number'=>$item_number])->value('material');
        if(!$material){
            $this->error('无辅材信息');
        }
        $material = json_decode($material,true);
        if($material){
            $material = array_values($material);
            $material = Db::name('f_materials')->where(['amcode'=>$material])->select();
            $p_amcode = array_column($material, 'p_amcode');
            $p_amcode = Db::name('basis_materials')->field('id,amcode,name,unit')->where(['amcode'=>$p_amcode])->select();
            $p_amcode = array_column($p_amcode,null, 'amcode');
            foreach($material as $k=>$v){
                if(!isset($p_amcode[$v['p_amcode']])){
                    $this->error('辅材信息有误');
                }
                $material[$k]['name'] = $p_amcode[$v['p_amcode']]['name'];
            }

            $this->success('success','',$material);
        }else{
            $this->error('辅材信息有误');
        }
    }

    //根据公共基础项目库 获取需要的细类
    public function get_fine(){
        $where = [];
        if(input('item_number')){
            $where['item_number'] = input('item_number');
        }
        if(input('bp_id')){
            // $where['id'] = input('bp_id');
        }
        if(!$where){
            $this->error('参数错误'); 
        }
        $fine = Db::name('basis_project')->where($where)->value('fine');
        if(!$fine || $fine == '{}' || $fine == '[]'){
            $this->success('none');
        }


        $fine = json_decode($fine,true);
        if(!$fine || !is_array($fine)){
            $this->error('该项目辅材配置有误，请联系管理员处理');
        }
        $fine = array_column($fine, 'fine');
        $basis_materials = Db::name('f_materials')->where(['fine'=>$fine,'fid'=>$this->_userinfo['companyid']])->select();
        $datas = [];
        foreach($basis_materials as $k=>$v){
            $basis_materials = Db::name('basis_materials')->where(['amcode'=>$v['p_amcode']])->find();
            if(!$basis_materials){
                $this->error('基础库'.$v['p_amcode'].'不存在');
            }
            $v['name'] = $basis_materials['name'];
            $v['unit'] = $basis_materials['unit'];
            $datas[$v['fine']][] = $v;
        }
        if(count($fine) != count($datas)){
            $this->error('该项目辅材不全，请及时补充');
        }
        $this->success('success','',$datas);
    }

    //导入基础辅材数据
    public function excel_public_warehouse(){
        require'../extend/PHPExcel/PHPExcel.php';
        $file = request()->file('file');
        if($file){
            $info = $file->validate(['size'=>10485760,'ext'=>'xls,xlsx'])->move(ROOT_PATH . 'public/'. 'excel');
            if (!$info) {
                $this->error('上传文件格式不正确');
            }else{
                //获取上传到后台的文件名
                $fileName = $info->getSaveName();
                //获取文件路径
                $filePath = ROOT_PATH . 'public/'. 'excel/'.$fileName;
                //获取文件后缀
                $suffix = $info->getExtension();

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
            for ($i = 2; $i <= $row_num; $i ++) {
                $data[$i]['amcode']  = trim($sheet->getCell("A".$i)->getValue());
                $data[$i]['type_of_work']  = trim($sheet->getCell("B".$i)->getValue());
                $data[$i]['classify']  = trim($sheet->getCell("C".$i)->getValue());
                $data[$i]['fine']  = trim($sheet->getCell("D".$i)->getValue());
                $data[$i]['brank']  = trim($sheet->getCell("E".$i)->getValue()); 
                $data[$i]['place']  = trim($sheet->getCell("F".$i)->getValue()); 
                $data[$i]['name']  = trim($sheet->getCell("G".$i)->getValue()); 
                $data[$i]['unit']  = trim($sheet->getCell("H".$i)->getValue()); 
                $data[$i]['coefficient']  = trim($sheet->getCell("I".$i)->getValue()); 
                $data[$i]['important']  = trim($sheet->getCell("J".$i)->getValue()); 
                if(empty($data[$i]['amcode']) || empty($data[$i]['type_of_work']) || empty($data[$i]['classify']) || empty($data[$i]['fine']) || empty($data[$i]['name']) || empty($data[$i]['unit'])){
                    $this->error('第'.$i.'行数据不能为空');
                }
                if((empty($data[$i]['coefficient']) && $data[$i]['coefficient'] != '0') || (empty($data[$i]['important']) && $data[$i]['important'] != '0')){
                    $this->error('第'.$i.'行数据不能为空');
                }
            }
            //判断是否有重复编码
            $amcodes = array_column($data, 'amcode');
            if(count($amcodes) != count(array_unique($amcodes))){
                $this->error('编码重复');
            }

            $basis_materials = Db::name('basis_materials')->select();
            $data_list = array_column($data, null ,'amcode');
            $del_amcode = [];//删除的
            // $edit_amcode = [];//修改单位的
            //判断项目是否发生改变
            foreach($basis_materials as $k=>$v){
                if(isset($data_list[$v['amcode']])){
                    if($v['unit'] != $data_list[$v['amcode']]['unit']){
                        $this->error('编号'.$v['amcode'].'的单位与之前不一致');
                    }
                }else{
                    $del_amcode[] = $v['amcode'];//已删除的项目
                }
            }
            
            

            //将数据保存到数据库
            if ($data) {
            //把获取到的二维数组遍历进数据库
                Db::startTrans();
                try {
                    $this->save_basis_materials_img(1);
                    Db::name('basis_materials')->delete(true);

                    foreach ($data as $key => $value) {
                        //判断是否存在
                        $is_has = Db::name('basis_materials')->where(['fine'=>$value['fine']])->find();
                        if($is_has && $is_has['unit'] != $value['unit']){
                            // throw new \think\Exception('编号 - '.$value['amcode'].' 的分类与其他分类单位不一致', 10006);
                            throw new \think\Exception('编号 - '.$value['amcode'].' 的分类与其他分类单位不一致，应为'.$is_has['unit'], 10006);
                        }
                        Db::name('basis_materials')->insert($value);
                       
                    }
                    //修改了单位/删除了辅材  (就是对报价有影响的)
                    foreach($del_amcode as $k=>$v){
                        Db::name('f_project')->where('material','like','%'.$v.'%')->update(['status'=>2]);
                    }
                    $this->update_basis_materials_img(1);
                    Db::commit();
                }catch (\Exception $e) {
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

    //导入基础报价数据
    public function excel_public_project(){
        require'../extend/PHPExcel/PHPExcel.php';
        $file = request()->file('file');
        if($file){
            $info = $file->validate(['size'=>10485760,'ext'=>'xls,xlsx'])->move(ROOT_PATH . 'public/'. 'excel');
            if (!$info) {
                $this->error('上传文件格式不正确');
            }else{
                //获取上传到后台的文件名
                $fileName = $info->getSaveName();
                //获取文件路径
                $filePath = ROOT_PATH . 'public/'. 'excel/'.$fileName;
                //获取文件后缀
                $suffix = $info->getExtension();

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
            //获取所有工种
            $basis_type_work = array_column(Db::name('basis_type_work')->select(),null, 'name');
            $data = []; //数组形式获取表格数据 
            //获取所有辅材分类
            $basis_materials_fine = array_column(Db::name('basis_materials')->group('fine')->field('fine')->select(), 'fine');
            $arrletter = array('F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS');//辅材基数字母
            for ($i = 2; $i <= $row_num; $i ++) {
                $data[$i]['item_number']  = trim($sheet->getCell("A".$i)->getValue());

                $type_word = trim($sheet->getCell("B".$i)->getValue());
                if(isset($basis_type_work[$type_word])){
                    $data[$i]['type_word_id']  = $basis_type_work[$type_word]['id'];
                }else{
                    $this->error('第'.$i.'行，工种'.$type_word.' 不存在');
                }

                $data[$i]['name']  = trim($sheet->getCell("C".$i)->getValue());
                $data[$i]['unit']  = trim($sheet->getCell("D".$i)->getValue());
                $data[$i]['content']  = trim($sheet->getCell("E".$i)->getValue()); 

                //辅材基数对应转json数组开始
                $fine = '';
                $j = 0;
                foreach ($arrletter as $key => $value) {
                  if($j%2==0){
                    if($j === 0){
                        if($sheet->getCell($arrletter[$key].$i)->getValue()){
                            if(!$sheet->getCell($arrletter[$key+1].$i)->getValue()){
                                $this->error('第'.$i.'行,项目编号'.$data[$i]['item_number'].'所需的辅材分类数量不能为空', 10006);
                            }
                            $fine .= trim($sheet->getCell($arrletter[$key].$i)->getValue()).'-'.trim($sheet->getCell($arrletter[$key+1].$i)->getValue());
                        }
                    }else{
                        if($sheet->getCell($arrletter[$key].$i)->getValue()){
                            if(!$sheet->getCell($arrletter[$key+1].$i)->getValue()){
                                $this->error('第'.$i.'行,项目编号'.$data[$i]['item_number'].'所需的辅材分类数量不能为空', 10006);
                            }
                            $fine .= ','.trim($sheet->getCell($arrletter[$key].$i)->getValue()).'-'.trim($sheet->getCell($arrletter[$key+1].$i)->getValue());
                        }
                    }
                  }
                  $j++;
                }

                // $fine = trim($sheet->getCell("F".$i)->getValue()); 
                try {
                    $data_fine = [];
                    if($fine){
                        $fine = str_replace("，",",",$fine);
                        $fine = explode(',', $fine);
                        foreach($fine as $k=>$v){
                            $info = explode('-', $v);
                            if(!in_array($info[0], $basis_materials_fine)){
                                // $this->error('第'.$i.'行,项目编号'.$data[$i]['item_number'].'所需的辅材分类:'.$info[0].'不存在');
                                throw new \think\Exception('第'.$i.'行,项目编号'.$data[$i]['item_number'].'所需的辅材分类:'.$info[0].'不存在', 10006);
                            }
                            $data_fine[$k]['fine'] = $info[0];
                            $data_fine[$k]['funit'] = $info[1];
                        }
                    }
                }catch (\Exception $e) {
                    $this->error('第'.$i.'行，项目用料有误.  '.$e->getMessage());
                }
                $data[$i]['fine'] = json_encode($data_fine);

                
                if(empty($data[$i]['item_number']) || empty($data[$i]['type_word_id']) || empty($data[$i]['name']) || empty($data[$i]['unit']) || empty($data[$i]['content']) || empty($data[$i]['fine'])){
                    $this->error('第'.$i.'行数据不能为空');
                }
            }
            //判断是否有重复编码
            $data_list = array_column($data, null ,'item_number');
            $item_numbers = array_keys($data_list);
            if(count($item_numbers) != count(array_unique($item_numbers))){
                $this->error('编码重复');
            }
            $del_item_number = [];//删除的
            $edit_item_number = [];//修改用料的
            $basis_project = Db::name('basis_project')->select();

            //判断项目是否发生改变
            foreach($basis_project as $k=>$v){
                if(isset($data_list[$v['item_number']])){
                    if($v['fine'] != $data_list[$v['item_number']]['fine']){
                        $edit_item_number[] = $v['item_number'];//修改用料的项目
                    }
                }else{
                    $del_item_number[] = $v['item_number'];//已删除的项目
                }
            }
            //将数据保存到数据库
            if ($data) {
            //把获取到的二维数组遍历进数据库
                Db::startTrans();
                try {
                    Db::name('basis_project')->delete(true);
                    Db::name('basis_project')->insertAll($data);
                    Db::name('f_project')->where(['p_item_number'=>$edit_item_number])->update(['status'=>2]);
                    Db::name('f_project')->where(['p_item_number'=>$del_item_number])->update(['status'=>3]);
                    Db::commit();
                }catch (\Exception $e) {
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


    //没有的辅材分公司申请提交
    public function apply_new_material_index(){
        // $this->assign('data',$res);
        $where = [];
        if($this->_userinfo['userid'] == 1){
            if(input('fid')){
                $where['fid'] = input('fid');
            }
        }else{
            $where['fid'] = $this->_userinfo['companyid'];
        }
        if(input('status')){
            $where['status'] = input('status');
        }
        $datas = Db::name('apply_material')->where($where)->order('status','asc')->order('id','desc')->paginate(20,false,['query'=>request()->param()]);

        //判断是否已添加
        $amcode = array_column($datas->items(), 'p_amcode');
        $condintion = [];
        $condintion['p_amcode'] = $amcode;
        if(isset($where['fid'])){
            $condintion['fid'] = $where['fid'];
        }
        $p_amcode = array_column(Db::name('f_materials')->where($condintion)->field('p_amcode')->select(),'p_amcode');


        $frame = array_column(Db::name('frame')->where('levelid',3)->field('id,name')->select(),null,'id');
        $this->assign('admininfo',$this->_userinfo);
        $this->assign('frame',$frame);
        $this->assign('p_amcode',$p_amcode);
        $this->assign('datas',$datas);
        return $this->fetch();
    }

    //ajax申请辅材
    public function apply_new_material(){
        $name = input('name');
        $brank = input('brank');
        $place = input('place');
        $unit = input('unit');
        if(empty($name) || empty($brank) || empty($place) || empty($unit)){
            $this->error('参数有误');
        }
        foreach($name as $k=>$v){
            $name[$k] = trim($v);
            if(empty($name[$k])){
                $this->error('辅材名称不能为空');
            }
        }
        foreach($brank as $k=>$v){
            $brank[$k] = trim($v);
            if(empty($brank[$k])){
                $this->error('品牌不能为空');
            }
        }
        foreach($place as $k=>$v){
            $place[$k] = trim($v);
            if(empty($place[$k])){
                $this->error('产地不能为空');
            }
        }
        foreach($place as $k=>$v){
            $place[$k] = trim($v);
            if(empty($place[$k])){
                $this->error('单位不能为空');
            }
        }
        if(count($name) != count(array_unique($name))){
            // $this->error('名字重复');
        }
        // if(count($brank) != count(array_unique($brank))){
        //     $this->error('品牌重复');
        // }
        // if(count($place) != count(array_unique($place))){
        //     $this->error('产地重复');
        // }
        $insert_datas = [];
        foreach($name as $k=>$v){
            $insert_datas[$k]['fid'] = $this->_userinfo['companyid'];
            $insert_datas[$k]['name'] = $v;
            $insert_datas[$k]['brank'] = $brank[$k];
            $insert_datas[$k]['place'] = $place[$k];
            $insert_datas[$k]['unit'] = $unit[$k];
        }
        $res = Db::name('apply_material')->insertAll($insert_datas);
        if($res){
            $this->success('申请成功，请等待回复');
        }else{
            $this->error('申请失败');
        }
    }

    //没有的 项目 分公司申请提交
    public function apply_new_project_index(){
        // $this->assign('data',$res);
        $where = [];
        if($this->_userinfo['userid'] == 1){
            if(input('fid')){
                $where['fid'] = input('fid');
            }
        }else{
            $where['fid'] = $this->_userinfo['companyid'];
        }
        if(input('status')){
            $where['status'] = input('status');
        }
        $datas = Db::name('apply_project')->where($where)->order('status','asc')->order('id','desc')->paginate(20,false,['query'=>request()->param()]);

        //判断是否已添加
        $item_number = array_column($datas->items(), 'p_item_number');
        $condintion = [];
        $condintion['p_item_number'] = $item_number;
        if(isset($where['fid'])){
            $condintion['fid'] = $where['fid'];
        }
        $p_item_number = array_column(Db::name('f_project')->where($condintion)->field('p_item_number')->select(),'p_item_number');

        $frame = array_column(Db::name('frame')->where('levelid',3)->field('id,name')->select(),null,'id');
        $type_work = array_column(Db::name('basis_type_work')->field('id,name')->select(),null,'id');//获取所有辅材细类
        $fines = Db::name('basis_materials')->field('fine,unit')->group('fine')->select();
        $this->assign('admininfo',$this->_userinfo);
        $this->assign('frame',$frame);
        $this->assign('type_work',$type_work);
        $this->assign('fines',$fines);
        $this->assign('p_item_number',$p_item_number);
        $this->assign('datas',$datas);
        return $this->fetch();
    }

    //ajax申请项目
    public function apply_new_project(){
        $name = input('name');
        $content = input('content');
        $unit = input('unit');
        $material = input('material');
        if(empty($name) || empty($content) || empty($unit)){
            $this->error('参数有误');
        }
        foreach($name as $k=>$v){
            $name[$k] = trim($v);
            if(empty($name[$k])){
                $this->error('项目名称不能为空');
            }
        }
        foreach($content as $k=>$v){
            $content[$k] = trim($v);
            if(empty($content[$k])){
                $this->error('施工工艺与材料说明不能为空');
            }
        }
        foreach($unit as $k=>$v){
            $unit[$k] = trim($v);
            if(empty($unit[$k])){
                $this->error('单位不能为空');
            }
        }
        if(count($name) != count(array_unique($name))){
            // $this->error('项目名称重复');
        }
        $insert_datas = [];
        foreach($name as $k=>$v){
            $insert_datas[$k]['fid'] = $this->_userinfo['companyid'];
            $insert_datas[$k]['name'] = $v;
            $insert_datas[$k]['content'] = $content[$k];
            $insert_datas[$k]['unit'] = $unit[$k];
            $insert_datas[$k]['material'] = $material[$k];
        }
        $res = Db::name('apply_project')->insertAll($insert_datas);
        if($res){
            $this->success('申请成功，请等待回复');
        }else{
            $this->error('申请失败');
        }
    }

    //绑定辅材
    public function bind_material(){
        $id = input('id');
        $amcode = input('amcode');
        $basis_materials = Db::name('basis_materials')->where(['amcode'=>$amcode])->find();
        if(!$basis_materials){
            $this->error('未找到绑定的辅材');
        }
        $res = Db::name('apply_material')->where(['id'=>$id])->update(['audittime'=>time(),'p_amcode'=>$amcode,'status'=>2]);
        if($res){
            $this->success('绑定成功');
        }else{
            $this->error('绑定失败');
        }
    }

    //绑定项目
    public function bind_project(){
        $id = input('id');
        $item_number = input('item_number');
        $basis_project = Db::name('basis_project')->where(['item_number'=>$item_number])->find();
        if(!$basis_project){
            $this->error('未找到绑定的报价项目');
        }
        $res = Db::name('apply_project')->where(['id'=>$id])->update(['audittime'=>time(),'p_item_number'=>$item_number,'status'=>2]);
        if($res){
            $this->success('绑定成功');
        }else{
            $this->error('绑定失败');
        }
    }

    public function img(Request $request){
        $data =$request->post();
        if($_FILES['image']['error'] !=4) {
            $file = request()->file('image');
            if($file){
                $info = $file->validate(['size'=>1048576])->move( './uploads/images');
                if($info){
                    // 成功上传后 获取上传信息
                    $images = $info->getSaveName();
                    $images = str_replace('\\', '/', $images);
                    $res=  Db::name('f_materials')->where('id', $data['id'])->data(['img' => $images])->update();
                    session('msg','上传成功');
                    session('msg1',1);
                    $this->redirect($_SERVER['HTTP_REFERER']);
                }else{
                    // 上传失败获取错误信息
                    $this->error($file->getError());
                }
            }
        }else{
            $this->error('图片未上传');
        }

    }
//辅材上传图片

    public function uploading(Request $request){
        $data =$request->post();
//        dump($data);die;
        if($_FILES['image']['error'] !=4) {
            $file = request()->file('image');
            if($file){
                $info = $file->validate(['size'=>1048576])->move( './uploads/images');
                if($info){
                    // 成功上传后 获取上传信息
                    $images = $info->getSaveName();
                    $images = str_replace('\\', '/', $images);
                    $res=  Db::name('basis_materials')->where('id', $data['id'])->data(['img' => $images])->update();
                    session('msg','上传成功');
                    session('msg1',1);
                    $this->redirect($_SERVER['HTTP_REFERER']);
                }else{
                    // 上传失败获取错误信息
                    $this->error($file->getError());
                }
            }
        }else{
            $this->error('图片未上传');
        }

    }

    public function export(){
        //1.从数据库中取出数据
        $data = Db::table('fdz_basis_project')->select();
        $type_word_ids = array_unique(array_column($data,'type_word_id'));
        $basis_type_work = Db::table('fdz_basis_type_work')->where(['id'=>$type_word_ids])->select();
        $basis_type_work = array_column($basis_type_work,null,'id');
        foreach ($data as $k=>$v){
            $data[$k]['word'] = $basis_type_work[$v['type_word_id']]['name'];
        }
//        dump($data);die;
        //2.加载PHPExcle类库
        require '../extend/PHPExcel/PHPExcel.php';
        //3.实例化PHPExcel类
        $objPHPExcel = new \PHPExcel();
        //4.激活当前的sheet表
        $objPHPExcel->setActiveSheetIndex(0);
        //5.设置表格头（即excel表格的第一行）
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '编码')
            ->setCellValue('B1', '工种类别')
            ->setCellValue('C1', '项目名称')
            ->setCellValue('D1', '单位')
            ->setCellValue('E1', ' 施工工艺与说明')
            ->setCellValue('F1', ' 用料1')
            ->setCellValue('G1', ' 数量1')
            ->setCellValue('H1', ' 用料2')
            ->setCellValue('I1', ' 数量2')
            ->setCellValue('J1', ' 用料3')
            ->setCellValue('K1', ' 数量3')
            ->setCellValue('L1', ' 用料4')
            ->setCellValue('M1', ' 数量4')
            ->setCellValue('N1', ' 用料5')
            ->setCellValue('O1', ' 数量5')
            ->setCellValue('P1', ' 用料6')
            ->setCellValue('Q1', ' 数量6')
            ->setCellValue('R1', ' 用料7')
            ->setCellValue('S1', ' 数量7')
            ->setCellValue('T1', ' 用料8')
            ->setCellValue('U1', ' 数量8')
            ->setCellValue('V1', ' 用料9')
            ->setCellValue('W1', ' 数量9')
            ->setCellValue('X1', ' 用料10')
            ->setCellValue('Y1', ' 数量10')
            ->setCellValue('Z1', ' 用料11')
            ->setCellValue('AA1', ' 数量11')
            ->setCellValue('AB1', ' 用料12')
            ->setCellValue('AC1', ' 数量12')
            ->setCellValue('AD1', ' 用料13')
            ->setCellValue('AE1', ' 数量13')
            ->setCellValue('AF1', ' 用料14')
            ->setCellValue('AG1', ' 数量14')
            ->setCellValue('AH1', ' 数量15')
            ->setCellValue('AI1', ' 数量15');
        //设置F列水平居中
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('F')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //设置单元格宽度
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(30);
        $arrletter = array('F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS');//辅材基数字母
        //6.循环刚取出来的数组，将数据逐一添加到excel表格。

        $con = Db::view('basis_project', 'type_word_id')
            ->view('basis_type_work', 'name', 'basis_project.type_word_id=basis_type_work.id')
            ->select();
//        dump($con);die;
        foreach ($data as $k => $v) {
            $data[$k]['js'] = json_decode($v['fine'],true);
        }
//        dump($data);die;
//        dump($data);die;
        for($i=0;$i<count($data);$i++){
            $objPHPExcel->getActiveSheet()->setCellValue('A'.($i+2),$data[$i]['item_number']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.($i+2),$data[$i]['word']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.($i+2),$data[$i]['name']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.($i+2),$data[$i]['unit']);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.($i+2),$data[$i]['content']);
            if(is_array($data[$i]['js'])){
                $j = 0;
                foreach($data[$i]['js'] as $k=>$v){
                    $objPHPExcel->getActiveSheet()->setCellValue($arrletter[$j].($i+2),$v['fine']);
                    $j++;
                    $objPHPExcel->getActiveSheet()->setCellValue($arrletter[$j].($i+2),$v['funit']);
                    $j++;
                }
            }


        }
//        var_dump($data);die;

        //7.设置保存的Excel表格名称
        $filename = '报价信息'.date('ymd',time()).'.xls';
        //8.设置当前激活的sheet表格名称；
        $objPHPExcel->getActiveSheet()->setTitle('学生信息');
        //9.设置浏览器窗口下载表格
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$filename.'"');
        //生成excel文件
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //下载文件在浏览器窗口
        $objWriter->save('php://output');
        exit;
    }


    public function excel()
    {

        $data = Db::table('fdz_basis_materials')->select();
        //2.加载PHPExcle类库
        require '../extend/PHPExcel/PHPExcel.php';
        //3.实例化PHPExcel类
        $objPHPExcel = new \PHPExcel();
        //4.激活当前的sheet表
        $objPHPExcel->setActiveSheetIndex(0);
        //5.设置表格头（即excel表格的第一行）
        // Add title
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '辅材编码')
            ->setCellValue('B1', '工种类别')
            ->setCellValue('C1', '辅材细类')
            ->setCellValue('D1', '分类')
            ->setCellValue('E1', ' 品牌')
            ->setCellValue('F1', '产地')
            ->setCellValue('G1', '辅材名称')
            ->setCellValue('H1', '最小单位')
            ->setCellValue('I1', '系数')
            ->setCellValue('J1', '是否重要(0:不是,1:是)');
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('基础辅材报表');

        $i = 2;
        foreach ($data as $rs) {
            // Add data
            $objPHPExcel->getActiveSheet()
                ->setCellValue('A' . $i, $rs['amcode'])
                ->setCellValue('B' . $i, $rs['type_of_work'])
                ->setCellValue('C' . $i, $rs['classify'])
                ->setCellValue('D' . $i, $rs['fine'])
                ->setCellValue('E' . $i, $rs['brank'])
                ->setCellValue('F' . $i, $rs['place'])
                ->setCellValue('G' . $i, $rs['name'])
                ->setCellValue('H' . $i, $rs['unit'])
                ->setCellValue('I' . $i, $rs['coefficient'])
                ->setCellValue('J' . $i, $rs['important']);
            $i++;
        }

        //7.设置保存的Excel表格名称
        $filename = '辅材信息'.date('ymd',time()).'.xls';
        //8.设置当前激活的sheet表格名称；
        $objPHPExcel->getActiveSheet()->setTitle('学生信息');
        //9.设置浏览器窗口下载表格
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$filename.'"');
        //生成excel文件
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //下载文件在浏览器窗口
        $objWriter->save('php://output');
        exit;

    }

  


    //更新导入图片
    public function update_img(){
        $file = "./uploads/update_img";//存储图片的文件
        $target_file = "./uploads/images/".date('Ymd');//储存目标文件夹
        $temp = scandir($file);
        if (file_exists($target_file) == false){
            if (!mkdir($target_file, 0755, true)) {
                $this->error('创建文件夹'.$target_file.'失败');
            }
        }
        
        foreach($temp as $k=>$v){
            if($k <= 1){
                continue;
            }
            if(@copy($file.'/'.$v,$target_file.'/'.$v)){
                $amcode = explode('.', $v)[0];
                Db::name('basis_materials')->where(['amcode'=>$amcode])->update(['img'=>date('Ymd').'/'.$v]);
            }
        }
        $this->success('success');
    }

    //另建一个表 储存basis_materials的图片
    //把basis_materials的图片存到basis_materials_other
    public function save_basis_materials_img($type="1"){
        $basis_materials = Db::name('basis_materials')->order('id','asc')->select();
        foreach($basis_materials as $k=>$v){
            if(!$v['img']){
                continue;
            }
            $basis_materials_other_info = Db::name('basis_materials_other')->where(['amcode'=>$v['amcode']])->find();
            if($basis_materials_other_info){
                if($v['img'] != $basis_materials_other_info['img']){
                    Db::name('basis_materials_other')->where(['amcode'=>$v['amcode']])->update(['img'=>$v['img']]);
                }
            }else{
                Db::name('basis_materials_other')->insert(['amcode'=>$v['amcode'],'img'=>$v['img']]);
            }
        }
        if($type){
            return 1;
        }
        $this->success('成功');
    }
    //吧basis_materials_other的图片存到basis_materials
    public function update_basis_materials_img($type="1"){
        $basis_materials_other = Db::name('basis_materials_other')->select();
        foreach($basis_materials_other as $k=>$v){
            if(!$v['img']){
                continue;
            }
            $basis_materials = Db::name('basis_materials')->where(['amcode'=>$v['amcode']])->find();
            if($basis_materials){
                if($v['img'] != $basis_materials['img']){
                    Db::name('basis_materials')->where(['amcode'=>$v['amcode']])->update(['img'=>$v['img']]);
                }
            }
        }
        if($type){
            return 1;
        }
        $this->success('成功');
    }


    public function excel_f_warehouse(){
        require'../extend/PHPExcel/PHPExcel.php';
        $f_project = Db::name('f_project')->where(['fid'=>$this->_userinfo['companyid']])->find();
        if($f_project){
            $this->error('新增定额数据后，此功能将会无法使用');
        }
        $file = request()->file('file');
        if($file){
            $info = $file->validate(['ext'=>'xls,xlsx'])->move(ROOT_PATH . 'public/'. 'excel');
            if (!$info) {
                $this->error('上传文件格式不正确');
            }else{
                //获取上传到后台的文件名
                $fileName = $info->getSaveName();
                //获取文件路径
                $filePath = ROOT_PATH . 'public/'. 'excel/'.$fileName;
                //获取文件后缀
                $suffix = $info->getExtension();

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
            for ($i = 2; $i <= $row_num; $i ++) {
                $info = [];
                $info['p_amcode']  = trim($sheet->getCell("A".$i)->getValue());
                
                
                $info['fid']  = $this->_userinfo['companyid'];
                $info['brank']  = trim($sheet->getCell("C".$i)->getValue());
                $info['place']  = trim($sheet->getCell("D".$i)->getValue()); 
                $info['in_price']  = trim($sheet->getCell("E".$i)->getValue()); 
                $info['price']  = trim($sheet->getCell("F".$i)->getValue()); 
                $info['pack']  = trim($sheet->getCell("G".$i)->getValue()); 
                $info['phr']  = trim($sheet->getCell("I".$i)->getValue()); 
                $info['source']  = trim($sheet->getCell("J".$i)->getValue()); 
                $info['status']  = 1;



                if( empty($info['in_price']) && empty($info['price']) && empty($info['pack']) && empty($info['phr']) && empty($info['source'])){
                    //没有填资料 默认不上传
                    continue;
                }
                if( empty($info['pack']) || empty($info['phr']) || empty($info['source']) ){
                    //有添数据,但是没填完整
                    $this->error('第'. ($i) .'行，数据不能留空');
                }

                if(empty($info['brank']) || empty($info['place'])){
                    $this->error('第'. ($i) .'行，数据不能为空');
                }

                if(!is_numeric($info['in_price']) || !is_numeric($info['price']) || $info['in_price'] < 0 || $info['price'] < 0 ){
                    $this->error('第'. ($i) .'行，价格格式错误');
                }

                if($info['source'] != '公司仓库' && $info['source'] != '公司定点' && $info['source'] != '监理自购'){
                    $this->error('第'. ($i) .'行，材料来源必须为 公司仓库 或 公司定点 或 监理自购');
                }

                $basis = Db::name('basis_materials')->where(['amcode'=>$info['p_amcode']])->find();
                if(!$basis){
                    $this->error('第'. ($i) .'行，编码'.$data[$i]['p_amcode'].'不存在');
                }
                $info['fine']  = $basis['fine'];
                $info['img']  = $basis['img'];
                $data[] = $info;
            }

            //将数据保存到数据库
            if ($data) {
            //把获取到的二维数组遍历进数据库
                Db::startTrans();
                try{
                    Db::name('f_materials')->where(['fid'=>$this->_userinfo['companyid']])->delete();
                    foreach($data as $k=>$v){
                        $id = Db::name('f_materials')->insertGetId($v);
                        Db::name('f_materials')->where(['id'=>$id])->update(['amcode'=>$v['p_amcode'].'_'.$id]);
                    }
                    Db::commit();
                }catch (\Exception $e) {
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

    //新增 删除 修改 分公司辅材 及时更新到线上    以后加到model里面
    public function update_fwarehouse($amcode,$is_del=0){
        // return true;
        $f_materials = Db::name('f_materials')->where(['amcode'=>$amcode])->find();
        $f_project = Db::name('f_project')->where('material','like','%"'.$amcode.'"%')->select();
        if(!$f_materials){
            throw new \think\Exception('分公司辅材库有误', 10006);
        }
        if($is_del == 1){
            if($f_project){
                $item_numbers = implode(',', array_column($f_project, 'item_number'));
                throw new \think\Exception('项目编号：'.$item_numbers.'已使用该辅材，请修改对应项目后再删除', 10006);
            }
            Db::name('materials')->where(['amcode'=>$amcode])->delete();
            return true;
        }
        $basis_materials = Db::name('basis_materials')->where(['amcode'=>$f_materials['p_amcode']])->find();
        if(!$basis_materials){
            throw new \think\Exception('辅材基础库有误', 10006);
        }
        $info = [];
        $info['frameid'] = $f_materials['fid'];
        $info['userid'] = $this->_userinfo['userid'];
        $info['amcode'] = $f_materials['amcode'];
        $info['fine'] = $basis_materials['classify'];
        $info['brand'] = $f_materials['brank'];
        $info['place'] = $f_materials['place'];
        $info['category'] = $basis_materials['type_of_work'];
        $info['name'] = $basis_materials['name'];
        $info['img'] = $f_materials['img'];
        $info['units'] = $f_materials['phr'];
        $info['phr'] = $f_materials['pack'].$basis_materials['unit'].'/'.$f_materials['phr'];
        $info['price'] = $f_materials['price'];
        $info['in_price'] = $f_materials['in_price'];
        $info['remarks'] = $f_materials['source'];
        $info['coefficient'] = $basis_materials['coefficient'];
        $info['important'] = $basis_materials['important'];
        $materials = Db::name('materials')->where(['amcode'=>$info['amcode']])->find();
        if($f_project){
            foreach($f_project as $k=>$v){
                $this->update_fproject($v['item_number']);
            }
        }
        if($materials){
            //已有了 就修改
            Db::name('materials')->where(['amcode'=>$info['amcode']])->update($info);
        }else{
            Db::name('materials')->insert($info);
        }
    }

    //新增 删除 修改 分公司报价 及时更新到线上    以后加到model里面
    public function update_fproject($item_number,$is_del=0){
        $f_project = Db::name('f_project')->where(['item_number'=>$item_number])->find();
        if(!$f_project){
            throw new \think\Exception('分公司项目库库有误', 10006);
        }
        if($is_del == 1){
            Db::name('offerquota')->where(['item_number'=>$item_number])->delete();
            return true;
        }
        $basis_project = Db::name('basis_project')->where(['item_number'=>$f_project['p_item_number']])->find();
        if(!$basis_project){
            throw new \think\Exception('辅材基础库有误', 10006);
        }
        $basis_type_work = array_column(Db::name('basis_type_work')->select(), 'name','id') ;
        $info = [];
        //计算辅材基数
        if($f_project['material']){
            $fine = json_decode($basis_project['fine'],true);
            $fine = array_column($fine, 'funit','fine');//公式

            $material = json_decode($f_project['material'],true);
            $datas_material = [];
            foreach($material as $k1=>$v1){
                // $fine[$k1] 需要的数量
                $pack = Db::name('f_materials')->where(['amcode'=>$v1])->value('pack');//包装数量
                if(!$pack){
                    // $this->error('项目编号'.$f_project['item_number'].'中,辅材编号'.$v1.'不存在');
                    throw new \think\Exception('项目编号'.$f_project['item_number'].'中,辅材编号'.$v1.'不存在', 10006);
                }
                //下面这个格式是按照之前的格式的 [对应辅材id,基数]
                // v1 的id不对
                $num = round($fine[$k1]/$pack,2);
                if($num <= 0){
                    $num = 0.001;
                }
                $datas_material[] = [$v1,round($num,2)];
            }
            $info['content'] = json_encode($datas_material);
        }else{
            $info['content'] = '';
        }
        $info['frameid'] = $f_project['fid'];
        $info['userid'] = $this->_userinfo['companyid'];
        $info['item_number'] = $f_project['item_number'];
        $info['type_of_work'] = $basis_type_work[$basis_project['type_word_id']];

        if($f_project['remark']){
            $info['project'] = $basis_project['name'].'（'.$f_project['remark'].'）';
        }else{
            $info['project'] = $basis_project['name'];
        }
        
        $info['company'] = $basis_project['unit'];
        $info['cost_value'] = $f_project['cost_value'];
        $info['quota'] = $f_project['quota'];
        $info['craft_show'] = $f_project['craft_show'];
        $info['labor_cost'] = $f_project['labor_cost'];
        $info['material'] = $basis_project['content'];
        $offerquota = Db::name('offerquota')->where(['item_number'=>$info['item_number']])->find();
        if($offerquota){
            //已有了 就修改
            Db::name('offerquota')->where(['item_number'=>$info['item_number']])->update($info);
        }else{
            Db::name('offerquota')->insert($info);
        }
    }

    public function auxiliaryderive(Request $request)
    {

        $da=$request->get();
        if(empty($da['id'])){
            $data = Db::name('f_materials')->select();
            $frame='全部数据';
        }else{
            $frame=Db::table('fdz_frame')->where('id',$da['id'])->value('name');
            $data = Db::name('f_materials')->where('fid',$da['id'])->select();
        }
        foreach ($data as $k=>$v)
        {
            $data[$k]['type_of_work']=Db::table('fdz_basis_materials')->where('amcode',$v['p_amcode'])->value('type_of_work');
            $data[$k]['classify']=Db::table('fdz_basis_materials')->where('amcode',$v['p_amcode'])->value('classify');
            $data[$k]['name']=Db::table('fdz_basis_materials')->where('amcode',$v['p_amcode'])->value('name');
            $data[$k]['unit']=Db::table('fdz_basis_materials')->where('amcode',$v['p_amcode'])->value('unit');
        }
        //2.加载PHPExcle类库
        require '../extend/PHPExcel/PHPExcel.php';
        //3.实例化PHPExcel类
        $objPHPExcel = new \PHPExcel();
        //4.激活当前的sheet表
        $objPHPExcel->setActiveSheetIndex(0);
        //5.设置表格头（即excel表格的第一行）
        // Add title
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '编码')
            ->setCellValue('B1', '工种类别')
            ->setCellValue('C1', '辅材细类 ')
            ->setCellValue('D1', '分类')
            ->setCellValue('E1', '辅材名称')
            ->setCellValue('F1', '品牌')
            ->setCellValue('G1', '产地')
            ->setCellValue('H1', '入库价')
            ->setCellValue('I1', '出库价')
            ->setCellValue('J1', '包装数量')
            ->setCellValue('K1', '计量单位')
            ->setCellValue('L1', '出库单位')
            ->setCellValue('M1', '来源');
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('基础辅材报表');
                $i = 2;
        foreach ($data as $rs) {
            // Add data
            $objPHPExcel->getActiveSheet()
                ->setCellValue('A' . $i, $rs['amcode'])
                ->setCellValue('B' . $i, $rs['type_of_work'])
                ->setCellValue('C' . $i, $rs['classify'])
                ->setCellValue('D' . $i, $rs['fine'])
                ->setCellValue('E' . $i, $rs['name'])
                ->setCellValue('F' . $i, $rs['brank'])
                ->setCellValue('G' . $i, $rs['place'])
                ->setCellValue('H' . $i, $rs['in_price'])
                ->setCellValue('I' . $i, $rs['price'])
                ->setCellValue('J' . $i, $rs['pack'])
                ->setCellValue('k' . $i, $rs['unit'])
                ->setCellValue('L' . $i, $rs['phr'])
                ->setCellValue('M' . $i, $rs['source']);
            $i++;
        }
        //7.设置保存的Excel表格名称
        $filename =$frame. '辅材信息'.date('ymd',time()).'.xls';
        //8.设置当前激活的sheet表格名称；
        $objPHPExcel->getActiveSheet()->setTitle('学生信息');
        //9.设置浏览器窗口下载表格
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$filename.'"');
        //生成excel文件
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //下载文件在浏览器窗口
        $objWriter->save('php://output');
        exit;
    }
}