<?php

namespace models;
use app\models\SendMoneyForm;

class SendMoneyFormTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected $tester1;
    protected $tester2;
    
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

    protected function _after()
    {
        \Yii::$app->user->logout();
    }

    // tests
    public function testNoUsernameNoAmountValidate()
    {
        $model = new SendMoneyForm();
        expect_not($model->validate());
    }
    
    public function testNoUsernameValidate()
    {
        $model = new SendMoneyForm([
            'amount' => 100
        ]);
        expect_not($model->validate());
    }
    
    public function testNoAmountValidate()
    {
        $model = new SendMoneyForm([
            'username' => 'tester1'
        ]);
        expect_not($model->validate());
    }
    
    public function testCorrectValidate()
    {
        $model = new SendMoneyForm([
            'username' => 'tester1',
            'amount' => 100,
        ]);
        expect_that($model->validate());  
    }
    
    public function testNonExistingUserValidate()
    {
        $model = new SendMoneyForm([
            'username' => 'tester3',
            'amount' => 100,
        ]);
        expect_not($model->validate());  
    }
    
    /**
     * from tester1 to tester2 sending 100 points
     */
    public function testCorrectSending()
    {
        $model = new SendMoneyForm([
            'username' => 'tester2',
            'amount' => 100,
        ]);
        
        expect_that($model->send());
        $this->tester1->refresh();
        $this->tester2->refresh();
        $this->assertEquals(-100.00, $this->tester1->balance);
        $this->assertEquals(100.00,  $this->tester2->balance);
    }
    
    /**
     * from tester1 to tester3 sending 100 points
     */
    public function testIncorrectSending()
    {
        $model = new SendMoneyForm([
            'username' => 'tester3',
            'amount' => 100,
        ]);
        
        $balance0 = $this->tester1->balance;
        expect_not($model->send());
        $this->tester1->refresh();
        $balance1 = $this->tester1->balance;
        $delta = $balance1 - $balance0;
        $this->assertEquals(0, $delta);
        
    }
    
    public function testSelfSending()
    {
        $model = new SendMoneyForm([
            'username' => 'tester1',
            'amount' => 100,
        ]);
        
        $balance0 = $this->tester1->balance;
        expect_not($model->send());
        $this->tester1->refresh();
        $balance1 = $this->tester1->balance;
        $delta = $balance1 - $balance0;
        $this->assertEquals(0, $delta);
    }
    
    public function testNotEnoughBalanceSending()
    {
        $model = new SendMoneyForm([
            'username' => 'tester2',
            'amount' => 9999,
        ]);
        
        expect_not($model->send());
        $this->tester1->refresh();
        $this->tester2->refresh();
        $this->assertEquals(0, $this->tester1->balance);
        $this->assertEquals(0,  $this->tester2->balance);
    }
}