<?php
/**
 * File for the EmailNotification Class which represents a Notification by Email
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
 * A class that represents a Email Notification which implements
 * the Notification Interface
 *
 * @category Notification
 * @package  CIPS\Notifications
 * @author   Alfred Danda <alfred.danda@gmail.com>
 * @license  MIT License
 * @link     Notification
 */
class EmailNotification extends Notification
{
    /**
     * The recipients of the Email Notification
     * @var string
     */
    private $_recipients = '';

    /**
     * The sender of the Email Notification
     * @var string
     */
    private $_sender = '';

    /**
     * Constructor for the Class
     * 
     * @param string  $recipients The emailadresses of the recipients
     * @param string  $sender     The sender emailadress
     * @param boolean $alwaysSend Always send notifications or only after failures
     * 
     * @return void
     */
    public function __construct($recipients, $sender, $alwaysSend = false)
    {
        parent::__construct($alwaysSend);
        $this->_recipients = $recipients;
        $this->_sender = $sender;
    }

    /**
     * Notifies users by email
     *
     * @param Project           $project The Project to send the Notification for
     * @param string            $output  The output of the failed tests
     * @param Silex\Application $app     The application
     * 
     * @return void
     */
    public function notify($project, $output, $app)
    {
        $app['mailer']->send(
            \Swift_Message::newInstance()
            ->setFrom($this->_sender)
            ->addTo($this->_recipients)
            ->setSubject('[CIPS '.$project->getName().'] Tests failed')
            ->setBody(
                "One or more Tests of the Project ".$project->getName().
                " failed:\n\r\n\r".$output, 'Text/PLAIN'
            )
            ->setBody(
                $app['twig']->render(
                    'mailFailure.html.twig', array(
                        'name'      => $project->getName(),
                        'output'    => $output)
                ), 'Text/HTML'
            )
        );
    }
}