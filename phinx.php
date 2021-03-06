<?php
/**
 * Joomla! Framework Status Application
 *
 * @copyright  Copyright (C) 2014 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

// Application constants
define('JPATH_ROOT', __DIR__);
define('JPATH_TEMPLATES', JPATH_ROOT . '/templates');

$container = (new Joomla\DI\Container)
	->registerServiceProvider(new Joomla\FrameworkWebsite\Service\ApplicationProvider)
	->registerServiceProvider(new Joomla\FrameworkWebsite\Service\ConfigurationProvider(JPATH_ROOT . '/etc/config.json'))
	->registerServiceProvider(new Joomla\FrameworkWebsite\Service\DatabaseProvider)
	->registerServiceProvider(new Joomla\FrameworkWebsite\Service\LoggingProvider)
	->registerServiceProvider(new Joomla\FrameworkWebsite\Service\TemplatingProvider);

// Alias the web application to Joomla's base application class as this is the primary application for the environment
$container->alias(Joomla\Application\AbstractApplication::class, Joomla\Application\AbstractCliApplication::class);

// Alias the Joomla\Input\Input service to the CLI input class (overloads the original definition)
$container->alias(Joomla\Input\Input::class, Joomla\Input\Cli::class);

// Get the config so we can push stuff into the Phinx config
/** @var Joomla\Registry\Registry $config */
$config = $container->get('config');

// Get the DBO so we can share the raw connection
/** @var Joomla\Database\Mysql\MysqlDriver $db */
$db = $container->get('db');
$db->connect();

return [
	'paths'        => [
		'migrations' => '%%PHINX_CONFIG_DIR%%/etc/migrations',
	],
	'environments' => [
		'default_database' => 'application',
		'application'      => [
			'name'         => $config->get('database.name'),
			'connection'   => $db->getConnection(),
			'table_prefix' => $config->get('database.prefix'),
		],
	],
];
