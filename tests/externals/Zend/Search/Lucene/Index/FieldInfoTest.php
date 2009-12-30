<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: FieldInfoTest.php 17363 2009-08-03 07:40:18Z bkarwin $
 */

/**
 * Zend_Search_Lucene_Index_FieldInfo
 */
require_once 'Zend/Search/Lucene/Index/FieldInfo.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Search_Lucene
 */
class Zend_Search_Lucene_Index_FieldInfoTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $fieldInfo = new Zend_Search_Lucene_Index_FieldInfo('field_name', true, 3, false);
        $this->assertTrue($fieldInfo instanceof Zend_Search_Lucene_Index_FieldInfo);

        $this->assertEquals($fieldInfo->name, 'field_name');
        $this->assertEquals($fieldInfo->isIndexed, true);
        $this->assertEquals($fieldInfo->number, 3);
        $this->assertEquals($fieldInfo->storeTermVector, false);
    }
}
