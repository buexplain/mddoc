/**
 * 对layui进行全局配置
 */
layui.config({
    base: doc_root+'statics/js/'
}).extend({
    //全文检索
    searchMdDoc: 'searchMdDoc'
});

/**
 * 侧边栏控制按钮
 */
function layui_side_control() {
    document.querySelector('.layui-side-shade').style.display = 'block';
    document.querySelector('.layui-side-control').style.display = 'none';
    document.querySelector('.layui-side').classList.add('show');
    document.querySelector('.layui-side-shade').classList.add('show');
}

/**
 * 侧边栏遮罩
 */
function layui_side_shade() {
    document.querySelector('.layui-side').classList.remove('show');
    document.querySelector('.layui-side-shade').classList.remove('show');
    document.querySelector('.layui-side-control').style.display = 'block';
    setTimeout(function(){
        document.querySelector('.layui-side-shade').style.display = 'none';
    }, 500);
}

/**
 * 渲染侧边栏
 */
function render_catalog_json() {
    layui.use(['element', 'jquery'], function () {
        var element = layui.element;
        var $ = layui.jquery;
        var loop = function (catalog) {
            var html = '';
            var cleanUrl = function (url) {
                if(url === '') {
                    url = 'javascript:;';
                }else {
                    url = url.substr(0, url.length-2)+'html';
                    if(url.substr(0,1) === '.') {
                        url = url.substr(1);
                    }
                    if(url.substr(0,1) === '/') {
                        url = url.substr(1);
                    }
                    url = doc_root+url;
                }
                return url;
            };
            var _loop = function (catalog) {
                var html = '';
                for(var i in catalog) {
                    var v = catalog[i];
                    v['url'] = cleanUrl(v['url']);
                    //判断当前url是否选中
                    if (v['url'] === window.location.pathname) {
                        html += '<dd class="layui-this" id="nav-id-' + v['id'] + '">';
                    } else {
                        html += '<dd class="" id="nav-id-' + v['id'] + '">';
                    }
                    html += '<a href="'+v['url']+'">'+v['title']+'</a>';
                    if (v['child'].length > 0) {
                        html += '<dl class="layui-nav-child">';
                        html += _loop(v['child']);
                        html += '</dl>';
                    }
                    html += '</dd>';
                }
                return html;
            };
            for(var i in catalog) {
                var v = catalog[i];
                v['url'] = cleanUrl(v['url']);
                //判断当前url是否选中
                if(v['url'] === window.location.pathname) {
                    html += '<li class="layui-nav-item layui-this" id="nav-id-'+v['id']+'">';
                }else {
                    html += '<li class="layui-nav-item" id="nav-id-'+v['id']+'">';
                }
                html += '<a href="'+v['url']+'">'+v['title']+'</a>';
                if(v['child'].length > 0) {
                    html += '<dl class="layui-nav-child">';
                    html += _loop(v['child']);
                    html += '</dl>';
                }
                html += '</li>';
            }
            return html;
        };
        $.get(doc_root+'statics/js/catalog.json', function (catalog) {
            if(typeof catalog === 'string') {
                catalog = JSON.parse(catalog);
            }
            document.getElementById('j-catalog').innerHTML = loop(catalog);
            //寻找选中节点的父级，将其设置为选中
            var target = $("#j-catalog").find(".layui-this");
            if(target.length > 0) {
                target.parents('dd').each(function () {
                    $(this).addClass('layui-nav-itemed');
                });
                target.parents('li').each(function () {
                    $(this).addClass('layui-nav-itemed');
                });
            }
            //绘制菜单栏
            element.init('nav', 'lay-filter-catalog');
            //滚动到对应的菜单栏
            if(target.length > 0) {
                target[0].scrollIntoView && target[0].scrollIntoView();
            }
        });
    });
}

/**
 * 渲染返回顶部按钮
 */
function returnTop() {
    layui.use(['jquery'], function () {
        var $ = layui.jquery;
        var dom = $(".layui-body").eq(0);
        var returnTop = $(".return-top").eq(0);
        returnTop.on('click', function () {
            dom.animate({
                scrollTop : 0
            }, 200);
        });
        dom.on('scroll', function(){
            var stop = dom.scrollTop();
            if(stop >= 200) {
                returnTop.show();
            }else {
                returnTop.hide();
            }
        });
    });
}

