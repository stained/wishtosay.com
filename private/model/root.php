<?php namespace Model;

use \System\Mysql;

abstract class Root {

    /**
     * Create object
     */
    protected abstract function create();

    /**
     * Update object
     */
    public abstract function update();

    /**
     * Delete object
     */
    public abstract function delete();

    /**
     * Elastic search body
     * @return array
     */
    public abstract function toSearchBody();

    /**
     * @return string
     */
    public function getType()
    {
        $reflect = new \ReflectionClass($this);
        return strtolower($reflect->getShortName());
    }

}