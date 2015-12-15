<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/14/15
 * Time: 8:51 PM
 */

namespace Epfremme\Everything\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Inline
{
    public $value;

    /**
     * @return string
     */
    public function getField()
    {
        return $this->value;
    }

}
