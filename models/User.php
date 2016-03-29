<?php

namespace app\models;
use Yii;

class User extends \yii\base\Object implements \yii\web\IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $roleId;
    public $authKey;
    public $accessToken;

    public function __construct($userdata)
    {
        $this->id = $userdata['id'];
        $this->username = $userdata['name'];
        $this->password = $userdata['password'];
        $this->roleId = $userdata['roleId'];
        $this->authKey = 0;
        $this->accessToken = 0;
    }

    public static function getAllUsers()
    {
        $connection = Yii::$app->db;
        $sql = "SELECT user.id, user.name, user.phone, user.email, user.password, role.name as role".
            " FROM user LEFT JOIN role ON user.roleId = role.id ORDER BY user.id";
        $command = $connection->createCommand($sql);
        $rows = $command->queryAll();
        return $rows;
    }

    public static function getUserById($id)
    {
        $connection = Yii::$app->db;
        $sql = "SELECT * FROM user WHERE id = " . $id;
        $command = $connection->createCommand($sql);
        $res = $command->queryOne();
        return new User($res);
    }

    public function getNameById($id)
    {
        $user = self::getUserById($id);
        return $user->getName();
    }

    public static function deleteUser($id)
    {
        $connection = Yii::$app->db;
        $sql = sprintf("DELETE FROM user WHERE id = %d", $id);
        $command = $connection->createCommand($sql);
        $command->query();
    }

    public static function updateUser($user)
    {
        $roleId = Yii::$app->db->createCommand("SELECT id FROM role WHERE name='" . $user['role'] . "'")->queryOne();

        $sql = sprintf("UPDATE user SET name='%s', phone='%s', email='%s', roleId=%d WHERE id=%d", 
            $user['name'], $user['phone'], $user['email'], $roleId['id'], $user['id']);
        Yii::$app->db->createCommand($sql)->query();
    }

    public static function addNewUser($user)
    {
        $connection = Yii::$app->db;
        $sql = sprintf("INSERT INTO user VALUES (0, '%s', '%s', '%s', '%s', 2)", 
            $user['name'], $user['phone'], $user['email'], md5($user['password']));
        $command = $connection->createCommand($sql);
        $command->query();
    }

    public static function getUserTypes()
    {
        $command = Yii::$app->db->createCommand("SELECT name FROM role");
        $ret = $command->queryAll();
        $result = [];
        foreach ($ret as $index => $field) {
            $result[] = $field['name'];
        }
        return $result;
    }

    public static function isAdmin($id)
    {
        $user = self::getUserById($id);

        return $user->roleId == 1; // 1 is admin
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        //return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
        return self::getUserById($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    public static function findByUsername($username)
    {
        $users = self::getAllUsers();
        foreach ($users as $user) {
            if(strcasecmp($user['name'], $username) === 0) {
                return new User($user);
            }
        }

        return null;
    }

    public function getName()
    {
        return $this->username;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return true; //$this->authKey === $authKey;
    }

    public function validatePassword($password)
    {
        return $this->password === md5($password);
    }
}
