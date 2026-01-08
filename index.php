<?php

namespace daandelange\SimpleStats;

use Kirby\Cms\App;

@include_once __DIR__ . '/vendor/autoload.php';

App::plugin('daandelange/simplestats', [
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
