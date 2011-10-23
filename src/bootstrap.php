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

require_once __DIR__.'/../vendor/silex.phar';

// create new Application
$app = new Silex\Application();

// register Namespaces
$app['autoloader']->registerNamespaces(
    array(
        'Cips'      => __DIR__,
        'Symfony'   => __DIR__.'/../vendor'
    )
);

// register Extensions for the Application
$app->register(
    new Silex\Extension\TwigExtension(), array(
        'twig.path' => __DIR__.'/../views',
        'twig.class_path' => __DIR__.'/../vendor/twig/lib',
    )
);

$app['swiftmailer.class_path'] = __DIR__.'/../vendor/swift/lib/classes';
$app->register(new Silex\Extension\SwiftmailerExtension(), array());

$app['data.path']   = __DIR__.'/../data';
$app['build.path']  = $app->share(function ($app)
{
    return $app['data.path'].'/build';
});
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
EOF;

$app['db'] = $app->share(function () use ($app)
{
    $chmod = FALSE;
    if (!file_exists($app['db.path'])) {
        $chmod = TRUE;
    }
    try {
        $db = new \SQLite3($app['db.path']);
        $db->busyTimeout(1000);
        $db->exec($app['db.schema']);
        if ($chmod) {
            chmod($app['db.path'], 0777);
        }
    } catch (Exception $e) {
        return NULL;
    }

    return $db;
});

return $app;