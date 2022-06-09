@extends('admin.base')

@section('content')
    @if(auth()->user()->user_type==0)
        <div class="layui-row layui-col-space15">
            <div class="layui-col-sm6 layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-header">
                        总用户
                        <span class="layui-badge layui-bg-green layuiadmin-badge">全部</span>
                    </div>
                    <div class="layui-card-body layuiadmin-card-list">
                        <p class="layuiadmin-big-font">{{$data['user_count']??0}}</p>
                        <p>
                            合计代理人数
                            <span class="layuiadmin-span-color">{{$data['user_agent_count']??0}} <i class="layui-inline layui-icon layui-icon-user"></i></span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="layui-col-sm6 layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-header">
                        今日登录用户
                        <span class="layui-badge  layui-bg-red layuiadmin-badge">今日</span>
                    </div>
                    <div class="layui-card-body layuiadmin-card-list">
                        <p class="layuiadmin-big-font">{{$data['user_active_count']??0}}</p>
                        <p>
                            今日充值人数
                            <span class="layuiadmin-span-color">{{$data['user_charge_count']??0}} <i class="layui-inline layui-icon layui-icon-user"></i></span>
                        </p>
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
                        <p>
                            今日提现人数
                            <span class="layuiadmin-span-color">{{$data['user_withdrawal_count']??0}}<i class="layui-inline layui-icon layui-icon-user"></i></span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="layui-col-sm6 layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-header">
                        今日充值金额
                        <span class="layui-badge layui-bg-orange layuiadmin-badge">今日</span>
                    </div>
                    <div class="layui-card-body layuiadmin-card-list">

                        <p class="layuiadmin-big-font">{{$data['user_charge_sum']??0}}</p>
                        <p>
                            今日投注金额
                            <span class="layuiadmin-span-color">{{$data['today_bet_sum_money']??0}}<i class="layui-inline layui-icon layui-icon-rmb"></i></span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="layui-col-sm6 layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-header">
                        今日盈亏金额
                        <span class="layui-badge layui-bg-blue layuiadmin-badge">今日</span>
                    </div>
                    <div class="layui-card-body layuiadmin-card-list">
                        <p class="layuiadmin-big-font">{{$data['today_sum_win_money']??0}}</p>
                        <p>
                            今日中奖金额
                            <span class="layuiadmin-span-color">{{$data['today_win_money_sum']??0}} <i class="layui-inline layui-icon layui-icon-rmb"></i></span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="layui-col-sm6 layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-header">
                        总投注
                        <span class="layui-badge layui-bg-cyan layuiadmin-badge">合计</span>
                    </div>
                    <div class="layui-card-body layuiadmin-card-list">
                        <p class="layuiadmin-big-font">{{$data['bet_sum_money']??0}}</p>
                        <p>
                           总盈亏
                            <span class="layuiadmin-span-color">{{$data['sum_win_money']??0}} <i class="layui-inline layui-icon layui-icon-cart"></i></span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="layui-col-sm6 layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-header">
                        总中奖
                        <span class="layui-badge layui-bg-green layuiadmin-badge">全部</span>
                    </div>
                    <div class="layui-card-body layuiadmin-card-list">

                        <p class="layuiadmin-big-font">{{$data['win_money_sum']??0}}</p>
                        <p>
                            总余额
                            <span class="layuiadmin-span-color">{{$data['user_money_sum']??0}} <i class="layui-inline layui-icon layui-icon-cart"></i></span>
                        </p>
                    </div>
                </div>
            </div>
            {{--<div class="layui-col-sm6 layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-header">
                        今日代理总订单数
                        <span class="layui-badge layui-bg-orange layuiadmin-badge">今日</span>
                    </div>
                    <div class="layui-card-body layuiadmin-card-list">

                        <p class="layuiadmin-big-font">{{$data['agent_order_count']??0}}</p>
                        <p>
                            今日代理总扣单数
                            <span class="layuiadmin-span-color">{{$data['agent_deduct_order_count']??0}} <i class="layui-inline layui-icon layui-icon-cart"></i></span>
                        </p>
                    </div>
                </div>
            </div>--}}
        </div>

        <div class="layui-row">
            <div class="layui-col-sm12 layui-col-md12">
                <div class="layui-card">
                    <div class="layui-card-header">收入图形统计</div>
                    <div class="layui-card-body">
                        <div class="layui-carousel layadmin-carousel layadmin-dataview" id="EchartZhu">
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
    @endif
@endsection

@section('script')
    @if(auth()->user()->user_type==0)
        <script>
            //访问量
            layui.use(['index', "admin", "carousel", "echarts", "echartsTheme", 'element'], function () {
                var chartZhu = echarts.init(document.getElementById('EchartZhu'));
                $.get("{{ route('admin.line_chart') }}", {}, function (result) {
                    if (result.code == 0) {
                        // console.log(result.data_time);
                        var optionchartZhe = {
                            title: {
                                text: '最近10天每天的投注情况'
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
                                data: ['投注金额', '中奖金额', '总盈亏']
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
                                name: '投注金额',
                                type: 'bar', //线性
                                data: result.data_bet_money,
                                itemStyle: {
                                    normal: {
                                        label: {
                                            show: true,
                                        }
                                    }
                                }
                            }, {
                                name: '中奖金额',
                                type: 'bar', //线性
                                data: result.data_win_money,
                                itemStyle: {
                                    normal: {
                                        label: {
                                            show: true,
                                        }
                                    }
                                }
                            }, {
                                smooth: true, //曲线 默认折线
                                name: '总收入',
                                type: 'line', //线性
                                data: result.data_account,
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
                $.get("{{ route('admin.pie_chart') }}", {}, function (result) {
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
                                data: ['PC', 'H5',]
                            },
                            series: [
                                {
                                    type: 'pie',
                                    radius: '65%',
                                    center: ['50%', '50%'],
                                    selectedMode: 'single',
                                    data: [

                                        {value: result.all_android_count, name: 'PC'},
                                        {value: result.all_ios_count, name: 'H5'},

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
                                data: ['pc', 'H5',]
                            },
                            series: [
                                {
                                    type: 'pie',
                                    radius: '65%',
                                    center: ['50%', '50%'],
                                    selectedMode: 'single',
                                    data: [

                                        {value: result.today_android_count, name: 'PC'},
                                        {value: result.today_ios_count, name: 'H5'},

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
                                text: '今日输赢比',
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
                                data: ['赢', '输',]
                            },
                            series: [
                                {
                                    type: 'pie',
                                    radius: '65%',
                                    center: ['50%', '50%'],
                                    selectedMode: 'single',
                                    data: [

                                        {value: result.today_pay_android_count, name: '赢'},
                                        {value: result.today_pay_ios_count, name: '输'},

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
    @endif
@endsection
