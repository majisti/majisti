<?php

namespace MajistiX\Editing\Plugin;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc Tests the content monitor class.
 *
 * @author Steven Rosato
 */
class ContentMonitorTest extends \Majisti\Test\TestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager 
     */
    public $em;

    /**
     * @var \MajistiX\Editing\Model\ContentRepository
     */
    public $repo;

    public function setUp()
    {
        $this->plugin = new ContentMonitor();
        $bootstrap = $this->getHelper()->createBootstrapInstance();

        $bootstrap->registerPluginResource('Doctrine')
                  ->bootstrap('Doctrine')
                  ->registerPluginResource('Extensions') //FIXME: DRY violation
                  ->bootstrap('Extensions')
        ;

        //TODO: abstract test db manipulation
        $this->em   = $bootstrap->getPluginResource('Doctrine')->getEntityManager();
        $this->repo = $this->em->getRepository('MajistiX\Editing\Model\Content');

        $this->schema  = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $this->schema->updateSchema($this->em->getMetadataFactory()->getAllMetadata());
    }

    public function tearDown()
    {
        foreach( $this->repo->findAll() as $entity ) {
            $this->em->remove($entity);
        }

        $this->em->flush();
    }

    /**
     * @return \Zend_Controller_Request_HttpTestCase
     */
    private function getBestScenarioRequest()
    {
        $request = $this->getRequest();

        $request->setMethod('post')
                ->setPost(array(
                    'foo'                  => 'bar',
                    'majistix_editing_foo' => '##MAJISTIX_EDITING##',
                ))
        ;

        return $request;
    }

    public function testBestScenarioWillRedirectAndUpdateContent()
    {
        $request = $this->getBestScenarioRequest();

        $this->dispatch('/');
        $this->assertRedirectTo('/');

        /* @var $model \MajistiX\Editing\Model\Content */
        $model = $this->repo->findOneByName('foo');

        $this->assertNotNull($model, 'Post data was incorrectly read.');
        $this->assertEquals('bar', $model->getContent());
    }

    public function testNoPostWillNotRedirect()
    {
        $request = $this->getRequest();

        $request->setPost(array(
                'foo'                  => 'bar',
                'majistix_editing_foo' => '##MAJISTIX_EDITING##',
            )
        );

        $this->dispatch();
        $this->assertNotRedirect();
    }

    public function testPostWithMultipleDataWillFindAndStoreCorrectContent()
    {
        $request = $this->getRequest();

        $request->setMethod('post')
                ->setPost(array(
                    'email'                => 'foo@bar.com',
                    'foo'                  => 'bar', //content to store
                    'majistix_editing_foo' => '##MAJISTIX_EDITING##',
                )
        );

        $this->dispatch();

        /* @var $model \MajistiX\Editing\Model\Content */
        $model = $this->repo->findOneByName('foo');

        $this->assertNotNull($model, 'Post data was incorrectly read.');
        $this->assertEquals('bar', $model->getContent());
    }

    /**
     * @desc Test XmlHttpRequest will get a json response
     */
    public function testXmlHttpRequest()
    {
        $request = $this->getBestScenarioRequest();
        //TODO: test xml http request
    }
}

ContentMonitorTest::runAlone();
