@extends('admin.base')

@section('content')
    <div class="layui-row layui-col-space15">
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">
                    总用户
                    <span class="layui-badge layui-bg-green layuiadmin-badge">全部</span>
                </div>
                <div class="layui-card-body layuiadmin-card-list">
                    <p class="layuiadmin-big-font">{{$data['user_count']??0}}</p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">
                    今日活跃用户
                    <span class="layui-badge  layui-bg-red layuiadmin-badge">今日</span>
                </div>
                <div class="layui-card-body layuiadmin-card-list">
                    <p class="layuiadmin-big-font">{{$data['user_active_count']??0}}</p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">
                    今日新增用户
                    <span class="layui-badge layui-bg-green layuiadmin-badge">今日</span>
                </div>
                <div class="layui-card-body layuiadmin-card-list">

                    <p class="layuiadmin-big-font">{{$data['user_newadd_count']??0}}</p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">
                    今日收入
                    <span class="layui-badge layui-bg-orange layuiadmin-badge">今日</span>
                </div>
                <div class="layui-card-body layuiadmin-card-list">

                    <p class="layuiadmin-big-font">{{$data['real_agent_account_count']??0}}</p>
                </div>
            </div>
        </div>

        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">
                    昨日收入
                    <span class="layui-badge layui-bg-blue layuiadmin-badge">昨日</span>
                </div>
                <div class="layui-card-body layuiadmin-card-list">
                    <p class="layuiadmin-big-font">{{$data['yesterday_real_agent_account_count']??0}}</p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">
                    今日新用户订单
                    <span class="layui-badge layui-bg-cyan layuiadmin-badge">今日</span>
                </div>
                <div class="layui-card-body layuiadmin-card-list">
                    <p class="layuiadmin-big-font">{{$data['newadd_pay_count']??0}}</p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">
                    今日vip订单数
                    <span class="layui-badge layui-bg-green layuiadmin-badge">今日</span>
                </div>
                <div class="layui-card-body layuiadmin-card-list">

                    <p class="layuiadmin-big-font">{{$data['vip_order_count']??0}}</p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">
                    今日金币订单数
                    <span class="layui-badge layui-bg-orange layuiadmin-badge">今日</span>
                </div>
                <div class="layui-card-body layuiadmin-card-list">

                    <p class="layuiadmin-big-font">{{$data['gold_order_count']??0}}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="layui-row">
        <div class="layui-col-sm12 layui-col-md12">

            <div class="layui-card">
                <div class="layui-card-header">图形统计</div>
                <div class="layui-card-body">
                    <div class="layui-carousel layadmin-carousel layadmin-dataview" id="EchartZhu" style="height: 500px;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="layui-row layui-col-space15">
        <div class="layui-col-sm6 layui-col-md4">
            <div class="layui-card">

                <div class="layui-card-body layuiadmin-card-list">
                    <div class="layui-carousel layadmin-carousel layadmin-dataview" id="Ealluser"></div>
                </div>
            </div>
        </div>

        <div class="layui-col-sm6 layui-col-md4">
            <div class="layui-card">

                <div class="layui-card-body layuiadmin-card-list">
                    <div class="layui-carousel layadmin-carousel layadmin-dataview" id="Etodayreguser"></div>
                </div>
            </div>
        </div>

        <div class="layui-col-sm6 layui-col-md4">
            <div class="layui-card">

                <div class="layui-card-body layuiadmin-card-list">
                    <div class="layui-carousel layadmin-carousel layadmin-dataview" id="Etodaypayuser">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script>
        //访问量
        layui.use(['index', "admin", "carousel", "echarts", "echartsTheme", 'element'], function () {
            var chartZhu = echarts.init(document.getElementById('EchartZhu'));
            $.get("{{ route('admin.agent_line_chart') }}", {}, function (result) {
                if (result.code == 0) {
                    console.log(result.data_time);
                    var optionchartZhe = {
                        title: {
                            text: '最近10天每天的收入'
                        },
                        tooltip: {trigger: 'axis'},
                        toolbox: {
                            show: true,
                            feature: {
                                //dataView: {show: true, readOnly: false},
                                magicType: {show: true, type: ['line', 'bar']},
                                restore: {show: true},
                                saveAsImage: {show: true}
                            }
                        },
                        legend: { //顶部显示 与series中的数据类型的name一致
                            data: ['总收入']
                        },
                        //calculable: true,
                        xAxis: {
                            // type: 'category',
                            //boundaryGap: false, //从起点开始
                            data: result.data_time
                        },
                        yAxis: {
                            type: 'value'
                        },
                        series: [{
                            smooth: true, //曲线 默认折线
                            name: '总收入',
                            type: 'line', //bar:树状 line：曲线
                            data: result.data_agent,
                            itemStyle: {
                                normal: {
                                    label: {
                                        show: true,
                                    }
                                }
                            }
                        }]
                    };
                }
                chartZhu.setOption(optionchartZhe, true);
            });

            //设备总注册用户饼形图
            var alluser = echarts.init(document.getElementById('Ealluser'));
            var todayreguser = echarts.init(document.getElementById('Etodayreguser'));
            var todaypayuser = echarts.init(document.getElementById('Etodaypayuser'));
            $.get("{{ route('admin.agent_pie_chart') }}", {}, function (result) {
                if (result.code == 0) {
                    //console.log(result.all_android_count);
                    var optionchartalluser = {
                        title: {
                            text: '总用户',
                            left: 'center'
                        },
                        tooltip: {
                            trigger: 'item',
                        },
                        toolbox: {
                            show: true,
                            feature: {
                                restore: {show: true},
                                saveAsImage: {show: true}
                            }
                        },
                        legend: {
                            // orient: 'vertical',
                            // top: 'middle',
                            bottom: 10,
                            left: 'center',
                            data: ['安卓', 'ios',]
                        },
                        series: [
                            {
                                type: 'pie',
                                radius: '65%',
                                center: ['50%', '50%'],
                                selectedMode: 'single',
                                data: [

                                    {value: result.all_android_count, name: '安卓'},
                                    {value: result.all_ios_count, name: 'ios'},

                                ],
                                emphasis: {
                                    itemStyle: {
                                        shadowBlur: 10,
                                        shadowOffsetX: 0,
                                        shadowColor: 'rgba( 0, 0.5)'
                                    }
                                }
                            }
                        ]
                    };

                    var optioncharttodayreguser = {
                        title: {
                            text: '今日注册用户',
                            left: 'center'
                        },
                        tooltip: {
                            trigger: 'item',
                        },
                        toolbox: {
                            show: true,
                            feature: {
                                restore: {show: true},
                                saveAsImage: {show: true}
                            }
                        },
                        legend: {
                            // orient: 'vertical',
                            // top: 'middle',
                            bottom: 10,
                            left: 'center',
                            data: ['安卓', 'ios',]
                        },
                        series: [
                            {
                                type: 'pie',
                                radius: '65%',
                                center: ['50%', '50%'],
                                selectedMode: 'single',
                                data: [

                                    {value: result.today_android_count, name: '安卓'},
                                    {value: result.today_ios_count, name: 'ios'},

                                ],
                                emphasis: {
                                    itemStyle: {
                                        shadowBlur: 10,
                                        shadowOffsetX: 0,
                                        shadowColor: 'rgba( 0, 0.5)'
                                    }
                                }
                            }
                        ]
                    };

                    var optioncharttodaypayuser = {
                        title: {
                            text: '今日充值用户',
                            left: 'center'
                        },
                        tooltip: {
                            trigger: 'item',
                        },
                        legend: {
                            // orient: 'vertical',
                            // top: 'middle',
                            bottom: 10,
                            left: 'center',
                            data: ['安卓', 'ios',]
                        },
                        series: [
                            {
                                type: 'pie',
                                radius: '65%',
                                center: ['50%', '50%'],
                                selectedMode: 'single',
                                data: [

                                    {value: result.today_pay_android_count, name: '安卓'},
                                    {value: result.today_pay_ios_count, name: 'ios'},

                                ],
                                emphasis: {
                                    itemStyle: {
                                        shadowBlur: 10,
                                        shadowOffsetX: 0,
                                        shadowColor: 'rgba( 0, 0.5)'
                                    }
                                }
                            }
                        ]
                    };
                }
                alluser.setOption(optionchartalluser, true);
                todayreguser.setOption(optioncharttodayreguser, true);
                todaypayuser.setOption(optioncharttodaypayuser, true);
            });
        });

    </script>
@endsection
