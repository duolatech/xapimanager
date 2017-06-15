<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\classifyRequest;
use App\Http\Controllers\Controller;
use Input, Validator, Log;
use App\Models\User;
use App\Models\Classify;
use App\Models\ApiList;
use App\Models\ApiParam;

class CategoryController extends Controller
{

    /**
     * 获取分类信息
     */
    public function getCategory(Request $request)
    {
        $data = Classify::where('status', 1)->where('pid', 0)->get();
        $info = ! empty($data) ? $data->toArray() : array();
        foreach ($info as &$value) {
            $value['classifyname'] = subString($value['classifyname'], 0, 30);
            $value['description'] = subString($value['description'], 0, 30);
        }
        return view('Category.index', [
            'info' => $info
        ]);
    }

    /**
     * 获取子分类信息
     */
    public function subCategory()
    {
        $classifyId = Input::get('classifyId');
        $data = Classify::where('id', $classifyId)->where('status', 1)->first();
        $info['classify'] = ! empty($data) ? $data->toArray() : array();
        if (! empty($classifyId)) {
            $info['sub'] = $this->getSubClassify($classifyId);
        }
        return view('Category.sub', [
            'info' => $info
        ]);
    }

    /**
     * 递归查询所有子分类
     * 
     * @param $pid 父级id            
     * @param $auth 用户所在组权限            
     * @return 可用菜单
     */
    public function getSubClassify($pid)
    {
        $data = Classify::where('pid', $pid)->where('status', 1)
            ->orderBy('addtime')
            ->get();
        $info = ! empty($data) ? $data->toArray() : array();
        foreach ($info as &$value) {
            $value['child'] = $this->getSubClassify($value['id']);
        }
        return $info;
    }

    /**
     * 添加分类
     * 
     * @return 分类视图
     */
    public function infoCategory()
    {
        $classifyId = Input::get('classifyId');
        $data = Classify::where('id', $classifyId)->where('status', 1)->first();
        $info = ! empty($data) ? $data->toArray() : array();
        // 查询负责人信息
        if (! empty($info['leader'])) {
            $userinfo = User::whereIn('uid', explode(',', $info['leader']))->get();
            $info['user'] = ! empty($userinfo) ? $userinfo->toArray() : array();
        }
        
        return view('Category.add', [
            'info' => $info
        ]);
    }

    /**
     * 添加子分类
     * 
     * @return 分类视图
     */
    public function infoSubCategory()
    {
        $classifyId = Input::get('subClassifyId');
        $data = Classify::where('id', $classifyId)->where('status', 1)->first();
        $info = ! empty($data) ? $data->toArray() : array();
        // 查询负责人信息
        if (! empty($info['leader'])) {
            $userinfo = User::whereIn('uid', explode(',', $info['leader']))->get();
            $info['user'] = ! empty($userinfo) ? $userinfo->toArray() : array();
        }
        // 查询所有分类
        $all = Classify::where('pid', 0)->where('status', 1)->get();
        $info['classify'] = ! empty($all) ? $all->toArray() : array();
        $info['currentClassify'] = Input::get('classify');
        
        return view('Category.addSub', [
            'info' => $info
        ]);
    }

    /**
     * 分类存储
     * 
     * @return 分类视图
     */
    public function categoryStore(classifyRequest $request)
    {
        $pid = Input::get('pid');
        $classifyname = Input::get('classify');
        $classifyId = Input::get('classifyId');
        $description = Input::get('description');
        $csrf_user = trim(Input::get('csrf_user'), ',');
        if (! empty($classifyId)) {
            $classify = Classify::find($classifyId);
            $classify->pid = $pid;
            $classify->classifyname = $classifyname;
            $classify->description = $description;
            $classify->addtime = time();
            $classify->leader = $csrf_user;
            $classify->status = 1;
        } else {
            $classify = new Classify();
            $classify->classifyname = $classifyname;
            $classify->pid = $pid;
            $classify->description = $description;
            $classify->addtime = time();
            $classify->creator = session::get('uid');
            $classify->leader = $csrf_user;
            $classify->status = 1;
        }
        $classify->save();
        Cache::forget('classify');
        if (! empty($classify->id)) {
            return response()->json([
                'status' => 200,
                'message' => '保存成功'
            ]);
        } else {
            return response()->json([
                'status' => 4010,
                'message' => '用户名不存在，请核对和输入'
            ]);
        }
    }

