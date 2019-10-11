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
        if(input('type_of_work')){
            $where[] = ['type_of_work','like','%'.input('type_of_work').'%'];
        }
        if(input('fine')){
            $where[] = ['fine','like','%'.input('fine').'%'];
        }
        if(input('name')){
            $where[] = ['name','like','%'.input('name').'%'];
        }
        $res = Db::name('basis_materials')->where($where)->order('id','desc')->paginate(20,false,['query'=>request()->param()]);
        $this->assign('data',$res);
        return $this->fetch();
    }

    //添加辅材公共基础库
    public function add_public_warehouse(){
        $data['amcode'] = input('amcode');
        $data['type_of_work'] = input('type_of_work');
        $data['fine'] = input('fine');
        $data['name'] = input('name');
        $data['unit'] = input('unit');
        $data['coefficient'] = input('coefficient');
        $data['important'] = input('important');
        if(!$data['amcode'] || !$data['type_of_work'] || !$data['fine'] || !$data['name'] || !$data['unit']){
            $this->error('参数错误');
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
            $this->error('细类单位错误，应为：'.$unit);
        }

        $res = Db::name('basis_materials')->insert($data);
        if($res){
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
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
        $res = Db::name('basis_project')->where($where)->order('id','desc')->paginate(20,false,['query'=>request()->param()]);
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
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
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
        $res = Db::name('basis_materials')->where($where)->order('id','desc')->paginate(20,false,['query'=>request()->param()]);
        $bs_ids = array_column($res->items(), 'id');
        $bs_ids = array_column(Db::name('f_materials')->where(['bs_id'=>$bs_ids])->field('bs_id')->select(),'bs_id');
        $this->assign('bs_ids',$bs_ids);
        $this->assign('data',$res);
        return $this->fetch();
    }

     //分公司添加页面 
    public function pporject(){
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
        $res = Db::name('basis_project')->where($where)->order('id','desc')->paginate(20,false,['query'=>request()->param()]);
        //获取所有辅材细类
        $fines = Db::name('basis_materials')->field('fine,unit')->group('fine')->select();
        $type_work = array_column(Db::name('basis_type_work')->field('id,name')->select(),null,'id');

        $bp_ids = array_column($res->items(), 'id');
        $bp_ids = array_column(Db::name('f_project')->where(['bp_id'=>$bp_ids])->field('bp_id')->select(),'bp_id');
        $this->assign('bp_ids',$bp_ids);

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
          
        $data = Db::name('f_materials')->where($where)->order('id','desc')->paginate(20,false,['query'=>request()->param()]);
        $bs_ids = array_unique(array_column($data->items(), 'bs_id'));
        $basis_materials = array_column(Db::name('basis_materials')->where(['id'=>$bs_ids])->select(),null, 'id');
        $frame = Db::name('frame')->where('levelid',3)->field('id,name')->select();
        $this->assign('frame',$frame);
        $this->assign('admininfo',$this->_userinfo);
        $this->assign('basis_materials',$basis_materials);
        $this->assign('data',$data);
        return $this->fetch();
    }

    //分公司 添加辅材页面
    public function add_fwarehouse(){
        $id = input('id');
        $info = Db::name('basis_materials')->where(['id'=>$id])->find();
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
        $datas['fid'] = $this->_userinfo;
        $info = Db::name('basis_materials')->where(['id'=>$datas['bs_id']])->find();
        if(!$info){
            $this->error('参数错误');
        }
        $datas['fine'] = $info['fine'];
        $res = Db::name('f_materials')->insertGetId($datas);
        if($res){
            Db::name('f_materials')->where(['id'=>$res])->update(['amcode'=>$info['amcode'].'_'.$res]);
            $this->success('添加成功','fwarehouse_list');
        }else{
            $this->error('添加失败');
        }
    }

    //公司添加的报价项目
    public function fproject_list(){
        $where = [];
        if(input('amcode')){
            $where[] = ['amcode','like','%'.input('amcode').'%'];
        } 
        if(input('fid')){
            $where[] = ['fid','=',input('fid')];
        }else{
            $where[] = ['fid','=',$this->_userinfo['companyid']];
        }
        $data = Db::name('f_project')->where($where)->order('id','desc')->paginate(20,false,['query'=>request()->param()]);
        $bp_id = array_unique(array_column($data->items(), 'bp_id'));
        $basis_project = array_column(Db::name('basis_project')->where(['id'=>$bp_id])->select(),null, 'id');
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
        $id = input('id');
        $info = Db::name('basis_project')->where(['id'=>$id])->find();
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

    //分公司 添加项目操作(改ajax添加了 暂时不用了)
    public function add_fproject_operation(){
        $datas = input();
        $datas['fid'] = $this->_userinfo;
        $info = Db::name('basis_project')->where(['id'=>$datas['bp_id']])->find();
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

    public function create_datas(){
        $admininfo = $this->_userinfo;
        $fid = input('fid')?input('fid'):$admininfo['companyid'];
        try {
            $materials = $this->create_materials($fid);
            $projects = $this->create_project($fid);
            // 先清空原来的
            Db::name('materials')->where(['frameid'=>$fid])->delete();
            Db::name('offerquota')->where(['frameid'=>$fid])->delete();
            // // //再添加
            Db::name('materials')->insertAll($materials);
            Db::name('offerquota')->insertAll($projects);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    //生成 最终仓库数据 并保存
    public function create_materials($fid){
        $admininfo = $this->_userinfo;
        $materials = Db::name('f_materials')->where(['fid'=>$fid])->select();
        $bs_ids = array_unique(array_column($materials, 'bs_id'));
        $basis_materials = array_column(Db::name('basis_materials')->where(['id'=>$bs_ids])->select(),null ,'id');
        $datas = [];
        foreach($materials as $k=>$v){
            if(!isset($basis_materials[$v['bs_id']])){
                $this->error('数据有误');
            }
            $info = [];
            $info['frameid'] = $v['fid'];
            $info['userid'] = $admininfo['userid'];
            $info['amcode'] = $v['amcode'];
            $info['fine'] = $v['fine'];
            $info['brand'] = $v['brank'];
            $info['place'] = $v['place'];
            $info['category'] = $basis_materials[$v['bs_id']]['type_of_work'];
            $info['name'] = $basis_materials[$v['bs_id']]['name'];
            $info['img'] = $v['img'];
            // $info['norms'] = $v['xxx'];
            $info['units'] = $basis_materials[$v['bs_id']]['unit'];
            $info['phr'] = $v['phr'];
            $info['price'] = $v['price'];
            $info['in_price'] = $v['in_price'];
            $info['remarks'] = $v['source'];
            $info['coefficient'] = $basis_materials[$v['bs_id']]['coefficient'];
            $info['important'] = $basis_materials[$v['bs_id']]['important'];
            $datas[] = $info;
        }
        return $datas;
    }

    //生成 最终项目数据 并保存
    public function create_project($fid){
        $admininfo = $this->_userinfo;
        $project = Db::name('f_project')->where(['fid'=>$fid])->select();
        $bp_ids = array_unique(array_column($project, 'bp_id'));
        $basis_project = array_column(Db::name('basis_project')->where(['id'=>$bp_ids])->select(),null ,'id');
        $datas = [];
        foreach($project as $k=>$v){
            if(!isset($basis_project[$v['bp_id']])){
                $this->error('数据有误');
            }
            $info = [];
            //计算辅材基数
            if($v['material']){
                $fine = json_decode($basis_project[$v['bp_id']]['fine'],true);
                $fine = array_column($fine, 'funit','fine');//公式

                $material = json_decode($v['material'],true);
                $datas_material = [];
                foreach($material as $k1=>$v1){
                    // $fine[$k1] 需要的数量
                    $phr = Db::name('f_materials')->where(['amcode'=>$v1])->value('phr');//包装数量
                    //下面这个格式是按照之前的格式的 [对应辅材id,基数]
                    // v1 的id不对
                    $num = round($fine[$k1]/$phr,2);
                    if($num <= 0){
                        $num = 0.01;
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
            $info['type_of_work'] = $basis_project[$v['bp_id']]['type_word_id'];
            $info['project'] = $basis_project[$v['bp_id']]['name'];
            $info['company'] = $basis_project[$v['bp_id']]['unit'];
            $info['cost_value'] = $v['cost_value'];
            $info['quota'] = $v['quota'];
            $info['craft_show'] = $v['craft_show'];
            $info['labor_cost'] = $v['labor_cost'];
            $info['material'] = $basis_project[$v['bp_id']]['content'];
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
            $bs_id = array_column($material, 'id');
            $bs_id = Db::name('basis_materials')->field('id,amcode,name,unit')->where(['id'=>$bs_id])->select();
            $bs_id = array_column($bs_id,null, 'id');
            foreach($material as $k=>$v){
                $material[$k]['name'] = $bs_id[$v['bs_id']]['name'];
            }

            $this->success('success','',$material);
        }else{
            $this->error('无辅材有误');
        }
    }

    //根据公共基础项目库 获取需要的细类
    public function get_fine(){
        $item_number = input('item_number');
        $fine = Db::name('basis_project')->where(['item_number'=>$item_number])->value('fine');
        if(!$fine){
            $this->success('无细类信息');
        }
        $fine = json_decode($fine,true);
        $fine = array_column($fine , 'fine');
        $fine = Db::name('f_materials')->where(['fine'=>$fine])->select();
        $datas = [];
        foreach($fine as $k=>$v){
            $datas[$v['fine']][] = $v;
        }
        $this->success('success','',$datas);
    }
}