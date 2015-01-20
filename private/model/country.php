<?php namespace Model;

use \System\Mysql;

class Country extends root {

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string
     */
    protected $code;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param array $data
     * @return Country
     */
    private static function init($data)
    {
        if (empty($data))
        {
            return null;
        }

        $item = new self;
        $item->id = $data['id'];
        $item->country = $data['country'];
        $item->code = $data['code'];
        return $item;
    }

    /**
     * Get country by id
     *
     * @param int $id
     * @return Country|null
     */
    public static function getById($id)
    {
        $db = Mysql::getInstance();
        $data = $db->select('country', '`id` = :id', array('id' => $id));
        return static::init($data->fetch_one());
    }

    /**
     * Get country by code
     *
     * @param string code
     * @return Country|null
     */
    public static function getByCode($code)
    {
        $db = Mysql::getInstance();
        $data = $db->select('country', '`code` = :code', array('code' => $code));
        return static::init($data->fetch_one());
    }

    /**
     * @param string $country
     * @param string $code
     * @return bool|Country
     */
    public static function createCountry($country, $code)
    {
        $item = new self;
        $item->country = $country;
        $item->code = $code;

        if ($item->create())
        {
            return $item;
        }

        return false;
    }

    protected function create()
    {
        $db = Mysql::getInstance();

        if ($db->insert('country', array(
            'country' => $this->country,
            'code'  => $this->code
        )))
        {
            $this->id = $db->insert_id();
            return true;
        }

        return false;
    }

    public function update()
    {
    }

    public function delete()
    {
    }
}
