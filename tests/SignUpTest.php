<?php
use Goutte\Client;
use PHPUnit\Framework\TestCase;

class SignUpTest extends TestCase
{
    public function testRegistration()
    {
        $client = new Client();
        $crawler = $client->request('GET', 'http://localhost:8000/');
        $crawler = $client->click($crawler->selectLink('Register')->link());
        $crawler = $client->submit($crawler->selectButton('Submit')->form(), array('username' => 'test@invoicelion.com'));
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
        $crawler->filter('.alert')->each(function ($node) { $this->assertEquals('', $node->text(), 'Validation error occurred'); });
        $crawler = $client->submit($crawler->selectButton('Submit')->form(), array('username' => 'test@invoicelion.com','password'=>'test@invoicelion.com','password2'=>'test@invoicelion.com'));
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
        $crawler->filter('.alert')->each(function ($node) { $this->assertEquals('', $node->text(), 'Validation error occurred'); });
        return array('username' => 'test@invoicelion.com','password'=>'test@invoicelion.com');
    }

    /**
     * @depends testRegistration
     */
    public function testLogin($credentials)
    {
        $client = new Client();
        $crawler = $client->request('GET', 'http://localhost:8000/');
        $crawler = $client->submit($crawler->selectButton('Submit')->form(), $credentials);
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
        $crawler->filter('.alert')->each(function ($node) { $this->assertEquals('', $node->text(), 'Validation error occurred'); });
        return array($client, $crawler);
    }

    /**
     * @depends testLogin
     */
    public function testAddHours($context)
    {
        list($client, $crawler) = $context;
        $crawler = $client->click($crawler->selectLink('Hours')->link());
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
        $crawler = $client->click($crawler->selectLink('Add hours')->link());
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
    }

}
