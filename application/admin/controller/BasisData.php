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
        $res = Db::name('basis_materials')->where($where)->order('id','asc')->paginate(20,false,['query'=>request()->param()]);
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
        $unit = Db::name('basis_materials')->where(['fine'=>$data['fine']])->value('unit');
        if($unit && $unit != $data['unit']){
            $this->error('细类单位错误，应为：'.$unit);
        }

        $res = Db::name('basis_materials')->where(['amcode'=>$data['amcode']])->update($data);
        if($res){
            $this->success('编辑成功');
        }else{
            $this->error('编辑失败');
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
        $res = Db::name('basis_project')->where($where)->order('id','asc')->paginate(20,false,['query'=>request()->param()]);
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
        $res = Db::name('basis_materials')->where($where)->order('id','asc')->paginate(20,false,['query'=>request()->param()]);
        $p_amcode = array_column($res->items(), 'amcode');
        $p_amcode = array_column(Db::name('f_materials')->where(['p_amcode'=>$p_amcode])->field('p_amcode')->select(),'p_amcode');
        // var_dump($p_amcode);die;
        $this->assign('amcode',$p_amcode);
        $this->assign('data',$res);
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
            $m_fine = Db::name('f_materials')->where(['fine'=>$fine])->group('fine')->select();
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
        if(input('amcode')){
            $where[] = ['amcode','like','%'.input('amcode').'%'];
        }
        if(input('fid')){
            $where[] = ['fid','=',input('fid')];
        }else{
            $where[] = ['fid','=',$this->_userinfo['companyid']];
        }
          
        $data = Db::name('f_materials')->where($where)->order('id','asc')->paginate(20,false,['query'=>request()->param()]);
        $p_amcode = array_unique(array_column($data->items(), 'p_amcode'));
        $basis_materials = array_column(Db::name('basis_materials')->where(['amcode'=>$p_amcode])->select(),null, 'amcode');
        $frame = Db::name('frame')->where('levelid',3)->field('id,name')->select();
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
        $datas['fine'] = $info['fine'];
        // var_dump($datas);die;
        $res = Db::name('f_materials')->insertGetId($datas);
        if($res){
            Db::name('f_materials')->where(['id'=>$res])->update(['amcode'=>$info['amcode'].'_'.$res]);
            $this->success('添加成功','fwarehouse_list');
        }else{
            $this->error('添加失败');
        }
    }

    //分公司 修改辅材操作
    public function edit_fwarehouse_operation(){
        $datas = input();
        $res = Db::name('f_materials')->where(['amcode'=>$datas['amcode']])->update($datas);
        if($res){
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }
    }

    //分公司 删除辅材操作
    public function del_fwarehouse_operation(){
        $amcode = input('amcode');
        $info = Db::name('f_materials')->where(['amcode'=>$amcode])->find();
        Db::startTrans();
        try {
            Db::name('f_project')->where('material','like','%'.$amcode.'%')->where(['fid'=>$info['fid']])->update(['status'=>2]);
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
        // $where[] = ['status', 'IN', [1,2]];
        if(input('amcode')){
            $where[] = ['amcode','like','%'.input('amcode').'%'];
        } 
        if(input('fid')){
            $where[] = ['fid','=',input('fid')];
        }else{
            $where[] = ['fid','=',$this->_userinfo['companyid']];
        }
        $data = Db::name('f_project')->where($where)->order('status','desc')->order('id','asc')->paginate(20,false,['query'=>request()->param()]);
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
        
        $res = Db::name('f_project')->insertGetId($datas);
        if($res){
            Db::name('f_project')->where(['id'=>$res])->update(['item_number'=>$info['item_number'].'_'.$res]);
            $this->success('添加成功','fproject_list');
        }else{
            $this->error('添加失败');
        }
    }

    //分公司 编辑项目操作
    public function edit_fproject_operation(){
        $datas = input();
        // $datas['fid'] = $this->_userinfo['companyid'];
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
        $datas['status'] = 1;
        $res = Db::name('f_project')->where(['id'=>$datas['id']])->update($datas);
        if($res){
            
            $this->success('添加成功','fproject_list');
        }else{
            $this->error('添加失败');
        }
    }

    //删除分公司报价
    public function del_fproject(){
        $id = input('id');
        $res = Db::name('f_project')->where(['id'=>$id])->delete();
        if($res){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }

    }

    public function create_datas(){
        $admininfo = $this->_userinfo;
        $fid = input('fid')?input('fid'):$admininfo['companyid'];
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
            $info['type_of_work'] = $basis_project[$v['p_item_number']]['type_word_id'];

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
        if(!$fine || $fine == '{}'){
            $this->success('none');
        }


        $fine = json_decode($fine,true);
        if(!$fine || !is_array($fine)){
            $this->error('该项目辅材配置有误，请联系管理员处理');
        }
        $fine = array_column($fine, 'fine');
        $basis_materials = Db::name('f_materials')->where(['fine'=>$fine])->select();
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
                        // $edit_amcode[] = $v['amcode'];//修改了单位
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


        $frame = Db::name('frame')->where('levelid',3)->field('id,name')->select();
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
            $this->error('名字重复');
        }
        if(count($brank) != count(array_unique($brank))){
            $this->error('品牌重复');
        }
        if(count($place) != count(array_unique($place))){
            $this->error('产地重复');
        }
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

        $frame = Db::name('frame')->where('levelid',3)->field('id,name')->select();
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
            $this->error('项目名称重复');
        }
        $insert_datas = [];
        foreach($name as $k=>$v){
            $insert_datas[$k]['fid'] = $this->_userinfo['companyid'];
            $insert_datas[$k]['name'] = $v;
            $insert_datas[$k]['content'] = $content[$k];
            $insert_datas[$k]['unit'] = $unit[$k];
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

}