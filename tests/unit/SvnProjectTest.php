<?php

require_once __DIR__.'/../../src/Cips/Projects/Project.php';
require_once __DIR__.'/../../src/Cips/Projects/SvnProject.php';
require_once __DIR__.'/../stubs/Process.php';
require_once __DIR__.'/../stubs/DB.php';

/**
 * Unit Tests for SvnProject Class
 *
 * @author Alfred Danda
 */
class SvnProjectTest extends PHPUnit_Framework_TestCase
{
    public function testCheckout()
    {
        $svnProject = new Cips\Projects\SvnProject('test');

        $result = $this->getMock('Process');
        $result->expects($this->any())
            ->method('run')
            ->will($this->returnValue(true));

        $this->assertEquals($svnProject, $svnProject->checkout(''),
            'SvnProject::checkout() returns the object itself when the project '.
            'does exist and the checkout is sucessfull');

        $svnProject->setSlug(uniqid('cips', true));
        $this->assertEquals($svnProject, $svnProject->checkout(sys_get_temp_dir()),
            'SvnProject::checkout() returns the object itself when the project '.
            'does not exist and the checkout is sucessfull');
    }

    public function testUpdate()
    {
        $svnProject = new Cips\Projects\SvnProject('test');

        $result = $this->getMock('Process');
        $result->expects($this->any())
            ->method('run')
            ->will($this->returnValue(true));

        $this->assertEquals($svnProject, $svnProject->update(''),
            'SvnProject::update() returns the object itself when the project '.
            'does exist and the update is sucessfull');
    }
}