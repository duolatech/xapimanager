<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta charset="utf-8" />
<title>{{$sys['Website']['title']}}</title>

<meta name="keywords" content="{{$sys['Website']['keywords']}}" />
<meta name="description" content="{{$sys['Website']['description']}}" />
<meta name="viewport"
	content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />


<!-- bootstrap & fontawesome -->
<link rel="stylesheet" href="{{URL::asset('css/bootstrap.css')}}" />
<link rel="stylesheet" href="{{URL::asset('css/font-awesome.css')}}" />
<link rel="stylesheet" href="{{URL::asset('css/jquery-ui.css')}}" />
<!-- page specific plugin styles -->

<!-- text fonts -->
<link rel="stylesheet" href="{{URL::asset('css/ace-fonts.css')}}" />

<!-- ace styles -->
<link rel="stylesheet" href="{{URL::asset('css/ace.css')}}"
	class="ace-main-stylesheet" id="main-ace-style" />

<!--[if lte IE 9]>
    <link rel="stylesheet" href="{{URL::asset('css/ace-part2.css')}}" class="ace-main-stylesheet"/>
    <![endif]-->

<!--[if lte IE 9]>
    <link rel="stylesheet" href="{{URL::asset('css/ace-ie.css')}}"/>

    <![endif]-->

<!-- inline styles related to this page -->
<link rel="stylesheet/less" type="text/css" href="{{URL::asset('css/less/default.less')}}">
<script type="text/javascript" src="{{URL::asset('css/less/less-1.7.4.min.js')}}"></script>
<!-- ace settings handler -->
<script src="{{URL::asset('js/ace-extra.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/jquery-1.9.1.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/layer/layer.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/jquery.cookie.js')}}"></script>

<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

<!--[if lte IE 8]>
    <script src="{{URL::asset('js/html5shiv.js')}}"></script>
    <script src="{{URL::asset('js/respond.js')}}"></script>
    <![endif]-->
</head>