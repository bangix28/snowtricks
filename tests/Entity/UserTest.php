<?php


namespace App\Tests\Entity;


use App\Entity\Comment;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{

    public function testGetMailUser()
    {
        $user = new User();
        $user->setMail('test@gmail.com');
        $result = $user->getMail();

        $this->assertSame('test@gmail.com', $result);
    }

    public function testGetRolesUser(){
        $user = new User();
        $result = $user->getRoles();

        $this->assertSame(['ROLE_USER'], $result);
    }

    public function testGetPasswordUser()
    {
        $user = new User();
        $user->setPassword('test');
        $result = $user->getPassword();

        $this->assertSame('test', $result);
    }

    public function testIsVerifiedUser()
    {
        $user = new User();
        $user->setIsVerified(false);


        $this->assertSame(false ,$user->isVerified());
    }

    public function testGetFirstNameUser()
    {
        $user = new User();
        $user->setFirstName('test');

        $this->assertSame('test', $user->getFirstName());
    }
    public function testGetLastNameUser()
    {
        $user = new User();
        $user->setLastName('test');

        $this->assertSame('test', $user->getLastName());
    }

    public function testGetImageUser()
    {
        $user = new User();
        $user->setImage('user.png');
        $this->assertSame('user.png', $user->getImage());
    }

    public function testGetResetTokenUser()
    {
        $user = new User();
        $user->setResetToken('123456789');

        $this->assertSame('123456789', $user->getResetToken());
    }
}
