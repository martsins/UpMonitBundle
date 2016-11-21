<?php
namespace Martsins\UpMonitBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Martsins\UpMonitBundle\Model\Version;
use Packagist\Api\Client;

/**
 * UpMonitDataCollector
 */
class UpMonitDataCollector extends DataCollector
{

    const UP_MONET_HANDLER = 'symfony';

    private $kernel;

    /**
     * Class constructor
     *
     * @param KernelInterface $kernel Kernel object
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $client = new Client();


        $rootDir = realpath($this->kernel->getRootDir() . '/../');
        $installed = json_decode(file_get_contents($rootDir.'/composer.lock'));
        $require = json_decode(file_get_contents($rootDir.'/composer.json'));
        $require = (array)$require->require;

        $packages = [];
        foreach ($installed->packages as $installedPackage)
        {
            $package = $installedPackage->name;

            if (!isset($require[$package])) {
                continue;
            }

            $version = Version::normalize($installedPackage->version);
            $url = $installedPackage->source->url;
            $description = $installedPackage->description;
            $priority = 'false'; //can't get data
            $externalPackage = $client->get($package);

            if (isset($externalPackage)) {
                $versions = Version::all($externalPackage);
                $newVersion = Version::latest($versions);

                if ($version == $newVersion) {
                    continue;
                }

                $packages[] = compact('package', 'currentVersion', 'newVersion', 'url', 'description', 'priority');
            }
        }

        $handler = self::UP_MONET_HANDLER;
        $this->data = compact('handler', 'packages');
    }

    /**
     * Method returns the installed packages
     *
     * @return number
     */
    public function getPackages()
    {
        return $this->data['packages'];
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'up_monit_data_collector';
    }
}
