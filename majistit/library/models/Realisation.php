<?php

namespace MyApp\Model;

/**
 * @entity(repositoryClass="MyApp\Model\RealisationRepository")
 * @table(name="myapp_realisation")
 */
class Realisation
{
    /**
     * @id @column(name="id", type="integer")
     * @generatedValue
     *
     * @var int
     */
    private $id;
}

class RealisationRepository extends \Doctrine\ORM\EntityRepository
{

}
