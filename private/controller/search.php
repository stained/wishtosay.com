<?php

namespace Controller;

class Search extends Root {

    public static function base($parameters = array()) {
        return static::toJson(array(
            array('text'=>'tag 2323', 'class'=>'tag-location'),
            array('text'=>'sdf sdf sfd', 'class'=>'tag-gender'),
            array('text'=>'moose'),
        ), 200);
    }
} 