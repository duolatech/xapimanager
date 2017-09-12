<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Help;
use Input, Session, Log;
class HelpController extends Controller
{
    //Api请求超时时间，单位s
    const CURLOPT_TIMEOUT=30;
    protected $limit = 7;
    public function index(Request $request){
        
        return view('help.index');
    }
    /**
     * 获取帮助中心数据
     */
    public function ajaxHelp(){
        
        $page = Input::get('page');
        $page = !empty($page) ? $page : 1;
        $start = ($page - 1) * ($this->limit);
        $info = Help::leftJoin('user as u', 'help.author','=', 'u.uid')->where(['help.status'=>1])
                ->select('help.id', 'help.title', 'help.content', 'u.username', 'help.ctime');
        $totalCount = $info->count();
        $info = $info->orderBy('help.ctime','desc')->offset($start)->limit($this->limit)->get();
        $info = !empty($info) ? $info->toArray() : array();
        
        foreach ($info as &$value){
            $value['time'] = date('Y-m-d H:i', $value['ctime']);
            $value['title'] = htmlspecialchars($value['title']);
        }
        return response()->json([
            'status'=>200,
            'message'=>'成功',
            'data'=>array(
                'pageCount'=>ceil($totalCount/$this->limit),
                'info'=>$info
            )
        ]);
    }
    /**
     * 添加帮助中心页
     */
    public function helpInfo(){
        
        $id = Input::get('id');
        $info = array();
        if(!empty($id)){
            $info = Help::where(['id'=>$id, 'status'=>1])->first();
        }
        return view('help.info',['info'=>$info]);
    }
    /**
     * 保持帮助中心
     */
    public function helpStore(){
        
        $id = Input::get('id');
        if(!empty($id)){
            $help = Help::find($id);
        }
        if(empty($help)){
            $help = new Help();
            $help->ctime = time();
        }else{
            $help->id = $id;
        }
        $help->title = Input::get('title');
        $help->content = Input::get('content');
        $help->author = Session::get('uid');
        $help->save();
        
        if($help->id){
            return response()->json(['status'=>200, 'message'=>'保存成功']);
        }else{
            return response()->json(['status'=>4010, 'message'=>'保存失败，请稍后重试！']);
        }
    }
    /**
     * 删除文章
     */
    public function del(){
        $id = Input::get('id');
        if(!empty($id)){
            $delid = Help::where(['id'=>$id])->update(['status'=>2]);
        }
        if($delid){
            return response()->json(['status'=>200, 'message'=>'删除成功']);
        }else{
            return response()->json(['status'=>4010, 'message'=>'删除失败，请稍后重试！']);
        }
    }
    
}
