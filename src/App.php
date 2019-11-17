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
            // 运行 "php console_command list" 时的简短描述
            ->setDescription('This command can convert markdown to HTML')
            // 配置一个参数
            ->addArgument('markdown', InputArgument::REQUIRED, 'path of markdown documents')
            ->addArgument('html', InputArgument::REQUIRED, 'path of html documents')
            // 配置一个可选参数
            ->addArgument('catalog', InputArgument::OPTIONAL, 'catalog file of markdown documents', 'README.md');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $markdown_path = $input->getArgument('markdown');
        $html_path = $input->getArgument('html');
        $catalog = $input->getArgument('catalog');
        $builder = new Builder($markdown_path, $html_path, $catalog);
        $output->writeln('starting');
        $builder->run();
        $output->writeln('succeed');
    }
}