<?php

spl_autoload_register(function (string $className){
	$prefix = 'Mapt\\Beejeetest\\';

	$len = strlen($prefix);

	if (strncmp($prefix, $className, $len) !== 0) {
		return;
	}

	$relative_class = substr($className, $len);

	$relative_class = "\\Classes\\".$relative_class;

	if (substr($relative_class, 0, 1) != "\\") {
		$relative_class = "\\".$relative_class;
	}

	$file = __DIR__.str_replace('\\', '/', $relative_class).'.php';

	$file = realpath($file);

	if (
		$file &&
		file_exists($file) &&
		substr($file, 0, strlen(__DIR__)) == __DIR__ &&
		substr($file, 0, strlen(__DIR__."/www/") != __DIR__."/www/")
	) {
		require_once $file;
	}
});

use Mapt\Beejeetest\Application;

/**
 * @return \Mapt\Beejeetest\Application
 */
function app()
{
    return Application::instance();
}

/**
 * @return \Mapt\Beejeetest\Database
 */
function db()
{
    return app()->db();
}

/**
 * @return \Mapt\Beejeetest\Appuser
 */
function user()
{
    return app()->user();
}

/**
 * @return \Mapt\Beejeetest\Config
 */
function config()
{
    return app()->config();
}