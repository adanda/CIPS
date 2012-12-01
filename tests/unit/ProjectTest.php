<?php

require_once __DIR__.'/../../src/Cips/Projects/Project.php';
require_once __DIR__.'/../../src/Cips/Build.php';
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
        $svnProject = new Cips\Projects\SvnProject('test');
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
        $svnProject = new Cips\Projects\SvnProject('test');
        $svnProject->setName('myTestName with öäü');
        $this->assertEquals('myTestName with öäü', $svnProject->getName(),
            'SvnProject::setName() sets the Name for the Project');
    }

    public function testSetRepository()
    {
        $svnProject = new Cips\Projects\SvnProject('test');
        $svnProject->setRepository('svn://testRepository/testProject');
        $this->assertEquals('svn://testRepository/testProject',
            $svnProject->getRepository(),
            'SvnProject::setRepository() sets the Repository for the Project');
    }

    public function testSetBranch()
    {
        $svnProject = new Cips\Projects\SvnProject('test');
        $svnProject->setBranch('branches/myTestBranch');
        $this->assertEquals('branches/myTestBranch', $svnProject->getBranch(),
            'SvnProject::setBranch() sets the Branch for the Project');
    }

    public function testSetSlug()
    {
        $svnProject = new Cips\Projects\SvnProject('test');
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
        $svnProject = new Cips\Projects\SvnProject('test');
        $svnProject->setPreBuildCommands($commands);
        $this->assertEquals($commands, $svnProject->getPreBuildCommands(),
            'SvnProject::setPreBuildCommands() sets the PreBuildCommands for '.
            'the Project');
    }

    public function testSetNotifier()
    {
        $svnProject = new Cips\Projects\SvnProject('test');
        $svnProject->setNotifier('myNotifier@notifier.cips');
        $this->assertEquals('myNotifier@notifier.cips', $svnProject->getNotifier(),
            'SvnProject::setNotifier() sets the Notifier for the Project');
    }

    public function testGetLastBuild()
    {
        $svnProject = new Cips\Projects\SvnProject('test');

        $this->assertfalse($svnProject->getLastBuild(false),
            'SvnProject::getLastBuild() returns false when no DB is given');

        $result = $this->getMock('myResult');
        $result->expects($this->any())
            ->method('fetchArray')
            ->will($this->returnValue(false));

        $statement = $this->getMock('myStatement');
        $statement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($result));

        $db = $this->getMock('myDb');
        $db->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($statement));

        $this->assertfalse($svnProject->getLastBuild($db),
            'SvnProject::getLastBuild() returns false when no result is given '.
            'from the DB');

        $result = $this->getMock('myResult');
        $result->expects($this->any())
            ->method('fetchArray')
            ->will($this->returnValue('db-result'));

        $statement = $this->getMock('myStatement');
        $statement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue(false));

        $db = $this->getMock('myDb');
        $db->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($statement));

        $this->assertfalse($svnProject->getLastBuild($db),
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
        $svnProject = new Cips\Projects\SvnProject('test');

        $this->assertEquals(array(), $svnProject->getBuilds(false, 20),
            'SvnProject::getBuilds() returns an empty Array when no DB is given');

        $result = $this->getMock('myResult');
        $result->expects($this->any())
            ->method('fetchArray')
            ->will($this->returnValue(false));

        $statement = $this->getMock('myStatement');
        $statement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($result));

        $db = $this->getMock('myDb');
        $db->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($statement));

        $this->assertEquals(array(), $svnProject->getBuilds($db, 20),
            'SvnProject::getBuilds() returns an empty Array when no result is '.
            'given from the DB');

        $result = $this->getMock('myResult');
        $result->expects($this->any())
            ->method('fetchArray')
            ->will($this->returnValue('db-result'));

        $statement = $this->getMock('myStatement');
        $statement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue(false));

        $db = $this->getMock('myDb');
        $db->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($statement));

        $this->assertEquals(array(), $svnProject->getBuilds($db, 20),
            'SvnProject::getBuilds() returns an empty Array when the Statement '.
            'returns no Result');

        $build1 = array(
            'build'         => 1,
            'success'       => true,
            'build_date'    => '2010-12-12 12:12:12',
            'output'        => 'SUCCESS',
            'revision'      => '1'
        );
        $build2 = array(
            'build'         => 2,
            'success'       => false,
            'build_date'    => '2010-12-13 12:12:12',
            'output'        => 'FAILURE',
            'revision'      => '2'
        );
        $build3 = array(
            'build'         => 3,
            'success'       => true,
            'build_date'    => '2010-12-14 12:12:12',
            'output'        => 'SUCCESS',
            'revision'      => '3'
        );

        $result = $this->getMock('myResult');
        $result->expects($this->any())
            ->method('fetchArray')
            ->will($this->onConsecutiveCalls(
                $build1,
                $build2,
                $build3,
                false
            ));

        $statement = $this->getMock('myStatement');
        $statement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($result));

        $db = $this->getMock('myDb');
        $db->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($statement));

        $this->assertEquals(array(
                new Cips\Build($build1),
                new Cips\Build($build2),
                new Cips\Build($build3)
            ), $svnProject->getBuilds($db, 20),
            'SvnProject::getBuilds() returns the Result of the Query as Builds');
    }

    public function testBuild()
    {
        $this->markTestIncomplete('Not implemented yet!');
    }

    public function testGetCheckstyleChartData()
    {
        $result = $this->getMock('myResult');
        $result->expects($this->any())
            ->method('fetchArray')
            ->will($this->onConsecutiveCalls(
                array('build' => 4, 'errors' => 10),
                array('build' => 5, 'errors' => 15),
                array('build' => 6, 'errors' => 7),
                array('build' => 7, 'errors' => 0),
                array('build' => 8, 'errors' => 23),
                false
            ));

        $statement = $this->getMock('myStatement');
        $statement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($result));

        $db = $this->getMock('myDb');
        $db->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($statement));

        $svnProject = new Cips\Projects\SvnProject('test');

        $this->assertEquals('[[[4,10],[5,15],[6,7],[7,0],[8,23],]]',
            $svnProject->getCheckstyleChartData($db, 20),
            'SvnProject::getCheckstyleChartData() returns a string with the data');

        $result->expects($this->any())
            ->method('fetchArray')
            ->will($this->returnValue(false));

        $this->assertEquals('[[]]', $svnProject->getCheckstyleChartData($db, 20),
            'SvnProject::getCheckstyleChartData() returns a string without data '.
            'if an empty result is given');

        $statement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue(false));

        $this->assertEquals('[[]]', $svnProject->getCheckstyleChartData($db, 20),
            'SvnProject::getCheckstyleChartData() returns a string without data '.
            'if an error with the statement occours');
    }

    public function testGetCoverageChartData()
    {
        $result = $this->getMock('myResult');
        $result->expects($this->any())
            ->method('fetchArray')
            ->will($this->onConsecutiveCalls(
                array('build' => 4, 'coveredelements' => 10, 'elements' => 20),
                array('build' => 5, 'coveredelements' => 15, 'elements' => 60),
                array('build' => 6, 'coveredelements' => 3, 'elements' => 9),
                array('build' => 7, 'coveredelements' => 0, 'elements' => 5),
                array('build' => 8, 'coveredelements' => 20, 'elements' => 20),
                false
            ));

        $statement = $this->getMock('myStatement');
        $statement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($result));

        $db = $this->getMock('myDb');
        $db->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($statement));

        $svnProject = new Cips\Projects\SvnProject('test');

        $this->assertEquals('[[[4,50],[5,25],[6,33.333333333333],[7,0],[8,100],]]',
            $svnProject->getCoverageChartData($db, 20),
            'SvnProject::getCoverageChartData() returns a string with the data');

        $result->expects($this->any())
            ->method('fetchArray')
            ->will($this->returnValue(false));

        $this->assertEquals('[[]]', $svnProject->getCoverageChartData($db, 20),
            'SvnProject::getCoverageChartData() returns a string without data '.
            'if an empty result is given');

        $statement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue(false));

        $this->assertEquals('[[]]', $svnProject->getCoverageChartData($db, 20),
            'SvnProject::getCoverageChartData() returns a string without data '.
            'if an error with the statement occours');
    }

    public function testGetTestresultChartData()
    {
        $result = $this->getMock('myResult');
        $result->expects($this->any())
            ->method('fetchArray')
            ->will($this->onConsecutiveCalls(
                array('build' => 4, 'tests' => 10, 'assertions' => 20,
                    'errors' => 5, 'failures' => 3),
                array('build' => 5, 'tests' => 11, 'assertions' => 30,
                    'errors' => 4, 'failures' => 6),
                array('build' => 6, 'tests' => 12, 'assertions' => 40,
                    'errors' => 3, 'failures' => 3),
                array('build' => 7, 'tests' => 13, 'assertions' => 50,
                    'errors' => 2, 'failures' => 6),
                array('build' => 8, 'tests' => 14, 'assertions' => 60,
                    'errors' => 1, 'failures' => 3),
                false
            ));

        $statement = $this->getMock('myStatement');
        $statement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($result));

        $db = $this->getMock('myDb');
        $db->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($statement));

        $svnProject = new Cips\Projects\SvnProject('test');

        $this->assertEquals('[[[4,10],[5,11],[6,12],[7,13],[8,14],], '.
            '[[4,20],[5,30],[6,40],[7,50],[8,60],], '.
            '[[4,3],[5,6],[6,3],[7,6],[8,3],], '.
            '[[4,5],[5,4],[6,3],[7,2],[8,1],]]',
            $svnProject->getTestresultChartData($db, 20),
            'SvnProject::getTestresultChartData() returns a string with the data');

        $result->expects($this->any())
            ->method('fetchArray')
            ->will($this->returnValue(false));

        $this->assertEquals('[[], [], [], []]',
            $svnProject->getTestresultChartData($db, 20),
            'SvnProject::getTestresultChartData() returns a string without data '.
            'if an empty result is given');

        $statement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue(false));

        $this->assertEquals('[[], [], [], []]',
            $svnProject->getTestresultChartData($db, 20),
            'SvnProject::getTestresultChartData() returns a string without data '.
            'if an error with the statement occours');
    }

    public function testSetPostBuildCommands()
    {
        $commands = array(
            'my_first_command',
            'my second command',
            'my-third-command'
        );
        $svnProject = new Cips\Projects\SvnProject('test');
        $svnProject->setPostBuildCommands($commands);
        $this->assertEquals($commands, $svnProject->getPostBuildCommands(),
            'SvnProject::setPostBuildCommands() sets the PostBuildCommands for '.
            'the Project');
    }

    public function testSetTestCommand()
    {
        $command = 'my Test Command';
        $svnProject = new Cips\Projects\SvnProject('test');
        $svnProject->setTestCommand($command);
        $this->assertEquals($command, $svnProject->getTestCommand(),
            'SvnProject::setTestCommand() sets the TestCommand for the Project');
    }
}