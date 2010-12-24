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

        $this->em   = \Zend_Registry::get('Doctrine_EntityManager');

        $this->repo = $this->em->getRepository('MajistiX\Editing\Model\Content');
    }

    public function tearDown()
    {
        foreach( $this->repo->findAll() as $entity ) {
            $this->em->remove($entity);
        }

        $this->em->flush();
    }

    public function testRedirect()
    {
        $request = $this->getRequest();

        $request->setMethod('post')
                ->setPost(array(
                    'foo'                  => 'bar',
                    'majistix_editing_foo' => '##MAJISTIX_EDITING##',
                )
        );
        $this->dispatch();

        $this->assertRedirect();
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

    public function testXmlHttpRequest()
    {
        //TOOD: test xml http request
    }
}

ContentMonitorTest::runAlone();
