<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:41:"../template/default/order/order-list.html";i:1568085409;s:66:"/www/wwwroot/epay.3ii.cn/template/default/common/admin_header.html";i:1568086704;s:66:"/www/wwwroot/epay.3ii.cn/template/default/common/admin_footer.html";i:1568085444;}*/ ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo $title; ?> - <?php echo get_sys('site_name'); ?></title>
	<meta name="renderer" content="webkit|ie-comp|ie-stand">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
	<meta http-equiv="Cache-Control" content="no-siteapp" />

	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="/static/admin/css/font.css">
	<link rel="stylesheet" href="/static/admin/css/xadmin.css">
	<link rel="stylesheet" href="/static/admin/css/public.css">
</head>
<body class="layui-anim layui-anim-up">
<div class="x-nav">
      <span class="layui-breadcrumb">

        <a>
          <cite><?php echo $title; ?></cite></a>
      </span>
    <a class="layui-btn layui-btn-primary layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:38px">ဂ</i></a>
</div>
<div class="x-body">
    <div class="layui-row">
        <div class="layui-form layui-col-md12 x-so">
            <input class="layui-input" placeholder="开始日" name="start" id="start">
            <input class="layui-input" placeholder="截止日" name="end" id="end">
            <input type="text" id="keyword" name="keyword"  placeholder="请输入订单编号" autocomplete="off" class="layui-input">
            <button class="layui-btn sreach"><i class="layui-icon">&#xe615;</i>搜素</button>
        </div>
    </div>
    <xblock>
        <button class="layui-btn layui-btn-danger" id="delAll">删除所有过期订单</button>
        <span class="x-right" style="line-height:40px">共有数据：<?php echo $count; ?> 条</span>
    </xblock>
    <table class="layui-table" id="list" lay-filter="list"></table>
