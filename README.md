# mddoc
这个是一个可以将markdown转换成html的包。
创建它的最初目的是用于管理接口文档，当然您也可以用于编写静态博客或其它场景。

## 快速体验

```bash
# 下载包
composer require buexplain/mddoc
# 创建存储html的文件夹
mkdir doc
# 根据markdown文档生成html文件，linux下将 mddoc.bat 替换为 mddoc 
./vendor/bin/mddoc.bat make ./vendor/buexplain/mddoc/tests ./doc
# 启动web服务器
php -S 127.0.0.1:1991 -t ./doc
# 浏览器打开 http://127.0.0.1:1991/index.html 查看效果
```
如果生成的html需要部署到站点的次级目录，则需要指定次级目录，请用 `./vendor/bin/mddoc.bat make -h` 查看使用说明。

## 一个小案例
假设我们需要做一个文档管理，目录与文件结构如下：
```text
E:.
│  composer.json
│  composer.lock
│  make.sh
│
├─doc
│      README.md
│      
└─public
```
`composer require buexplain/mddoc "dev-master"`初始化项目，然后新建相关文件。
其中`make.sh`的内容是：
```bash
#!/bin/bash
rm -rf public/*
chmod u+x ./vendor/bin/mddoc
chmod u+x ./vendor/buexplain/mddoc/bin/mddoc
./vendor/bin/mddoc make ./doc/ ./public README.md ./
```
`README.md`的内容请参考[README.md编写范例](https://github.com/buexplain/mddoc/blob/master/tests/README.md)
然后运行 `./make.sh`，到这里，doc目录下的markdown文件就都转换成public目录里面的html文件了。然后搭建一个web服务器，将其根目录指向public目录即可。


## 注意事项
1. 不支持自定义模板
2. 不支持解析公式，[点击查看支持的语法](https://github.com/buexplain/mddoc/blob/master/tests/test1_one_2.md)
3. 目录列表的每一项目之间不能有空行。[点击查看测试范例](https://github.com/buexplain/mddoc/blob/master/tests/README.md)
4. 如果需要将目录列表划分成多块，必须使用二级标题进行划分。[点击查看测试范例](https://github.com/buexplain/mddoc/blob/master/tests/README.md)
5. 如果在Linux服务器上运行`./vendor/buexplain/mddoc/bin/mddoc`报错误`没有那个文件或目录`，则是因为该文件的编码格式错误
   `vim ./vendor/buexplain/mddoc/bin/mddoc`然后用命令`set ff`可以查看到文件编码是`fileformat=dos`，我们可以用命令`set ff=unix`，
   然后`wq!`改变文件编码。
6. 如果在Linux服务器上运行`./vendor/buexplain/mddoc/bin/mddoc`报错误`/usr/bin/env: php: 没有那个文件或目录`，则是因为当前服务器的环境变量里面没有php命令导致的，配置php环境变量的方式，请自行使用搜索引擎获取答案。   

## 二次开发相关
```bash
# 运行测试数据
cd ./tests && mkdir doc & cd ../ && php bin/mddoc make ./tests ./tests/doc README.md ./tests/doc & echo http://127.0.0.1:1991/tests/doc/index.html && php -S 127.0.0.1:1991 
```

## License
[Apache-2.0](http://www.apache.org/licenses/LICENSE-2.0.html)
