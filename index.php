<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Composer\Semver\Semver;
use Kirby\Cms\App as Kirby;

if (Semver::satisfies(Kirby::version() ?? '0.0.0', '~4.0 || ~5.0') === false) {
	throw new Exception('SimpleStats requires Kirby 4 or 5');
}

Kirby::plugin('daandelange/simplestats', [
    'api'          => require 'src/config/api.php',
    'areas'        => require 'src/config/areas.php',
    'hooks'        => require 'src/config/hooks.php',
    'options'      => require 'src/config/options.php',
    'pageMethods'  => require 'src/config/pagemethods.php',
    'userMethods'  => require 'src/config/usermethods.php',
    'routes'       => require 'src/config/routes.php',
    'sections'     => require 'src/config/sections.php',
    'translations' => require 'src/config/i18n.php',
]);
