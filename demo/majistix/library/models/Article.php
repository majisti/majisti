<?php

namespace MyApp\Model;

/**
 * @entity
 * @table(name="myapp_article")
 */
class Article
{
    /**
     * @id @column(name="id", type="integer")
     * @generatedValue
     */
    private $id;

    /**
     * @column(name="title", type="string")
     */
    private $title;

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
}
