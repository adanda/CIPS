<?php
/**
 * File for the abstract Notification Class
 *
 * PHP Version 5.3
 *
 * @category Notification
 * @package  CIPS\Notifications
 * @author   Alfred Danda <alfred.danda@gmail.com>
 * @license  MIT License
 * @link     Notification
 */

namespace Cips\Notifications;

/**
 * A abstract class that represents a Notification for a failing CIPS Project
 *
 * @category Notification
 * @package  CIPS\Notifications
 * @author   Alfred Danda <alfred.danda@gmail.com>
 * @license  MIT License
 * @link     Notification
 */
abstract class Notification
{
    /**
     * Function that notifies of failing builds
     *
     * @param Project           $project The Project to send the Notification for
     * @param string            $output  The output of the failed tests
     * @param Silex\Application $app     The application
     * 
     * @return void
     */
    public abstract function notify($project, $output, $app);
}