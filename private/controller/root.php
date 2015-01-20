<?php namespace Controller;

use \System\View;

class Root
{
    /**
     * default route
     * @return \System\View
     */
    public static function base()
    {
        // load master view
        $view = View::load('master');
        return $view;
    }

}

