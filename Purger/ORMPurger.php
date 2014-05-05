<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Purger;

use Doctrine\ORM\EntityManager;

/**
 * Class responsible for purging databases of data before reloading data fixtures.
 *
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class ORMPurger extends \Doctrine\Common\DataFixtures\Purger\ORMPurger implements \Doctrine\Common\DataFixtures\Purger\PurgerInterface, \Symfony\Component\DependencyInjection\ContainerAwareInterface
{
    /**
     * Instance used for persistence.
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * If the purge should be done through DELETE or TRUNCATE statements
     *
     * @var int
     */
    private $purgeMode = self::PURGE_MODE_DELETE;
    
    /**
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * Construct new purger instance.
     *
     * @param EntityManager $em EntityManager instance used for persistence.
     */
    public function __construct(EntityManager $em = null)
    {
        $this->em = $em;
    }

    /** @inheritDoc */
    public function purge()
    {
        // Get platform parameters
        $platform = $this->em->getConnection()->getDatabasePlatform();

        
        $eventDispatcher = $this->container->get('event_dispatcher');
        $event = new \Tecnocreaciones\Bundle\ToolsBundle\Event\PurgeEvent($this->em);
        $eventDispatcher->dispatch(\Tecnocreaciones\Bundle\ToolsBundle\ToolsEvents::PRE_PURGER,$event);
        
        foreach($event->getSqlConditions() as $condition) {
            $condition = preg_replace("/DELETE FROM/", '', $condition);
            if ($this->purgeMode === self::PURGE_MODE_DELETE) {
                $this->em->getConnection()->executeUpdate("DELETE FROM " . $condition);
            } else {
                $this->em->getConnection()->executeUpdate($platform->getTruncateTableSQL($condition, true));
            }
        }
        
    }
    
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        $this->container = $container;
    }
    
}
