<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\ApiDetail;
use App\Models\ApiParam;
class InstallController extends Controller
{
    //Api请求超时时间，单位s
    const CURLOPT_TIMEOUT=30;
    public function index(Request $request){
        
        $data = array();
        //检查目录是否有写权限
        $basePath = base_path();
        if(!is_writable($basePath)){
            $data['dirauth'] = 1;
        }
        //检查扩展是否安装，php_curl，php_mbstring,php_openssl
        if(!extension_loaded('curl')){
            $data['ext'][] = 'curl';
        }
        if(!extension_loaded('mbstring')){
            $data['ext'][] = 'mbstring';
        }
        if(!extension_loaded('openssl')){
            $data['ext'][] = 'openssl';
        }
        /* 
         * 模拟参数
         * $data = array(
            'dirauth'   => 1,
            'ext' => array('curl','mbsring')
        ); */
        $version = Config('app.version');
        $version = !empty($version) ? 0 : 1;
        return view('install.index', ['info'=>array(
            'data'=>$data, 
            'version'=>$version
        )]);
    }
    /**
     * 安装数据库文件
     */
    public function info(){
        
        $data = $_POST;
        $port = !empty($data['port']) ? $data['port'] : 3306;
        $database = !empty($data['database'])? $data['database'] : 'xapimanager';
        $address = $data['address'].':'.$port;
        
        $dsn = 'mysql:host='.$data['address'].';port='.$port;
        $user = $data['username'];
        $password = $data['password'];
        try {
            $pdo = new \PDO($dsn, $user, $password);
            $res = $this->dealSql($pdo, $database);
            $config = array(
                'APP_INSTALL' => 1,
                'DB_HOST'   => $data['address'],
                'DB_PORT'   => $port,
                'DB_DATABASE'   => $database,
                'DB_PREFIX' => 'mx_',
                'DB_USERNAME'   => $data['username'],
                'DB_PASSWORD'   => $data['password']
            );
            $up = modifyEnv($config);
            if($up){
                $result = array(
                    'status'    => 200,
                    'message'   => '安装成功，2s后将跳转到登录页面',
                );
            }else{
                $result = array(
                    'status'    => 4011,
                    'message'   => '操作失败，请稍后重试',
                );
            }
        } catch (\Exception $e) {
            $result = array(
                'status'    => 4010,
                'message'   => $e->getMessage(),
            );
        }
        return response()->json($result);
    }
    /**
     * xApi升级
     */
    public function update(){
        
        $data = config('database.connections.mysql');
        
        $port = !empty($data['port']) ? $data['port'] : 3306;
        $database = !empty($data['database'])? $data['database'] : 'xapimanager';
        $address = $data['host'].':'.$port;
        
        $dsn = 'mysql:host='.$data['host'].';port='.$port;
        $user = $data['username'];
        $password = $data['password'];
        try {
            $pdo = new \PDO($dsn, $user, $password);
            $res = $this->dealUpdateSql($pdo, $database);
            $config = array(
                'APP_INSTALL' => 1
            );
            $up = modifyEnv($config);
            if($up){
                $result = array(
                    'status'    => 200,
                    'message'   => '安装成功，2s后将跳转到登录页面',
                );
            }else{
                $result = array(
                    'status'    => 4011,
                    'message'   => '操作失败，请稍后重试',
                );
            }
        } catch (\Exception $e) {
            $result = array(
                'status'    => 4010,
                'message'   => $e->getMessage(),
            );
        }
        $result = array(
            'status'    => 200,
            'message'   => '升级成功，2s后将跳转到登录页面',
        );
        return response()->json($result);
        
    }
    /**
     * sql导入升级表
     * @param $pdo
     */
    public function dealUpdateSql($pdo, $database){
        
        $file = base_path().'/install/update.sql';
        $num = 0;
        if(file_exists($file)){
            //读取文件
            $_sql = file_get_contents($file);
            $_arr = explode(';', $_sql);
            
            //执行sql语句
            $pdo->exec('USE `'.$database.'`;');
            foreach ($_arr as $_value) {
                $num = $pdo->exec($_value.';');
            }
            $pdo = null;
            //更新apidetail表
            $data = array();
            $detail = ApiDetail::get()->toArray();
            $param = ApiParam::get()->toArray();
            
            $type = array('GET'=>1,'POST'=>2, 'PUT'=>3, 'DELETE'=>4);
            foreach ($param as $value){
                $newParam[$value['detailid']] = array(
                    'id' => $value['id'],
                    'header' => $value['header'],
                    'request' => json_decode($value['request'], true),
                    'response' => json_decode($value['response'], true),
                    'statuscode' => $value['statuscode']
                );
            }
            foreach ($detail as $key=>$value){
                $did = $value['id'];
                $apiparam = !empty($newParam[$did]) ? $newParam[$did] : array();
                if(empty($apiparam)){
                	continue;
                }else{
                    foreach($apiparam['request'] as $ko=>$vol){
                        if(!empty($vol[0]['field']) || !empty($apiparam['response'][$ko][0]['field'])){
                            $parts = parse_url($value['gateway']);
                            $value['gateway'] = $parts['path'];
                            unset($value['id']);
                            $value['type'] = $type[$ko];
                            $value['request'] = json_encode($vol, JSON_UNESCAPED_UNICODE);
                            $value['isheader'] = 1;
                            $value['header'] = $apiparam['header'];
                            $value['statuscode'] = $apiparam['statuscode'];
                            if(!empty($apiparam['request']['response'][$ko])){
                                $value['response'] = json_encode($apiparam['request']['response'][$ko], JSON_UNESCAPED_UNICODE);
                            }else{
                                $value['response'] = '[{"field":"","must":"1","des":"","default":""}]';
                            }
                            $data[] = $value;
                        }
                    }
                }
                
            }
            //重新写入详情表
            ApiDetail::where('id','>',0)->delete();
            ApiDetail::insert($data);
        }
        return $num;
    }
    /**
     * sql导入数据库
     * @param $pdo
     */
    public function dealSql($pdo, $database){
    
        $file = base_path().'/install/xapimanager.sql';
        $num = 0;
        if(file_exists($file)){
            //读取文件
            $_sql = file_get_contents($file);
            $_arr = explode(';\n\r', $_sql);
            //执行sql语句
            $pdo->exec('CREATE DATABASE /*!32312 IF NOT EXISTS*/`'.$database.'` /*!40100 DEFAULT CHARACTER SET utf8 */;');
            $pdo->exec('USE `'.$database.'`;');
            foreach ($_arr as $_value) {
                $num = $pdo->exec($_value.';');
            }
            $pdo = null;
        }
        return $num;
    }
  
}
