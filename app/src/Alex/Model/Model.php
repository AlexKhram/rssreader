<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 19.11.2016
 * Time: 19:36
 */

namespace Alex\Model;


abstract class Model
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
}