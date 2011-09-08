<?php
/**
 * File for the abstract Project Class
 *
 * PHP Version 5.3
 *
 * @category Project
 * @package  CIPS
 * @author   Alfred Danda <alfred.danda@gmail.com>
 * @license  MIT License
 * @link     Project
 */

namespace Cips;

use Symfony\Component\Process\Process;

/**
 * A abstract class that represents a Project that can be build by CIPS
 *
 * @category Project
 * @package  CIPS
 * @author   Alfred Danda <alfred.danda@gmail.com>
 * @license  MIT License
 * @link     Project
 */
abstract class Project
{
    /**
     * The Name of the Project
     * @var string
     */
    private $_name = '';

    /**
     * The URL to the SVN-Repository of the Project
     * @var string
     */
    private $_repository = '';

    /**
     * The Name of the Branch of the Project
     * @var string
     */
    private $_branch = '';

    /**
     * The Slug-Name of the Project
     * @var string
     */
    private $_slug = '';

    /**
     * The Commands to execute before the Building of the Project
     * @var array
     */
    private $_preBuildCommands = array();

    /**
     * The Commands to execute after the Building of the Project
     * @var array
     */
    private $_postBuildCommands = array();

    /**
     * The Command to execute the Tests of the Project
     * @var string
     */
    private $_testCommand = '';

    /**
     * The Emails to be notified for the Project
     * @var string
     */
    private $_notifier = '';

    /**
     * Constructor
     *
     * @param string $name The Name of the Project
     */
    public function __construct($name)
    {
        $this->_name = $name;
    }

    /**
     * Setter for the Name of the Project
     *
     * @param string $name The Name of the Project
     * 
     * @return Project The Object itself
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Getter for the Name
     *
     * @return string The Name of the Project
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Setter for the Repository
     *
     * @param string $repository The URL to the SVN-Repository of the Project
     *
     * @return Project The Object itself
     */
    public function setRepository($repository)
    {
        $this->_repository = $repository;
        return $this;
    }

    /**
     * Getter for the Repository
     *
     * @return string The URL to the SVN-Repository of the Project
     */
    public function getRepository()
    {
        return $this->_repository;
    }

    /**
     * Setter for the Branch
     *
     * @param string $branch The Name of the Branch of the Project
     *
     * @return Project The Object itself
     */
    public function setBranch($branch)
    {
        $this->_branch = $branch;
        return $this;
    }

    /**
     * Getter for the Branch
     *
     * @return string The Name of the Branch of the Project
     */
    public function getBranch()
    {
        return $this->_branch;
    }

    /**
     * Setter for the Slug
     *
     * @param string $slug The Slug-Name of the Project
     *
     * @return Project The Object itself
     */
    public function setSlug($slug)
    {
        $this->_slug = $slug;
        return $this;
    }

    /**
     * Getter for the Slug
     *
     * @return string The Slug of the Project
     */
    public function getSlug()
    {
        return $this->_slug;
    }

    /**
     * Setter for the PreBuildCommands
     *
     * @param array $commands The Commands to execute before the Building
     * of the Project
     *
     * @return Project The Object itself
     */
    public function setPreBuildCommands($commands)
    {
        $this->_preBuildCommands = $commands;
        return $this;
    }

    /**
     * Getter for the PreBuildCommands
     *
     * @return array The Commands to execute before the Building of the Project
     */
    public function getPreBuildCommands()
    {
        return $this->_preBuildCommands;
    }

    /**
     * Setter for the PostBuildCommands
     *
     * @param array $commands The Commands to execute after the Building
     * of the Project
     *
     * @return Project The Object itself
     */
    public function setPostBuildCommands($commands)
    {
        $this->_postBuildCommands = $commands;
        return $this;
    }

    /**
     * Getter for the PostBuildCommands
     *
     * @return array The Commands to execute after the Building of the Project
     */
    public function getPostBuildCommands()
    {
        return $this->_postBuildCommands;
    }

    /**
     * Setter for the TestCommand
     *
     * @param string $command The Command to execute the Tests of the Project
     *
     * @return Project The Object itself
     */
    public function setTestCommand($command)
    {
        $this->_testCommand = $command;
        return $this;
    }

