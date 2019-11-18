# mddoc
这个是一个可以将markdown转换成html的包。
创建它的最初目的是用于管理接口文档，当然您也可以用于编写静态博客或其它场景。

## 使用方式
```bash
# 下载包
composer require buexplain/mddoc "dev-master"
# 运行测试数据
mkdir doc
./vendor/bin/mddoc.bat make ./vendor/buexplain/mddoc/test ./doc
php -S 127.0.0.1:1991 -t ./doc
# 浏览器打开 http://127.0.0.1:1991/doc/index.html 查看效果
```
1. 如果是linux下composer安装的，则运行如下命令进行文档生成：
 `./vendor/bin/mddoc make ./vendor/buexplain/mddoc/test ./doc`
2. 如果生成后的文档需要部署到站点的次级目录下，则需要指定次级目录名称，比如下面的命令：
 `./vendor/bin/mddoc make ./vendor/buexplain/mddoc/test ./doc README.md doc`
 然后启动web服务器`php -S 127.0.0.1:1991`，然后进入`http://127.0.0.1:1991/doc/index.html`即可查看效果
3. 更多使用方式请查看：`./vendor/bin/mddoc.bat make -h`

## 注意事项
1. 不支持自定义模板
2. 不支持解析公式，[点击查看支持的语法](https://github.com/buexplain/mddoc/blob/master/test/test1_one_2.md)

## 二次开发相关
```bash
# 运行测试数据
cd ./test && mkdir doc & cd ../ && php bin/mddoc make ./test ./test/doc && php -S 127.0.0.1:1991 -t ./test/doc 
```

## License
[Apache-2.0](http://www.apache.org/licenses/LICENSE-2.0.html)
