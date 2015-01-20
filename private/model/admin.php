<?php namespace Model;

use \System\Mysql;

class Admin extends root {

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @param array $data
     * @return Admin
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
        return $item;
    }

    /**
     * Get admin by id
     *
     * @param int $id
     * @return Admin|null
     */
    public static function getById($id)
    {
        $db = Mysql::getInstance();
        $data = $db->select('admin', '`id` = :id', array('id' => $id));
        return static::init($data->fetch_one());
    }

    /**
     * Authenticate admin
     *
     * @param string email
     * @param string password
     *
     * @return Admin|null
     */
    public static function authenticate($email, $password)
    {
        $db = Mysql::getInstance();
        $data = $db->select('admin', '`email` = :email', array('email' => $email));

        $admin = static::init($data->fetch_one());

        if (empty($admin))
        {
            return null;
        }

        if (password_verify($password, $admin->getPasswordHash()))
        {
            return $admin;
        }

        return null;
    }

    /**
     * @param $email
     * @param $password
     * @return Admin
     */
    public static function createAdmin($email, $password)
    {
        $item = new self;
        $item->setEmail($email);
        $item->setPassword($password);

        if ($item->create())
        {
            return $item;
        }

        return false;
    }

    protected function create()
    {
        $db = Mysql::getInstance();

        if ($db->insert('admin', array(
            'email' => $this->email,
            'passwordHash'  => $this->passwordHash,
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

        $db->update('admin', array(
            'email' => $this->email,
            'passwordHash' => $this->passwordHash
        ), '`id` = :id', array('id' => $this->id));
   }

    public function delete()
    {
        $db = Mysql::getInstance();
        $db->delete('admin', '`id` = :id', array('id' => $this->id));
    }
}
