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
    'routes'       => require 'src/config/routes.php',
    'translations' => require 'src/config/i18n.php',
    'userMethods'  => require 'src/config/usermethods.php',

    // One page stats detail section
    'sections'  => [
        'pagestats' => [
            // Data that comes from blueprint
            'props' => [
                'headline' => function (string $headline = 'Page Stats') {
                    return $headline;
                },
                'showFullInfo' => function (bool $showFullInfo = false) {
                    return $showFullInfo;
                },
                'showTimeline' => function (bool $showTimeline = true) {
                    return $showTimeline;
                },
                'showLanguages' => function (bool $showLanguages = true) {
                    return $showLanguages;
                },
                'showTotals' => function (bool $showTotals = true) {
                    return $showTotals;
                },
                'size'  => function(string $size = 'medium') {
                    return $size;
                },
            ],
            'computed' => [
                'statsdata' => function () {
                    return $this->model()->getPageStats();
                }
            ]
        ],
    ],

]);
