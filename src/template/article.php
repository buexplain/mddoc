<!DOCTYPE html>
<html lang="zh">
<?php
/**
 * @var $title string
 * @var $doc_root string
 * @var $catalog_title string
 * @var $content
 */
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php echo $title;?></title>
    <link rel="stylesheet" href="<?php echo $doc_root;?>statics/plugin/layui/css/layui.css">
    <link rel="stylesheet" href="<?php echo $doc_root;?>statics/css/style.css">
    <link rel="stylesheet" href="<?php echo $doc_root;?>statics/plugin/github-markdown/github-markdown.css">
    <link rel="stylesheet" href="<?php echo $doc_root;?>statics/plugin/highlight/github.css">
</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <div class="layui-logo">
            <a href="<?php echo $doc_root;?>index.html" title="首页"><?php echo $catalog_title;?></a>
        </div>
        <!--搜索框-->
        <div class="search">
            <input type="text" autocomplete="off" id="j-search" class="layui-input" placeholder="请输入关键词，按enter搜索" onkeydown="search(this.value, event)">
        </div>
    </div>
    <!-- 左侧导航区域 -->
    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <ul class="layui-nav layui-nav-tree" id="j-catalog" lay-filter="lay-filter-catalog"></ul>
        </div>
    </div>
    <!-- 小屏幕遮罩 -->
    <div class="layui-side-shade" onclick="layui_side_shade()"></div>
    <!-- 小屏幕下显示菜单的按钮 -->
    <div class="layui-side-control" onclick="layui_side_control()">
        <i class="layui-icon layui-icon-spread-left"></i>
    </div>
    <!-- 内容主体区域 -->
    <div class="layui-body">
        <div style="padding: 15px;">
            <article class="markdown-body">
                <?php echo $content;?>
            </article>
        </div>
    </div>
</div>
<!--返回顶部按钮-->
<li class="layui-icon layui-icon-top return-top"></li>
<script>
    //当前文档部署到具体站点时候的目录
    var doc_root = '<?php echo $doc_root;?>';
</script>
<script src="<?php echo $doc_root;?>statics/plugin/layui/layui.js"></script>
<script src="<?php echo $doc_root;?>statics/plugin/highlight/highlight.min.js"></script>
<script src="<?php echo $doc_root;?>statics/js/index.js"></script>
<script>
//高亮代码
hljs.initHighlightingOnLoad();
layui.use(['jquery'], function () {
   var $ = layui.jquery;
   $(function () {
       //渲染目录
       render_catalog_json();
       //渲染返回顶部的按钮
       returnTop();
   });
});
</script>
</body>
</html>