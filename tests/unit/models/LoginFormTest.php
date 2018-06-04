<?php

namespace tests\models;

use app\models\LoginForm;

class LoginFormTest extends \Codeception\Test\Unit
{
    use \Codeception\AssertThrows;
    private $model;

    protected function _after()
    {
        \Yii::$app->user->logout();
    }

    public function testLoginNoUser()
    {
        $this->model = new LoginForm([
            'username' => 'notexistingusername',
        ]);

        expect_that($this->model->login());
        expect_not(\Yii::$app->user->isGuest);
    }

    public function testLoginWrongUsername()
    {
        $this->model = new LoginForm([
            'username' => '+___1123',
        ]);
        $this->model->getUser();
        

        
        expect_that(\Yii::$app->user->isGuest);
    }

    public function testLoginCorrect()
    {
        $this->model = new LoginForm([
            'username' => 'demo',
        ]);

        expect_that($this->model->login());
        expect_not(\Yii::$app->user->isGuest);
    }

}
