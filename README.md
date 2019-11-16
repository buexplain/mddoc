# mddoc
这个是一个可以将markdown转换成html的包。
创建它的最初目的是用于管理api文档，当然您也可以用户编写静态博客或其它场景。

## 使用方式
```bash
# 下载包
composer require buexplain/mddoc
# 运行测试数据，即可查看效果
cd ./vendor/buexplain/mddoc
php ./bin.php mddoc ./test ./test/doc
cd ./test/doc
php -S 127.0.0.1:1991
```

## 注意事项
1. 不支持自定义模板
2. 不支持解析公式，[点击查看支持的语法](https://github.com/buexplain/mddoc/blob/master/test/test1_one_2.md)