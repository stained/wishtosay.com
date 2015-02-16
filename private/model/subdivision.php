<?php namespace Model;

use \System\Mysql;
use \Util\String;

class Subdivision extends Country {

    const SEARCH_WEIGHT = 3;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $countryId;

    /**
     * @var int
     */
    protected $continentId;

    /**
     * @var string
     */
    protected $subdivision;

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
     * @return Country|null
     */
    public function getCountry()
    {
        return Country::getById($this->countryId);
    }

    /**
     * @param Country $country
     * @return $this
     */
    public function setCountry($country)
    {
        if(!empty($country)) {
            $this->countryId = $country->getId();
        }

        return $this;
    }
    
    /**
     * @return string
     */
    public function getSubdivision()
    {
        return $this->subdivision;
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
     * @return Subdivision
     */
    private static function init($data)
    {
        if (empty($data))
        {
            return null;
        }

        $item = new self;
        $item->id = $data['id'];
        $item->subdivision = $data['subdivision'];
        $item->code = $data['code'];
        $item->continentId = $data['continentId'];
        $item->countryId = $data['countryId'];
        return $item;
    }

    /**
     * @return Subdivision[]
     */
    public static function getAll()
    {
        $db = Mysql::getInstance();
        $data = $db->select('subdivision');

        $rows = array();

        while ($row = $data->fetch_one())
        {
            $rows[] = static::init($row);
        }

        return $rows;
    }

    /**
     * Get subdivision by id
     *
     * @param int $id
     * @return Subdivision|null
     */
    public static function getById($id)
    {
        $db = Mysql::getInstance();
        $data = $db->select('subdivision', '`id` = :id', array('id' => $id));
        return static::init($data->fetch_one());
    }

    /**
     * Get subdivision by code
     *
     * @param string code
     * @return Subdivision|null
     */
    public static function getByCode($code)
    {
        $db = Mysql::getInstance();
        $data = $db->select('subdivision', '`code` = :code', array('code' => $code));
        return static::init($data->fetch_one());
    }

    /**
     * @param Continent $continent
     * @param Country $country
     * @param string $subdivision
     * @param string $code
     * @return bool|Subdivision
     */
    public static function createSubdivision($continent, $country, $subdivision, $code)
    {
        $item = new self;
        $item->setContinent($continent);
        $item->setCountry($country);
        $item->subdivision = $subdivision;
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

        if ($db->insert('subdivision', array(
            'continentId' => $this->continentId,
            'countryId' => $this->countryId,
            'subdivision' => $this->subdivision,
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
        $country = $this->getCountry();

        return array(
            'i'=>$this->id,
            'ty'=>'subdivision',
            'te'=>$this->getSubdivision() . ", " . $country->getCountry()
        );
    }

    /**
     * @return array
     */
    public function toSearchBody(){
        $country = $this->getCountry();

        return array(
            'search' => String::replaceForSearch($this->getSubdivision()) . ',' . String::replaceForSearch($country->getCountry()),
            'search_weight' => static::SEARCH_WEIGHT,
            'tag' => $this->getSubdivision() . ", " . $country->getCountry(),
            'code' => $this->getCode()
        );
    }
}
