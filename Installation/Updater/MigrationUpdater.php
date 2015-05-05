<?php
/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\AgendaBundle\Installation\Updater;

use Claroline\CoreBundle\Persistence\ObjectManager;
use Claroline\InstallationBundle\Updater\Updater;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Version;

class MigrationUpdater extends Updater
{
    public function preInstall(Connection $databaseConnection)
    {
        if ($databaseConnection->getSchemaManager()->tablesExist(['claro_event'])) {
            $this->log('Found existing database schema: skipping install migration...');
            $config = new Configuration($databaseConnection);
            $config->setMigrationsTableName('doctrine_clarolineagendabundle_versions');
            $config->setMigrationsNamespace('claro_agenda'); // required but useless
            $config->setMigrationsDirectory('claro_agenda'); // idem
            $version = new Version($config, '20150429110105', 'stdClass');
            $version->markMigrated();
         }
    }

    public function postInstall(ObjectManager $objectManager)
    {
        /** @var \Claroline\CoreBundle\Repository\ToolRepository $toolRepository */
        $toolRepository = $objectManager->getRepository('ClarolineCoreBundle:Tool\Tool');

        /** @var \Claroline\CoreBundle\Repository\PluginRepository $pluginRepository */
        $pluginRepository = $objectManager->getRepository('ClarolineCoreBundle:Plugin');

        $agendaToolName = 'agenda';

        /** @var \Claroline\CoreBundle\Entity\Plugin $agendaPlugin */
        $agendaPlugin = $pluginRepository->createQueryBuilder('plugin')
            ->where('plugin.vendorName = :agendaVendorName')
            ->andWhere('plugin.bundleName = :agendaShortName')
            ->setParameters(['agendaVendorName' => 'Claroline', 'agendaShortName' => 'AgendaBundle'])
            ->getQuery()
            ->getSingleResult();

        /** @var \Claroline\CoreBundle\Entity\Tool\Tool $agendaTool */
        $agendaTool = $toolRepository->createQueryBuilder('tool')
            ->where('tool.name = :agendaToolName')
            ->andWhere('tool.plugin = :agendaPlugin')
            ->setParameter('agendaToolName', $agendaToolName)
            ->setParameter('agendaPlugin', $agendaPlugin)
            ->getQuery()
            ->getSingleResult();

        /** @var \Claroline\CoreBundle\Entity\Tool\Tool $agendaCoreTool */
        $agendaCoreTool = $toolRepository->createQueryBuilder('tool')
            ->where('tool.name = :agendaToolName')
            ->andWhere('tool.plugin is NULL')
            ->setParameter('agendaToolName', $agendaToolName)
            ->getQuery()
            ->getSingleResult();

        $objectManager->remove($agendaTool);
        $objectManager->forceFlush();

        $agendaCoreTool->setPlugin($agendaPlugin);
        $objectManager->persist($agendaCoreTool);
        $objectManager->forceFlush();
    }
}
