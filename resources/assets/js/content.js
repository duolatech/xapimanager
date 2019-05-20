
//判断当前页面是否在iframe中
if (top == this) {
    var gohome = '<div class="gohome"><a class="animated bounceInUp" href="/main" title="返回首页"><i class="fa fa-home"></i></a></div>';
    $('body').append(gohome);
}
