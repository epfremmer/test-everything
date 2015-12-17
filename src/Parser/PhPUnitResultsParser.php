<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/16/15
 * Time: 12:26 AM
 */

namespace Epfremme\Everything\Parser;


class PHPUnitResultsParser
{
    public function parse($results)
    {
        $results = array_filter(explode(PHP_EOL, $results));

        return array_pop($results);
    }
}
