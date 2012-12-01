<?php
/**
 * File for the GitProject Class which represents a Project which is hosted
 * in a Git Repository
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

use Symfony\Component\Process\Process;

/**
 * A class that represents a Git-hosted Project which implements
 * the Project Interface
 *
 * @category Project
 * @package  CIPS\Projects
 * @author   Alfred Danda <alfred.danda@gmail.com>
 * @license  MIT License
 * @link     Project
 */
class GitProject extends Project
{
    /**
     * Checks out the Source to the data.path
     *
     * @param string $data_path The path to the build-Directory
     * @param string $composer  The path to the composer executable
     * 
     * @return GitProject The Object itself
     */
    public function checkout($data_path, $composer = '')
    {
        if (!is_dir($data_path.'/'.$this->getSlug())) {
            $umask = umask(0);
            mkdir($data_path.'/'.$this->getSlug(), 0777);
            mkdir($data_path.'/'.$this->getSlug().'/source', 0777);
            mkdir($data_path.'/'.$this->getSlug().'/reports', 0777);
            umask($umask);

            $cmd = 'git clone '.$this->getRepository().' '
                .$data_path.'/'.$this->getSlug().'/source';

            $process = new Process($cmd);
            $process->run();

            $this->runComposer($data_path, $composer);
        } else {
            return $this->update($data_path, $composer);
        }

        return $this;
    }

    /**
     * Updates the Source to the data.path
     *
     * @param string $data_path The path to the build-Directory
     * @param string $composer  The path to the composer executable
     * 
     * @return GitProject The Object itself
     */
    public function update($data_path, $composer = '')
    {
        if (is_dir($data_path.'/'.$this->getSlug())) {
            $cmd = 'git clean --force';
            $dir = $data_path.'/'.$this->getSlug().'/source';

            $process = new Process($cmd, $dir);
            $process->run();

            $cmd = 'git pull';
            $process = new Process($cmd, $dir);
            $process->run();

            $this->runComposer($data_path, $composer);
        }

        return $this;
    }
}
