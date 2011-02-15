#!/usr/bin/env php
<?php
/*
 * Symfony2 structure project installer
 *
 * (c) Bertrand Zuchuat
 *
 */

/**
 * @author Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 */

$ds = DIRECTORY_SEPARATOR;
$with_controller = false;

// ----------------- FILES
$deny_htaccess = 'deny from all';

$web_htaccess = <<<'EOF'
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
EOF;

$web_robots = <<<'EOF'
User-agent: *
Allow: /
EOF;

$app_cache = <<<'EOF'
<?php

require_once __DIR__.'/AppKernel.php';

use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;

class AppCache extends HttpCache
{
}
EOF;

$app_kernel = <<<'EOF'
<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),

            // enable third-party bundles
            new Symfony\Bundle\ZendBundle\ZendBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
%class%
            new %vendor%\%app%Bundle\%app%Bundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        return $bundles;
    }

    public function registerRootDir()
    {
        return __DIR__;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        // use YAML for configuration
        // comment to use another configuration format
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');

        // uncomment to use XML for configuration
        //$loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.xml');

        // uncomment to use PHP for configuration
        //$loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.php');
    }
}
EOF;

$app_boostrap = <<<'EOF'
<?php

require_once __DIR__.'/../vendor/symfony/src/Symfony/Component/HttpKernel/bootstrap.php';
require_once __DIR__.'/autoload.php';
EOF;

$app_console = <<<'EOF'
#!/usr/bin/env php
<?php

require_once __DIR__.'/bootstrap.php';
require_once __DIR__.'/AppKernel.php';

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

$input = new ArgvInput();
$env = $input->getParameterOption(array('--env', '-e'), 'dev');
$debug = $input->hasParameterOption(array('--debug', '-d'));

$kernel = new AppKernel($env, $debug);
$application = new Application($kernel);
$application->run();

EOF;

$app_phpunit = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>

<phpunit backupGlobals="false"
         backupStaticAttributes="false"
         colors="true"
         convertErrorsToExceptions="true"
         convertNoticesToExceptions="true"
         convertWarningsToExceptions="true"
         processIsolation="true"
         stopOnFailure="false"
         syntaxCheck="false"
         bootstrap="bootstrap.php"
