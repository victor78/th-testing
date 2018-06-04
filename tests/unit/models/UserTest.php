<?php

namespace tests\models;

use app\models\User;

class UserTest extends \Codeception\Test\Unit
{
    use \Codeception\AssertThrows;
    protected function _before()
    {
        $tester1 = \app\models\User::findByUsername('tester1');
        $tester2 = \app\models\User::findByUsername('tester2');
        $tester3 = \app\models\User::findByUsername('tester3');
        $tester1->balance = 0;
        $tester2->balance = 0;
        $tester1->save();
        $tester2->save();
        
        $this->tester1 = $tester1;
        $this->tester2 = $tester2;
        
        //for testing username of non-existing user
        $tester3->delete();
        
        $this->model = new \app\models\LoginForm([
            'username' => 'tester1',
        ]);

        $this->model->login();
        
    }    
    public function testFindUserById()
    {
        expect_that($user = User::findIdentity(1));
        expect($user->username)->equals('god');

        expect_not(User::findIdentity(999));
    }



    public function testFindUserByUsername()
    {
        expect_that($user = User::findByUsername('god'));
    }

    public function testCorrectSendMoney()
    {
        expect_that($this->tester1->sendMoney(100, $this->tester2));
        $this->tester1->refresh();
        $this->tester2->refresh();
        $this->assertEquals(-100, $this->tester1->balance);
        $this->assertEquals(100, $this->tester2->balance);
    }
    
    public function testIncorrectSendMoney()
    {
        $this->assertThrowsWithMessage(\Exception::class, 'Not enough balance.', function() {
            $this->tester1->sendMoney(9999, $this->tester2);
        });        
        
        $this->tester1->refresh();
        $this->tester2->refresh();
        $this->assertEquals(0, $this->tester1->balance);
        $this->assertEquals(0, $this->tester2->balance);        
    }
    
    public function testMinus1000()
    {
        $this->tester1->balance = -1001;
        expect_not($this->tester1->validate());
    }
}