/**
 * 选中侧边栏
 * @param id
 */
function target_nav_id(id) {
    layui.use(['jquery'], function () {
        var $ = layui.jquery;
        var target = $("#nav-id-"+id);
        if(target.length === 0) {
            return;
        }
        target.find('a').each(function () {
            var href = $(this).attr('href');
            if(href.toLowerCase().indexOf('javascript') === -1) {
                window.location.href = href;
            }
        });
    });
}

/**
 * 搜索
 */
function search(keyword, event) {
    var _search = function (keyword) {
        layui.use(['layer', 'jquery', 'searchMdDoc'], function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var searchMdDoc = layui.searchMdDoc;
            keyword = $.trim(keyword);
            if(keyword === '') {
                layer.tips('请输入关键词', '#j-search',{
                    tips: [2, '#FFB800']
                });
                return;
            }
            var result = searchMdDoc.get(keyword);
            if(result.length === 0) {
                layer.tips('没有找到相关信息', '#j-search',{
                    tips: [2, '#FFB800']
                });
                return;
            }
            var id = 'j-search-result-'+(+new Date());
            layer.open({
                type: 1,
                title:'搜索结果',
                area: ['60%', '60%'], //宽高
                content: '<div class="search-result" id="'+id+'"></div>'
            });
            var div = document.createElement('div');
            for(var i in result) {
                var a = document.createElement('a');
                a.setAttribute('href', "javascript:;");
                a.setAttribute('onclick', "target_nav_id("+result[i]['id']+")");
                var h2 = document.createElement('h2');
                h2.innerText = result[i]['title'];
                var pre = document.createElement('pre');
                pre.innerText = result[i]['context'];
                a.append(h2);
                a.append(pre);
                div.append(a);
            }
            document.getElementById(id).append(div);
        });
    };
    var e = event || window.event;
    if(e && e.keyCode === 13) {
        _search(keyword);
    }
}

/**
 * 优化嵌入的markdown文件的显示
 */
function viewEmbeddedMarkdown() {
    layui.use(['jquery', 'layer'], function () {
        var $ = layui.jquery;
        var document_w = parseInt($(document).width());
        var document_h = parseInt($(document).height());
        $("#j-markdown-body").find("a[href]").each(function (index) {
            var o = $(this);
            var href = o.attr('href');
            //非html文件，非本地文件，则跳过不做处理
            if(href.length < '/.html'.length || href.substr(0, 1) !== '/' || href.substr(href.length-'.html'.length, '.html'.length) !== '.html') {
                return '';
            }
            if(document_w < 750) {
                //如果是手机屏幕，则不做点击弹窗，而是直接设置为新开窗口
                if(o.attr('target') === undefined) {
                    o.attr('target', '_blank');
                }
                return;
            }
            //当前为pc屏幕
            //改href属性为不可跳转
            o.attr('href', 'javascript:;');
            //将当前url保存到data-href属性
            o.attr('data-href', href);
            o.attr('data-id', 'j-viewEmbeddedMarkdown'+index);
            //改为点击后弹出窗口
            o.on('click', function () {
                var o = $(this);
                var href = o.attr('data-href');
                var id = o.attr('data-id');
                //如果存在，则直接打开
                var has = $("#"+id);
                if(has.length > 0) {
                    layer.restore(has.parent().attr('times'));
                    return;
                }
                layer.open({
                    type: 2,
                    id:id,
                    title: o.text(),
                    area: [(document_w > 749 ? 749 : document_w)+'px', (document_h-180)+'px'],
                    fixed: true, //不固定
                    maxmin: true,
                    shade:0,
                    shadeClose: true,
                    content: href,
                    min: function (obj) {
                        //弹出窗口缩小的时候，将其移动到右侧顶部
                        setTimeout(function () {
                            var target_w = 340;
                            var w = parseInt(obj.css('width'));
                            var h = parseInt(obj.css('height'));
                            var l = parseInt(obj.css('left'));
                            var multiple = l%w;
                            var curr_top = 75+multiple*h+l-w*multiple;
                            var curr_left = document_w - target_w-15;
                            obj.css({"width":target_w+'px', 'left':curr_left+'px', 'top':+curr_top+'px'});
                        }, 80);
                    }
                });
            });
        });
    });
}