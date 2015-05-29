<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Comando para desinstaltar la app
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class UninstallCommand extends ContainerAwareCommand 
{
    protected function configure()
    {
        $this
            ->setName('tec:uninstall')
            ->setDescription('Uninstall the application')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $appName = $this->getContainer()->getParameter('tecnocreaciones_tools.app_name');
        $env = $input->getOption('env');
        $output->writeln(sprintf('<info>Uninstalling <comment>%s</comment> in environment <comment>"%s"</comment>.</info>',$appName,$env));
        $output->writeln('');

        $this
            //->checkStep($input, $output)
            ->setupStep($input, $output)
        ;

        $output->writeln(sprintf('<info><comment>%s</comment> has been successfully uninstalled.</info>',$appName));
    }

    protected function setupStep(InputInterface $input, OutputInterface $output)
    {
        $env = $input->getOption('env');
        $dialog = $this->getHelperSet()->get('dialog');
        
        $output->writeln('<info>Setting down database.</info>');
        $parameters = array();
        $parameters = array_merge($parameters,$input->getArguments());
        foreach ($input->getOptions() as $key => $value) {
            $parameters['--'.$key] = $value;
        }
        $parameters['--force'] = 'true';
        $argvInput = new \Symfony\Component\Console\Input\ArrayInput($parameters);
        try {
            $this->runCommand('doctrine:schema:drop', $argvInput, $output);
            
        } catch (\PDOException $exc) {
            if($exc->getCode() != 1049){
                throw $exc;
            }
            $output->writeln('<info>Schema do not exists.</info>');
        }

        if ($dialog->askConfirmation($output, '<question>Delete database (Y/N)?</question>', false)) {
            $this->runCommand('doctrine:database:drop', $argvInput, $output);
        }
        if($env != 'dev'){
            $output->writeln('<info>Clear cache.</info>');
            $this->runCommand('cache:clear', $input, $output);
        }
        
        $output->writeln('');
        
        return $this;
    }

    private function runCommand($command, InputInterface $input, OutputInterface $output)
    {
        $this
            ->getApplication()
            ->find($command)
            ->run($input, $output)
        ;

        return $this;
    }
}
