<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 19.11.2016
 * Time: 13:51
 */

namespace Alex\Model;

use Alex\Model\Model;

class User extends Model
{
    protected $table = 'users';

    public function getUserByEmail($email)
    {
        $email = addslashes($email);
        return $this->app['db']->fetchAssoc("SELECT * FROM {$this->table} WHERE email = '{$email}'");
    }

    public function addNewUser($email, $password)
    {
        $email = addslashes($email);
        $password = password_hash($password, PASSWORD_DEFAULT);
        $this->app['db']->insert($this->table, array('email' => $email, 'password' => $password));
        $userId = $this->app['db']->lastInsertId();
        $this->app['session']->set('auth', 1);
        $this->app['session']->set('userId', $userId);
        return;
    }

    public function userDataValidate($email, $password)
    {
        $warning = [];
        if ($email == NULL or !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $warning[] = "E-mail not valid.";
        }
        if ($password == NULL) {
            $warning[] = "Password is empty.";
        }
        return $warning;
    }

    public function auth()
    {
        $auth = $this->app['session']->get('auth');
        $id = $this->app['session']->get('userId');

        if (!empty($auth) and $auth == 1 and !empty($id)) {
            return $id;
        } else {
            return false;
        }
    }

    public function checkPassword($user, $password)
    {
        if (password_verify($password, $user['password'])) {
            $this->setAuth($user['id']);
            return;
        }
        return 'Wrong password';
    }

    public function setAuth($userId)
    {
        $this->app['session']->set('auth', 1);
        $this->app['session']->set('userId', $userId);
        return;
    }

    public function unsetAuth()
    {
        $this->app['session']->clear();
        return;
    }
}