</div>
<link rel="stylesheet" href="/static/lib/layui/css/layui.css">
<link rel="stylesheet" href="/static/lib/layui/css/layui.mobile.css">
<script type="text/javascript" src="/static/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/static/admin/js/xadmin.js"></script>
<script type="text/javascript" src="/static/lib/layui/layui.js"></script>
<script type="text/html" id="action">
    {{# if(d.state==2){ }}
    <a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="bd">补单</a>
    {{# } }}
    <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="info">详情</a>
    <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="del">删除</a>
</script>
<script>
    layui.use(['table','form','laydate'], function() {
        var table = layui.table,form = layui.form,$ = layui.jquery, laydate = layui.laydate;
        laydate.render({
            elem: '#start' //指定元素
        });
        laydate.render({
            elem: '#end' //指定元素
        });
        var tableIn = table.render({
            id: 'agent',
            elem: '#list',
            url: '<?php echo url("/order"); ?>',
            method: 'post',
            cellMinWidth: 80,
            totalRow: true,
            page:true,
            cols: [[
                {field: 'id', title: 'ID', width: 80, fixed: true},
                {field: 'order_id', title: '云端订单号', width:200, align: 'center'},
                {field: 'pay_id', title: '商户订单号', align: 'center'},
                {field: 'type', title: '支付方式', align: 'center'},
                {field: 'price', align: 'center',  title: '订单金额', totalRow: true},
                {field: 'really_price', align: 'center',  title: '实际支付金额', totalRow: true},
                {field: 'create_date', align: 'center', width:180, title: '创建时间'},
                {field: 'state', align: 'center', width:160, title: '状态',templet: function(d){
                        if (d.state=="2"){
                            return '<span class="layui-btn layui-btn-xs layui-btn-danger">通知失败</span>';
                        }else if (d.state=="1"){
                            return '<span class="layui-btn layui-btn-xs layui-btn-normal">订单完成</span>';
                        }else if (d.state=="0"){
                            return '<span class="layui-btn layui-btn-xs">待支付</span>';
                        }else if (d.state=="-1"){
                            return '<span class="layui-btn layui-btn-xs layui-btn-warm">过期订单</span>';
                        }
                    }},
                {width: 160, align: 'center', toolbar: '#action'}
            ]],
            limit:10
        });
        $('.search').on('click', function () {
            var keyword = $('#keyword').val();
            var logmin = $('#start').val();
            var logmax = $('#end').val();

            tableIn.reload({ page: {page: 1}, where: {keyword: keyword,logmin:logmin,logmax:logmax}});
        });
        table.on('tool(list)', function(obj) {
            var data = obj.data;
            if (obj.event === 'info'){
                var out = "<p>创建时间："+data.create_date+"</p>";
                out += "<p>支付时间："+formatDate(data.pay_date)+"</p>";
                out += "<p>关闭时间："+formatDate(data.close_date)+"</p>";
                out += "<p>自定义参数："+data.param+"</p>";
                layer.open({
                    offset: 'auto',
                    title:'订单详情',
                    content: out,
                })
            }else if(obj.event === 'bd'){
                layer.confirm('确定要补单吗？该操作将会将该订单标记为已支付，并向您的服务器发送订单完成通知', function(index){
                    var loading = layer.load(1, {shade: [0.1, '#fff']});
                    $.post("<?php echo url('order/setBd'); ?>",{id:data.id},function(res){
                        layer.close(loading);
                        if(res.code==1){
                            layer.msg(res.msg,{time:1000,icon:1});
                            tableIn.reload();
                        }else if(res.code==-2){
                            layer.confirm('补单失败，异步通知返回错误，是否查看通知返回数据？', {
                                btn: ['查看','取消'] //按钮
                            }, function(){
                                alert(res.data);
                            }, function(){

                            });
                        }else{
                            layer.msg(res.msg,{time:1000,icon:2});
                        }
                    });
                    layer.close(index);
                });
            }else if(obj.event === 'del'){
                layer.confirm('您确定要删除该订单吗？', function(index){
                    var loading = layer.load(1, {shade: [0.1, '#fff']});
                    $.post("<?php echo url('order/delOrder'); ?>",{id:data.id},function(res){
                        layer.close(loading);
                        if(res.code>0){
                            layer.msg(res.msg,{time:1000,icon:1});
                            tableIn.reload();
                        }else{
                            layer.msg('操作失败！',{time:1000,icon:2});
                        }
                    });
                    layer.close(index);
                });
            }
        });
    });
    function formatDate(now) {
        if (now=="0"){
            return "无";
        }

        now = new Date(now*1000);
        return now.getFullYear()
            + "-" + (now.getMonth()>8?(now.getMonth()+1):"0"+(now.getMonth()+1))
            + "-" + (now.getDate()>9?now.getDate():"0"+now.getDate())
            + " " + (now.getHours()>9?now.getHours():"0"+now.getHours())
            + ":" + (now.getMinutes()>9?now.getMinutes():"0"+now.getMinutes())
            + ":" + (now.getSeconds()>9?now.getSeconds():"0"+now.getSeconds());

    }
    /*用户-停用*/
    function member_stop(obj,id){
        layer.confirm('确认要停用吗？',function(index){

            if($(obj).attr('title')=='启用'){

                //发异步把用户状态进行更改
                $(obj).attr('title','停用')
                $(obj).find('i').html('&#xe62f;');

                $(obj).parents("tr").find(".td-status").find('span').addClass('layui-btn-disabled').html('已停用');
                layer.msg('已停用!',{icon: 5,time:1000});

            }else{
                $(obj).attr('title','启用')
                $(obj).find('i').html('&#xe601;');

                $(obj).parents("tr").find(".td-status").find('span').removeClass('layui-btn-disabled').html('已启用');
                layer.msg('已启用!',{icon: 5,time:1000});
            }

        });
    }

    /*用户-删除*/
    function member_del(obj,id){
        layer.confirm('确认要删除吗？',function(index){
            //发异步删除数据
            $(obj).parents("tr").remove();
            layer.msg('已删除!',{icon:1,time:1000});
        });
    }



    function delAll (argument) {

        var data = tableCheck.getData();

        layer.confirm('确认要删除吗？'+data,function(index){
            //捉到所有被选中的，发异步进行删除
            layer.msg('删除成功', {icon: 1});
            $(".layui-form-checked").not('.header').parents('tr').remove();
        });
    }
</script>
</body>

</html>