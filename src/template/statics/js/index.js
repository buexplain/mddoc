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