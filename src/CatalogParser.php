<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/14
 * Time: 17:47
 */

namespace MdDoc;

/**
 * 目录解析类
 * Class CatalogParser
 * @package MdDoc
 */
class CatalogParser extends Parser
{
    /**
     * 一级标题
     * @var string
     */
    public $title;

    /**
     * 介绍
     * @var
     */
    public $introduction;

    /**
     * 章节数据
     * @var array
     */
    public $tree = [];

    /**
     * 是否为二级标题块
     * @var bool
     */
    protected $levelTwoTitleBlock = true;

    /**
     * 自增id
     * @var int
     */
    protected $id = 0;

    /**
     * 分析文本并返回从中获取的章节列表
     * @param string $text
     */
    public function analyze($text)
    {
        $this->parse($text);
    }

    /**
     * 解析标题
     */
    protected function renderHeadline($block)
    {
        if($block['level'] === 1) {
            $this->title = $this->renderAbsy($block['content']);
        }elseif($block['level'] === 2) {
            $this->tree[] = [
                'id'=>++$this->id,
                'title'=>$this->renderAbsy($block['content']),
                'url'=>'',
                'child'=>[],
            ];
            $this->levelTwoTitleBlock = true;
        }
        return parent::renderHeadline($block);
    }

    /**
     * 解析段落
     */
    protected function renderParagraph($block)
    {
        if (count($this->tree) == 0) {
            //解析介绍内容
            $this->introduction .= $this->renderAbsy($block['content']);
        }
        return parent::renderParagraph($block);
    }

    /**
     * 解析列表
     */
    protected function renderList($block)
    {
        if($block[0] === 'list' && $this->levelTwoTitleBlock) {
            $counter = count($this->tree);
            if($counter == 0) {
                //没有二级标题，直接全是list的情况
                $this->tree = $this->parseTree($block['items']);
            }else{
                //有二级标题，将二级标题下面的list解析成一棵树
                $this->tree[$counter-1]['child'] = $this->parseTree($block['items']);
            }
            //将二级标题块锁定，等待下一个二级标题块
            $this->levelTwoTitleBlock = false;
        }
        return parent::renderList($block);
    }

    /**
     * 将列表解析成一颗树
     * @param $blockItems
     * @return array
     */
    protected function parseTree($blockItems)
    {
        $tree = [];
        foreach ($blockItems as $item) {
            if($item[0][0] === 'link') {
                $tree[] = [
                    'id'=>++$this->id,
                    'title'=>$item[0]['text'][0][1],
                    'url'=>$item[0]['url'],
                    'child'=>[],
                ];
            }elseif($item[0][0] === 'text') {
                $tmp = [
                    'id'=>++$this->id,
                    'title'=>$item[0][1],
                    'url'=>'',
                    'child'=>[],
                ];
                if($item[1][0] === 'list') {
                    $tmp['child'] = $this->parseTree($item[1]['items']);
                }
                $tree[] = $tmp;
            }
        }
        return $tree;
    }
}