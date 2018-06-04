<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $balance
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username'], 'required'],
            [['username'], 'filter', 'filter' => 'trim'],
            [['balance'], 'number', 'min' => Yii::$app->params['user.minAmount']],
            [['username'], 'string', 'min' => 3, 'max' => 255],
            [['username'], 'match', 'pattern' => '#^[a-zA-Z]{1}[a-zA-Z0-9]+$#is'],
            [['username'], 'unique', 'targetClass' => self::className(), 'message'=> 'This username has already been taken!'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'balance' => 'Balance',
        ];
    }
    
    /**
     * 
     * @param int $id
     * @return User
     */
    public static function findIdentity($id) {
        return static::findOne($id);
    }
    
    /**
     * 
     * @param string $token
     * @param type $type
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new \yii\base\NotSupportedException('findIdentityByAccessToken is not implemented.');
    }
 
    /**
     * 
     * @return int
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
 
    /**
     * 
     * @throws NotSupportedException
     */
    public function getAuthKey()
    {
        return $this->username;
    }
 
    /**
     * 
     * @param string $authKey
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        if ($user = static::findOne(['username' => $username])){
            return $user;
        }
        
        return static::create($username);
    }   

    public function __toString() {
        return $this->username;
    }
    
    /**
     * 
     * @param string $username
     * @return \self
     */
    static public function create($username) 
    {
        $user = new self([
            'username' => $username,
        ]);
        $user->save();
        return $user;
    }
    
    
    public function sendMoney($amount, User $receiver)
    {
        if ($receiver->id == $this->id) {
            throw new \Exception('You can\'t send means yourself.');
        }
        $curBalance = $this->balance;
        $this->balance -= $amount;
        if ($this->validate('balance')) {
            $historyItem = new History([
                'amount' => $amount,
                'sender_id' => $this->id,
                'receiver_id' => $receiver->id
            ]);
            $transaction = static::getDb()->beginTransaction();
            if ($historyItem->save()) {
//                var_dump('URA!'); exit;
                $receiver->balance += $amount;
                try {
                    if (($meValidated = $this->save()) 
                        && ($heValidated = $receiver->save())) {
                        $transaction->commit();
                        return true;
                    } else {
                        if (!$meValidated) {
                            $errors = implode($this->getErrorSummary(0), PHP_EOL);
                        } elseif (!$heValidated) {
                            $errors = 'Receiver can\'nt receive means '
                                . 'because of the account limitation.';
                        }
                        throw new \Exception($errors);
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }
            } else {
                throw new \Exception(implode($historyItem->getErrorSummary(0), PHP_EOL));
            }
            
        }
        $this->balance = $curBalance;
        throw new \Exception('Not enough balance.');
    }
}