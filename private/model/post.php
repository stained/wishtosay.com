<?php namespace Model;

use \System\Mysql;
use \Util\String;

class Post extends root {

    const MAX_REPORTS = 5;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var int
     */
    protected $timestamp;

    /**
     * @var int
     */
    protected $upVotes;

    /**
     * @var int
     */
    protected $downVotes;

    /**
     * @var int
     */
    protected $ageFrom;

    /**
     * @var int
     */
    protected $ageTo;

    /**
     * @var Posttag[]
     */
    protected $postTags;

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
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return int
     */
    public function getUpVotes()
    {
        return $this->upVotes;
    }

    /**
     * @return int
     */
    public function getDownVotes()
    {
        return $this->downVotes;
    }

    /**
     * @return $this
     */
    public function incrementUpVotes()
    {
        $db = Mysql::getInstance();
        $db->query('UPDATE post SET upVotes = upVotes + 1 WHERE `id` = :id', array('id' => $this->id));
        $result = $db->query('SELECT upVotes FROM post WHERE `id` = :id', array('id' => $this->id));
        $this->upVotes = max($result->fetch_one('upVotes'), $this->upVotes);
        return $this;
    }

    /**
     * @return $this
     */
    public function incrementDownVotes()
    {
        $db = Mysql::getInstance();
        $db->query('UPDATE post SET downVotes = downVotes + 1 WHERE `id` = :id', array('id' => $this->id));
        $result = $db->query('SELECT downVotes FROM post WHERE `id` = :id', array('id' => $this->id));
        $this->downVotes = max($result->fetch_one('downVotes'), $this->downVotes);
        return $this;
    }

    /**
     * @param Posttag[] $tags
     * @return $this
     */
    public function setTags($tags)
    {
        $this->postTags = $tags;
        return $this;
    }

    /**
     * @return Posttag[]
     */
    public function getPostTags()
    {
        if(!empty($this->postTags))
        {
            return $this->postTags;
        }

        $this->postTags = Posttag::getAllForPost($this);
        return $this->postTags;
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
        $item->text = $data['text'];
        $item->timestamp = $data['timestamp'];
        $item->upVotes = $data['upVotes'];
        $item->downVotes = $data['downVotes'];
        $item->ageFrom = $data['ageFrom'];
        $item->ageTo = $data['ageTo'];
        return $item;
    }

    /**
     * @param array $tags
     * @param int $ageFrom
     * @param int $ageTo
     * @param int $start
     * @param int $limit
     * @return Post[]
     */
    public static function getAllForTagsAndAgeRange($tags, $ageFrom, $ageTo, $start = 0, $limit = 25)
    {
        $db = Mysql::getInstance();

        $query = 'SELECT p.* FROM post p ';
        $bindings = array();

        if (!empty($tags))
        {
            $index = 0;
            foreach($tags as $tag)
            {
                $tableIndex = 'pt' . $index;
                $query .= "INNER JOIN posttag {$tableIndex} ON {$tableIndex}.postId = p.id AND " .
                          "{$tableIndex}.tagId = :tagId{$index} AND {$tableIndex}.type = :type{$index} ";

                $bindings["tagId{$index}"] = $tag['i'];
                $bindings["type{$index}"] = $tag['ty'];
                $index++;
            }
        }

        $query .= 'WHERE ageTo >= :ageTo AND ageFrom <= :ageFrom GROUP BY p.id ORDER BY timestamp DESC LIMIT :start,:limit';
        $bindings['ageTo'] = $ageTo;
        $bindings['ageFrom'] = $ageFrom;
        $bindings['start'] = $start;
        $bindings['limit'] = $limit;

        $data = $db->query($query, $bindings);

        $rows = array();

        while ($row = $data->fetch_one())
        {
            $rows[] = static::init($row);
        }

        return $rows;
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
     * @param string $text
     * @param int $ageFrom;
     * @param int $ageTo;
     * @return bool|Post
     */
    public static function createPost($text, $ageFrom, $ageTo)
    {
        $item = new self;
        $item->text = $text;
        $item->ageFrom = $ageFrom;
        $item->ageTo = $ageTo;
        $item->timestamp = time();
        $item->upVotes = 0;
        $item->downVotes = 0;

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
            'text' => $this->text,
            'timestamp' => $this->timestamp,
            'ageFrom' => $this->ageFrom,
            'ageTo' => $this->ageTo,
            'upVotes' => $this->upVotes,
            'downVotes' => $this->downVotes
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
        $tagJsonArray = array();

        $tags = $this->getPostTags();

        if (!empty($tags))
        {
            foreach($tags as $tag)
            {
                if (!empty($tag))
                {
                    $tagJsonArray[] = $tag->toJsonArray();
                }
            }
        }

        return array(
            'id'=>$this->id,
            'te'=>$this->text,
            'ti'=>$this->timestamp,
            'a'=>array('f'=>$this->ageFrom, 't'=>$this->ageTo),
            'v'=>array('u'=>$this->upVotes, 'd'=>$this->downVotes),
            'ta'=>$tagJsonArray
        );
    }

    /**
     * @return array
     */
    public function toSearchBody(){
    }
}
