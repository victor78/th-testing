<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class SendMoneyForm extends Model
{
    public $username;
    public $amount;

    private $_receiver = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'amount'], 'required'],
            [['username'], 'filter', 'filter' => 'trim'],
            [['username'], 'string', 'min' => 3, 'max' => 255],
            [['username'], 'match', 'pattern' => '#^[a-zA-Z]{1}[a-zA-Z0-9]+$#is'],
            [['username'], 'exist', 'targetClass' => User::class,
                'targetAttribute' => ['username' => 'username'],
                'message'=> 'Such user does not exist!'],
            [['amount'], 'number', 'min' => 1],
        ];
    }



    public function send()
    {
        if ($this->validate()) {
            $user = Yii::$app->user->identity;
            try {
                $result = $user->sendMoney($this->amount, $this->receiver);
                return $result;
            } catch (\Exception $e) {
                $this->addError('amount', $e->getMessage());
            }
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getReceiver()
    {
        if ($this->_receiver === false) {
            $this->_receiver = User::findByUsername($this->username);
        }

        return $this->_receiver;
    }
}
