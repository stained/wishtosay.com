<?php namespace Model;

use \System\Mysql;

class User extends root {

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $passwordHash;

    /**
     * @var int
     */
    protected $signupTimestamp;

    /**
     * @var int
     */
    protected $age;

    /**
     * @var string
     */
    protected $location;

    /**
     * @var int
     */
    protected $locationId;

    /**
     * @var UserGenderTag[]
     */
    protected $genderTags;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $age
     * @return $this
     */
    public function setAge($age)
    {
        $this->age = $age;
        return $this;
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @return int
     */
    public function getLocation()
    {
        return Location::getById($this->locationId);
    }

    /**
     * @param Location $location
     * @return $this
     */
    public function setLocation($location)
    {
        $this->locationId = $location->getId();
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->passwordHash = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }

    /**
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return $this
     */
    public function activate()
    {
        $this->active = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function deactivate()
    {
        $this->active = false;
        return $this;
    }

    /**
     * @param bool $force
     * @return UserGenderTag[]
     */
    public function getGenderTags($force = false)
    {
        if (!empty($this->genderTags) && !$force)
        {
            return $this->genderTags;
        }

        $this->genderTags = UserGenderTag::getForUser($this->id);
        return $this->genderTags;
    }

    /**
     * @param array $data
     * @return User
     */
    private static function init($data)
    {
        if (empty($data))
        {
            return null;
        }

        $item = new self;
        $item->id = $data['id'];
        $item->email = $data['email'];
        $item->passwordHash = $data['passwordHash'];
        $item->signupTimestamp = $data['signupTimestamp'];
        $item->age = $data['age'];
        $item->location = $data['location'];
        $item->locationId = $data['locationId'];

        return $item;
    }

    /**
     * Get user by id
     *
     * @param int $id
     * @return User|null
     */
    public static function getById($id)
    {
        $db = Mysql::getInstance();
        $data = $db->select('user', '`id` = :id', array('id' => $id));
        return static::init($data->fetch_one());
    }

    /**
     * Authenticate user
     *
     * @param string email
     * @param string password
     *
     * @return User|null
     */
    public static function authenticate($email, $password)
    {
        $db = Mysql::getInstance();
        $data = $db->select('user', '`email` = :email', array('email' => $email));

        $user = static::init($data->fetch_one());

        if (empty($user))
        {
            return null;
        }

        if (password_verify($password, $user->getPasswordHash()))
        {
            return $user;
        }

        return null;
    }

    /**
     * @param $email
     * @param $password
     * @return User
     */
    public static function createUser($email, $password)
    {
        $item = new self;
        $item->setEmail($email);
        $item->setPassword($password);
        $item->signupTimestamp = time();

        if ($item->create())
        {
            return $item;
        }

        return false;
    }

    protected function create()
    {
        $db = Mysql::getInstance();

        if ($db->insert('user', array(
            'email' => $this->email,
            'passwordHash'  => $this->passwordHash,
            'signupTimestamp' => $this->signupTimestamp
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

        $db->update('user', array(
            'email' => $this->email,
            'passwordHash' => $this->passwordHash,
            'locationId' => $this->locationId,
            'age' => $this->age,
        ), '`id` = :id', array('id' => $this->id));
   }

    public function delete()
    {
        $db = Mysql::getInstance();

        $genderTags = $this->getGenderTags();

        if (!empty($genderTags))
        {
            foreach($genderTags as $tag)
            {
                $tag->delete();
            }
        }

        $db->delete('user', '`id` = :id', array('id' => $this->id));
    }
}
