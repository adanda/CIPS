<?php

require_once __DIR__.'/../../src/Cips/Project.php';
require_once __DIR__.'/../stubs/DB.php';

/**
 * Unit Tests for Project Class
 *
 * @author Alfred Danda
 */
class ProjectTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $svnProject = new Cips\SvnProject('test');
        $this->assertEquals('test', $svnProject->getName(),
            'SvnProject::__construct() sets the Name of the Project');
        $this->assertEquals('', $svnProject->getRepository(),
            'SvnProject::__construct() sets an empty Repository');
        $this->assertEquals('', $svnProject->getBranch(),
            'SvnProject::__construct() sets an empty Branch');
        $this->assertEquals('', $svnProject->getSlug(),
            'SvnProject::__construct() sets an empty Slug');
        $this->assertEquals(array(), $svnProject->getPreBuildCommands(),
            'SvnProject::__construct() sets an empty Array for the '.
            'PreBuildCommands');
        $this->assertEquals('', $svnProject->getTestCommand(),
            'SvnProject::__construct() sets an empty TestCommand');
    }

    public function testSetName()
    {
        $svnProject = new Cips\SvnProject('test');
        $svnProject->setName('myTestName with öäü');
        $this->assertEquals('myTestName with öäü', $svnProject->getName(),
            'SvnProject::setName() sets the Name for the Project');
    }

    public function testSetRepository()
    {
        $svnProject = new Cips\SvnProject('test');
        $svnProject->setRepository('svn://testRepository/testProject');
        $this->assertEquals('svn://testRepository/testProject',
            $svnProject->getRepository(),
            'SvnProject::setRepository() sets the Repository for the Project');
    }

    public function testSetBranch()
    {
        $svnProject = new Cips\SvnProject('test');
        $svnProject->setBranch('branches/myTestBranch');
        $this->assertEquals('branches/myTestBranch', $svnProject->getBranch(),
            'SvnProject::setBranch() sets the Branch for the Project');
    }

    public function testSetSlug()
    {
        $svnProject = new Cips\SvnProject('test');
        $svnProject->setSlug('my-test-project');
        $this->assertEquals('my-test-project', $svnProject->getSlug(),
            'SvnProject::setSlug() sets the Slug for the Project');
    }

    public function testSetPreBuildCommands()
    {
        $commands = array(
            'my_first_command',
            'my second command',
            'my-third-command'
        );
        $svnProject = new Cips\SvnProject('test');
        $svnProject->setPreBuildCommands($commands);
        $this->assertEquals($commands, $svnProject->getPreBuildCommands(),
            'SvnProject::setPreBuildCommands() sets the PreBuildCommands for '.
            'the Project');
    }

    public function testSetNotifier()
    {
        $svnProject = new Cips\SvnProject('test');
        $svnProject->setNotifier('myNotifier@notifier.cips');
        $this->assertEquals('myNotifier@notifier.cips', $svnProject->getNotifier(),
            'SvnProject::setNotifier() sets the Notifier for the Project');
    }

    public function testGetLastBuild()
    {
        $svnProject = new Cips\SvnProject('test');

        $this->assertFalse($svnProject->getLastBuild(FALSE),
            'SvnProject::getLastBuild() returns false when no DB is given');

        $result = $this->getMock('myResult');
        $result->expects($this->any())
            ->method('fetchArray')
            ->will($this->returnValue(FALSE));

        $statement = $this->getMock('myStatement');
        $statement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($result));

        $db = $this->getMock('myDb');
        $db->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($statement));

        $this->assertFalse($svnProject->getLastBuild($db),
            'SvnProject::getLastBuild() returns false when no result is given '.
            'from the DB');

        $result = $this->getMock('myResult');
        $result->expects($this->any())
            ->method('fetchArray')
            ->will($this->returnValue('db-result'));

        $statement = $this->getMock('myStatement');
        $statement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue(FALSE));

        $db = $this->getMock('myDb');
        $db->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($statement));

        $this->assertFalse($svnProject->getLastBuild($db),
            'SvnProject::getLastBuild() returns false when the Statement returns '.
            'no Result');

        $result = $this->getMock('myResult');
        $result->expects($this->any())
            ->method('fetchArray')
            ->will($this->returnValue('db-result'));

        $statement = $this->getMock('myStatement');
        $statement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($result));

        $db = $this->getMock('myDb');
        $db->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($statement));

        $this->assertEquals('db-result', $svnProject->getLastBuild($db),
            'SvnProject::getLastBuild() returns the Result of the Query');
    }

    public function testGetBuilds()
    {
        $this->markTestIncomplete('Not implemented yet!');
    }

    public function testSendFailNotification()
    {
        $this->markTestIncomplete('Not implemented yet!');
    }

    public function testBuild()
    {
        $this->markTestIncomplete('Not implemented yet!');
    }

    public function testGetCheckstyleData()
    {
        $this->markTestIncomplete('Not implemented yet!');
    }

    public function testSetPostBuildCommands()
    {
        $commands = array(
            'my_first_command',
            'my second command',
            'my-third-command'
        );
        $svnProject = new Cips\SvnProject('test');
        $svnProject->setPostBuildCommands($commands);
        $this->assertEquals($commands, $svnProject->getPostBuildCommands(),
            'SvnProject::setPostBuildCommands() sets the PostBuildCommands for '.
            'the Project');
    }
}