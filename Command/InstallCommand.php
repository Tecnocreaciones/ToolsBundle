<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use RuntimeException;

/**
 * Comando para instalar la app
 */
class InstallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tec:install')
            ->setDescription('Install the application')
            ->addOption('reinstall',null,  \Symfony\Component\Console\Input\InputOption::VALUE_NONE,'Reinstall app')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = $input->getOption('env');
        $reinstall = $input->getOption('reinstall');
        $parameters = array();
        $parameters = array_merge($parameters,$input->getArguments());
        foreach ($input->getOptions() as $key => $value) {
            if($key == 'reinstall'){
                continue;
            }
            $parameters['--'.$key] = $value;
        }
        $argvInput = new \Symfony\Component\Console\Input\ArrayInput($parameters);
        if($reinstall){
            $this->runCommand('tec:uninstall', $argvInput, $output);
        }
        $output->writeln(sprintf('<info>Installing App in environment "%s".</info>',$env));
        $output->writeln('');

        $this
            //->checkStep($input, $output)
            ->setupStep($argvInput, $output)
        ;

        $output->writeln('<info>App has been successfully installed.</info>');
    }

    protected function checkStep(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Checking system requirements.</info>');

        $fulfilled = true;

        foreach ($this->getContainer()->get('app.requirements') as $collection) {
            $output->writeln(sprintf('<comment>%s</comment>', $collection->getLabel()));
            foreach ($collection as $requirement) {
                $output->write($requirement->getLabel());
                if ($requirement->isFulfilled()) {
                    $output->writeln(' <info>OK</info>');
                } else {
                    if ($requirement->isRequired()) {
                        $fulfilled = false;
                        $output->writeln(' <error>ERROR</error>');
                        $output->writeln(sprintf('<comment>%s</comment>', $requirement->getHelp()));
                    } else {
                        $output->writeln(' <comment>WARNING</comment>');
                    }
                }
            }
        }

        if (!$fulfilled) {
            throw new RuntimeException('Some system requirements are not fulfilled. Please check output messages and fix them.');
        }

        $output->writeln('');

        return $this;
    }

    protected function setupStep(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Setting up database.</info>');

        $dialog = $this->getHelperSet()->get('dialog');

        $this
            ->runCommand('doctrine:database:create', $input, $output)
            ->runCommand('doctrine:schema:create', $input, $output)
            ->runCommand('assetic:dump', $input, $output)
        ;
        
        if($this->hasCommand('doctrine:fixtures:load')){
            if ($dialog->askConfirmation($output, '<question>Load fixtures (Y/N)?</question>', false)) {
                $this->runCommand('doctrine:fixtures:load', $input, $output);
            }
        }

        $output->writeln('');
        $output->writeln('<info>Administration setup.</info>');
        
        $userManager = $this->getContainer()->get('fos_user.user_manager');
        $user = $userManager->createUser();
        
        $username = $this->getContainer()->getParameter('tecnocreaciones_tools.credentials.username');
        $password = $this->getContainer()->getParameter('tecnocreaciones_tools.credentials.password');
        $email = $this->getContainer()->getParameter('tecnocreaciones_tools.credentials.email');
        $role = $this->getContainer()->getParameter('tecnocreaciones_tools.credentials.role');
         
        $roles = array();
        $roles['role'] = $role;
        if($this->getApplication()->getKernel()->isClassInActiveBundle('Sonata\AdminBundle\SonataAdminBundle')){
            $roles[]= 'ROLE_SONATA_ADMIN';//Rol para acceder al administrador
            $roles[]= 'ROLE_ALLOWED_TO_SWITCH';//Rol para porder probar usuarios
        }
        $username = $dialog->ask($output, sprintf('<question>Username[%s]:</question>',$username),$username);
        $password = $dialog->ask($output, sprintf('<question>Password[%s]:</question>',$password),$password);
        $email = $dialog->ask($output, sprintf('<question>Email[%s]:</question>',$email),$email);
        $role = $dialog->ask($output, sprintf('<question>Role[%s]:</question>',implode(',',$roles)),$role);
        $roles['role'] = $role;
        
        $user->setUsername($username);
        $user->setPlainPassword($password);
        $user->setEmail($email);
        $user->setEnabled(true);
        
        $user->setRoles($roles);
        $userManager->updateUser($user,true);

        $output->writeln('');
        
        $this->runCommand('assets:install', $input, $output);

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
    
    private function hasCommand($name)
    {
        return $this->getApplication()->has($name);
    }
}