    /**
     * Getter for the TestCommand
     *
     * @return string The Command to execute the Tests of the Project
     */
    public function getTestCommand()
    {
        return $this->_testCommand;
    }

    /**
     * Setter for the Notifier
     *
     * @param string $notifier The Emailadresses for Notifications for the Project
     *
     * @return Project The Object itself
     */
    public function setNotifier($notifier)
    {
        $this->_notifier = $notifier;
        return $this;
    }

    /**
     * Getter for the Notifier
     *
     * @return string The Notifier for the Project
     */
    public function getNotifier()
    {
        return $this->_notifier;
    }

    /**
     * Gets the last Build of the Project
     *
     * @param SQLite3 $db The SQLite Database Object
     * 
     * @return SQLite3Result The Result of the last Build
     */
    public function getLastBuild($db)
    {
        if ($db) {
            $stmt = $db->prepare(
                'SELECT * FROM builds WHERE slug = :slug ORDER BY build DESC LIMIT 1'
            );
            $stmt->bindValue(':slug', $this->getSlug(), SQLITE3_TEXT);

            if (FALSE !== $result = $stmt->execute()) {
                if (FALSE !== $result = $result->fetchArray(\SQLITE3_ASSOC)) {
                    return $result;
                }
            }
        }

        return FALSE;
    }

    /**
     * Gets the Builds for the Project
     *
     * @param SQLite3 $db            The SQLite Database Object
     * @param int     $num_of_builds The number of builds that should be returned
     * @param int     $offset        The offset of builds that should be returned
     * 
     * @return SQLite3Result The Builds for the Project
     */
    public function getBuilds($db, $num_of_builds, $offset = 0)
    {
        $builds = array();

        if ($db) {
            $stmt = $db->prepare(
                'SELECT * FROM builds WHERE slug = :slug '.
                'ORDER BY build DESC LIMIT :offset, :num'
            );
            $stmt->bindValue(':slug', $this->getSlug(), SQLITE3_TEXT);
            $stmt->bindValue(':offset', $offset, SQLITE3_TEXT);
            $stmt->bindValue(':num', $num_of_builds, SQLITE3_TEXT);

            if (FALSE !== $result = $stmt->execute()) {
                while ($build = $result->fetchArray(\SQLITE3_ASSOC)) {
                    $builds[] = $build;
                }
            }
        }

        return $builds;
    }

    /**
     * Get a Chart for the Checkstyle Errors of the Project
     *
     * @param SQLite3 $db            The SQLite3 Database Object
     * @param in      $num_of_builds The Number of builds shown in the Chart
     *
     * @return string The URL of a Chart from the Google Chart API
     */
    public function getCheckstyleData($db, $num_of_builds)
    {
        $stmt = $db->prepare(
            'SELECT * FROM builds_checkstyle WHERE slug = :slug '.
            'ORDER BY build DESC LIMIT :limit'
        );
        $stmt->bindValue(':slug', $this->getSlug(), SQLITE3_TEXT);
        $stmt->bindValue(':limit', $num_of_builds, SQLITE3_TEXT);

        $data = '[[';

        if (FALSE !== $result = $stmt->execute()) {
            while ($row = $result->fetchArray(\SQLITE3_ASSOC)) {
                $data .= '['.$row['build'].','.$row['errors'].'],';
            }
        }

        return $data.']]';
    }

    /**
     * Sends Emails with a fail Notification to the Notifier
     *
     * @param SwiftMailerExtension $mailer The SwiftMailer Object
     * @param string               $output The Output of the failing Test
     * @param TwigExtension        $twig   The Twig Templating Class
     * @param string               $sender The Sender (from) of the Mails
     * 
     * @return SvnProject The Object itself
     */
    protected function sendFailNotification($mailer, $output, $twig, $sender)
    {
        $mailer->send(
            \Swift_Message::newInstance()
            ->setFrom($sender)
            ->addTo($this->getNotifier())
            ->setSubject('[CIPS '.$this->getName().'] Tests failed')
            ->setBody(
                "One or more Tests of the Project ".$this->getName().
                " failed:\n\r\n\r".$output, 'Text/PLAIN'
            )
            ->setBody(
                $twig->render(
                    'mailFailure.html.twig', array(
                        'name'      => $this->getName(),
                        'output'    => $output)
                ), 'Text/HTML'
            )
        );
    }

