# test1一级标题1

[测试嵌入兄弟级markdown文件](./test1_one_2.md "./test1_one_2.md")

[测试嵌入兄弟级markdown文件](test1_one_2.md "test1_one_2.md")

[测试嵌入其它目录markdown文件](test2/one_1.md "test2/one_1.md")

[测试嵌入其它目录markdown文件](./test2/one_1.md "./test2/one_1.md")

**请求URL：** 
- ` https://api.xxx.top/getJoke `
  
**请求方式：**
- GET 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|page |  是  |    int   |    分页   |
|count |  是  |    int   |    总页数   |
|type |  是  |    string   |    段子类型   |

**示例代码**
```php
require_once 'Zend/Uri/Http.php';

namespace Location\Web;

interface Factory
{
    static function _factory();
}

abstract class URI extends BaseURI implements Factory
{
    abstract function test();

    public static $st1 = 1;
    const ME = "Yo";
    var $list = NULL;
    private $var;

    /**
     * Returns a URI
     *
     * @return URI
     */
    static public function _factory($stats = array(), $uri = 'http')
    {
        echo __METHOD__;
        $uri = explode(':', $uri, 0b10);
        $schemeSpecific = isset($uri[1]) ? $uri[1] : '';
        $desc = 'Multi
line description';

        // Security check
        if (!ctype_alnum($scheme)) {
            throw new Zend_Uri_Exception('Illegal scheme');
        }

        $this->var = 0 - self::$st;
        $this->list = list(Array("1"=> 2, 2=>self::ME, 3 => \Location\Web\URI::class));

        return [
            'uri'   => $uri,
            'value' => null,
        ];
    }
}

echo URI::ME . URI::$st1;

__halt_compiler () ; datahere
datahere
datahere */
datahere
```

**返回示例**

```json
{
  "code": 200,
  "message": "成功!",
  "result": [
    {
      "sid": "29871101",
      "text": "你认我做大哥，我教你梳中分！",
      "type": "video"
    },
    {
      "sid": "29928992",
      "text": "大部分都是这么拍的，可是也有不是这么样拍的吧",
      "type": "video"
    }
  ]
}

```

