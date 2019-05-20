$(function(){
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
    iboxurl = $(".ibox-url").attr("iboxurl");
    setInterval(stat(iboxurl), 300000);
    function stat(iboxurl){
        $.ajax({
            cache: false,
            type: "GET",
            url: iboxurl,
            data:{},
            headers: {
                'X-CSRF-TOKEN': ""
            },
            dataType: 'json',
            success: function(res) {

                if(res.status!="200"){
                    swal("权限错误", res.message, "error")
                }else{
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
                }
            },
            error: function(request) {
                swal("网络错误", "请稍后重试！","error")
            },
        });
    }
});