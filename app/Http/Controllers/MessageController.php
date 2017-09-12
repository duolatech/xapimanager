<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Message;
use Input, Session, Log;
class MessageController extends Controller
{
    protected $limit=10;
    public function index(Request $request){
        
        //获取列表信息
        $type = Input::get('type');
        $uid = Session::get('uid');
        $page = Input::get('page');
        $page = !empty($page) ? $page : 1;
        $start = ($page - 1) * ($this->limit);
        if($type=='unread'){
            $info = Message::where(['receiver'=>$uid, 'isread'=>2]);
        }elseif($type=='send'){
            $info = Message::where(['sender'=>$uid]);
        }else{
            $info = Message::where(['receiver'=>$uid]);
        }
        $totalCount = $info->count();
        $info = $info->orderBy('sendtime','desc')->offset($start)->limit($this->limit)->get();
        $info = !empty($info) ? $info->toArray() : array();
        //获取用户信息
        $userIds = array();
        foreach ($info as $value){
            $userIds[] = $value['sender'];
            $userIds[] = $value['receiver'];
        }
        $userInfo = $this->getUserInfo($userIds);
        foreach ($info as &$value){
            $value['sender'] = $userInfo[$value['sender']];
            $value['receiver'] = $userInfo[$value['receiver']];
            $value['time'] = date('Y-m-d H:i', $value['sendtime']);
            $value['content'] = subString($value['content'],0,300);
        }
        return view('message.index', ['mes'=>array(
            'pageCount'=>ceil($totalCount/$this->limit),
            'page'=>$page,
            'info'=>$info,
            'type'=>$type
        )]);
    }
    /**
     * 批量获取用户信息
     * @param $uids 用户id
     */
    public function getUserInfo($uids){
        
        $result = array();
        if(!empty($uids)){
            $info = User::whereIn('uid', array_unique($uids))
                ->select('uid','username','email','avatar')
                ->get();
            $info = !empty($info) ? $info->toArray() : array();
            foreach ($info as $value){
                if(!file_exists($value['avatar'])){
                    $value['avatar'] = '/img/avatar.jpg';
                }
                $result[$value['uid']] = $value;
            }
        }
        return $result;
    }
    /**
     * 消息详情
     */
    public function detail(){
        
        $id = Input::get('id');
        $uid = Session::get('uid');
        //获取消息数据
        if(!empty($id)){
            $info = Message::where(function($query) use($uid){
                    $query->where('sender','=', $uid)->orWhere('receiver','=',$uid);
                })->where(['id'=>$id])->get();
        }
        if(!empty($info)){
            $info = $info->toArray();
            if($info[0]['pid']==0){
                $info2 = Message::where(function($query) use($uid){
                    $query->where('sender','=', $uid)->orWhere('receiver','=',$uid);
                })->where(['pid'=>$id])->orderBy('sendtime','asc')->get();
            }else if($info[0]['pid']>0){
                $info2 = Message::where(function($query) use($uid){
                    $query->where('sender','=', $uid)->orWhere('receiver','=',$uid);
                })->where(['id'=>$info[0]['pid']])->orWhere(['pid'=>$info[0]['pid']])->orderBy('sendtime','asc')->get();
            }
            $info2 = !empty($info2) ? $info2->toArray() : array();
        }else{
            $info = array();
        }
        $info = array_merge($info, $info2);
        //获取用户信息
        $userIds = array();
        $mesIds = array();
        foreach ($info as $value){
            $userIds[] = $value['sender'];
            $userIds[] = $value['receiver'];
            $mesIds[] = $value['id'];
        }
        $userInfo = $this->getUserInfo($userIds);
        //数据处理
        $data = array();
        foreach($info as $value){
            $value['sender'] = $userInfo[$value['sender']];
            $value['receiver'] = $userInfo[$value['receiver']];
            $value['time'] = date('Y-m-d H:i', $value['sendtime']);
            if($value['pid']==0){
                $data['mes'] = $value;
            }else{
                $data['reply'][$value['id']] = $value;
            }
        }
        //更新未读状态
        if(!empty($mesIds)){
            Message::whereIn('id', $mesIds)->update(['isread'=>1]);
        }
        return view('message.detail', ['info'=>array(
            'uid'=> $uid,
            'detail'=>$data
        )]);
    }
    /**
     * 发送消息页面
     */
    public function MessageInfo(){
        return view('message.info');
    }
    /**
     * 保存消息
     */
    public function MessageStore(){
        
        $pid = Input::get('pid');
        $subject = Input::get('subject');
        $content = Input::get('content');
        $members = trim(Input::get('members'), ',');
        $sender = Session::get('uid');
        
        $uids = explode(',', $members);
        $message = new Message();
        $time = time();
        $data = array();
        foreach ($uids as $uid){
            if(!empty($uid)){
                $data[] = array(
                    'sender'    => $sender,
                    'receiver'  => $uid,
                    'pid'   => $pid,
                    'subject'   => $subject,
                    'content'   => $content,
                    'sendtime'  => $time,
                    'isread'    => 2
                );
            }
        }
        if(!empty($data)){
            $id = Message::insert($data);
        }
        if($id){
            return response()->json(['status'=>200, 'message'=>'发送成功']);
        }else{
            return response()->json(['status'=>4010, 'message'=>'发送失败，请稍后重试！']);
        }
        
    }
    /**
     * 删除消息
     */
    public function del(){
        
        $id = Input::get('id');
        $uid = Session::get('uid');
        if(!empty($id)){
            $delid = Message::where(function($query) use($uid){
                    $query->where('sender','=', $uid)->orWhere('receiver','=',$uid);
                })->where(['id'=>$id])->delete();
        }
        if($delid){
            return response()->json(['status'=>200, 'message'=>'删除成功']);
        }else{
            return response()->json(['status'=>4010, 'message'=>'删除失败，请稍后重试！']);
        }
    }
    
}
