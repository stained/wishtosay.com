<?php namespace Model;

use \System\Mysql;

class UserGenderTag extends root {

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var int
     */
    protected $tagId;

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return GenderTag
     */
    public function getTag()
    {
        return GenderTag::getById($this->tagId);
    }

    /**
     * @param array $data
     * @return UserGenderTag
     */
    private static function init($data)
    {
        if (empty($data))
        {
            return null;
        }

        $item = new self;
        $item->userId = $data['userId'];
        $item->tagId = $data['tagId'];

        return $item;
    }

    /**
     * Get UserGenderTags for userId
     *
     * @param int $userId
     * @return UserGenderTag[]|null
     */
    public static function getByUserId($userId)
    {
        $db = Mysql::getInstance();
        $data = $db->select('usergendertag', '`userId` = :userId', array('userId' => $userId));

        $result = $data->fetch_all();

        if(empty($result))
        {
            return null;
        }

        $items = array();

        foreach($result as $tag)
        {
            $items[] = static::init($tag);
        }

        return items;
    }

    /**
     * @param int $userId
     * @param GenderTag $tag
     * @return UserGenderTag
     */
    public static function createGenderTag($userId, $tag)
    {
        $item = new self;
        $item->userId = $userId;
        $item->tagId = $tag->getId();

        if ($item->create())
        {
            return $item;
        }

        return false;
    }

    protected function create()
    {
        $db = Mysql::getInstance();

        if ($db->insert('usergendertag', array(
            'userId' => $this->userId,
            'tagId' => $this->tagId
        )))
        {
            return true;
        }

        return false;
    }

    public function update()
    {
    }

    public function delete()
    {
        $db = Mysql::getInstance();

        $db->delete('usergendertag', '`userId` = :userId AND tagId = :tagId',
            array('userId' => $this->id, 'tagId' => $this->tagId));
    }
}
