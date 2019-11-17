layui.define(['jquery'], function(exports) {
    var $ = layui.jquery;
    var search = <?php echo json_encode($search);?>;
    var get = function(keyword) {
        var result = [];
        for(var i in search) {
            var data = search[i];
            //检索文档内容
            var index = data.content.indexOf(keyword);
            if(index !== -1) {
                var start = index - 50;
                var end = keyword.length + 100;
                if(start<0) {
                    start = 0;
                }
                result.push({
                    id:data.id,
                    title:data.title,
                    context:$.trim(data.content.substr(start, end))
                });
            }else if(data.title.indexOf(keyword) !== -1) {
                //检索标题
                result.push({
                    id:data.id,
                    title:data.title,
                    context:''
                });
            }
        }
        return result;
    };
    exports('searchMdDoc', {get:get});
});
