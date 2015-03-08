<?php namespace Model;

use \System\Mysql;
use \Util\String;

class Country extends Continent {

    const SEARCH_WEIGHT = 4;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $continentId;

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
     * @return Continent|null
     */
    public function getContinent()
    {
        return Continent::getById($this->continentId);
    }

    /**
     * @param Continent $continent
     * @return $this
     */
    public function setContinent($continent)
    {
        if(!empty($continent)) {
            $this->continentId = $continent->getId();
        }

        return $this;
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
        $item->continentId = $data['continentId'];
        return $item;
    }

    /**
     * @return Country[]
     */
    public static function getAll()
    {
        $db = Mysql::getInstance();
        $data = $db->select('country');

        $rows = array();

        while ($row = $data->fetch_one())
        {
            $rows[] = static::init($row);
        }

        return $rows;
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
     * @param Continent $continent
     * @param string $country
     * @param string $code
     * @return bool|Country
     */
    public static function createCountry($continent, $country, $code)
    {
        $item = new self;
        $item->setContinent($continent);
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
            'continentId' => $this->continentId,
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

    /**
     * @return array
     */
    public function toJsonArray()
    {
        return array(
            'i'=>$this->id,
            'ty'=>'country',
            'te'=>utf8_decode($this->getCountry())
        );
    }

    /**
     * @return array
     */
    public function toSearchBody(){
        return array(
            'search' => String::replaceForSearch($this->getCountry()),
            'search_weight' => static::SEARCH_WEIGHT,
            'tag' => $this->getCountry(),
            'code' => $this->getCode()
        );
    }
}
