<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/14
 * Time: 16:05
 */

namespace MdDoc;

use InvalidArgumentException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * 将markdown解析成html
 * @package MdDoc
 */
class Builder
{
    /**
     * markdown文档地址
     * @var string
     */
    protected $markdown_path;
    /**
     * html文档地址
     * @var string
     */
    protected $html_path;
    /**
     * markdown文档的目录
     * @var string
     */
    protected $catalog_file;
    /**
     * 生成的html文档部署为站点的时候所在的站点目录
     * @var string
     */
    protected $doc_root = '/';
    /**
     * @var Filesystem
     */
    protected $fileSystem;
    /**
     * @var CatalogParser
     */
    protected $catalogParser;
    /**
     * @var ArticleParser
     */
    protected $articleParser;
    /**
     * 文档模板
     * @var string
     */
    protected $templateArticle;
    /**
     * 搜索模板
     * @var string
     */
    protected $templateSearch;
    /**
     * 全文索引的数据
     * @var array
     */
    protected $search = [];

    public function __construct($markdown_path, $html_path, $catalog_file, $doc_root='/')
    {
        if(!is_dir($markdown_path)) {
            throw new InvalidArgumentException('not found markdown path: '.$markdown_path);
        }
        if(!is_dir($html_path)) {
            throw new InvalidArgumentException('not found html path: '.$html_path);
        }
        $this->markdown_path = rtrim(str_replace('\\', '/', realpath($markdown_path)), '/');
        $this->html_path = rtrim(str_replace('\\', '/', realpath($html_path)), '/');
        $this->catalog_file = $this->markdown_path.'/'.$catalog_file;
        if(!is_file($this->catalog_file)) {
            throw new InvalidArgumentException('not found catalog file: '.$this->catalog_file);
        }
        $doc_root = trim($doc_root, '/');
        if($doc_root == '') {
            $this->doc_root = '/';
        }else{
            $this->doc_root = "/{$doc_root}/";
        }
        $this->fileSystem = new Filesystem();
        $this->catalogParser = new CatalogParser();
        $this->articleParser = new ArticleParser();
        $this->templateArticle = __DIR__.'/template/article.php';
        $this->templateSearch = __DIR__.'/template/search.php';
    }

    /**
     * 渲染模板
     * @param $template
     * @param $data
     * @return false|string
     */
    protected function renderTemplate($template, $data)
    {
        ob_start();
        extract($data);
        include $template;
        return ob_get_clean();
    }

    /**
     * 初始化静态资源
     */
    protected function copyStatic()
    {
        $this->fileSystem->remove($this->html_path.'/*');
        $this->fileSystem->mkdir($this->html_path);
        $this->fileSystem->mirror(__DIR__.'/template/statics', $this->html_path.'/statics');
    }

    /**
     * 渲染章节
     */
    public function renderCatalog()
    {
        //读取章节内容
        $content = file_get_contents($this->catalog_file);
        //解析章节
        $this->catalogParser->parse($content);
        //渲染章节内容
        $content = preg_replace('/#.*(\n|\r\n)/i', '', $content, 1);
        $html = $this->renderTemplate($this->templateArticle, [
            'catalog_title'=>$this->catalogParser->title,
            'title'=>$this->catalogParser->title,
            'content'=>str_replace('.md', '.html', $this->articleParser->parse($content)),
            'doc_root'=>$this->doc_root,
        ]);
        //保存为html
        $savePath = $this->html_path.'/index.html';
        $this->fileSystem->mkdir(dirname($savePath));
        $this->fileSystem->dumpFile($savePath, $html);
        //保存章节数据到json
        $savePath = $this->html_path.'/statics/js/catalog.json';
        $this->fileSystem->mkdir(dirname($savePath));
        $this->fileSystem->dumpFile($savePath, json_encode($this->catalogParser->tree));
    }

