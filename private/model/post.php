<?php namespace Model;

use \System\Mysql;

class Post extends root {

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $userId;

    /**
     * @var PostGenderTag[]
     */
    protected $genderTags;

    /**
     * @var int
     */
    protected $postTimestamp;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var int
     */
    protected $locationId;

    /**
     * @var int
     */
    protected $age;

    /**
     * @var bool
     */
    protected $moderated;

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param bool $force
     * @return PostGenderTag[]
     */
    public function getGenderTags($force = false)
    {
        if (!empty($this->genderTags) && !$force)
        {
            return $this->genderTags;
        }

        $this->genderTags = PostGenderTag::getForUser($this->id);
        return $this->genderTags;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return Location::getById($this->locationId);
    }

    /**
     * @return boolean
     */
    public function isModerated()
    {
        return $this->moderated;
    }

    /**
     * @param bool $moderated
     * @return $this;
     */
    public function setModerated($moderated)
    {
        $this->moderated = $moderated;
        return $this;
    }

    /**
     * @return int
     */
    public function getPostTimestamp()
    {
        return $this->postTimestamp;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return User::getById($this->userId);
    }

    /**
     * @param array $data
     * @return Post
     */
    private static function init($data)
    {
        if (empty($data))
        {
            return null;
        }

        $item = new self;
        $item->id = $data['id'];
        $item->userId = $data['userId'];
        $item->postTimestamp = $data['postTimestamp'];
        $item->text = $data['text'];
        $item->locationId = $data['locationId'];
        $item->age = $data['age'];
        $item->moderated = $data['moderated'];

        return $item;
    }

    /**
     * Get post by id
     *
     * @param int $id
     * @return Post|null
     */
    public static function getById($id)
    {
        $db = Mysql::getInstance();
        $data = $db->select('post', '`id` = :id', array('id' => $id));
        return static::init($data->fetch_one());
    }

    /**
     * @param User $user
     * @param string $text
     * @return bool|Post
     */
    public static function createPost($user, $text)
    {
        $item = new self;
        $item->userId = $user->getId();
        $item->postTimestamp = time();
        $item->text = $text;
        $item->locationId = $user->getLocation()->getId();
        $item->age = $user->getAge();
        $item->moderated = false;

        if ($item->create())
        {
            return $item;
        }

        return false;
    }

    protected function create()
    {
        $db = Mysql::getInstance();

        if ($db->insert('post', array(
            'userId' => $this->userId,
            'postTimestamp' => $this->postTimestamp,
            'text' => $this->text,
            'locationId' => $this->locationId,
            'age' => $this->age,
            'moderated' => $this->moderated,
        )))
        {
            $this->id = $db->insert_id();
            return true;
        }

        return false;
    }

    public function update()
    {
        $db = Mysql::getInstance();

        $db->update('post', array(
            'userId' => $this->userId,
            'postTimestamp' => $this->postTimestamp,
            'text' => $this->text,
            'locationId' => $this->locationId,
            'age' => $this->age,
            'moderated' => $this->moderated,
        ), '`id` = :id', array('id' => $this->id));
   }

    public function delete()
    {
        $db = Mysql::getInstance();
        $db->delete('post', '`id` = :id', array('id' => $this->id));
    }
}
