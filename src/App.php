<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/14
 * Time: 15:37
 */

namespace MdDoc;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class App extends Command
{
    protected function configure()
    {
        $this
            // 命令的名称
            ->setName('make')
            // 命令描述
            ->setDescription('This command can convert markdown to HTML')
            // markdown文档的地址
            ->addArgument('markdown', InputArgument::REQUIRED, 'path of markdown documents')
            // html文档的地址
            ->addArgument('html', InputArgument::REQUIRED, 'path of html documents')
            // markdown文档的章节文件名称
            ->addArgument('catalog', InputArgument::OPTIONAL, 'catalog file of markdown documents', 'README.md')
            // 站点根目录
            ->addArgument('doc-root', InputArgument::OPTIONAL, 'web site path of html documents', '/');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $markdown_path = $input->getArgument('markdown');
        $html_path = $input->getArgument('html');
        $catalog = $input->getArgument('catalog');
        $doc_root = $input->getArgument('doc-root');
        $builder = new Builder($markdown_path, $html_path, $catalog, $doc_root);
        $output->writeln('starting');
        $builder->run();
        $output->writeln('succeed');
    }
}