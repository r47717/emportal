<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

class RegisterForm extends Model
{
    public $username;
    public $password;
    public $cpassword;
    public $phone;
    public $email;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    
    public function rules()
    {
        return [
            [['username', 'password', 'cpassword', 'phone', 'email'], 'required', 'message' => 'Поле не может быть пустым'],
            ['password', 'validatePassword'],
            ['email', 'email', 'message' => 'Неверный формат адреса электронной почты'],
            ['cpassword', 'compare', 'compareAttribute' => 'password', 'message' => "Пароли не совпадают"],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            //if (!$user || !$user->validatePassword($this->password)) {
            //    $this->addError($attribute, 'Incorrect username or password.');
            //}
        }
    }

    public function register()
    {
        if ($this->validate()) {

            $user = [];
            $user['name'] = $this->username;
            $user['password'] = $this->password;
            $user['email'] = $this->email;
            $user['phone'] = $this->phone;
            $user['password'] = $this->password;
            User::addNewUser($user);

            //return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
            return true;
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