    /**
     * 查询分类信息
     * 
     * @param $cid 分类id            
     */
    public function getClassify($cid)
    {
        $data = Classify::where('id', $cid)->where('status', 1)->first();
        $info = ! empty($data) ? $data->toArray() : array();
        
        return $info;
    }

    /**
     * 查询分类接口信息并导出
     * 
     * @param $cid 分类id            
     */
    public function classify(Request $request, $cid)
    {
        
        // 查询分类信息
        $class = $this->getClassify($cid);
        // 查询子分类id
        $subClassify = $this->getSubClassify($cid);
        $envid = Input::get('envid');
        $subIds = array();
        foreach ($subClassify as $value) {
            $subIds[] = $value['id'];
        }
        // 分类接口详情信息
        $param = array(
            'envid' => intval(Input::get('envid')),
            'classify' => $subIds
        );
        $status = array(
            1,
            2,
            3
        );
        $alt = new ApiList();
        $list = $alt->getApiDetail($param, $status, 0, 500);
        // 分类参数信息
        $dids = array();
        foreach ($list['info'] as $value) {
            $dids[] = $value['id'];
        }
        // 接口参数
        $apiParam = $this->getParam($dids);
        if (! empty($apiParam)) {
            foreach ($list['info'] as &$vol) {
                if (empty($apiParam[$vol['id']])) {
                    $apiParam[$vol['id']] = array(
                        'GET' => array(
                            'request' => array(),
                            'response' => array()
                        ),
                        'HEADER' => array(
                            'request' => array()
                        )
                    );
                }
                $vol['param'] = $apiParam[$vol['id']];
            }
        }
        $list['classify'] = $class;
        $list['site'] = $request['sys']['Website'];
        $list['time'] = date('Y-m-d', time());
        
        // 输出doc文件
        return view('Category.doc', [
            'data' => $list
        ]);
    }

    /**
     * 批量获取Api参数信息
     * 
     * @param array $dids
     *            Api详情id
     */
    public function getParam(array $dids)
    {
        $data = array();
        $type = array(
            'GET',
            'POST',
            'PUT',
            'DELETE'
        );
        if (! empty($dids)) {
            $apiParam = ApiParam::whereIn('detailid', $dids)->get();
            $apiParam = ! empty($apiParam) ? $apiParam->toArray() : array();
            foreach ($apiParam as $param) {
                // 常规参数
                $res = array();
                $way = array(
                    'request',
                    'response'
                );
                foreach ($way as $value) {
                    $arr[$value] = json_decode($param[$value], true);
                    foreach ($type as $vol) {
                        $only = $arr[$value][$vol];
                        $param_info = $this->filter($only);
                        if (! empty($param_info)) {
                            $res[$vol][$value] = $param_info;
                        }
                    }
                }
                // 每种type都需要request、response
                foreach ($res as &$rex) {
                    foreach ($way as $value) {
                        if (empty($rex[$value])) {
                            $rex[$value] = array();
                        }
                    }
                }
                // header头信息
                $header = json_decode($param['header'], true);
                $res['HEADER'] = array(
                    'request' => $this->filter($header)
                );
                // 状态码
                $res['statuscode'] = json_decode($param['statuscode'], true);
                
                $data[$param['detailid']] = $res;
            }
        }
        return $data;
    }

    /**
     * 过滤参数中的空字段
     * 
     * @param $data 参数信息            
     */
    public function filter($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $field = trim($value['field']);
                if (empty($field)) {
                    unset($data[$key]);
                }
            }
        }
        return $data;
    }
}
