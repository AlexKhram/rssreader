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
            return $app->redirect('/login');
        }

        $channels = $app['modelChannel']->getChannelsForUser($userId);
        $feeds = [];
        foreach ($channels as $channel) {
            $feeds[] = [
                'channelId' => $channel['chanel_id'],
                'channelUrl' => $channel['url'],
                'feedList' => $app['modelFeed']->getFeedsByChannel($channel['chanel_id'])
            ];
        }

        return $app['twig']->render('channels.twig', array(
            'userId' => $userId,
            'channels' => $channels,
            'feeds' => $feeds,
        ));
    }

    public function add(Request $request, Application $app)
    {
        if (!$userId = $app['modelUser']->auth()) {
            return $app->redirect('/login');
        }
        $warning = [];
        $chanelUrl = $request->get('channelUrl');
        if (empty($chanelUrl)) {
            return $app->json(["error" => "Url is empty"], 400);
        }
        if ($channel = $app['modelChannel']->getChannelByUrl($chanelUrl)) {
            if (!$chanelForUser = $app['modelChannel']->getChannelForUser($userId, $channel['id'])) {
                $app['modelChannel']->addChannelForUser($userId, $channel['id']);
            } else {
                return $app->json(["error" => "Channel already exis"], 400);
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
                return $app->json(["error" => "Invalid url for RSS"], 400);
            }
        }
        return $app->json(["status" => "New channel was added"], 200);
    }

    public function delete(Request $request, Application $app)
    {
        if (!$userId = $app['modelUser']->auth()) {
            return $app->redirect('/login');
        }

        $channelId = $request->get('channelId');
        if (empty($channelId)) {
            return $app->json(["error" => "Chose any channel"], 400);
        }
        $app['modelChannel']->deleteChannelForUser($userId, (int)$channelId);
        if (!$chanelForUser = $app['modelChannel']->getChannelsForUserById($channelId)) {
            $app['modelFeed']->dleteFeedsForChannel($channelId);
            $app['modelChannel']->dleteChannel($channelId);
        }
        return $app->json(["status" => "Channel was deleted"], 200);
    }

    public function update(Request $request, Application $app)
    {
        $channels = $app['modelChannel']->getAllChannels();
        foreach ($channels as $channel) {
            $rss = @simplexml_load_file($channel['url']);
            if ($rss and isset($rss->channel) and isset($rss->channel->item)) {
                foreach ($rss->channel->item as $feed) {
                    $app['modelFeed']->addFeed($channel['id'], $feed);
                }
            }
        }
        return $app->json(['status' => 'updated']);
    }
}