<?php
/**
 * File InlineTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Epfremme\Everything\Annotation\Inline;
use Epfremme\Everything\Entity\Package;

/**
 * Class InlineTest
 *
 * @package Annotation
 */
class InlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AnnotationReader
     */
    protected $reader;

    /**
     * Constructor
     */
    public function setUp()
    {
        parent::setUp();

        $this->reader = new AnnotationReader();
    }

    public function testReflection()
    {
        $reflectionClass = new \ReflectionClass(Package::class);

        /** @var Inline $annotation */
        $annotation = $this->reader->getClassAnnotation($reflectionClass, Inline::class);

        $this->assertNotEmpty($annotation);
        $this->assertInstanceOf(Inline::class, $annotation);
    }

    public function testGetField()
    {
        $reflectionClass = new \ReflectionClass(Package::class);

        /** @var Inline $annotation */
        $annotation = $this->reader->getClassAnnotation($reflectionClass, Inline::class);

        $this->assertEquals('package', $annotation->getField());
    }
}
