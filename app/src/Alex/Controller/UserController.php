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
            return $app->redirect('/');
        }

        return $app['twig']->render('login.twig', array(
            'userId' => $userId,
            'act' => 'login'
        ));
    }

    public function loginPost(Request $request, Application $app)
    {
        if ($userId = $app['modelUser']->auth()) {
            return $app->redirect('/');
        }

        $email = $request->get('email');
        $password = $request->get('password');
        $warning = $app['modelUser']->userDataValidate($email, $password);
        if (empty($warning)) {
            $user = $app['modelUser']->getUserByEmail($email);
            if (!$user) {
                return $app->json(["error" => "User with given e-mail not found"], 400);
            } else {
                if (!$warning[] = $app['modelUser']->checkPassword($user, $password)) {
                    return $app->json(["status" => "User is logined"], 200);
                }
            }
        }
        return $app->json($warning, 400);
    }

    public function register(Request $request, Application $app)
    {
        if ($userId = $app['modelUser']->auth()) {
            return $app->redirect('/');
        }

        return $app['twig']->render('login.twig', array(
            'userId' => $userId,
            'act' => 'register',
        ));
    }

    public function registerPost(Request $request, Application $app)
    {
        if ($userId = $app['modelUser']->auth()) {
            return $app->redirect('/');
        }

        $email = $request->get('email');
        $password = $request->get('password');
        $warning = $app['modelUser']->userDataValidate($email, $password);

        if (empty($warning)) {
            $user = $app['modelUser']->getUserByEmail($email);
            if (!$user) {
                $app['modelUser']->addNewUser($email, $password);
                return $app->json(["status" => "User is registered"], 200);
            } else {
                return $app->json(["error"=>'User with given e-mail already exist'], 400);
            }
        }

        return $app->json($warning, 400);
    }


    public function logout(Request $request, Application $app)
    {
        $app['modelUser']->unsetAuth();

        return $app->redirect('/login');
    }
}