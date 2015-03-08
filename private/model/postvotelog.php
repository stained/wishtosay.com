<?php namespace Model;

use \System\Mysql;
use \Util\String;

class PostVoteLog extends root {

    /**
     * @var int
     */
    protected $postId;

    /**
     * @var string
     */
    protected $userHash;

    /**
     * @var bool
     */
    protected $upvote;

    /**
     * @return int
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @return int
     */
    public function getUserHash()
    {
        return $this->userHash;
    }

    /**
     * @return bool
     */
    public function wasUpvoted()
    {
        return $this->upvote;
    }

    /**
     * @return $this
     */
    public function downVote()
    {
        $this->upvote = 0;
        return $this;
    }

    /**
     * @return $this
     */
    public function upVote()
    {
        $this->upvote = 1;
        return $this;
    }

    /**
     * @return Post
     */
    public function getPost()
    {
        return Post::getById($this->postId);
    }

    /**
     * @param array $data
     * @return PostVoteLog
     */
    private static function init($data)
    {
        if (empty($data))
        {
            return null;
        }

        $item = new self;
        $item->postId = $data['postId'];
        $item->userHash = $data['userHash'];
        $item->upvote = $data['upvote'];
        return $item;
    }

    /**
     * @param Post $post
     * @return PostVoteLog[]
     */
    public static function getAllForPost($post)
    {
        $db = Mysql::getInstance();
        $data = $db->select('postvotelog', '`postId` = :postId', array('postId' => $post->getId()));

        $rows = array();

        while ($row = $data->fetch_one())
        {
            $rows[] = static::init($row);
        }

        return $rows;
    }

    /**
     * Get log by userhash and post id
     *
     * @param Post $post
     * @param string $userHash
     * @return PostVoteLog|null
     */
    public static function getByPostAndUserHash($post, $userHash)
    {
        $db = Mysql::getInstance();
        $data = $db->select('postvotelog', '`postId` = :postId AND userHash = :userHash',
            array('postId' => $post->getId(), 'userHash' => $userHash));

        return static::init($data->fetch_one());
    }

    /**
     * @param Post $post
     * @param string UserHash
     * @param bool upvoted
     * @return bool|PostVoteLog
     */
    public static function createLogEntry($post, $userHash, $upvoted = 0)
    {
        $item = new self;
        $item->postId = $post->getId();
        $item->userHash = $userHash;
        $item->upvote = $upvoted;

        if ($item->create())
        {
            return $item;
        }

        return false;
    }

    protected function create()
    {
        $db = Mysql::getInstance();

        if ($db->insert('postvotelog', array(
            'postId' => $this->postId,
            'userHash' => $this->userHash,
            'upvote' => $this->upvote
        )))
        {
            $this->id = $db->insert_id();
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function toJsonArray()
    {
    }

    public function update()
    {
        $db = Mysql::getInstance();

        $db->update('postvotelog', array(
            'upvote'=>$this->upvote
        ),
            'postId = :postId AND userHash = :userHash',
            array('postId' => $this->postId, 'userHash' => $this->userHash)
        );
    }

    public function delete()
    {
    }

    public function toSearchBody(){
    }
}
