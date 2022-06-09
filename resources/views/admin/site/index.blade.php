@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h3>站点配置</h3>
        </div>
        <div class="layui-card-body">
            <form class="layui-form layui-form-pane" action="{{route('admin.site.update')}}" method="post">
                {{csrf_field()}}
                {{method_field('put')}}
                <input type="hidden" name="sitekey" value="website">
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">站点名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="webname" value="{{ $config['webname']??'' }}" lay-verify="required" lay-vertype="tips" placeholder="请输入标题" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">图片域名</label>
                    <div class="layui-input-block">
                        <input type="text" name="img_domain" value="{{ $config['img_domain']??'' }}" placeholder="请输入视频图片域名" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">视频域名</label>
                    <div class="layui-input-block">
                        <input type="text" name="video_domain" value="{{ $config['video_domain']??'' }}" placeholder="请输入视频域名" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">api域名</label>
                    <div class="layui-input-block">
                        <input type="text" name="api_domain" value="{{ $config['api_domain']??'' }}" lay-verify="required" lay-vertype="tips" placeholder="请输入api域名" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">启动域名</label>
                    <div class="layui-input-block">
                        <input type="text" name="start_domain" value="{{ $config['start_domain']??'' }}" lay-verify="required" lay-vertype="tips" placeholder="请输入app启动域名" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">客服地址</label>
                    <div class="layui-input-block">
                        <input type="text" name="service_url" value="{{ $config['service_url']??'' }}" placeholder="请输入客服地址" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分享域名</label>
                    <div class="layui-input-block">
                        <input type="text" name="share_domain" value="{{ $config['share_domain']??'' }}" placeholder="请输入分享链接域名" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分享赠送vip天数</label>
                    <div class="layui-input-block">
                        <input type="text" name="share_vip_days" value="{{ $config['share_vip_days']??'' }}" placeholder="请输入分享赠送vip天数" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分享赠送金币数</label>
                    <div class="layui-input-block">
                        <input type="text" name="share_gold_num" value="{{ $config['share_gold_num']??'' }}" placeholder="请输入分享赠送金币数" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分享内容</label>
                    <div class="layui-input-block">
                        <input type="text" name="share_title" value="{{ $config['share_title']??'' }}" placeholder="请输入分享赠送金币数" class="layui-input">
                    </div>
                </div>
                {{--<div class="layui-form-item layui-form-text">
                    <label for="" class="layui-form-label">客服地址</label>
                    <div class="layui-input-block">
                        <textarea class="layui-textarea" name="copyright"  rows="8">{{ $config['客服地址']??'' }}</textarea>
                    </div>
                </div>--}}


                @can('config.site.update')
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button type="submit" class="layui-btn" lay-submit="" lay-filter="formDemo">确 认</button>
                        </div>
                    </div>
                @endcan
            </form>
        </div>
    </div>
@endsection
