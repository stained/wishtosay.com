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

    protected static function toJson($array, $code = 200)
    {
        $view = View::load('json', null, $code);
        $view->setContentType('application/json');
        $view->set('array', $array);
        return $view;
    }
}

