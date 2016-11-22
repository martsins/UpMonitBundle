<?php

namespace Martsins\UpMonitBundle\Model;

use Composer\Semver\Comparator;
use Composer\Semver\Semver;
use Composer\Semver\VersionParser;
use Packagist\Api\Result\Package;

/**
 * This is the version class.
 *
 */
class Version extends Semver
{
    /**
     * Normalize the version number.
     *
     * @param string $version
     *
     * @return string
     */
    public static function normalize($version)
    {
        $version = preg_replace('/^(v|\^|~)/', '', $version);

        if (preg_match('/^\d\.\d$/', $version)) {
            $version .= '.0';
        }

        return $version;
    }

    /**
     * Get the last version number from a list of versions.
     *
     * @param array $versions
     *
     * @return string
     */
    public static function latest(array $versions)
    {
        // Normalize version numbers.
        $versions = array_map(
          function ($version) {
              return static::normalize($version);
          },
          $versions
        );

        // Get the highest version number.
        $latest = array_reduce(
          $versions,
          function ($carry, $item) {
              // Skip unstable versions.
              if (VersionParser::parseStability($item) !== 'stable') {
                  return $carry;
              }

              return Comparator::greaterThan($carry, $item) ? $carry : $item;
          },
          '0.0.0'
        );

        return $latest;
    }

    /**
     * Get all versions from package
     *
     * @param Package $package
     * @return array
     */
    public static function all($package)
    {
        $versions = array_map(
          function ($version) {
              return $version->getVersion();
          },
          $package->getVersions()
        );

        return $versions;
    }
}