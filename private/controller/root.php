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

    /**
     * @return string
     */
    protected static function getUserHash()
    {
        return sha1($_SERVER['REMOTE_ADDR'] . '_' . $_SERVER['HTTP_USER_AGENT']);
    }

    /**
     * @param $array
     * @param int $code
     * @return View
     */
    protected static function toJson($array, $code = 200)
    {
        $view = View::load('json', null, $code);
        $view->setContentType('application/json');
        $view->set('array', $array);
        return $view;
    }
}

