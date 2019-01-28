<?php
use Goutte\Client;
use PHPUnit\Framework\TestCase;

class SignUpTest extends TestCase
{
    public function testRegistration()
    {
        $client = new Client();
        $crawler = $client->request('GET', 'http://localhost:8000/');
        $link = $crawler->selectLink('Register')->link();
        $crawler = $client->click($link);
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $client->submit($form, array('username' => 'test@invoicelion.com'));
        $nodes = $crawler->filter('.alert');
        $nodes->each(function ($node) {
            $this->assertEquals('', $node->text(), 'Validation error occurred');
        });
        $this->assertCount(0, $nodes);
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $client->submit($form, array('username' => 'test@invoicelion.com','password'=>'test@invoicelion.com','password2'=>'test@invoicelion.com'));
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');        
    }

}
