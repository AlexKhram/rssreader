<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 19.11.2016
 * Time: 12:52
 */

namespace Alex\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class UserController
{
    public function login(Request $request, Application $app)
    {
        if ($userId = $app['modelUser']->auth()) {
            echo 'go to rss';
            return $app->redirect('/');
        }
        if ($request->getMethod() == 'POST') {
            $email = $request->get('email');
            $password = $request->get('password');
            $warning = $app['modelUser']->userDataValidate($email, $password);
            if (empty($warning)) {
                $user = $app['modelUser']->getUserByEmail($email);
                if (!$user) {
                    $warning[] = 'User with given e-mail not found';
                } else {
                    if (!$warning[] = $app['modelUser']->checkPassword($user, $password)) {
                        echo 'go to rss';
                        return $app->redirect('/');
                    }
                }
            }
        }

        print_r($warning);
        return '<form action="/login" method="post">
 <label>Email<input type="text" name="email"></label> 
 <label>Password<input type="password" name="password"></label>
  <input type="submit">
 </form>';
    }

    public function register(Request $request, Application $app)
    {
        if ($userId = $app['modelUser']->auth()) {
            echo 'go to rss';
            return $app->redirect('/');
        }
        if ($request->getMethod() == 'POST') {
            $email = $request->get('email');
            $password = $request->get('password');
            $warning = $app['modelUser']->userDataValidate($email, $password);

            if (empty($warning)) {
                $user = $app['modelUser']->getUserByEmail($email);
                if (!$user) {
                    $app['modelUser']->addNewUser($email, $password);
                    echo 'go to rss';
                    return $app->redirect('/');
                } else {
                    $warning[] = 'User with given e-mail already exist';
                }
            }
        }

        print_r($warning);
        return '<form action="/register" method="post">
 <label>Email<input type="text" name="email"></label> 
 <label>Password<input type="password" name="password"></label>
  <input type="submit">
 </form>';
    }

    public function logout(Request $request, Application $app)
    {
        $app['modelUser']->unsetAuth();
        echo 'go to login';
        return $app->redirect('/login');
    }
}