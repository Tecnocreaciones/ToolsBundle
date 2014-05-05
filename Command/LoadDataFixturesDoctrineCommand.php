<?php

/*
 * This file is part of the Doctrine Fixtures Bundle
 *
 * The code was originally distributed inside the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Doctrine Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Command;

use Doctrine\Bundle\FixturesBundle\Command\LoadDataFixturesDoctrineCommand as BaseLoadDataFixturesDoctrineCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tecnocreaciones\Bundle\ToolsBundle\Purger\ORMPurger;

/**
 * Load data fixtures from bundles.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jonathan H. Wage <jonwage@gmail.com>
 */
class LoadDataFixturesDoctrineCommand extends BaseLoadDataFixturesDoctrineCommand implements \Symfony\Component\DependencyInjection\ContainerAwareInterface
{
    /**
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;
    
    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription('Load data fixtures to your database.(Tecnocreaciones)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if(!$input->getOption('append')){
            /** @var $doctrine \Doctrine\Common\Persistence\ManagerRegistry */
            $doctrine = $this->getContainer()->get('doctrine');
            $em = $doctrine->getManager($input->getOption('em'));
            $purger = new ORMPurger($em);
            $purger->setContainer($this->container);
            $purger->setPurgeMode($input->getOption('purge-with-truncate') ? ORMPurger::PURGE_MODE_TRUNCATE : ORMPurger::PURGE_MODE_DELETE);
            $purger->purge();
        }
        
        parent::execute($input, $output);
    }
    
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        $this->container = $container;
    }
}