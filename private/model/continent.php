<?php namespace Model;

use \System\Mysql;
use \Util\String;

class Continent extends root {

    const SEARCH_WEIGHT = 5;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $continent;

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
    public function getContinent()
    {
        return $this->continent;
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
     * @return Continent
     */
    private static function init($data)
    {
        if (empty($data))
        {
            return null;
        }

        $item = new self;
        $item->id = $data['id'];
        $item->continent = $data['continent'];
        $item->code = $data['code'];
        return $item;
    }

    /**
     * @return Continent[]
     */
    public static function getAll()
    {
        $db = Mysql::getInstance();
        $data = $db->select('continent');

        $rows = array();

        while ($row = $data->fetch_one())
        {
            $rows[] = static::init($row);
        }

        return $rows;
    }

    /**
     * Get continent by id
     *
     * @param int $id
     * @return Continent|null
     */
    public static function getById($id)
    {
        $db = Mysql::getInstance();
        $data = $db->select('continent', '`id` = :id', array('id' => $id));
        return static::init($data->fetch_one());
    }

    /**
     * Get continent by code
     *
     * @param string code
     * @return Continent|null
     */
    public static function getByCode($code)
    {
        $db = Mysql::getInstance();
        $data = $db->select('continent', '`code` = :code', array('code' => $code));
        return static::init($data->fetch_one());
    }

    /**
     * @param string $continent
     * @param string $code
     * @return bool|Continent
     */
    public static function createContinent($continent, $code)
    {
        $item = new self;
        $item->continent = $continent;
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

        if ($db->insert('continent', array(
            'continent' => $this->continent,
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
            'ty'=>'continent',
            'te'=>utf8_decode($this->getContinent())
        );
    }

    /**
     * @return array
     */
    public function toSearchBody(){
        return array(
            'search' => String::replaceForSearch($this->getContinent()),
            'search_weight' => static::SEARCH_WEIGHT,
            'tag' => $this->getContinent(),
            'code' => $this->getCode()
        );
    }
}
