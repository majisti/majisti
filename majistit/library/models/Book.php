<?php

namespace MyApp\Model;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * @entity(repositoryClass="MyApp\Model\BookRepository")
 * @table(name="myapp_book")
 */
class Book
{
    /**
     * @id @column(name="id", type="integer")
     * @generatedValue
     * @var int
     */
    private $id;

    /**
     * @column(name="title", type="string")
     * @var string
     */
    private $title;

    /**
     * @column(name="publication_year", type="integer")
     * @var string
     */
    private $publicationYear;

    /**
     * @manyToMany(targetEntity="Article", cascade={"all"})
     * @joinTable(name="myapp_book_articles",
     *     joinColumns={@JoinColumn(name="book_id", referencedColumnName="id")},
     *     inverseJoinColumns={@JoinColumn(name="article_id",
     *         referencedColumnName="id", unique="true")}
     * )
     *
     * @var ArrayCollection of Article objects
     */
    private $articles;

    /**
     * @desc Constructs the book
     */
    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    /**
     * @return string the article title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @desc Sets the title
     * @param string $title The title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @desc Returns all the articles for this book.
     *
     * @return ArrayCollection The articles
     */
    public function getArticles()
    {
        return clone $this->articles;
    }

    /**
     * @desc Sets the publication year
     */
    public function setPublicationYear($publicationYear)
    {
        $this->publicationYear = $publicationYear;
    }

    /**
     * @desc Adds an article to the book.
     *
     * @param Article $article
     */
    public function addArticle(Article $article)
    {
        $this->articles->add($article);
    }
}

class BookRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return array
     */
    public function getThisYearBooks()
    {
        return $this->findByPublicationYear(date('Y'));
    }

    /**
     * @desc Get the recent books specified by a backtracking years span.
     *
     * @param int $span Years span
     * @return \Doctrine\ORM\AbstractQuery The query
     */
    public function getRecentBooks($span = 10)
    {
        $to   = date('Y');
        $from = $to - $span;

        return $this->_em->createQuery(
            "select b from " . __NAMESPACE__ . "\Book b where " .
            "b.publicationYear > $from and b.publicationYear <= $to"
        )->getResult();
    }
}
