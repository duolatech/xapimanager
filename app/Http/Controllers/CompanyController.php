<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\companyRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Models\Company;
use Input;
class CompanyController extends Controller
{
    protected $limit = 20;
    protected $proid;
    protected $request;
    
    public function __construct(Request $request){
    
        //请求数据
        $this->request = $request;
        //当前项目id
        if(!empty($this->request['sys']['Project']['proid'])){
            $this->proid = $this->request['sys']['Project']['proid'];
        }else{
            $this->proid = 0;
        }
    }
	/**
     * 企业密钥列表页面
     */
	public function index(){
	    
		return view('company.index');
	}
	/**
	 * ajax 请求公司密钥列表
	 */
	public function ajaxList(){
	    
	    $company = Input::get('company');
	    $page = !empty(Input::get('page')) ? Input::get('page') : 1;
	    $limit = !empty(Input::get('limit')) ? Input::get('limit') : $this->limit;
	    $proid = !empty(Input::get('proid')) ? Input::get('proid') : $this->proid;
	    $start = ($page - 1) * ($limit);
	    $cpy = Company::where(['proid'=> $proid])->whereIn('status',[1,2]);
	    if(!empty($company)){
	        $cpy = $cpy->where('company', 'like', "%{$company}%");
	    }
	    $totalCount =  $cpy->count();
	    $list = $cpy->offset($start)->limit($this->limit)->get();
	    $result = array(
	        'status' => 200,
	        'message' => '成功',
	        'data' => array(
	           'pageCount'=>ceil($totalCount/$this->limit),
	           'list' => $list
	        )
	    );
	    
	    return $result;
	}
    /**
     * 添加/编辑项目
     */
    public function secretInfo(){
        
        $id = Input::get('id');
        if(!empty($id)){
            $info = Company::where(['id'=>$id])->first();
        }
        $info = !empty($info) ? $info->toArray() : array();
        
        return view('company.info', ['data'=>$info]);
    }

    /**
     * 保存项目
     * @param projectRequest $request
     */
    public function store(companyRequest $request){
        
        $status = Input::get('status');
        
        $id = Input::get('id');
        if(!empty($id)){
            $cpy  = Company::find($id);
            $cpy->id = $id;
        }else{
            $cpy = new Company();
        }
        
        $cpy->company = Input::get('company');
        $cpy->appId = Input::get('appId');
        $cpy->appSecret = Input::get('appSecret');
        $cpy->status = (!empty($status) && $status=='on') ? 1 : 2;
        $cpy->proid = $this->proid;
        $cpy->ctime = time();
        
        //保存
        $info = $cpy->save();
        if(!empty($info)){
            return response()->json(['status'=>200, 'message'=>'保存成功']);
        }else{
            return response()->json(['status'=>4010, 'message'=>'保存失败，请稍后重试！']);
        }
    }
    /**
     * 公司密钥操作
     */
    public function operate(){
        
        $company = Input::get('company');
        $id = Input::get('id');
        if(!empty($id) && !empty($company)){
            $delid = Company::where(['id'=>$id, 'company'=>$company, 'proid'=>$this->proid])->update(['status'=>3]);
            if($delid){
                return response()->json(['status'=>200, 'message'=>'删除成功']);
            }else{
                return response()->json(['status'=>4011, 'message'=>'删除失败，请稍后重试！']);
            }
        }else{
            return response()->json(['status'=>4010, 'message'=>'请求参数错误！']);
        }
    }
}
