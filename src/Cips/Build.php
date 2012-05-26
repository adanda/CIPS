<?php
/**
 * File for the Build Class.
 *
 * PHP Version 5.3
 *
 * @category Build
 * @package  CIPS\Build
 * @author   Alfred Danda <alfred.danda@gmail.com>
 * @license  MIT License
 * @link     Build
 */

namespace Cips;

/**
 * A class that represents a single Build of a Project.
 *
 * @category Build
 * @package  CIPS\Build
 * @author   Alfred Danda <alfred.danda@gmail.com>
 * @license  MIT License
 * @link     Build
 */
class Build
{
    /**
     * The number of the build.
     * @var int
     */
    private $_number;

    /**
     * The console output of the Build.
     * @var string
     */
    private $_output = '';

    /**
     * The success status of the Build.
     * @var bool
     */
    private $_success;

    /**
     * The date and time of the Build.
     */
    private $_date;

    /**
     * Constructor.
     *
     * @param array $build An array with the Build values
     */
    public function __construct($build)
    {
        $this->_number = $build['build'];
        $this->_output = $build['output'];
        $this->_success = $build['success'];
        $this->_date = $build['build_date'];
    }

    /**
     * Setter for the number of the Build.
     *
     * @param int $number The number of the Build
     * 
     * @return Build The Object itself
     */
    public function setNumber($number)
    {
        $this->_number = $number;
        return $this;
    }

    /**
     * Getter for the number of the Build.
     *
     * @return int The number of the Build
     */
    public function getNumber()
    {
        return $this->_number;
    }

    /**
     * Setter for the output of the Build.
     *
     * @param string $output The output of the Build
     * 
     * @return Build The Object itself
     */
    public function setOutput($output)
    {
        $this->_output = $output;
        return $this;
    }

    /**
     * Getter for the output of the Build.
     *
     * @return string The output of the Build
     */
    public function getOutput()
    {
        return $this->_output;
    }

    /**
     * Setter for the success of the Build.
     *
     * @param bool $success The success of the Build
     * 
     * @return Build The Object itself
     */
    public function setSuccess($success)
    {
        $this->_success = $success;
        return $this;
    }

    /**
     * Getter for the success of the Build.
     *
     * @return bool The success of the Build
     */
    public function getSuccess()
    {
        return $this->_success;
    }

    /**
     * Setter for the date of the Build.
     *
     * @param string $date The date of the Build
     * 
     * @return Build The Object itself
     */
    public function setDate($date)
    {
        $this->_date = $date;
        return $this;
    }

    /**
     * Getter for the date of the Build.
     *
     * @return string The date of the Build
     */
    public function getDate()
    {
        return $this->_date;
    }

    /**
     * Changes the ANSI-Colors of a CLI-Output to a HTML-String.
     * 
     * @return string A HTML-String
     */
    public function getOutputAsHtml()
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
        $html = str_replace(array_keys($converts), $converts, $this->_output);
        return $html;
    }
}
