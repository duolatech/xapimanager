<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Area;

class StatisticsController extends Controller
{
    /**
     * 获取城市经纬度及统计信息
     * @param Request $request
     * @return 统计信息
     */
    public function area(Request $request){
        
        //排查省份及经度为0的情况
        $data = Area::where('pid', '!=', 0)->where('longitude', '!=', 0)->get();
        $data = !empty($data) ? $data->toArray() : array();
        
        //随机生成各城市Api请求量，便于观测
        foreach ($data as &$value){
            $value['number'] = rand(1, 200);
        }
        //返回请求数据
        return response()->json([
            'status'=>200,
            'message' => '成功',
            'data'=>$data
        ]);
        
    }
}
