<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 19.11.2016
 * Time: 19:35
 */

namespace Alex\Model;

use Alex\Model\Model;

class Chanel extends Model
{
    protected $table = "channels";

    public function getChannelByUrl($url)
    {
        return $this->app['db']->fetchAssoc("SELECT * FROM {$this->table} WHERE url = '{$url}'");
    }

    public function getAllChannels()
    {
        return $this->app['db']->fetchAll("SELECT * FROM {$this->table}");
    }

    public function addChannel($url)
    {
        $this->app['db']->insert($this->table, array('url' => $url));
        $channelId = $this->app['db']->lastInsertId();
        return $channelId;
    }

    public function dleteChannel($channelId){
        return $this->app['db']->delete($this->table, array('id'=>$channelId));
    }

    public function addChannelForUser($userId, $channelId)
    {
        $this->app['db']->insert('user_channels', array('user_id' => $userId, 'chanel_id' => $channelId,));
    }

    public function getChannelForUser($userId, $channelId){
        return $this->app['db']->fetchAssoc("SELECT * FROM user_channels WHERE user_id = '{$userId}' AND chanel_id = {$channelId}");
    }

    public function getChannelsForUser($userId){
        return $this->app['db']->fetchAll("SELECT user_channels.chanel_id, channels.url FROM user_channels LEFT JOIN channels ON user_channels.chanel_id = channels.id WHERE user_channels.user_id = {$userId}");
    }

    public function getChannelsForUserById($channelId){
        return $this->app['db']->fetchAll("SELECT * FROM user_channels WHERE chanel_id = {$channelId}");
    }

    public function deleteChannelForUser($userId, $channelId){
        return $this->app['db']->delete('user_channels', array('user_id' => $userId, 'chanel_id'=>$channelId));
    }
}