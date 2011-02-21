<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Util\Filesystem;
use Symfony\Bundle\FrameworkBundle\Util\Mustache;

use Installer\Bundle\Bundle;
use Installer\Bundle\BundleCollection;
use Installer\Nspace\Nspace;
use Installer\Nspace\NspaceCollection;
use Installer\Prefix\Prefix;
use Installer\Prefix\PrefixCollection;
use Installer\Repository\Repository;
use Installer\Repository\RepositoryCollection;

/**
 * GenerateProjectCommand
 *
 * @author      Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @author      Clément Jobeili <clement.jobeili@gmail.com>
 */
class GenerateProjectCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('app', InputArgument::REQUIRED, 'Application name'),
                new InputArgument('vendor', InputArgument::REQUIRED, 'Vendor name'),
                new InputArgument('path', InputArgument::REQUIRED, 'Directory name (path)'),
            ))
            ->addOption('controller', null, InputOption::VALUE_OPTIONAL, 'Your first controller name', 'Main')
            ->addOption('protocol', null, InputOption::VALUE_OPTIONAL, 'git or http', 'git')
            ->addOption('session-start', null, InputOption::VALUE_NONE, 'To start session automatically')
            ->addOption('session-name', null, InputOption::VALUE_OPTIONAL, 'Session name', 'symfony')
            ->addOption('orm', null, InputOption::VALUE_OPTIONAL, 'doctrine or propel', null)
            ->addOption('odm', null, InputOption::VALUE_OPTIONAL, 'mongodb', null)
            ->addOption('assetic', null, InputOption::VALUE_NONE, 'Enable assetic')
            ->addOption('swiftmailer', null, InputOption::VALUE_NONE, 'Enable swiftmailer')
            ->addOption('doctrine-migration', null, InputOption::VALUE_NONE, 'Enable doctrine migration')
            ->addOption('doctrine-fixtures', null, InputOption::VALUE_NONE, 'Enable doctrine fixtures')
            ->addOption('template-engine', null, InputOption::VALUE_OPTIONAL, 'twig or php', 'twig')
            ->addOption('force-delete', null, InputOption::VALUE_NONE, 'Force re-generation of project')
            ->setName('generate:project')
            ->setDescription('Generate a Symfony2 project')
            ->setHelp(<<<EOT
The <info>generate:project</info> command generates a Symfony2 Project.
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkOptionParameters($input);

        $filesystem = new Filesystem();

        $output->writeln('<info>Initializing Project</info>');
        $path = $input->getArgument('path');
        if($input->getOption('force-delete')) {
            $output->writeln(sprintf('> Remove project on <comment>%s</comment>', $path));
            $this->removeProject($path, $filesystem);
        }
        $this->checkPathAvailable($path, $filesystem);

        $output->writeln(sprintf('> Generate project on <comment>%s</comment>', $path));
        $this->generateProjectFolder($input, $filesystem);

        $bundles = $this->generateBundlesCollection($input);
        $namespaces = $this->generateNamespacesCollection($input);
        $prefixes = $this->generatePrefixesCollection($input);
        $this->findAndReplace($input, $bundles, $namespaces, $prefixes);
        $repositories = $this->getRepositoriesCollection($input);
        $this->installRepositories($repositories, $input, $output);

        $output->writeln(sprintf(' > <info>Clear cache and log</info>'));
        $filesystem->remove('app/cache/dev');
        $filesystem->remove('app/logs/dev.log');
    }

    /**
     * Check option parameters
     *
     * @param $input
     */
    private function checkOptionParameters($input)
    {
        if (!in_array($input->getOption('protocol'), array('git', 'http'))) {
            throw new \RuntimeException('Protocol error. Values accepted: git or http');
        }
        if (!in_array($input->getOption('template-engine'), array('twig', 'php'))) {
            throw new \RuntimeException('Template engine error. Values accepted: twig or php');
        }
    }

    /**
     * Remove project
     *
     * @param $path
     * @param $filesystem
     */
    private function removeProject($path, $filesystem)
    {
        foreach (array('.git', 'app', 'src', 'vendor', 'web') as $file) {
            $file_path = sprintf('%s/%s', $path, $file);
            if (file_exists($file_path)) {
                $filesystem->remove($file_path);
            }
        }
    }
    
    /**
     * Generate Bundle Collection
     *
     * @param $input
     * @param $filesystem
     */
    private function checkPathAvailable($path, $filesystem)
    {
        if (!is_dir($path)) {
            $filesystem->mkdirs($path);
        }

        if (!is_writable($path)) {
            throw new \RuntimeException(sprintf("The path %s is not writable\n", $path));
        }

        $files = scandir($path);
        foreach (array('.git', 'app', 'src', 'vendor', 'web') as $file) {
            if (in_array($file, $files)) {
                throw new \RuntimeException(sprintf('The folder %s contain a symfony project. Use another destination', $path));
            }
        }
    }

    /**
     * Generate Project Folder
     *
     * @param $input
     * @param $filesystem
     */
    private function generateProjectFolder($input, $filesystem)
    {
        $path = $input->getArgument('path');
        $app = $input->getArgument('app');
        $vendor = $input->getArgument('vendor');

        $filesystem->mirror(__DIR__.'/../Resources/skeleton/project', $path);
        $targetBundleDir = sprintf('%s/src/%s/%sBundle', $path, $vendor, $app);

        $filesystem->mkdirs($targetBundleDir);
        $filesystem->mirror(__DIR__.'/../Resources/skeleton/bundle', $targetBundleDir);

        $filesystem->rename($targetBundleDir.'/AppBundle.php', $targetBundleDir.'/'.$app.'Bundle.php');

        if ($controller = $input->getOption('controller')) {
            $filesystem->rename(
                $targetBundleDir.'/Controller/BundleController.php',
                $targetBundleDir.'/Controller/'.$controller.'Controller.php'
            );
            $filesystem->rename(
                $targetBundleDir.'/Resources/views/Bundle',
                $targetBundleDir.'/Resources/views/'.$controller
            );
        } else {
            $filesystem->remove($targetBundleDir.'/Controller/BundleController.php');
            $filesystem->remove($targetBundleDir.'/Resources/Views/Bundle');
            $filesystem->remove($targetBundleDir.'/Resources/config/routing.yml');
        }

        $extension = ('twig' === $input->getOption('template-engine')) ? 'php' : 'twig';
        $filesystem->remove($targetBundleDir.'/Resources/Views/layout.html.'.$extension);
        $filesystem->remove($targetBundleDir.'/Resources/Views/'.$controller.'/index.html.'.$extension);
        $filesystem->remove($targetBundleDir.'/Resources/Views/'.$controller.'/welcome.html.'.$extension);
        $filesystem->remove($path.'/app/Views/base.html.'.$extension);

        /* create empty folder */
        $filesystem->mkdirs($path.'/app/cache', 0777);
        $filesystem->mkdirs($path.'/app/logs', 0777);
        $filesystem->mkdirs($path.'/src', 0755);
        $filesystem->mkdirs($path.'/vendor', 0755);
        $filesystem->mkdirs($targetBundleDir.'/Resources/public');
        $filesystem->mkdirs($targetBundleDir.'/Tests');

        $filesystem->chmod($path.'/app/console', 0755);
    }

    /**
     * Generate Bundle Collection
     *
     * @param $input
     */
    private function generateBundlesCollection($input)
    {
        $bundlesCollection = new BundleCollection();
        $bundlesCollection->add(new Bundle('Framework'));
        $bundlesCollection->add(new Bundle('Zend'));
        if ('twig' === $input->getOption('template-engine')) {
            $bundlesCollection->add(new Bundle('Twig'));
        }
        if ($input->getOption('swiftmailer')) {
            $bundlesCollection->add(new Bundle('Swiftmailer'));
        }
        if ($input->getOption('assetic')) {
            $bundlesCollection->add(new Bundle('Assetic'));
        }
        if ($input->getOption('orm')) {
            if ('doctrine' === $input->getOption('orm'))
            {
                $bundlesCollection->add(new Bundle('Doctrine'));
                if ($input->getOption('doctrine-migration')) {
                    $bundlesCollection->add(new Bundle('DoctrineMigrations'));
                }
            }
            if ('propel' === $input->getOption('orm')) {
                $bundlesCollection->add(new Bundle('Propel', 'Propel\PropelBundle'));
            }
        }
        if ('mongodb' === $input->getOption('odm')) {
            $bundlesCollection->add(new Bundle('DoctrineMongoDB'));
        }

        $app = $input->getArgument('app');
        $vendor = $input->getArgument('vendor');
        $bundlesCollection->add(new Bundle($app, sprintf('%s\%sBundle', $vendor, $app)));

        return $bundlesCollection->getFormatted(12);
    }

    /**
     * Generate Namespace collection
     *
     * @param $input
     */
    private function generateNamespacesCollection($input)
    {
        $nsCollection = new NspaceCollection();
        $nsCollection->add(new Nspace('Symfony', 'vendor/symfony/src'));
        $nsCollection->add(new Nspace($input->getArgument('vendor'), 'src'));
        if ('doctrine' === $input->getOption('orm')) {
            if ($input->getOption('doctrine-fixtures')) {
                $nsCollection->add(new Nspace('Doctrine\Common\DataFixtures', 'vendor/doctrine-data-fixtures/lib'));
            }
            $nsCollection->add(new Nspace('Doctrine\Common', 'vendor/doctrine-common/lib'));
            if ($input->getOption('doctrine-migration')) {
                $nsCollection->add(new Nspace('Doctrine\DBAL\Migrations', 'vendor/doctrine-migrations/lib'));
            }
        }
        if ('mongodb' === $input->getOption('odm')) {
            $nsCollection->add(new Nspace('Doctrine\MongoDB', 'vendor/doctrine-mongodb/lib'));
            $nsCollection->add(new Nspace('Doctrine\ODM\MongoDB', 'vendor/doctrine-mongodb-odm/lib'));
        }
        if ('doctrine' === $input->getOption('orm')) {
            $nsCollection->add(new Nspace('Doctrine\DBAL', 'vendor/doctrine-dbal/lib'));
        }
        if (('doctrine' === $input->getOption('orm')) || ('mongodb' === $input->getOption('odm'))) {
            $nsCollection->add(new Nspace('Doctrine', 'vendor/doctrine/lib'));
        }
        if ('propel' === $input->getOption('orm')) {
            $nsCollection->add(new Nspace('Propel', 'src'));
        }
        if ($input->getOption('assetic')) {
            $nsCollection->add(new Nspace('Assetic', 'vendor/assetic/src'));
        }
        $nsCollection->add(new Nspace('Zend\Log', 'vendor/zend-log'));

        return $nsCollection->getFormatted(4);
    }

    /**
     * Generate Prefix collection
     *
     * @param $input
     */
    private function generatePrefixesCollection($input)
    {
        $prefixCollection = new PrefixCollection();
        if ('twig' === $input->getOption('template-engine')) {
            $prefixCollection->add(new Prefix('Twig_Extensions', 'vendor/twig-extensions/lib'));
            $prefixCollection->add(new Prefix('Twig', 'vendor/twig/lib'));
        }
        if ($input->getOption('swiftmailer')) {
            $prefixCollection->add(new Prefix('Swift', 'vendor/swiftmailer/lib/classes'));
        }

        return $prefixCollection->getFormatted(4);
    }

    /**
     * Generate Repository collection
     *
     * @param $input
     */
    private function getRepositoriesCollection($input)
    {
        $reposCollection = new RepositoryCollection();
        $reposCollection->add(new Repository('github.com/symfony/symfony.git', 'vendor/symfony'));
        $reposCollection->add(new Repository('github.com/symfony/zend-log.git', 'vendor/zend-log/Zend/Log'));
        if ($input->getOption('swiftmailer')) {
            $reposCollection->add(new Repository('github.com/swiftmailer/swiftmailer.git', 'vendor/swiftmailer'));
        }
        if ($input->getOption('assetic')) {
            $reposCollection->add(new Repository('github.com/kriswallsmith/assetic.git', 'vendor/assetic'));
        }
        if ('twig' === $input->getOption('template-engine')) {
            $reposCollection->add(new Repository('github.com/fabpot/Twig.git', 'vendor/twig'));
            $reposCollection->add(new Repository('github.com/fabpot/Twig-extensions.git', 'vendor/twig-extensions'));
        }
        if ('doctrine' === $input->getOption('orm')) {
            $reposCollection->add(new Repository('github.com/doctrine/doctrine2.git', 'vendor/doctrine'));
            $reposCollection->add(new Repository('github.com/doctrine/dbal.git', 'vendor/doctrine-dbal'));
            if ($input->getOption('doctrine-fixtures')) {
                $reposCollection->add(new Repository('github.com/doctrine/data-fixtures.git', 'vendor/doctrine-data-fixtures'));
            }
            if ($input->getOption('doctrine-migration')) {
                $reposCollection->add(new Repository('github.com/doctrine/migrations.git', 'vendor/doctrine-migrations'));
            }
        }
        if (('doctrine' === $input->getOption('orm')) || ('mongodb' === $input->getOption('odm'))) {
            $reposCollection->add(new Repository('github.com/doctrine/common.git', 'vendor/doctrine-common'));
        }
        if ('mongodb' === $input->getOption('odm')) {
            $reposCollection->add(new Repository('github.com/doctrine/mongodb.git', 'vendor/doctrine-mongodb'));
            $reposCollection->add(new Repository('github.com/doctrine/mongodb-odm.git', 'vendor/doctrine-mongodb-odm'));
        }
        if ('propel' === $input->getOption('orm')) {
            $reposCollection->add(new Repository('github.com/willdurand/PropelBundle.git', 'src/Propel/PropelBundle'));
            $reposCollection->add(new Repository('github.com/KaroDidi/phing.git', 'vendor/phing'));
            $reposCollection->add(new Repository('github.com/KaroDidi/propel1.6.git', 'vendor/propel'));
        }

        return $reposCollection->get();
    }

    /**
     * Install repository
     *
     * @param $repositories
     * @param $input
     * @param $output
     */
    private function installRepositories($repositories, $input, $output)
    {
        chdir($input->getArgument('path'));
        $output->writeln(' > <info>Git init</info>');
        exec('git init');
        foreach ($repositories as $repository) {
            $gitcommand = sprintf('git submodule add %s://%s %s',
                                            $input->getOption('protocol'),
                                            $repository->getSource(),
                                            $repository->getTarget()
                                            );
            $output->writeln(sprintf(' > <info>Git %s</info>', $repository->getSource()));
            exec($gitcommand);
        }

        exec('git submodule init');
        exec('git submodule update');
    }

    /**
     * Find and replace
     *
     * @param $input
     * @param $bundles
     * @param $registerNamespaces
     * @param $registerPrefixes
     */
    private function findAndReplace($input, $bundles, $registerNamespaces, $registerPrefixes)
    {
        $twig_config = ('twig' === $input->getOption('template-engine')) ? $this->loadConfigFile('twig') : '';
        $assetic_config = ($input->getOption('assetic')) ? $this->loadConfigFile('assetic') : '';
        $doctrine_config = ('doctrine' === $input->getOption('orm')) ? $this->loadConfigFile('doctrine') : '';
        $doctrine_config = str_replace('{{ app }}', $input->getArgument('app'), $doctrine_config);
        $swift_config = ($input->getOption('swiftmailer')) ? $this->loadConfigFile('swiftmailer') : '';
        $routing = ($input->getOption('controller')) ? $this->loadConfigFile('routing') : '';
        $routing = str_replace('{{ app }}', $input->getArgument('app'), $routing);

        Mustache::renderDir($input->getArgument('path'), array(
            'namespace' => $input->getArgument('vendor'),
            'appname' => $input->getArgument('app'),
            'controller' => $input->getOption('controller'),
            'session_start' => $input->getOption('session-start') ? 'true' : 'false',
            'session_name'  => $input->getOption('session-name'),
            'template_engine' => $input->getOption('template-engine'),
            'routing' => $routing,
            'bundles' => $bundles,
            'registerNamespaces' => $registerNamespaces,
            'registerPrefixes' => $registerPrefixes,
            'twig' => $twig_config,
            'assetic' => $assetic_config,
            'doctrine' => $doctrine_config,
            'swiftmailer' => $swift_config,
        ));
    }

    /**
     * Load config file
     *
     * @param $file
     */
    private function loadConfigFile($file)
    {
        return file_get_contents(__DIR__.'/../Resources/Installer/Config/'.$file.'.txt');
    }
}
