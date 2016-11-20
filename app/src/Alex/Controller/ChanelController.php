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
    public function index(Request $request, Application $app, $warning = [])
    {
        if (!$userId = $app['modelUser']->auth()) {
            echo 'go to login';
            return $app->redirect('/login');
        }

        $channels = $app['modelChannel']->getChannelsForUser($userId);
        $feeds = [];
        foreach ($channels as $channel) {
            $feeds[] = $app['modelFeed']->getFeedsByChannel($channel['chanel_id']);
        }

        return $app['twig']->render('channels.twig', array(
            'userId' => $userId,
            'channels' => $channels,
            'feeds' => $feeds,
            'warning' => $warning,
        ));
    }

    public function add(Request $request, Application $app)
    {
        if (!$userId = $app['modelUser']->auth()) {
            return $app->redirect('/login');
        }
        $warning = [];
        $chanelUrl = $request->get('channelUrl');
        if ($channel = $app['modelChannel']->getChannelByUrl($chanelUrl)) {
            if (!$chanelForUser = $app['modelChannel']->getChannelForUser($userId, $channel['id'])) {
                $app['modelChannel']->addChannelForUser($userId, $channel['id']);
            } else {
                $warning[] = "Channel already exist";
            }
        } else {
            $rss = @simplexml_load_file($chanelUrl);
            if ($rss and isset($rss->channel) and isset($rss->channel->item)) {
                $channelId = $app['modelChannel']->addChannel($chanelUrl);
                $app['modelChannel']->addChannelForUser($userId, $channelId);
                foreach ($rss->channel->item as $feed) {
                    $app['modelFeed']->addFeed($channelId, $feed);
                }
            } else {
                $warning[] = "Invalid url for RSS";
            }
        }
        return $this->index($request, $app, $warning);
    }

    public function delete(Request $request, Application $app)
    {
        if (!$userId = $app['modelUser']->auth()) {
            return $app->redirect('/login');
        }

        $channelId = $request->get('channelId');
        var_dump($channelId);
        $app['modelChannel']->deleteChannelForUser($userId, $channelId);
        if (!$chanelForUser = $app['modelChannel']->getChannelsForUserById($channelId)) {
            $app['modelFeed']->dleteFeedsForChannel($channelId);
            $app['modelChannel']->dleteChannel($channelId);
            echo 'delete chanel';
        }
        return $app->redirect('/');
    }

    public function update(Request $request, Application $app)
    {
        $channels = $app['modelChannel']->getAllChannels();
        foreach ($channels as $channel){
            $rss = @simplexml_load_file($channel['url']);
            if ($rss and isset($rss->channel) and isset($rss->channel->item)) {
                foreach ($rss->channel->item as $feed) {
                    $app['modelFeed']->addFeed($channel['id'], $feed);
                }
            }
        }
        return $app->json(['status'=>'updated']);
    }
}