<?php




use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserEntityTest extends TestCase
{
    public function testIsTrue(): void
    {
        $user = new User();

        $user->setEmail('true@test.fr')
            ->setFirstname('prénom')
            ->setLastname('nom')
            ->setPassword('password')
            ->setAddress('60 rue de la Rue')
            ->setCity('maville')
            ->setZipcode('94000');

        $this->assertTrue($user->getEmail() === 'true@test.fr');
        $this->assertTrue($user->getFirstname() === 'prénom');
        $this->assertTrue($user->getLastname() === 'nom');
        $this->assertTrue($user->getPassword() === 'password');
        $this->assertTrue($user->getAddress() === '60 rue de la Rue');
        $this->assertTrue($user->getCity() === 'maville');
        $this->assertTrue($user->getZipcode() === '94000');
    }
    public function testIsFales(): void
    {
        $user = new User();

        $user->setEmail('true@test.fr')
            ->setFirstname('prénom')
            ->setLastname('nom')
            ->setPassword('password')
            ->setAddress('60 rue de la Rue')
            ->setCity('maville')
            ->setZipcode('94000');

        $this->assertFalse($user->getEmail() === 'false@test.fr');
        $this->assertFalse($user->getFirstname() === 'false');
        $this->assertFalse($user->getLastname() === 'false');
        $this->assertFalse($user->getPassword() === 'false');
        $this->assertFalse($user->getAddress() === 'false');
        $this->assertFalse($user->getCity() === 'false');
        $this->assertFalse($user->getZipcode() === '14000');
    }
    public function testIsEmpty(): void
    {
        $user = new User();

        $this->assertEmpty($user->getEmail());
        $this->assertEmpty($user->getFirstname());
        $this->assertEmpty($user->getLastname());
//        $this->assertEmpty($user->getPassword());
        $this->assertEmpty($user->getAddress());
        $this->assertEmpty($user->getCity());
        $this->assertEmpty($user->getZipcode());
    }
}
