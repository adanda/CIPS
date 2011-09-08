<?php
/**
 * File for the SvnProject Class which represents a Project which is hosted
 * in a SVN Repository
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
 * A class that represents a SVN-hosted Project which implements
 * the Project Interface
 *
 * @category Project
 * @package  CIPS
 * @author   Alfred Danda <alfred.danda@gmail.com>
 * @license  MIT License
 * @link     Project
 */
class SvnProject extends Project
{
    /**
     * Checks out the Source to the data.path
     *
     * @param string $data_path The path to the build-Directory
     * 
     * @return SvnProject The Object itself
     */
    public function checkout($data_path)
    {
        if (!is_dir($data_path.'/'.$this->getSlug())) {
            $umask = umask(0);
            mkdir($data_path.'/'.$this->getSlug(), 0777);
            mkdir($data_path.'/'.$this->getSlug().'/source', 0777);
            mkdir($data_path.'/'.$this->getSlug().'/reports', 0777);
            umask($umask);

            $cmd = 'svn checkout '.$this->getRepository().'/'.$this->getBranch().' '
                .$data_path.'/'.$this->getSlug().'/source';

            $process = new Process($cmd);
            $process->run();
        } else {
            return $this->update($data_path);
        }

        return $this;
    }

    /**
     * Updates the Source to the data.path
     *
     * @param string $data_path The path to the build-Directory
     * 
     * @return SvnProject The Object itself
     */
    public function update($data_path)
    {
        if (is_dir($data_path.'/'.$this->getSlug())) {
            $cmd = 'svn up '.$data_path.'/'.$this->getSlug().'/source';
        }

        $process = new Process($cmd);
        $process->run();

        return $this;
    }
}