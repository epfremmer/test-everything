<?php
/**
 * File PHPUnitResultsParser.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Parser;

/**
 * Class PHPUnitResultsParser
 *
 * @package Epfremme\Everything\Parser
 */
class PHPUnitResultsParser
{
    const PHPUNIT_FAILURES = 'FAILURES!';

    /**
     * @param string $results
     * @return string
     */
    public function parse($results)
    {
        if (!is_string($results)) {
            throw new \InvalidArgumentException('PHPUnit results must be fo type string');
        }

        $results = array_filter(explode(PHP_EOL, $results));
        $result = array_pop($results);
        $previous = array_pop($results);

        if ($previous && $previous === self::PHPUNIT_FAILURES) {
            $result = sprintf('%s %s', $previous, $result);
        }

        return $result;
    }
}
