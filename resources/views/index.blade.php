@extends('base') @section('page-content')
<script type="text/javascript"
	src="{{URL::asset('js/echarts/echarts.min.js')}}"></script>
<script type="text/javascript"
	src="{{URL::asset('js/echarts/china.js')}}"></script>
<div class="page-content">
	<div class="alert alert-block alert-success">
		<button type="button" class="close" data-dismiss="alert">
			<i class="ace-icon fa fa-times"></i>
		</button>
		<!--i class="ace-icon fa fa-check green"></i-->
		线上各城市Api调用次数统计，每五分钟更新一次数据
	</div>
	@include('public/setbox')
	<div class="row">
		<!-- PAGE CONTENT BEGINS -->
		<div id="main" style="width: 800px; height: 600px; margin-left: 60px;"></div>
		<script type="text/javascript">

                var myChart = echarts.init(document.getElementById('main'));
                // 指定图表的配置项和数据
                var convertData = function (data, geoCoordMap) {
                   var res = [];
                   for (var i = 0; i < data.length; i++) {
                       var geoCoord = geoCoordMap[data[i].area_id];
                       if (geoCoord) {
                           res.push({
                               name: data[i].name,
                               //单击节点后,显示经纬度和数量
                               value: geoCoord.concat(data[i].value)
                           });
                       }
                   }
                   return res;
                };
                option = {
                   backgroundColor: '#FFFFFF',
                   title: {
                       text: '城市Api调用次数统计',
                       subtext: 'Api调用次数统计',
                       left: 'center',
                       textStyle: {
                           color: '#fff'
                       }
                   },
                   tooltip : {
                       trigger: 'item'
                   },
                   legend: {
                       orient: 'vertical',
                       y: 'bottom',
                       x:'right',
                       data:['pm2.5'],
                       textStyle: {
                           color: '#fff'
                       }
                   },
                   geo: {
                       map: 'china',
                       label: {
                           emphasis: {
                               show: false
                           }
                       },
                       roam: true,
                       itemStyle: {
                           normal: {
                               areaColor: '#D1F2FA',
                               borderColor: '#1ABDE6'
                           },
                           emphasis: {
                               areaColor: '#1ABDE6'
                           }
                       }
                   },
                   series : []
               };
               // 使用刚指定的配置项和数据显示图表
               myChart.hideLoading();
               myChart.setOption(option);
			   //每五分钟更新一次数据
               stat();
               setInterval(stat, 300000);
               function stat(){
            	   $.ajax({
                       cache: false,
                       type: "GET",
                       url:"{{route('Index.area')}}",
                       data:{},
                       headers: {
                           'X-CSRF-TOKEN': $("input[name='_token']").val()
                       },
                       dataType: 'json',
                       success: function(res) {

  						   var data = res.data;
  						   var geoCoordMap = [];
    					   $.each(res.data, function(i,vol){
    							 geoCoordMap[vol.area_id] = [vol.longitude, vol.latitude];
        				   });
                    	   option = {
                    			   visualMap: {
                                       min: 0,
                                       max: 200,
                                       calculable: true,
                                       inRange: {
                                           color: ['#50a3ba', '#CCFF00', '#FF9900']
                                       },
                                       textStyle: {
                                           color: '#fff'
                                       }
                                   },
                    			   series : [
                                             {
                                                 name: 'api num',
                                                 type: 'scatter',
                                                 coordinateSystem: 'geo',
                                                 data: convertData(data, geoCoordMap),
                                                 symbolSize: function (val) {
                                                     return val[2] / 10;
                                                 },
                                                 label: {
                                                     normal: {
                                                         formatter: '{b}',
                                                         position: 'right',
                                                         show: false
                                                     },
                                                     emphasis: {
                                                         show: true
                                                     }
                                                 },
                                                 itemStyle: {
                                                     normal: {
                                                         color: '#ddb926'
                                                     }
                                                 }
                                             },
                                             {
                                                 name: 'Top 5',
                                                 type: 'effectScatter',
                                                 coordinateSystem: 'geo',
                                                 data: convertData(data.sort(function (a, b) {
                                                     return b.value - a.value;
                                                 }).slice(0, 5), geoCoordMap),
                                                 symbolSize: function (val) {
                                                     return val[2] / 10;
                                                 },
                                                 showEffectOn: 'render',
                                                 rippleEffect: {
                                                     brushType: 'stroke'
                                                 },
                                                 hoverAnimation: true,
                                                 label: {
                                                     normal: {
                                                         formatter: '{b}',
                                                         position: 'right',
                                                         show: true
                                                     }
                                                 },
                                                 itemStyle: {
                                                     normal: {
                                                         color: '#f4e925',
                                                         shadowBlur: 10,
                                                         shadowColor: '#333'
                                                     }
                                                 },
                                                 zlevel: 1
                                             }
                                         ]
                           }
                    	   myChart.setOption(option);
                       },
                       error: function(request) {
                           layer.msg("网络错误，请稍后重试");
                       },
                   });
               }        
                
            </script>
		<!-- PAGE CONTENT ENDS -->
	</div>
	<!-- /.row -->
</div>
<!-- /.page-content -->
@endsection
