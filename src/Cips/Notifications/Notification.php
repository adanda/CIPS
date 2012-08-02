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
     * Always send a notification or only after a failure.
     * @var boolean
     */
    private $_alwaysSend = false;

    /**
     * Constructor for the Notification class.
     * 
     * @param boolean $alwaysSend Always send notifications or only after failure
     * 
     * @return void
     */
    public function __construct($alwaysSend = false)
    {
        $this->setAlwaysSend($alwaysSend);
    }

    /**
     * Setter for the alwaysSend property of the Notification.
     *
     * @param boolean $alwaysSend Always send notifications or only on failure
     * 
     * @return Cips\Notifications\Notification The Object itself
     */
    public function setAlwaysSend($alwaysSend)
    {
        $this->_alwaysSend = $alwaysSend;
        return $this;
    }

    /**
     * Getter for the alwaysSend property.
     *
     * @return boolean Always send a notification or only on failure
     */
    public function isAlwaysSend()
    {
        return $this->_alwaysSend;
    }

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