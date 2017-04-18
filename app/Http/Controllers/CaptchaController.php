<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//引用对应的命名空间use Session;
use Captcha;
class CaptchaController extends Controller
{
    /**
     * 创建验证码
     */
    public function mews() {
        return Captcha::create('flat');
    }
}
