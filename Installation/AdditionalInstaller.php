<?php

namespace Claroline\AgendaBundle\Installation;

use Claroline\InstallationBundle\Additional\AdditionalInstaller as BaseInstaller;

class AdditionalInstaller extends BaseInstaller
{
    public function preInstall()
    {
        $updater = new Updater\MigrationUpdater();
        $updater->setLogger($this->logger);
        $updater->preInstall($this->container->get('database_connection'));
    }
    public function postInstall()
    {
        $updater = new Updater\MigrationUpdater();
        $updater->setLogger($this->logger);
        $updater->postInstall($this->container->get('claroline.persistence.object_manager'));
    }
}
