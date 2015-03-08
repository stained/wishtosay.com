<?php namespace Model;

use \System\Mysql;
use \Util\String;

class City extends SubDivision {

    const SEARCH_WEIGHT = 2;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $continentId;

    /**
     * @var int
     */
    protected $countryId;

    /**
     * @var int
     */
    protected $subdivisionId;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var float
     */
    protected $latitude;

    /**
     * @var float
     */
    protected $longitude;

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
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return SubDivision
     */
    public function getSubdivision()
    {
        return SubDivision::getById($this->subdivisionId);
    }

    /**
     * @param SubDivision $subDivision
     * @return $this
     */
    public function setSubdivision($subDivision)
    {
        if(!empty($subDivision)) {
            $this->subdivisionId = $subDivision->getId();
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param array $data
     * @return City
     */
    private static function init($data)
    {
        if (empty($data))
        {
            return null;
        }

        $item = new self;
        $item->id = $data['id'];
        $item->continentId = $data['continentId'];
        $item->countryId = $data['countryId'];
        $item->subdivisionId = $data['subdivisionId'];
        $item->city = $data['city'];
        $item->latitude = $data['latitude'];
        $item->longitude = $data['longitude'];
        return $item;
    }

    /**
     * @return City[]
     */
    public static function getAll()
    {
        $db = Mysql::getInstance();
        $data = $db->select('city');

        $rows = array();

        while ($row = $data->fetch_one())
        {
            $rows[] = static::init($row);
        }

        return $rows;
    }

    /**
     * Get city by id
     *
     * @param int $id
     * @return City|null
     */
    public static function getById($id)
    {
        $db = Mysql::getInstance();
        $data = $db->select('city', '`id` = :id', array('id' => $id));
        return static::init($data->fetch_one());
    }

    /**
     * Get city by city and country
     *
     * @param string city
     * @param Country $country
     * @return City|null
     */
    public static function getByCityAndCountry($city, $country)
    {
        $db = Mysql::getInstance();
        $data = $db->select('city', '`city` = :city AND `countryId` = :countryId',
                            array('city' => $city,'countryId' => $country->getId()));

        return static::init($data->fetch_one());
    }

    /**
     * @param Continent $continent
     * @param Country $country
     * @param SubDivision $subDivision
     * @param string $city
     * @param float $latitude
     * @param float $longitude
     * @return bool|City
     */
    public static function createCity($continent, $country, $subDivision, $city, $latitude, $longitude)
    {
        $item = new self;
        $item->setContinent($continent);
        $item->setCountry($country);
        $item->setSubdivision($subDivision);
        $item->city = $city;
        $item->latitude = $latitude;
        $item->longitude = $longitude;

        if ($item->create())
        {
            return $item;
        }

        return false;
    }

    protected function create()
    {
        $db = Mysql::getInstance();

        if ($db->insert('city', array(
            'continentId' => $this->continentId,
            'countryId' => $this->countryId,
            'subdivisionId' => $this->subdivisionId,
            'city'  => $this->city,
            'latitude'  => $this->latitude,
            'longitude'  => $this->longitude,
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
            'ty'=>'city',
            'te'=>utf8_decode($this->getCity() . ", " . $country->getCountry())
        );
    }

    /**
     * @return array
     */
    public function toSearchBody()
    {
        $country = $this->getCountry();

        return array(
            'search' => String::replaceForSearch($this->getCity()) . ',' . String::replaceForSearch($country->getCountry()),
            'search_weight' => static::SEARCH_WEIGHT,
            'tag' => $this->getCity() . ", " . $country->getCountry(),
            'pin' => array(
                'city' => array(
                    'lat' => $this->getLatitude(),
                    'lon' => $this->getLongitude()
                )
            )
        );
    }
}
