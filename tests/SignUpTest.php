<?php
use Goutte\Client;
use PHPUnit\Framework\TestCase;

class SignUpTest extends TestCase
{
    public function testRegistration()
    {
        $client = new Client();
        $crawler = $client->request('GET', 'http://localhost:8000/');
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
        $crawler = $client->click($crawler->selectLink('Register')->link());
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
        $crawler = $client->submit($crawler->selectButton('Submit')->form(), array('username' => 'test@invoicelion.com'));
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
        $crawler->filter('.alert-danger .message, .has-error .help-block')->each(function ($node) {$this->fail($node->text());});
        $crawler = $client->submit($crawler->selectButton('Submit')->form(), array('username' => 'test@invoicelion.com', 'password' => 'test@invoicelion.com', 'password2' => 'test@invoicelion.com'));
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
        $crawler->filter('.alert-danger .message, .has-error .help-block')->each(function ($node) {$this->fail($node->text());});
        return array('username' => 'test@invoicelion.com', 'password' => 'test@invoicelion.com');
    }

    /**
     * @depends testRegistration
     */
    public function testLogin($credentials)
    {
        $client = new Client();
        $crawler = $client->request('GET', 'http://localhost:8000/');
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
        $crawler = $client->submit($crawler->selectButton('Submit')->form(), $credentials);
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
        $crawler->filter('.alert-danger .message, .has-error .help-block')->each(function ($node) {$this->fail($node->text());});
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
        $crawler = $client->submit($crawler->selectButton('Save')->form(), array('hours[add_customer]' => 'test customer 1', 'hours[date]' => date('Y-m-d'), 'hours[hours_worked]' => '2', 'hours[hourly_fee]' => '75', 'hours[tax_percentage]' => '21'));
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
        $crawler->filter('.alert-danger .message, .has-error .help-block')->each(function ($node) {$this->fail($node->text());});
    }

    /**
     * @depends testLogin
     */
    public function testAddSubscription($context)
    {
        list($client, $crawler) = $context;
        $crawler = $client->click($crawler->selectLink('Subscriptions')->link());
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
        $crawler = $client->click($crawler->selectLink('Add subscription')->link());
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
        $crawler = $client->submit($crawler->selectButton('Save')->form(), array('subscriptions[add_customer]' => 'test customer 2', 'subscriptions[fee]' => '12', 'subscriptions[tax_percentage]' => '21', 'subscriptions[name]' => 'test subscription', 'subscriptions[months]' => '12', 'subscriptions[from]' => date('Y-m-d')));
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
        $crawler->filter('.alert-danger .message, .has-error .help-block')->each(function ($node) {$this->fail($node->text());});
    }

    /**
     * @depends testLogin
     */
    public function testAddDelivery($context)
    {
        list($client, $crawler) = $context;
        $crawler = $client->click($crawler->selectLink('Deliveries')->link());
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
        $crawler = $client->click($crawler->selectLink('Add delivery')->link());
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
        $crawler = $client->submit($crawler->selectButton('Save')->form(), array('deliveries[add_customer]' => 'test customer 3', 'deliveries[subtotal]' => '120', 'deliveries[tax_percentage]' => '21', 'deliveries[name]' => 'test delivery', 'deliveries[date]' => date('Y-m-d')));
        $this->assertEquals(200, $client->getResponse()->getStatus(), 'Server side error occurred');
        $crawler->filter('.alert-danger .message, .has-error .help-block')->each(function ($node) {$this->fail($node->text());});
    }

}
