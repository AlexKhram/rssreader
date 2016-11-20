<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 20.11.2016
 * Time: 0:21
 */

namespace Alex\Model;

use Alex\Model\Model;

class Feed extends Model
{
    protected $table = "feeds";

    public function addFeed($chanel_id, $feed)
    {
        return $this->app['db']->executeQuery("INSERT IGNORE INTO {$this->table} (chanel_id, guid, title, description, link) 
VALUES (?, ?, ?, ?, ?)", [$chanel_id, $feed->guid, $feed->title, $feed->description, $feed->link]);
    }

    public function getFeedsByChannel($channelId)
    {
        return $this->app['db']->fetchAll("SELECT * FROM {$this->table} WHERE chanel_id = {$channelId}");
    }

    public function dleteFeedsForChannel($channelId)
    {
        return $this->app['db']->delete('feeds', array('chanel_id' => $channelId));
    }


}