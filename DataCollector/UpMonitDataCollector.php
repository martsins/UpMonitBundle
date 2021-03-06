<?php
namespace Martsins\UpMonitBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Martsins\UpMonitBundle\Model\Version;
use Packagist\Api\Client;
use SensioLabs\Security\SecurityChecker;

/**
 * UpMonitDataCollector
 */
class UpMonitDataCollector extends DataCollector
{

    const UP_MONIT_HANDLER = 'symfony';

    private $kernel;

    private $checker;

    /**
     * UpMonitDataCollector constructor.
     *
     * @param KernelInterface $kernel
     * @param SecurityChecker $checker
     */
    public function __construct(
      KernelInterface $kernel,
      SecurityChecker $checker
    ) {
        $this->kernel = $kernel;
        $this->checker = $checker;
    }

    public function collect(
      Request $request,
      Response $response,
      \Exception $exception = null
    ) {

        $rootDir = realpath($this->kernel->getRootDir() . '/../');
        $installed = json_decode(
          file_get_contents($rootDir . '/composer.lock')
        );
        $require = json_decode(
          file_get_contents($rootDir . '/composer.json')
        );
        $require = (array) $require->require;

        $data = [];
        foreach ($installed->packages as $installedPackage) {
            $externalPackage = null;
            $package = $installedPackage->name;

            if (!isset($require[$package])) {
                continue;
            }

            $version = Version::normalize($installedPackage->version);
            $url = $installedPackage->source->url;
            $description = $installedPackage->description;

            $data[] = compact(
              'package',
              'version',
              'url',
              'description'
            );
         }

        $handler = self::UP_MONIT_HANDLER;
        $this->data = compact('handler', 'data');
    }

    /**
     * Method returns the installed packages
     *
     * @return array
     */
    public function getPackages()
    {
        return $this->data['packages'];
    }

    /**
     * Method returns data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'up_monit_data_collector';
    }
}
