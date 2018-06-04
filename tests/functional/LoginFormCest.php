<?php

class LoginFormCest
{
    public function _before(\FunctionalTester $I)
    {
        $I->amOnRoute('site/login');
    }

    public function openLoginPage(\FunctionalTester $I)
    {
        $I->see('Login', 'h1');

    }

    // demonstrates `amLoggedInAs` method
    public function internalLoginById(\FunctionalTester $I)
    {
        $I->amLoggedInAs(1);
        $I->amOnPage('/');
        $I->see('Logout (god)');
    }

    // demonstrates `amLoggedInAs` method
    public function internalLoginByInstance(\FunctionalTester $I)
    {
        $I->amLoggedInAs(\app\models\User::findByUsername('god'));
        $I->amOnPage('/');
        $I->see('Logout (god)');
    }

    public function loginWithEmptyCredentials(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', []);
        $I->expectTo('see validations errors');
        $I->see('Username cannot be blank.');
    }

    public function loginWithWrongCredentials(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', [
            'LoginForm[username]' => '+++',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Username is invalid.');
    }

    public function loginSuccessfully(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'demo',
        ]);
        $I->see('Logout (demo)');
        $I->dontSeeElement('form#login-form');              
    }
}