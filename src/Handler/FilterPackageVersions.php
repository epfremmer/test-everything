<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/14/15
 * Time: 7:48 PM
 */

namespace Epfremme\Everything\Handler;

use Composer\Semver\Semver;
use Composer\Semver\VersionParser;
use Epfremme\Everything\Composer\Package;

class FilterPackageVersions
{
    /**
     * @var string
     */
    private $constraint;

    /**
     * FilterPackageVersions constructor
     *
     * @param string $constraint
     */
    public function __construct($constraint)
    {
        $this->constraint = $constraint;
    }

    /**
     * Invoke handler
     *
     * @param Package $package
     * @return array
     */
    public function __invoke(Package $package)
    {
        $versions = array_filter($package->getVersions(), function($version) {
            return Semver::satisfies($version, $this->constraint)
                && VersionParser::parseStability($version) === 'stable';
        }, ARRAY_FILTER_USE_KEY);

        return $package->setVersions($versions);
    }
}
