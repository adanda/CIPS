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


$app->error(function(Exception $e) use ($app)
{
    return $app->redirect('/');
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
        )
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
