<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class LoginTest extends PantherTestCase
{
    public function testPasswordGettersAndSetters()
    {
        $user = new User();
        $this->assertNull($user->getPassword());
    
        $user->setPassword('Severity@Viselike9@Clobber');
    
        //le password est hashé dans le formulaire
        $this->assertEquals('Severity@Viselike9@Clobber', $user->getPassword());
    }

    public function testNameGettersAndSetters()
    {
        $user = new User();
        $this->assertNull($user->getNom());
    
        $user->setNom('Severity@Viselike9@Clobber');
    
        $this->assertEquals('Severity@Viselike9@Clobber', $user->getNom());
    }

    public function testEmailGettersAndSetters()
    {
        $user = new User();
        $this->assertNull($user->getEmail());
    
        $user->setEmail('Severity@Viselike9@Clobber');
    
        $this->assertEquals('Severity@Viselike9@Clobber', $user->getEmail());
    }


    public function testApi()
    {
        $user = new User();
        $this->assertNull($user->getEmail());
    
        $user->setEmail('Severity@Viselike9@Clobber');
    
        $this->assertEquals('Severity@Viselike9@Clobber', $user->getEmail());
    }

    public function testFormPwdHasher()
    {
        $uuid = Uuid::v7()->ToString();
        
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $this->assertSelectorExists('form[name="registration_form"]');

        $form = $crawler->selectButton('Register')->form();
        $form['registration_form[email]'] = 'newtuser'.$uuid.'@example.com';
        $form['registration_form[plainPassword]'] = 'password123';
        $form['registration_form[agreeTerms]'] = true;

        $client->submit($form);

        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'newtuser'.$uuid.'@example.com']);

        $this->assertNotNull($user);

        $this->assertNotEquals('password123', $user->getPassword() );

        $passwordHasher = $client->getContainer()->get(UserPasswordHasherInterface::class);
        $this->assertTrue($passwordHasher->isPasswordValid($user, 'password123'));

    }

    public function testUserCreation()
    {
        $uuid = Uuid::v7()->ToString();
        
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $this->assertSelectorExists('form[name="registration_form"]');

        $form = $crawler->selectButton('Register')->form();
        $form['registration_form[email]'] = 'newtuser'.$uuid.'@example.com';
        $form['registration_form[plainPassword]'] = 'password123';
        $form['registration_form[agreeTerms]'] = true;

        $client->submit($form);

        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'newtuser'.$uuid.'@example.com']);

        $this->assertNotNull($user);

    }

    public function testLogin()
    {
        
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertSelectorExists('form');

        $csrfToken = $crawler->filter('input[name="_csrf_token"]')->attr('value');
    
        $form = $crawler->filter('form')->form([
            '_username' => 'testuser@example.com',
            '_password' => 'password123',
            '_csrf_token' => $csrfToken,
        ]);
    
        $client->submit($form);

        $security = $client->getContainer()->get('security.token_storage');
        $token = $security->getToken();
        $user = $token ? $token->getUser() : null;
    
        $this->assertNotNull($user);
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('testuser@example.com', $user->getUserIdentifier());
    

    }
    


}