    /**
     * 根据章节渲染文档
     * @param null $catalog
     */
    protected function renderArticle($catalog=null)
    {
        if(is_null($catalog)) {
            $catalog = $this->catalogParser->tree;
        }
        foreach ($catalog as $value) {
            if(!empty($value['child'])) {
                $this->renderArticle($value['child']);
            }elseif($value['url'] !== '') {
                //渲染markdown文件
                $value['url'] = ltrim($value['url'], '. /');
                $markdownFile = $this->markdown_path.'/'.$value['url'];
                $htmlSavePath = $this->html_path.'/'.substr($value['url'], 0, -2).'html';
                if(!file_exists($markdownFile)) {
                    throw new ParserException("not found file: {$markdownFile}");
                }
                //读取markdown内容
                $markdownContent = file_get_contents($markdownFile);
                //存储到全文索引中
                $this->search[] = [
                    'id'=>$value['id'],
                    'title'=>$value['title'],
                    'content'=>$markdownContent,
                ];
                //迁移markdown文件中的附件，以及支持markdown中嵌入$this->markdown_path目录下的其它markdown文件
                preg_match_all('/!?\[.+?\]\((.+?)(\s+"(.*?)")?\)/', $markdownContent, $matches);
                if(isset($matches[1]) && !empty($matches[1])) {
                    foreach ($matches[1] as $key=>$attach) {
                        if(strlen($attach) == 0 || $attach[0] == '/') {
                            continue;
                        }
                        $cleanAttach = substr($attach,0,2) == './' ? substr($attach, 2) : $attach;
                        //以当前markdown文件所在目录为根目录，寻找附件文件地址
                        $attachFile = realpath(dirname($markdownFile).'/'.$cleanAttach);
                        if(!file_exists($attachFile)) {
                            continue;
                        }
                        //判断当前文件是否为markdown文件，如果是，则转成转换后的html文件地址
                        if (strtolower(pathinfo($attach, PATHINFO_EXTENSION)) === 'md') {
                            //构造成html地址
                            $attachFile = substr($this->doc_root.ltrim(str_replace('\\','/', substr($attachFile, strlen($this->markdown_path))), '/'), 0, -2).'html';
                            //替换markdown中的地址
                            if(isset($matches[0][$key])) {
                                $search = $matches[0][$key];
                                $replace = str_replace($attach, $attachFile, $matches[0][$key]);
                            }else{
                                $search = $attach;
                                $replace = $attachFile;
                            }
                            $markdownContent = str_replace($search, $replace, $markdownContent);
                            //跳过，不做拷贝
                            continue;
                        }
                        //保存路径，以当前markdown转html后的保存路径为根目录
                        $attachSavePath = dirname($htmlSavePath).'/'.$cleanAttach;
                        //保存文件
                        $this->fileSystem->mkdir(dirname($attachSavePath));
                        $this->fileSystem->copy($attachFile, $attachSavePath);
                    }
                }
                //渲染成html
                $html = $this->renderTemplate($this->templateArticle, [
                    'catalog_title'=>$this->catalogParser->title,
                    'title'=>$value['title'],
                    'content'=>$this->articleParser->parse($markdownContent),
                    'doc_root'=>$this->doc_root,
                ]);
                $this->fileSystem->mkdir(dirname($htmlSavePath));
                $this->fileSystem->dumpFile($htmlSavePath, $html);
            }else{
                //存储到全文索引中
                $this->search[] = [
                    'id'=>$value['id'],
                    'title'=>$value['title'],
                    'content'=>'',
                ];
            }
        }
    }

    /**
     * 渲染全文索引
     */
    public function renderSearch()
    {
        $html = $this->renderTemplate($this->templateSearch, [
            'search'=>$this->search,
        ]);
        //保存为js
        $savePath = $this->html_path.'/statics/js/searchMdDoc.js';
        $this->fileSystem->mkdir(dirname($savePath));
        $this->fileSystem->dumpFile($savePath, $html);
    }

    public function run()
    {
        ini_set('memory_limit', '512M');
        $this->copyStatic();
        $this->renderCatalog();
        $this->renderArticle();
        $this->renderSearch();
    }
}