    /**
     * Function which builds the Project
     *
     * @param string               $data_path    The path to the root of the
     *                                           Source Code
     * @param SQLite3              $db           The SQLite Database Object
     * @param SwiftMailerExtension $mailer       The SwiftMailer Object
     * @param TwigExtension        $twig         The Twig Templating Object
     * @param string               $email_sender The Sender (from) of the Mails
     *
     * @return Project The Object itself
     */
    public function build($data_path, $db, $mailer, $twig, $email_sender)
    {
        $success = TRUE;
        $output  = '';

        foreach ($this->getPreBuildCommands() as $cmd) {
            $process = new Process($cmd, $data_path.'/'.$this->getSlug().'/source');
            $process->run();
        }

        if ($this->getTestCommand() != '') {
            $process = new Process(
                $this->getTestCommand(), $data_path.'/'.$this->getSlug().'/source'
            );
            $process->run();
            if (!$process->isSuccessful()) {
                $success = FALSE;
            }
            $output = $process->getOutput();
        }

        foreach ($this->getPostBuildCommands() as $cmd) {
            $process = new Process($cmd, $data_path.'/'.$this->getSlug().'/source');
            $process->run();
        }

        if (!$success) {
            $this->sendFailNotification(
                $mailer,
                $output,
                $twig,
                $email_sender
            );
        }

        $last_build = $this->getLastBuild($db);

        $stmt = $db->prepare(
            'INSERT INTO builds (slug, build, success, output, build_date) '.
            'VALUES (:slug, :build, :success, :output, :date)'
        );
        $stmt->bindValue(':slug', $this->getSlug(), SQLITE3_TEXT);
        if ($last_build) {
            $stmt->bindValue(':build', $last_build['build']+1, SQLITE3_INTEGER);
        } else {
            $stmt->bindValue(':build', 1, SQLITE3_INTEGER);
        }
        $stmt->bindValue(':success', $success, SQLITE3_TEXT);
        $stmt->bindValue(':output', $output, SQLITE3_TEXT);
        $stmt->bindValue(':date', date('Y-m-d H:i:s'), SQLITE3_TEXT);

        if (FALSE === $stmt->execute()) {
            throw new \RuntimeException(
                sprintf('Unable to save project "%s".', $this->getName())
            );
        }

        // check if Checkstyle-Data is available
        if (is_file($data_path.'/'.$this->getSlug().'/reports/checkstyle.xml')) {
            $checkstyle = simplexml_load_file(
                $data_path.'/'.$this->getSlug().'/reports/checkstyle.xml'
            );
            $errors = 0;
            foreach ($checkstyle as $file) {
                $errors += count($file->error);
            }

            $stmt = $db->prepare(
                'INSERT INTO builds_checkstyle (slug, build, files, errors) '
                .'VALUES (:slug, :build, :files, :errors)'
            );

            $stmt->bindValue(':slug', $this->getSlug(), SQLITE3_TEXT);
            if ($last_build) {
                $stmt->bindValue(':build', $last_build['build']+1, SQLITE3_INTEGER);
            } else {
                $stmt->bindValue(':build', 1, SQLITE3_INTEGER);
            }
            $stmt->bindValue(
                ':files', count($checkstyle->file), SQLITE3_TEXT
            );
            $stmt->bindValue(':errors', $errors, SQLITE3_TEXT);

            if (FALSE === $stmt->execute()) {
                throw new \RuntimeException(
                    sprintf('Unable to save project "%s".', $this->getName())
                );
            }
        }

        return $this;
    }

    /**
     * Function which checks out the Source Code to the given path
     *
     * @param string $data_path The path to the root of the Source Code
     *
     * @return Project The Object itself
     */
    public abstract function checkout($data_path);

    /**
     * Function which updates the Source Code on the given path
     *
     * @param string $data_path The path to the root of the Source Code
     *
     * @return Project The Object itself
     */
    public abstract function update($data_path);
}