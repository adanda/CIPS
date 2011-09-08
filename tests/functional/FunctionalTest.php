<?php

require_once __DIR__ . '/../../vendor/silex.phar';

/**
 * Functional Tests for Cips
 *
 * @author Alfred Danda
 */
class FunctionalTest extends Silex\WebTestCase
{

    public function createApplication()
    {
        $app = require __DIR__ . '/../../src/bootstrap.php';
        require __DIR__ . '/../../src/app.php';
        return $app;
    }

    public function testIndex()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        $this->assertTrue(
            $crawler->filter('html:contains("Project")')->count() == 1
        );
        $this->assertTrue($crawler->filter('html:contains("Build")')->count() == 1);
        $this->assertTrue($crawler->filter('html:contains("Date")')->count() == 1);
    }
}