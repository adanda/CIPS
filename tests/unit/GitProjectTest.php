<?php

require_once __DIR__.'/../../src/Cips/Projects/Project.php';
require_once __DIR__.'/../../src/Cips/Projects/GitProject.php';
require_once __DIR__.'/../stubs/Process.php';
require_once __DIR__.'/../stubs/DB.php';

/**
 * Unit Tests for GitProject Class
 *
 * @author Alfred Danda
 */
class GitProjectTest extends PHPUnit_Framework_TestCase
{
    public function testCheckout()
    {
        $gitProject = new Cips\Projects\GitProject('test');

        $result = $this->getMock('Process');
        $result->expects($this->any())
            ->method('run')
            ->will($this->returnValue(TRUE));

        $this->assertEquals($gitProject, $gitProject->checkout(''),
            'GitProject::checkout() returns the object itself when the project '.
            'does not exist and the checkout is sucessfull');
    }

    public function testUpdate()
    {
        $gitProject = new Cips\Projects\GitProject('test');

        $result = $this->getMock('Process');
        $result->expects($this->any())
            ->method('run')
            ->will($this->returnValue(TRUE));

        $this->assertEquals($gitProject, $gitProject->update(''),
            'GitProject::update() returns the object itself when the project '.
            'does not exist and the update is sucessfull');
    }
}