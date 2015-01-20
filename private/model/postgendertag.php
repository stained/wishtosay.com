<?php namespace Model;

use \System\Mysql;

class PostGenderTag extends root {

    /**
     * @var int
     */
    protected $postId;

    /**
     * @var int
     */
    protected $tagId;

    /**
     * @return int
     */
    public function getPostId()
    {
        return $this->postId;
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
     * @return PostGenderTag
     */
    private static function init($data)
    {
        if (empty($data))
        {
            return null;
        }

        $item = new self;
        $item->postId = $data['postId'];
        $item->tagId = $data['tagId'];

        return $item;
    }

    /**
     * Get PostGenderTags for postId
     *
     * @param int $postId
     * @return PostGenderTag[]|null
     */
    public static function getByPostId($postId)
    {
        $db = Mysql::getInstance();
        $data = $db->select('postgendertag', '`postId` = :postId', array('postId' => $postId));

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
     * @param int $postId
     * @param GenderTag $tag
     * @return PostGenderTag
     */
    public static function createGenderTag($postId, $tag)
    {
        $item = new self;
        $item->postId = $postId;
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

        if ($db->insert('postgendertag', array(
            'postId' => $this->postId,
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

        $db->delete('postgendertag', '`postId` = :postId AND tagId = :tagId',
            array('postId' => $this->id, 'tagId' => $this->tagId));
    }
}
