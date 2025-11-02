<?php

namespace daandelange\SimpleStats;

//return (false===option('daandelange.simplestats.tracking.onLoad', true))?[]:[
//return (SimpleStatsTrackingMode::OnImage===option('daandelange.simplestats.tracking.method', SimpleStatsTrackingMode::OnLoad))?[]:[
//var_dump(option('daandelange.simplestats.tracking.method')); die();
$isMultiLang = kirby()->multilang();

// Todo: Only route this when the tracking method is onImage

return [
    // Intercept counter pixel on home page
    $isMultiLang?[
        'pattern' => 'counter.png',
        'language' => '*',
        'action' => function ($language) {
            $page = site()->homePage();
            if($page) return SimpleStats::trackPageAndServeImageResponse( $page );
            return false; // 404
        },
    ]:[
        'pattern' => 'counter.png',
        'action' => function () {
            $page = site()->homePage();
            if($page) return SimpleStats::trackPageAndServeImageResponse( $page );
            return false; // 404
        },
    ],
    // On all other pages
    $isMultiLang?[
        'pattern' => '(:all)/counter.png',
        'language' => '*',
        'action' => function ($language, $id) {
            $page = page($id);
            var_dump($page);
            if($page) return SimpleStats::trackPageAndServeImageResponse( $page );
            return false; // 404
        },
    ]:[
        'pattern' => '(:all)/counter.png',
        'action' => function ($id) {
            $page = page($id);
            if($page) return SimpleStats::trackPageAndServeImageResponse( $page );
            return false; // 404
        },
    ],
];
