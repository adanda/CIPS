<?php
/**
 * Define Routes of the Application
 *
 * PHP Version 5.3
 *
 * @category Application
 * @package  CIPS
 * @author   Alfred Danda <alfred.danda@gmail.com>
 * @license  MIT License
 * @link     Project
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->error(function(Exception $e) use ($app)
{
    return new Response(
        $app['twig']->render('error.html.twig', array(
            'error' => $e
        )),
        500
    );
});


$app->get('/', function() use ($app)
{
    $projects = require __DIR__.'/../config/projects.php';
    return $app['twig']->render('index.html.twig', array(
        'projects'  => $projects,
        'db'        => $app['db']
    ));
});

$app->get('/project/{slug}', function($slug) use ($app)
{
    $projects = require __DIR__.'/../config/projects.php';
    $project = $projects[$slug];
    return $app['twig']->render('project.html.twig', array(
        'project'           => $project,
        'has_checkstyle'    => file_exists(
            $app['data.path'].'/build/'.$project->getSlug().'/reports/checkstyle.xml'
        ),
        'has_testresult'    => file_exists(
            $app['data.path'].'/build/'.$project->getSlug().'/reports/testresult.xml'
        ),
        'has_coverage'      => file_exists(
            $app['data.path'].'/build/'.$project->getSlug().'/reports/coverage.xml'
        ),
        'db'                => $app['db']
    ));
});

$app->get('/checkstyle/{slug}', function($slug) use ($app)
{
    $projects = require __DIR__.'/../config/projects.php';
    $project = $projects[$slug];
    $checkstyle = simplexml_load_file(
        $app['data.path'].'/build/'.$project->getSlug().'/reports/checkstyle.xml'
    );
    $errors = 0;
    foreach ($checkstyle as $file) {
        $errors += count($file->error);
    }
    return $app['twig']->render('checkstyle.html.twig', array(
        'project'           => $project,
        'checkstyle'        => $checkstyle,
        'errors'            => $errors,
        'checkstyle_time'   => date(
            'd.m.Y H:i:s', filemtime($app['data.path'].'/build/'
            .$project->getSlug().'/reports/checkstyle.xml')
        ),
        'build_path'        => $app['data.path'].'/build/'
            .$project->getSlug().'/source/'
    ));
});

$app->get('/loadbuilds/{slug}/{limit}/{offset}',
    function($slug, $limit, $offset) use ($app)
    {
        $projects = require __DIR__.'/../config/projects.php';
        $project = $projects[$slug];
        $builds = $project->getBuilds($app['db'], $limit, $offset);

        return $app['twig']->render('builds.html.twig', array(
            'builds' => $builds
        ));
    }
);

$app->get('/testresult/{slug}', function($slug) use ($app)
{
    $projects = require __DIR__.'/../config/projects.php';
    $project = $projects[$slug];
    $testsuites = simplexml_load_file(
        $app['data.path'].'/build/'.$project->getSlug().'/reports/testresult.xml'
    );

    return $app['twig']->render('testresult.html.twig', array(
        'project'           => $project,
        'testsuites'        => $testsuites,
        'build_path'        => $app['data.path'].'/build/'
            .$project->getSlug().'/source/'
        )
    );
});

$app->get('/coverage/{slug}', function($slug) use ($app)
{
    $projects = require __DIR__.'/../config/projects.php';
    $project = $projects[$slug];
    $coverage = simplexml_load_file(
        $app['data.path'].'/build/'.$project->getSlug().'/reports/coverage.xml'
    );

    return $app['twig']->render('coverage.html.twig', array(
        'project'           => $project,
        'coverage'          => $coverage,
        'build_path'        => $app['data.path'].'/build/'
            .$project->getSlug().'/source/'
        )
    );
});

$app->post('/coverage/details', function(Request $request) use ($app)
{
    $projects = require __DIR__.'/../config/projects.php';
    $project = $projects[$request->get('slug')];
    $path = $request->get('path');
    $coverage = simplexml_load_file(
        $app['data.path'].'/build/'.$project->getSlug().'/reports/coverage.xml'
    );

    $file = file($path);
    if ($file === false) {
        return '<h1>Error reading file!</h1>';
    }

    foreach ($coverage->project->package as $package) {
        foreach ($package->file as $row) {
            if ($row['name'] == $path) {
                break 2;
            }
        }
    }

    if (!isset($row)) {
        return '<h1>Error reading file!</h1>';
    }

    $rows = array();
    foreach ($row->line as $line) {
        $rows[$line['num']->__toString()] = $line['count']->__toString();
    }

    return $app['twig']->render('coverage_details.html.twig', array(
        'file'  => $file,
        'rows'  => $rows
        )
    );
});

$app->get('/build/{slug}', function($slug) use ($app)
{
    $projects = require __DIR__.'/../config/projects.php';
    $project = $projects[$slug];
    $project->checkout($app['build.path'])
        ->build($app);

    return $app['twig']->render('builds.html.twig', array(
        'builds' => array(new Cips\Build($project->getLastBuild($app['db'])))
    ));
});
