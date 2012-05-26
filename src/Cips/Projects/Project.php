<?php
/**
 * File for the abstract Project Class
 *
 * PHP Version 5.3
 *
 * @category Project
 * @package  CIPS\Projects
 * @author   Alfred Danda <alfred.danda@gmail.com>
 * @license  MIT License
 * @link     Project
 */

namespace Cips\Projects;

use Cips\Build;
use Symfony\Component\Process\Process;

/**
 * A abstract class that represents a Project that can be build by CIPS
 *
 * @category Project
 * @package  CIPS\Projects
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
     * The Notification Class for the Project
     * @var Notification
     */
    private $_notifier = '';

    /**
     * The link to the documentation of the project
     * @var string
     */
    private $_documentation_link = '';

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
     * @param Notification $notifier The Notification Class for the Project
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
     * @return Notification The Notification for the Project
     */
    public function getNotifier()
    {
        return $this->_notifier;
    }

    /**
     * Setter for the documentation link
     * 
     * @param string $documentation_link The link to the projects documentation
     * 
     * @return Project The Object itself
     */
    public function setDocumentationLink($documentation_link)
    {
        $this->_documentation_link = $documentation_link;
        return $this;
    }

    /**
     * Getter for the documentation link
     *
     * @return string The link to the projects documentation
     */
    public function getDocumentationLink()
    {
        return $this->_documentation_link;
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

            if (false !== $result = $stmt->execute()) {
                if (false !== $result = $result->fetchArray(\SQLITE3_ASSOC)) {
                    return $result;
                }
            }
        }

        return false;
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

            if (false !== $result = $stmt->execute()) {
                while ($build = $result->fetchArray(\SQLITE3_ASSOC)) {
                    $builds[] = new Build($build);
                }
            }
        }

        return $builds;
    }

    /**
     * Get the data for a Chart for the testresults of the Project
     *
     * @param SQLite3 $db            The SQLite3 Database Object
     * @param int     $num_of_builds The Number of builds shown in the Chart
     *
     * @return string The data for the chart
     */
    public function getTestresultChartData($db, $num_of_builds)
    {
        $stmt = $db->prepare(
            'SELECT * FROM builds_testresult WHERE slug = :slug '.
            'ORDER BY build DESC LIMIT :limit'
        );
        $stmt->bindValue(':slug', $this->getSlug(), SQLITE3_TEXT);
        $stmt->bindValue(':limit', $num_of_builds, SQLITE3_TEXT);

        $tests_data = '[';
        $assertions_data = '[';
        $failures_data = '[';
        $errors_data = '[';

        if (false !== $result = $stmt->execute()) {
            while ($row = $result->fetchArray(\SQLITE3_ASSOC)) {
                $tests_data .= '['.$row['build'].','.$row['tests'].'],';
                $assertions_data .= '['.$row['build'].','.$row['assertions'].'],';
                $failures_data .= '['.$row['build'].','.$row['failures'].'],';
                $errors_data .= '['.$row['build'].','.$row['errors'].'],';
            }
        }

        $tests_data .= ']';
        $assertions_data .= ']';
        $failures_data .= ']';
        $errors_data .= ']';

        return '['.$tests_data.', '.$assertions_data.', '
            .$failures_data.', '.$errors_data.']';
    }

    /**
     * Get the data for a Chart for the coverage of the Project
     *
     * @param SQLite3 $db            The SQLite3 Database Object
     * @param int     $num_of_builds The Number of builds shown in the Chart
     *
     * @return string The data for the chart
     */
    public function getCoverageChartData($db, $num_of_builds)
    {
        $stmt = $db->prepare(
            'SELECT build, elements, coveredelements FROM builds_coverage '.
            'WHERE slug = :slug ORDER BY build DESC LIMIT :limit'
        );
        $stmt->bindValue(':slug', $this->getSlug(), SQLITE3_TEXT);
        $stmt->bindValue(':limit', $num_of_builds, SQLITE3_TEXT);

        $data = '[[';

        if (false !== $result = $stmt->execute()) {
            while ($row = $result->fetchArray(\SQLITE3_ASSOC)) {
                $data .= '['.$row['build'].',';
                $data .= ($row['coveredelements']) / ($row['elements'] / 100);
                $data .= '],';
            }
        }

        $data .= ']]';
        return $data;
    }

    /**
     * Get the data for a chart for the Checkstyle Errors of the Project
     *
     * @param SQLite3 $db            The SQLite3 Database Object
     * @param int     $num_of_builds The Number of builds shown in the Chart
     *
     * @return string The data for the chart
     */
    public function getCheckstyleChartData($db, $num_of_builds)
    {
        $stmt = $db->prepare(
            'SELECT * FROM builds_checkstyle WHERE slug = :slug '.
            'ORDER BY build DESC LIMIT :limit'
        );
        $stmt->bindValue(':slug', $this->getSlug(), SQLITE3_TEXT);
        $stmt->bindValue(':limit', $num_of_builds, SQLITE3_TEXT);

        $data = '[[';

        if (false !== $result = $stmt->execute()) {
            while ($row = $result->fetchArray(\SQLITE3_ASSOC)) {
                $data .= '['.$row['build'].','.$row['errors'].'],';
            }
        }

        return $data.']]';
    }

    /**
     * Function which builds the Project
     *
     * @param Silex\Application $app The application
     *
     * @return Project The Object itself
     */
    public function build($app)
    {
        $success = true;
        $output  = '';

        foreach ($this->getPreBuildCommands() as $cmd) {
            $process = new Process(
                $cmd, $app['build.path'].'/'.$this->getSlug().'/source'
            );
            $process->run();
            $output .= $this->generateComposedOutput($cmd, $process->getOutput());
        }

        if ($this->getTestCommand() != '') {
            $process = new Process(
                $this->getTestCommand(),
                $app['build.path'].'/'.$this->getSlug().'/source'
            );
            $process->run();
            if (!$process->isSuccessful()) {
                $success = false;
            }
            $output .= $this->generateComposedOutput(
                $this->getTestCommand(),
                $process->getOutput()
            );
        }

        foreach ($this->getPostBuildCommands() as $cmd) {
            $process = new Process(
                $cmd, $app['build.path'].'/'.$this->getSlug().'/source'
            );
            $process->run();
            $output .= $this->generateComposedOutput($cmd, $process->getOutput());
        }

        if (!$success) {
            $this->getNotifier()->notify($this, $output, $app);
        }

        $last_build = $this->getLastBuild($app['db']);

        $stmt = $app['db']->prepare(
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

        if (false === $stmt->execute()) {
            throw new \RuntimeException(
                sprintf('Unable to save project "%s".', $this->getName())
            );
        }

        // check if testresult-data is available
        if (is_file($app['build.path'].'/'.$this->getSlug().
        '/reports/testresult.xml')) {
            $testresult = simplexml_load_file(
                $app['build.path'].'/'.$this->getSlug().'/reports/testresult.xml'
            );
            $tests = 0;
            $assertions = 0;
            $failures = 0;
            $errors = 0;
            foreach ($testresult as $testsuite) {
                $tests += $testsuite['tests'];
                $assertions += $testsuite['assertions'];
                $failures += $testsuite['failures'];
                $errors += $testsuite['errors'];
            }

            $stmt = $app['db']->prepare(
                'INSERT INTO builds_testresult '
                .'(slug, build, tests, assertions, failures, errors) '
                .'VALUES (:slug, :build, :tests, :assertions, :failures, :errors)'
            );

            $stmt->bindValue(':slug', $this->getSlug(), SQLITE3_TEXT);
            if ($last_build) {
                $stmt->bindValue(':build', $last_build['build']+1, SQLITE3_INTEGER);
            } else {
                $stmt->bindValue(':build', 1, SQLITE3_INTEGER);
            }
            $stmt->bindValue(':tests', $tests, SQLITE3_INTEGER);
            $stmt->bindValue(':assertions', $assertions, SQLITE3_INTEGER);
            $stmt->bindValue(':failures', $failures, SQLITE3_INTEGER);
            $stmt->bindValue(':errors', $errors, SQLITE3_INTEGER);

            if (false === $stmt->execute()) {
                throw new \RuntimeException(
                    sprintf('Unable to save project "%s".', $this->getName())
                );
            }
        }

        // check if code-coverage data is available
        if (is_file($app['build.path'].'/'.$this->getSlug().
        '/reports/coverage.xml')) {
            $coverage = simplexml_load_file(
                $app['build.path'].'/'.$this->getSlug().'/reports/coverage.xml'
            );
            $metrics = $coverage->project->metrics;

            $stmt = $app['db']->prepare(
                'INSERT INTO builds_coverage '
                .'(slug, build, files, loc, ncloc, classes, methods, '
                .'coveredmethods, conditionals, coveredconditionals, statements, '
                .'coveredstatements, elements, coveredelements) '
                .'VALUES (:slug, :build, :files, :loc, :ncloc, :classes, :methods, '
                .':coveredmethods, :conditionals, :coveredconditionals, '
                .':statements, :coveredstatements, :elements, :coveredelements)'
            );

            $stmt->bindValue(':slug', $this->getSlug(), SQLITE3_TEXT);
            if ($last_build) {
                $stmt->bindValue(':build', $last_build['build']+1, SQLITE3_INTEGER);
            } else {
                $stmt->bindValue(':build', 1, SQLITE3_INTEGER);
            }
            $stmt->bindValue(':files', $metrics['files'], SQLITE3_INTEGER);
            $stmt->bindValue(':loc', $metrics['loc'], SQLITE3_INTEGER);
            $stmt->bindValue(':ncloc', $metrics['ncloc'], SQLITE3_INTEGER);
            $stmt->bindValue(':classes', $metrics['classes'], SQLITE3_INTEGER);
            $stmt->bindValue(':methods', $metrics['methods'], SQLITE3_INTEGER);
            $stmt->bindValue(
                ':coveredmethods',
                $metrics['coveredmethods'],
                SQLITE3_INTEGER
            );
            $stmt->bindValue(
                ':conditionals',
                $metrics['conditionals'],
                SQLITE3_INTEGER
            );
            $stmt->bindValue(
                ':coveredconditionals',
                $metrics['coveredconditionals'],
                SQLITE3_INTEGER
            );
            $stmt->bindValue(':statements', $metrics['statements'], SQLITE3_INTEGER);
            $stmt->bindValue(
                ':coveredstatements',
                $metrics['coveredstatements'],
                SQLITE3_INTEGER
            );
            $stmt->bindValue(':elements', $metrics['elements'], SQLITE3_INTEGER);
            $stmt->bindValue(
                ':coveredelements',
                $metrics['coveredelements'],
                SQLITE3_INTEGER
            );

            if (false === $stmt->execute()) {
                throw new \RuntimeException(
                    sprintf('Unable to save project "%s".', $this->getName())
                );
            }
        }

        // check if Checkstyle-Data is available
        if (is_file($app['build.path'].'/'.$this->getSlug().
        '/reports/checkstyle.xml')) {
            $checkstyle = simplexml_load_file(
                $app['build.path'].'/'.$this->getSlug().'/reports/checkstyle.xml'
            );
            $errors = 0;
            foreach ($checkstyle as $file) {
                $errors += count($file->error);
            }

            $stmt = $app['db']->prepare(
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

            if (false === $stmt->execute()) {
                throw new \RuntimeException(
                    sprintf('Unable to save project "%s".', $this->getName())
                );
            }
        }

        return $this;
    }

    /**
     * Composes the command and the output to a readable string.
     *
     * @param string $cmd    The command that was executed
     * @param string $output The output of the command
     *
     * @return string The composed output
     */
    protected function generateComposedOutput($cmd, $output)
    {
        $composed_output = '$ '.$cmd."\n\r";
        $composed_output .= $output."\n\r\n\r";

        return $composed_output;
    }

    /**
     * Changes the ANSI-Colors of a CLI-Output to a HTML-String.
     * 
     * @param string $ansi The ANSI-String to convert
     * 
     * @return string A HTML-String
     */
    public function consoleOutputToHtml($ansi)
    {
        $converts = array(
            "[30;42m"    => "<span class='green_bg'>",
            "[30;43m"    => "<span class='yellow_bg'>",
            "[41;37m"     => "<span class='red_bg'>",
            "[37;41m"     => "<span class='red_bg'>",
            "[33;1m"      => "<span class='yellow_fg'>",
            "[0m"         => "</span>",
            "[2K"         => "",
            "\n"          => "<br/>"
        );
        $html = str_replace(array_keys($converts), $converts, $ansi);
        return $html;
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