>
  <testsuites>
      <testsuite name="Project Test Suite">
          <directory>../src/*/*Bundle/Tests</directory>
      </testsuite>
  </testsuites>

  <!--
  <php>
      <server name="KERNEL_DIR" value="/path/to/your/app/" />
  </php>
  //-->

  <filter>
      <whitelist>
          <directory>../src</directory>
          <exclude>
              <directory>../src/*/*Bundle/Resources</directory>
              <directory>../src/*/*Bundle/Tests</directory>
          </exclude>
      </whitelist>
  </filter>
</phpunit>
EOF;



$app_bundle = <<<'EOF'
<?php
namespace %vendor%\%app%Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class %app%Bundle extends Bundle
{
}
EOF;


$config_yml = <<<'EOF'
framework:
    charset:       UTF-8
    error_handler: null
    csrf_secret:
        enabled: true
        secret: xxxxxxxxxx
    router:        { resource: "%kernel.root_dir%/config/routing.yml" }
    validation:    { enabled: true, annotations: true }
    templating:
        engines:   ['%engine%']
        #assets_version: SomeVersionScheme
    session:
        default_locale: en
        lifetime:       3600
        auto_start:     %start%
        name:           %sessionname%

twig:
    debug:            %kernel.debug%
    strict_variables: %kernel.debug%
%configdb%
## Swiftmailer Configuration
#swiftmailer:
#    transport:  smtp
#    encryption: ssl
#    auth_mode:  login
#    host:       smtp.gmail.com
#    username:   xxxxxxxx
#    password:   xxxxxxxx
EOF;

$config_db_yml = <<<'EOF'

## Doctrine Configuration
#doctrine:
#    dbal:
#        dbname:   xxxxxxxx
#        user:     xxxxxxxx
#        password: ~
#        logging:  %kernel.debug%
#    orm:
#        auto_generate_proxy_classes: %kernel.debug%
#        mappings:
#           %app%Bundle: ~

EOF;

$config_dev_yml = <<<'EOF'
imports:
    - { resource: config.yml }

framework:
    router:   { resource: "%kernel.root_dir%/config/routing_dev.yml" }
    profiler: { only_exceptions: false }

web_profiler:
    toolbar: true
    intercept_redirects: true

zend:
    logger:
        priority: debug
        path:     %kernel.logs_dir%/%kernel.environment%.log
EOF;

$config_prod_yml = <<<'EOF'
imports:
    - { resource: config.yml }
EOF;

$config_test_yml = <<<'EOF'
imports:
    - { resource: config_dev.yml }

framework:
    error_handler: false
    test: ~

web_profiler:
    toolbar: false
    intercept_redirects: false

zend:
    logger:
        priority: debug
EOF;


$routing_yml = <<<'EOF'
homepage:
    pattern:  /
    defaults: { _controller: FrameworkBundle:Default:index }

#%app%:
#    resource: @%app%Bundle/Resources/config/routing.yml
EOF;

$routing_with_controller_yml = <<<'EOF'
homepage:
    pattern:  /
    defaults: { _controller: FrameworkBundle:Default:index }

%app%:
    resource: @%app%Bundle/Resources/config/routing.yml
EOF;


$routing_dev_yml = <<<'EOF'
_main:
    resource: routing.yml

_profiler:
    resource: @WebProfilerBundle/Resources/config/routing/profiler.xml
    prefix:   /_profiler
EOF;

$autoload = <<<'EOF'
<?php

$vendorDir = __DIR__.'/../vendor';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
%loader%
));
$loader->registerPrefixes(array(
    'Twig_Extensions_' => $vendorDir.'/twig-extensions/lib',
    'Twig_'            => $vendorDir.'/twig/lib',
    'Swift_'           => $vendorDir.'/swiftmailer/lib/classes',
));
$loader->register();
EOF;

$base_twig = <<<'EOF'
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>{% block title %}%app% Application{% endblock %}</title>
    </head>
    <body>
        {% block body %}{% endblock %}
    </body>
</html>
EOF;

$base_php = <<<'EOF'
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php $view['slots']->output('title', '%app% Application') ?></title>
    </head>
    <body>
        <?php $view['slots']->output('_content') ?>
    </body>
</html>

EOF;

$layout_twig = <<<'EOF'
{% extends "::base.html.twig" %}

{% block body %}
    <h1>%app% Application</h1>

    {% block content %}{% endblock %}
{% endblock %}
EOF;

$layout_php = <<<'EOF'
<?php $view->extend('::base.html.php') ?>

<h1>%app% Application</h1>

<?php $view['slots']->output('_content') ?>

EOF;


$index_prod = <<<'EOF'
<?php

require_once __DIR__.'/../app/bootstrap.php';
require_once __DIR__.'/../app/AppKernel.php';

use Symfony\Component\HttpFoundation\Request;

$kernel = new AppKernel('prod', false);
$kernel->handle(Request::createFromGlobals())->send();
EOF;

$index_dev = <<<'EOF'
<?php

// this check prevents access to debug front controllers that are deployed by accident to production servers.
// feel free to remove this, extend it, or make something more sophisticated.
if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) {
    die('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

require_once __DIR__.'/../app/bootstrap.php';
require_once __DIR__.'/../app/AppKernel.php';

use Symfony\Component\HttpFoundation\Request;

$kernel = new AppKernel('dev', true);
$kernel->handle(Request::createFromGlobals())->send();
EOF;


$controller_bundle = <<<'EOF'
<?php

namespace %vendor%\%app%Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class %controller%Controller extends Controller
{
    public function indexAction()
    {
        return $this->render('%app%Bundle:%controller%:index.html.%engine%');
    }
}
EOF;

$controller_template_twig = <<<'EOF'
{% extends "%app%Bundle::layout.html.twig" %}

{% block content %}
Controller: %controller%<br />
Action: index
{% endblock %}
EOF;

$controller_template_php = <<<'EOF'
<?php $view->extend('%app%Bundle::layout.html.php') ?>

Controller: %controller%<br />
Action: index
EOF;

$controller_routing = <<<'EOF'
%controller%_index:
    pattern:  /%controller%
    defaults: { _controller: %app%Bundle:%controller%:index }
EOF;

$git_ignore = <<<'EOF'
app/cache/*
app/logs/*
EOF;

$loader_array = array(
  'Symfony' => '$vendorDir.\'/symfony/src\'',
  '%vendor%' => '__DIR__.\'/../src\'',
  'Zend' => '$vendorDir.\'/zend/library\''
);

$loader_db_array = array(
  'Doctrine\\\\MongoDB' => '$vendorDir.\'/doctrine-mongodb/lib\'',
  'Doctrine\\\\ODM\\\\MongoDB' => '$vendorDir.\'/doctrine-mongodb-odm/lib\'',
  'Doctrine\\\\Common\\\\DataFixtures' => '$vendorDir.\'/doctrine-data-fixtures/lib\'',
  'Doctrine\\\\Common' => '$vendorDir.\'/doctrine-common/lib\'',
  'Doctrine\\\\DBAL\\\\Migrations' => '$vendorDir.\'/doctrine-migrations/lib\'',
  'Doctrine\\\\DBAL' => '$vendorDir.\'/doctrine-dbal/lib\'',
  'Doctrine' => '$vendorDir.\'/doctrine/lib\''
);

$kernel_class = <<<'EOF'
            new Symfony\Bundle\DoctrineBundle\DoctrineBundle(),
            //new Symfony\Bundle\DoctrineMigrationsBundle\DoctrineMigrationsBundle(),
            //new Symfony\Bundle\DoctrineMongoDBBundle\DoctrineMongoDBBundle(),

EOF;

// ----------------- MAIN

// Delete the first array position (scriptname)
array_shift($argv);

if (0 == count($argv))
{
  echo <<<'EOF'

Usage: php symfony2project.php --app=AppName --vendor=VendorName [--path=/your/destination/path] [--controller=controllerName] [--protocol=git|http] [--session-start=false|true] [--session-name=sessionName] [--symfony-repository=fabpot|symfony] [--with-db=false|true] [--template-engine=twig|php]

--app                : Application name (mandatory)
--vendor             : Vendor name (mandatory)
--path               : Directory name (path) (default: current dir)
--controller         : Your first controller name (optional)
                       (suggestion: home or main, you can change it later if you change your mind)
--protocol           : git or http (if git is not enable in your company)
--session-start      : false or true (auto_start parameter on session) (default: false)
--session-name       : Session name (default: Application name)
--symfony-repository : fabpot or symfony (default: symfony)
--with-db            : false or true (default: true)
--template-engine    : twig or php (default: twig)


EOF;
exit;
}


// Process script parameter(s)
$params = array();
foreach ($argv as $param)
{
  @list($name, $content) = explode('=', $param);
  $params[substr($name,2)] = $content;
}

if (!$app = @$params['app'])
{
  echo "Application name is mandatory\n";
  exit;
}

if (!$vendor = @$params['vendor'])
{
  echo "Vendor name is mandatory\n";
  exit;
}


if (!$dir = @$params['path'])
{
  $dir = dirname(__FILE__);
}

$protocols = array('http', 'git');
$protocol = "git";
if($pro = @$params['protocol'])
{
    if(in_array($pro, $protocols)){
        $protocol = $pro;
    }
}

if ($controller = @$params['controller'])
{
  $with_controller = true;
}

if (!$session_autostart = @$params['session-start'])
{
  $session_autostart = "false";
}

if (!$session_name = @$params['session-name'])
{
  $session_name = $app;
}

$repositories = array('fabpot', 'symfony');
$repository = "symfony";
if($repo = @$params['symfony-repository'])
{
    if(in_array($repo, $repositories)){
        $repository = $repo;
    }
}

$with_db = ((!@$params['with-db']) || ('true' === @$params['with-db'])) ? true : false;

if ($with_db)
{
  $loader_array = array_merge($loader_array, $loader_db_array);
  $loader_transform = array();
  foreach ($loader_array as $key => $value)
  {
      $key = str_replace('%vendor%', $vendor, $key);
      $loader_transform[$key] = str_replace('%vendor%', $vendor, $value);
  }
  $loader_array = $loader_transform;
}

$template_engine = array('twig', 'php');
$engine = 'twig';
if($template = @$params['template-engine'])
{
    if (in_array($template, $template_engine))
    {
        $engine = $template;
    }
}

$loader_key_size = 0;
foreach ($loader_array as $key => $value)
{
  if ($loader_key_size < strlen($key))
  {
    $loader_key_size = strlen($key);
  }
}
$loader_key_size = $loader_key_size + 3;

$loader_string = '';
foreach ($loader_array as $key => $value)
{
  $size = $loader_key_size - strlen($key);
  $mask = "    '%s'".str_repeat(' ', $size)." => %s,";
  $loader_string .= sprintf($mask, $key, $value)."\n";
}
$loader_string = rtrim($loader_string);


if (!is_dir($dir) || !is_writable($dir))
{
  echo sprintf("The path %s doesn't exist or not writable\n", $dir);
  exit;
}

// Check content folder
$files = scandir($dir);
$test = true;
foreach (array('app', 'src', 'web') as $file)
{
  if (in_array($file, $files))
  {
    $test = false;
    continue;
  }
}

if (!$test)
{
  echo sprintf("The folder %s contain a symfony project. Use another destination\n", basename($dir));
  exit;
}

// ----------------- STRUCTURE
$folders = array(
  'app',
  'app/cache',
  'app/config',
  'app/logs',
  'app/views',
  'src',
  'src/'.$vendor,
  'vendor',
  'web',
  'web/bundles'
);

// Start Install
echo "\n";
echo "-> Install to $dir\n";

chdir($dir);

$app_folder = 'src/'.$vendor.'/'.$app.'Bundle';
mkdir($app_folder);
array_push($folders, $app_folder,
  "$app_folder/Controller",
  "$app_folder/Resources",
  "$app_folder/Resources/config",
  "$app_folder/Resources/public",
  "$app_folder/Resources/views",
  "$app_folder/Tests"
);

foreach ($folders as $folder)
{
  echo "$folder\n";
  mkdir($folder);
}

file_put_contents('app/AppCache.php', $app_cache);

$app_kernel = str_replace(array('%app%', '%vendor%'), array($app, $vendor), $app_kernel);
if ($with_db)
{
  $app_kernel = str_replace('%class%', $kernel_class, $app_kernel);
}
else
{
  $app_kernel = str_replace('%class%', '', $app_kernel);
}
file_put_contents('app/bootstrap.php', $app_boostrap);
file_put_contents('app/AppKernel.php', $app_kernel);
file_put_contents('app/console', $app_console);
file_put_contents('app/phpunit.xml', $app_phpunit);
file_put_contents('app/.htaccess', $deny_htaccess);
file_put_contents('src/.htaccess', $deny_htaccess);
file_put_contents('web/.htaccess', $web_htaccess);
file_put_contents('web/robots.txt', $web_robots);

$config_yml = str_replace('%start%', $session_autostart, $config_yml);
$config_yml = str_replace('%sessionname%', $session_name, $config_yml);
$config_yml = str_replace('%engine%', $engine, $config_yml);

if ($with_db)
{
  $config_db_yml = str_replace('%app%', $app, $config_db_yml);
  $config_yml = str_replace('%configdb%', $config_db_yml, $config_yml);
}
else
{
  $config_yml = str_replace('%configdb%', '', $config_yml);
}

file_put_contents('app/config/config.yml', $config_yml);
file_put_contents('app/config/config_dev.yml', $config_dev_yml);
file_put_contents('app/config/config_test.yml', $config_test_yml);
file_put_contents('app/config/config_prod.yml', $config_prod_yml);
$routing_yml = ($with_controller ? $routing_with_controller_yml : $routing_yml);
file_put_contents('app/config/routing.yml', str_replace('%app%', $app, $routing_yml));
file_put_contents('app/config/routing_dev.yml', $routing_dev_yml);

if ('twig' === $engine) {
  file_put_contents('app/views/base.html.twig', str_replace('%app%', $app, $base_twig));
} else {
  file_put_contents('app/views/base.html.php', str_replace('%app%', $app, $base_php));
}


file_put_contents('app/autoload.php', str_replace('%loader%', $loader_string, $autoload));

file_put_contents($app_folder.'/'.$app.'Bundle.php', str_replace(array('%app%', '%vendor%'), array($app, $vendor), $app_bundle));
file_put_contents('web/index.php', $index_prod);
file_put_contents('web/index_dev.php', $index_dev);

if ($with_controller)
{
  if ('twig' === $engine) {
    file_put_contents("$app_folder/Resources/views/layout.html.twig", str_replace('%app%', $app, $layout_twig));
  } else {
    file_put_contents("$app_folder/Resources/views/layout.html.php", str_replace('%app%', $app, $layout_php));
  }
  
  $cpath = "$app_folder/Controller/".$controller."Controller.php";
  file_put_contents($cpath, str_replace(array('%app%', '%controller%', '%engine%', '%vendor%'), array($app, $controller, $engine, $vendor), $controller_bundle));

  $ftpath = "$app_folder/Resources/views/$controller";
  mkdir($ftpath);
  if ('twig' === $engine) {
    file_put_contents($ftpath.'/index.html.twig', str_replace(array('%app%', '%controller%'), array($app, $controller), $controller_template_twig));
  } else {
    file_put_contents($ftpath.'/index.html.php', str_replace(array('%app%', '%controller%'), array($app, $controller), $controller_template_php));
  }

  file_put_contents($app_folder.'/Resources/config/routing.yml', str_replace(array('%app%', '%controller%'), array($app, $controller), $controller_routing));
}
else
{
  file_put_contents($app_folder.'/Resources/config/routing.yml', '');
}

file_put_contents('.gitignore', $git_ignore);

chmod('app/cache', 0777);
chmod('app/logs', 0777);
chmod('app/console', 0755);

echo "\n";
echo "-> Init git project";
exec('git init');
echo "\n";

$git_repository = array(
  'git://github.com/'.$repository.'/symfony.git'          => 'vendor/symfony',
  'git://github.com/swiftmailer/swiftmailer.git'          => 'vendor/swiftmailer',
  'git://github.com/fabpot/Twig.git'                      => 'vendor/twig',
  'git://github.com/fabpot/Twig-extensions.git'           => 'vendor/twig-extensions',
  'git://github.com/zendframework/zf2.git'                => 'vendor/zend',
);

if ($with_db)
{
  $git_db_repository = array(
    'git://github.com/doctrine/doctrine2.git'               => 'vendor/doctrine',
    'git://github.com/doctrine/data-fixtures.git'           => 'vendor/doctrine-data-fixtures',
    'git://github.com/doctrine/dbal.git'                    => 'vendor/doctrine-dbal',
    'git://github.com/doctrine/common.git'                  => 'vendor/doctrine-common',
    'git://github.com/doctrine/migrations.git'              => 'vendor/doctrine-migrations',
    'git://github.com/doctrine/mongodb.git'                 => 'vendor/doctrine-mongodb',
    'git://github.com/doctrine/mongodb-odm.git'             => 'vendor/doctrine-mongodb-odm',
  );
  
  $git_repository = array_merge($git_repository, $git_db_repository);
}

echo "\n";
echo "-> Init and update submodules\n";
foreach ($git_repository as $source => $target)
{
  $source = preg_replace('/git/', $protocol, $source, 1);
  echo "--> $source\n";
  exec("git submodule add $source $target");
}

exec('git submodule init');
exec('git submodule update');
exec('php app/console assets:install --symlink web');

@remove('app/cache/dev');
@unlink('app/logs/dev.log');

echo "\n";
echo "-> Generation of Symfony2 project terminated\n";
echo "\n";
echo "Homepage: http://[domain]/index_dev.php\n";
if ($with_controller)
{
    echo "$controller: http://[domain]/index_dev.php/$controller\n";
}


// ---- FUNCTION (Symfony2 core)
function remove($files)
{
    if (!is_array($files)) {
        $files = array($files);
    }

    $files = array_reverse($files);
    foreach ($files as $file) {
        if (!file_exists($file)) {
            continue;
        }

        if (is_dir($file) && !is_link($file)) {
            $fp = opendir($file);
            while (false !== $item = readdir($fp)) {
                if (!in_array($item, array('.', '..'))) {
                    remove($file.'/'.$item);
                }
            }
            closedir($fp);

            rmdir($file);
        } else {
            unlink($file);
        }
    }
}
