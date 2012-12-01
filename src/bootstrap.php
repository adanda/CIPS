<?php
/**
 * Define Bootstraps of the Application
 *
 * PHP Version 5.3
 *
 * @category Application
 * @package  CIPS
 * @author   Alfred Danda <alfred.danda@gmail.com>
 * @license  MIT License
 * @link     Project
 */

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/twig_extensions.php';

// create new Application
$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));
$app['twig']->addFilter('str_replace', new Twig_Filter_Function('twig_str_replace'));

$app->register(new Silex\Provider\SwiftmailerServiceProvider(), array());

$app['config'] = require __DIR__.'/../config/config.php';
$app['data.path']   = realpath(__DIR__.'/../data');
$app['build.path']  = $app['data.path'].'/build';

$app['db.path']     = $app->share(function ($app)
{
    return $app['data.path'].'/cips.db';
});

$app['db.schema']   = <<<EOF
CREATE TABLE IF NOT EXISTS builds (
    slug        TEXT,
    build       INT,
    success     BOOLEAN,
    output      TEXT,
    build_date  TEXT DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (slug, build)
);

CREATE TABLE IF NOT EXISTS builds_checkstyle (
    slug     TEXT,
    build    INT,
    files    TEXT,
    errors   TEXT,
    PRIMARY KEY (slug, build)
);

CREATE TABLE IF NOT EXISTS builds_testresult (
    slug        TEXT,
    build       INT,
    tests       INT,
    assertions  INT,
    failures    INT,
    errors      INT,
    PRIMARY KEY (slug, build)
);

CREATE TABLE IF NOT EXISTS builds_coverage (
    slug                TEXT,
    build               INT,
    files               INT,
    loc                 INT,
    ncloc               INT,
    classes             INT,
    methods             INT,
    coveredmethods      INT,
    conditionals        INT,
    coveredconditionals INT,
    statements          INT,
    coveredstatements   INT,
    elements            INT,
    coveredelements     INT,
    PRIMARY KEY (slug, build)
);
EOF;

$app['db.migration']   = <<<EOF
ALTER TABLE builds ADD COLUMN revision TEXT;
EOF;

$app['db'] = $app->share(function () use ($app)
{
    $chmod = false;
    if (!file_exists($app['db.path'])) {
        $chmod = true;
    }
    try {
        $db = new \SQLite3($app['db.path']);
        $db->busyTimeout(1000);
        $db->exec($app['db.schema']);
        if ($chmod) {
            chmod($app['db.path'], 0777);
        }
    } catch (Exception $e) {
        return null;
    }

    return $db;
});

return $app;