<?php

namespace Herisson\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Herisson\Entity\Bookmark;
use Doctrine\Persistence\ManagerRegistry;
use Herisson\Entity\HerissonEntityInterface;

/**
 * @method Bookmark|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bookmark|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bookmark[]    findAll()
 * @method Bookmark[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookmarkRepository extends HerissonRepository implements BookmarkRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bookmark::class);
    }
/*
    public function save(HerissonEntityInterface $entity)
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }
*/

    /**
     * Check if the given url already exists in the bookmark database
     *
     * @param string $url the url to test
     *
     * @return true if the bookmarks exists, false otherwise
     */
    public static function checkDuplicate($url)
    {

        $bookmarks = self::getWhere("hash=?", array(md5($url)));
        if (sizeof($bookmarks)) {
            return true;
        }
        return false;
    }


    /**
     * Get a bookmark from the id
     *
     * @param integer $id the bookmark id
     *
     * @return Bookmark the bookmark object matching the id, or a new one
     */
    public static function get($id)
    {
        if (!is_numeric($id)) {
            return new Bookmark();
        }
        $bookmarks = self::getWhere("id=?", array($id));
        foreach ($bookmarks as $bookmark) {
            return $bookmark;
        }
        return new Bookmark();
    }


    /**
     * Count all the bookmarks in the table
     *
     * @return integer the number of total bookmarks
     */
    /*
    public static function countAll()
    {
        return self::countWhere("1=1");
    }
    */

    /**
     * Count the bookmarks with a specific condition
     *
     * @param string $where  the condition
     * @param array  $values the values for the where conditions
     *
     * @return integer the number of bookmarks matching the condition
     */
    /*
    public static function countWhere($where, $values=array())
    {
        $bookmarks = Doctrine_Query::create()
            ->select('COUNT(*)')
            ->from('Herisson\\Model\\Bookmark')
            ->where($where)
            ->execute($values, Doctrine_Core::HYDRATE_NONE);
        return $bookmarks[0][0];
    }
    */


    /**
     * Retrieve all bookmarks
     *
     * @param boolean $paginate wether we should paginate this select
     * 
     * @return a list of all Bookmarks object
     */
    /*
    public static function getAll($paginate=false)
    {
        return self::getWhere("1=1", null, $paginate);
    }
    */


    /**
     * Search for a bookmark keyword
     *
     * @param string  $search   the keyword
     * @param boolean $paginate wether the result should use pagination (optional)
     *
     * @return the list of matching bookmark objects
     */
    public static function getSearch($search, $paginate=false)
    {
        $where = array(
            't.name LIKE ?',
            'b.title LIKE ?',
            'b.url LIKE ?',
            'b.description LIKE ?',
            //'b.content LIKE ?',
        );
        
        $params = array(
            "%".$search."%",
            "%".$search."%",
            "%".$search."%",
            "%".$search."%",
            //"%".$search."%",
        );
        return self::getWhere(implode(' OR ', $where), $params, $paginate);
    }

    /*
    public static function getSearch($search, $paginate=false)
    {
        $q = Doctrine_Query::create();
        $q = self::addSearch($q, $params=array(), $search);
        return $q->execute($params
        return self::getWhere(implode(' OR ', $where), $params, $paginate);
    }

    public static function addSearch($query, &$params, $search) {
        $where = array(
            't.name LIKE ?',
            'b.title LIKE ?',
            'b.url LIKE ?',
            'b.description LIKE ?',
            //'b.content LIKE ?',
        );
        
        $params = array_merge($params,
            array(
                "%".$search."%",
                "%".$search."%",
                "%".$search."%",
                "%".$search."%",
                //"%".$search."%",
            )
        );
        $query->where(implode(' OR ', $where));
        return $query;
    }
     */

    /**
     * Search for bookmarks based on tag name
     *
     * @param string  $tag      the tag name
     * @param boolean $paginate wether the result should use pagination (optional)
     *
     * @return the list of matching bookmark objects
     */
    /*
    public static function getTag($tag, $paginate=false)
    {
        return self::getWhere("t.name = ?", $tag, $paginate);
    }
    */


    /**
     * Search for bookmarks based on where condition
     *
     * @param string  $where    the where string
     * @param array   $values   the values to create the prepared request
     * @param boolean $paginate wether the result should use pagination (optional)
     *
     * @return the list of matching bookmark objects
     */
    /*
    public static function getWhere($where, $values, $paginate=false)
    {
        $q = Doctrine_Query::create()
            ->from('Herisson\Entity\Bookmark b')
            ->leftJoin('b.Tag t')
            ->where($where);
        if ($paginate) {
            $pagination = Pagination::i()->getVars();
            $q->limit($pagination['limit'])->offset($pagination['offset']);
        }
        $bookmarks = $q->execute($values);
        return $bookmarks;
    }
    */

    /**
     * Truncate the table, delete all bookmarks from database
     *
     * @return void
     */
    /*
    public static function truncate()
    {
        $bookmarks = self::getAll();
        foreach ($bookmarks as $bookmark) {
            $bookmark->delete();
        }
    }
    */


    /**
     * Get one item with where paremeters
     *
     * @param array   $options the options array
     * @param integer $public  wether the bookmark is public or not
     *
     * @return the corresponding instance of Friend or a new one
     */
    public static function getBookmarksData($options, $public)
    {
        $params = array($public);
        $q = Doctrine_Query::create()
            ->from('Herisson\Entity\Bookmarks a b')
            ->where('is_public=?');
        $bookmarks = $q->execute($params);
        return $bookmarks;

        if (array_key_exists('tag', $options)) {
            $q->leftJoin('b.Tag t')
                ->where("t.name=?");
            $params[] = $options['tag'];
        }

        if (array_key_exists('search', $options)) {
            $search = "%".$options['search']."%";
            $q->leftJoin('b.Tag t')
                ->where("(t.name LIKE ? OR b.url like ? OR b.title LIKE ? OR b.description LIKE ? OR b.content LIKE ?)");
            $params = array_merge($params,
                array($search, $search, $search, $search, $search));
        }
        $bookmarks = $q->execute($params);
        return $bookmarks;
    }


    /**
     * Get size of the bookmarks table
     *
     * @return the formatted table size
     */
    public static function getTableSize()
    {
        /*
        $res = Doctrine::execute("SHOW TABLE STATUS Where Name like '%_bookmarks'");
        $tableInfo = $res->fetch();
        $size = $tableInfo['Data_length'];
        return Folder::formatSize($size);
        */
    }

}
