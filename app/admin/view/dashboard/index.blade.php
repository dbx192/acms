<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>数据看板</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/app/admin/component/pear/css/pear.css" />
    <style>
        .top-panel-number {
            font-size: 2.2rem;
            font-weight: bold;
        }

        .top-panel-tips {
            text-align: right;
        }

        .list-item {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .list-item .title {
            color: #333;
            text-decoration: none;
        }

        .list-item .title:hover {
            color: #4e8cff;
            text-decoration: underline;
        }

        .list-item .footer {
            color: #aaa;
            font-size: 0.95em;
        }

        .hot-title {
            font-weight: 600;
            color: #f56e73;
        }
    </style>
</head>

<body class="pear-container">
    <div class="layui-row layui-col-space10">
        <div class="layui-col-xs6 layui-col-md3">
            <div class="layui-card top-panel">
                <div class="layui-card-header">文章总数</div>
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space5">
                        <div class="layui-col-xs8 layui-col-md8 top-panel-number" id="total-article">
                            {{ $total_article }}</div>
                        <div class="layui-col-xs4 layui-col-md4 top-panel-tips"><i
                                class="layui-icon layui-icon-read"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-xs6 layui-col-md3">
            <div class="layui-card top-panel">
                <div class="layui-card-header">总访问量</div>
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space5">
                        <div class="layui-col-xs8 layui-col-md8 top-panel-number" id="total-views">{{ $total_views }}
                        </div>
                        <div class="layui-col-xs4 layui-col-md4 top-panel-tips"><i
                                class="layui-icon layui-icon-chart"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-xs6 layui-col-md3">
            <div class="layui-card top-panel">
                <div class="layui-card-header">评论总数</div>
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space5">
                        <div class="layui-col-xs8 layui-col-md8 top-panel-number" id="total-comment">
                            {{ $total_comment }}</div>
                        <div class="layui-col-xs4 layui-col-md4 top-panel-tips"><i
                                class="layui-icon layui-icon-dialogue"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-xs6 layui-col-md3">
            <div class="layui-card top-panel">
                <div class="layui-card-header">今日新增</div>
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space5">
                        <div class="layui-col-xs8 layui-col-md8 top-panel-number" id="today-article">
                            {{ $today_article }}</div>
                        <div class="layui-col-xs4 layui-col-md4 top-panel-tips"><i
                                class="layui-icon layui-icon-add-1"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-row layui-col-space10">
        <div class="layui-col-md9">
            <div class="layui-card">
                <div class="layui-card-header">访问/评论趋势</div>
                <div class="layui-card-body">
                    <div id="echarts-trend" style="background-color:#fff;min-height:400px;padding:10px"></div>
                </div>
            </div>
            <div class="layui-card">
                <div class="layui-card-header hot-title">热点文章</div>
                <div class="layui-card-body">
                    <ul class="list" id="hot-articles">
                        @foreach ($hot_articles as $a)
                            <li class="list-item">
                                <a class="title" href="/app/acms/article/{{ $a->id }}"
                                    target="_blank">{{ $a->title }}</a>
                                <span class="footer">{{ $a->views }} 浏览</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header hot-title">热点评论</div>
                <div class="layui-card-body">
                    <ul class="list" id="hot-comments">
                        @foreach ($hot_comments as $c)
                            <li class="list-item">
                                <a class="title" href="/app/acms/article/{{ $c->article_id }}"
                                    target="_blank">{{ $c->content }}</a>
                                <span class="footer">{{ $c->likes }} 赞</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script src="/app/admin/component/layui/layui.js"></script>
    <script src="/app/admin/component/pear/pear.js"></script>
    <script>
        layui.use(['layer', 'echarts', 'count'], function() {
            var echarts = layui.echarts,
                count = layui.count;
            count.up("total-article", {
                time: 1200,
                num: {{ $total_article }},
                bit: 0,
                regulator: 50
            });
            count.up("total-views", {
                time: 1200,
                num: {{ $total_views }},
                bit: 0,
                regulator: 50
            });
            count.up("total-comment", {
                time: 1200,
                num: {{ $total_comment }},
                bit: 0,
                regulator: 50
            });
            count.up("today-article", {
                time: 1200,
                num: {{ $today_article }},
                bit: 0,
                regulator: 50
            });
            var chart = echarts.init(document.getElementById('echarts-trend'));
            var option = {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['访问量', '评论数']
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: @json($trend_dates)
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                        name: '访问量',
                        type: 'line',
                        smooth: true,
                        data: @json($trend_views),
                        areaStyle: {
                            color: '#e0eafc'
                        }
                    },
                    {
                        name: '评论数',
                        type: 'line',
                        smooth: true,
                        data: @json($trend_comments),
                        areaStyle: {
                            color: '#f093fb'
                        }
                    }
                ]
            };
            chart.setOption(option);
            window.onresize = function() {
                chart.resize();
            };
        });
    </script>
</body>

</html>
