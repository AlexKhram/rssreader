<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 19.11.2016
 * Time: 19:30
 */

namespace Alex\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class ChanelController
{
    public function index(Request $request, Application $app)
    {
        if (!$userId = $app['modelUser']->auth()) {
            echo 'go to login';
            return $app->redirect('/login');
        }

        $channels = $app['modelChannel']->getChannelsForUser($userId);
        echo '<ul>';
        foreach ($channels as $channel){
            echo '<li>';
            echo $channel['url'];
            echo '</li>';
            $feeds = $app['modelFeed']->getFeedsByChannel($channel['chanel_id']);
            echo '<ul>';
            foreach ($feeds as $feed){
                echo '<li>';
                echo $feed['title'];
                echo '</li>';
            }
            echo '</ul>';
        }
        echo '</ul>';

//
//        $rss = @simplexml_load_file($channels[0]['url']);
////        var_dump($rss->channel->item);
//
//        foreach ($rss->channel->item as $feed) {
//            $app['modelFeed']->addFeed($channels[0]['chanel_id'], $feed);
//        }


        return '<form action="/chanel" method="post">
 <label>URL for rss<input type="text" name="url"></label> 
  <input type="submit">
 </form>';
    }

    public function add(Request $request, Application $app){
        if (!$userId = $app['modelUser']->auth()) {
            echo 'go to login';
            return $app->redirect('/login');
        }
        $chanelUrl = $request->get('url');
        if($channel = $app['modelChannel']->getChannelByUrl($chanelUrl)){
            if(!$chanelForUser = $app['modelChannel']->getChannelForUser($userId, $channel['id'])){
                $app['modelChannel']->addChannelForUser($userId, $channel['id']);
            }
            echo 'channel exist';
        } else {
            $rss = @simplexml_load_file($chanelUrl);
            if($rss and isset($rss->channel) and isset($rss->channel->item)){
                $channelId = $app['modelChannel']->addChannel($chanelUrl);
                $app['modelChannel']->addChannelForUser($userId, $channelId);
                foreach ($rss->channel->item as $feed) {
                    $app['modelFeed']->addFeed($channelId, $feed);
                }
                echo "Work url";
            } else {
                echo "Invalid url";
            }
        }
        return 1;
    }

    public function delete(Request $request, Application $app){
        if (!$userId = $app['modelUser']->auth()) {
            echo 'go to login';
            return $app->redirect('/login');
        }
        $channelId =  $request->get('channelId');
        $channelId = 8;
        $app['modelChannel']->deleteChannelForUser($userId, $channelId);
        if(!$chanelForUser = $app['modelChannel']->getChannelsForUserById($channelId)){
            $app['modelFeed']->dleteFeedsForChannel($channelId);
            $app['modelChannel']->dleteChannel($channelId);
            echo 'delete chanel';
        }
        return 1;
    }
}