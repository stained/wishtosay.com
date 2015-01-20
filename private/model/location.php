<?php namespace Model;

use \System\Mysql;

class Location extends root {

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $countryId;

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
     * @return Country
     */
    public function getCountry()
    {
        return Country::getById($this->countryId);
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
     * @return Location
     */
    private static function init($data)
    {
        if (empty($data))
        {
            return null;
        }

        $item = new self;
        $item->id = $data['id'];
        $item->countryId = $data['countryId'];
        $item->city = $data['city'];
        $item->latitude = $data['latitude'];
        $item->longitude = $data['longitude'];
        return $item;
    }

    /**
     * Get location by id
     *
     * @param int $id
     * @return Location|null
     */
    public static function getById($id)
    {
        $db = Mysql::getInstance();
        $data = $db->select('location', '`id` = :id', array('id' => $id));
        return static::init($data->fetch_one());
    }

    /**
     * @param Country $country
     * @param string $city
     * @param float $latitude
     * @param float $longitude
     * @return bool|Location
     */
    public static function createLocation($country, $city, $latitude, $longitude)
    {
        $item = new self;
        $item->countryId = $country->getId();
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

        if ($db->insert('location', array(
            'countryId' => $this->countryId,
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
}
