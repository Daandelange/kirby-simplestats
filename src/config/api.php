<?php

namespace daandelange\SimpleStats;

use Kirby\Exception\PermissionException;
use Kirby\Exception\Exception;
use Kirby\Toolkit\I18n;
use Throwable;

return [
    'routes' => function ($kirby): array {

        // Wrapper: normal panel access + logging
        $wrapAction = function (callable $callback): callable {
            return function () use ($callback): mixed {
                if (!$this->user()?->hasSimpleStatsPanelAccess()) {
                    throw new PermissionException('You are not authorised to view statistics.');
                }

                try {
                    return $callback();
                } catch (Throwable $e) {
                    Logger::logTracking('Error: ' . $e->getMessage() . ' (file: ' . $e->getFile() . '#L' . $e->getLine() . ')');
                    throw $e;
                }
            };
        };

        // Helper: safely get query parameter
        $getQueryParam = function (string $key, mixed $default = null) use ($kirby): mixed {
            $query = $kirby->request()->query();
            $data  = is_callable([$query, 'data']) ? $query->data() : [];
            return $data[$key] ?? $default;
        };

        // Helper: parse date strings or timestamps
        $parseDateRange = function (string|int $value): int {
            if (is_int($value)) return $value;
            if (strpos($value, '-') === 2) {
                $day = (int) substr($value, 0, 2);
                $month = (int) substr($value, 3, 2);
                $year = (int) substr($value, 6, 4);
                return mktime(0, 0, 0, $month, $day, $year);
            }
            return (int)$value;
        };

        // API Routes
        return [
            [
                'pattern' => 'simplestats/listvisitors',
                'method'  => 'GET',
                'action'  => $wrapAction(fn(): array => Stats::listvisitors())
            ],

            [
                'pattern' => 'simplestats/devicestats',
                'method'  => 'GET',
                'action'  => $wrapAction(function (): array {
                    [$from, $to] = Stats::getTimeSpanFromUrl();
                    return Stats::deviceStats($from, $to);
                })
            ],

            [
                'pattern' => 'simplestats/refererstats',
                'method'  => 'GET',
                'action'  => $wrapAction(function (): array {
                    [$from, $to] = Stats::getTimeSpanFromUrl();
                    return Stats::refererStats($from, $to);
                })
            ],

            [
                'pattern' => 'simplestats/pagestats',
                'method'  => 'GET',
                'action'  => $wrapAction(function (): array {
                    [$from, $to] = Stats::getTimeSpanFromUrl();
                    return Stats::pageStats($from, $to);
                })
            ],

            [
                'pattern' => 'simplestats/listdbinfo',
                'method'  => 'GET',
                'action'  => $wrapAction(fn(): array => Stats::listDbInfo())
            ],

            [
                'pattern' => 'simplestats/configinfo',
                'method'  => 'GET',
                'action'  => $wrapAction(function (): array {
                    $salt = option('daandelange.simplestats.tracking.salt', '');
                    $logLevels = [];
                    if (option('daandelange.simplestats.log.tracking', false)) $logLevels[] = I18n::translate('simplestats.info.config.log.level.tracking');
                    if (option('daandelange.simplestats.log.warnings', false)) $logLevels[] = I18n::translate('simplestats.info.config.log.level.warnings');
                    if (option('daandelange.simplestats.log.verbose', false)) $logLevels[] = I18n::translate('simplestats.info.config.log.level.verbose');

                    return [
                        'saltIsSet'             => is_string($salt) && !empty($salt) && $salt !== 'CHANGEME',
                        'trackingPeriodName'    => getTimeFrameUtility()->getPeriodAdjective(),
                        'uniqueSeconds'         => option('daandelange.simplestats.tracking.uniqueSeconds', -1),
                        'enableReferers'        => option('daandelange.simplestats.tracking.enableReferers', false),
                        'enableDevices'         => option('daandelange.simplestats.tracking.enableDevices', false),
                        'enableVisits'          => option('daandelange.simplestats.tracking.enableVisits', false),
                        'enableVisitLanguages'  => kirby()->multilang() && option('daandelange.simplestats.tracking.enableVisitLanguages', false),
                        'ignoredRoles'          => option('daandelange.simplestats.tracking.ignore.roles', []),
                        'ignoredPages'          => option('daandelange.simplestats.tracking.ignore.pages', []),
                        'ignoredTemplates'      => option('daandelange.simplestats.tracking.ignore.templates', []),
                        'logFile'               => str_replace(realpath(kirby()->root('config') . DIRECTORY_SEPARATOR . '..'), '.', option('daandelange.simplestats.log.file', [])),
                        'logLevels'             => $logLevels,
                        'trackingSince'         => 'todo', // todo
                    ];
                })
            ],

            [
                'pattern' => 'simplestats/trackingtester',
                'method'  => 'GET',
                'action'  => $wrapAction(function (): array {
                    $device = SimpleStats::detectSystemFromUA();
                    if (isset($device['device'])) $device['device'] = Stats::humanizeKey($device['device']);
                    return [
                        'currentUserAgent'  => $_SERVER['HTTP_USER_AGENT'] ?? '',
                        'currentDeviceInfo' => $device,
                    ];
                })
            ],

            [
                'pattern' => 'simplestats/trackingtester/referrer',
                'method'  => 'GET',
                'action'  => $wrapAction(function () use ($getQueryParam): array {
                    $referrer = (string) $getQueryParam('referrer', $_SERVER['HTTP_REFERER'] ?? '');
                    return [
                        'referrerInfo' => SimpleStats::getRefererInfo(['Referer' => $referrer]) ?? 'Invalid referrer!',
                    ];
                })
            ],

            [
                'pattern' => 'simplestats/trackingtester/ua',
                'method'  => 'GET',
                'action'  => $wrapAction(function () use ($getQueryParam): array {
                    $ua = (string) $getQueryParam('ua', '');
                    $uainfo = SimpleStats::detectSystemFromUA(['User-Agent' => $ua]);
                    if ($uainfo && isset($uainfo['device'])) $uainfo['device'] = Stats::humanizeKey($uainfo['device']);
                    return $uainfo ?? ['error' => 'Invalid referrer url!'];
                })
            ],

            [
                'pattern' => 'simplestats/trackingtester/generatestats',
                'method'  => 'GET',
                'action'  => $wrapAction(function () use ($getQueryParam, $parseDateRange): array {
                    $from = $parseDateRange($getQueryParam('from'));
                    $to   = $parseDateRange($getQueryParam('to'));

                    if (!$from || !$to) return ['status' => false, 'error' => 'No range!'];

                    if ((string) $getQueryParam('proceed', '') !== 'yes') {
                        return [
                            'status' => false,
                            'error'  => 'Please confirm that the date ranges from ' . date('d-m-Y', $from) . ' to ' . date('d-m-Y', $to)
                        ];
                    }

                    $mode = $getQueryParam('mode', null);
                    return StatsGenerator::GenerateVisits($from, $to, $mode);
                })
            ],

            [
                'pattern' => 'simplestats/checkrequirements',
                'method'  => 'GET',
                'action'  => $wrapAction(function (): array {
                    $versionArray = explode('.', kirby()->version());
                    $reqs = [
                        'php'    => kirby()->system()->php(),
                        'kirby'  => ((int)$versionArray[0] === 3 && (int)$versionArray[1] >= 5) || ((int)$versionArray[0] === 5 && (int)$versionArray[1] >= 0),
                        'sqlite3'=> class_exists('SQLite3') && in_array('pdo_sqlite', get_loaded_extensions()) && in_array('sqlite3', get_loaded_extensions()),
                    ];

                    $dbRequirements = "PHP=" . ($reqs['php'] ? 'OK' : 'ERROR') . ', ';
                    $dbRequirements .= "SQLite3=" . ($reqs['sqlite3'] ? 'OK' : 'ERROR') . ', ';
                    $dbRequirements .= "Kirby=" . ($reqs['kirby'] ? 'OK' : 'ERROR') . ' --- --- --- ';
                    $dbRequirements .= 'PHP Extensions=' . implode(', ', get_loaded_extensions());

                    return [
                        'dbRequirements'       => $dbRequirements,
                        'dbRequirementsPassed' => $reqs['php'] && $reqs['kirby'] && $reqs['sqlite3'],
                    ];
                })
            ],

            [
                'pattern' => 'simplestats/dbupgrade',
                'method'  => 'GET',
                'action'  => function (): array {
                    $user = kirby()->user();
                    if (!$user || !$user->hasSimpleStatsPanelAccess(true)) {
                        throw new PermissionException('You are not authorised to upgrade the db file.');
                    }

                    $result = Stats::checkUpgradeDatabase(false);
                    return [
                        'status'  => $result,
                        'message' => ($result ? 'Success!' : 'Error!') . ' Check your logs file for more details.',
                    ];
                }
            ],
        ];
    }
];
