<?php
namespace Martsins\UpMonitBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Martsins\UpMonitBundle\Model\Version;
use Packagist\Api\Client;
use SensioLabs\Security\SecurityChecker;
use Composer\Semver\Comparator;

/**
 * UpMonitDataCollector
 */
class UpMonitDataCollector extends DataCollector
{

    const UP_MONET_HANDLER = 'symfony';

    private $kernel;

    private $checker;

    /**
     * UpMonitDataCollector constructor.
     *
     * @param KernelInterface $kernel
     * @param SecurityChecker $checker
     */
    public function __construct(KernelInterface $kernel, SecurityChecker $checker)
    {
        $this->kernel = $kernel;
        $this->checker = $checker;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $client = new Client();

        $lockfile = $request->request->get('lockfile');

        $rootDir = realpath($this->kernel->getRootDir() . '/../');
        $installed = json_decode(file_get_contents($rootDir.'/composer.lock'));
        $require = json_decode(file_get_contents($rootDir.'/composer.json'));
        $require = (array)$require->require;

        $vulnerabilities = $this->checker->check($lockfile);

        $packages = [];
        foreach ($installed->packages as $installedPackage)
        {
            $package = $installedPackage->name;

            if (!isset($require[$package])) {
                continue;
            }

            $currentVersion = Version::normalize($installedPackage->version);
            $url = $installedPackage->source->url;
            $description = $installedPackage->description;
            $priority = isset($vulnerabilities[$package]) ? 'ture' : 'false';
            $externalPackage = $client->get($package);

            if (isset($externalPackage)) {
                $newVersion = null;
                $versions = Version::all($externalPackage);
                $satisfied = Version::satisfiedBy($versions, $require[$package]);
                foreach ($satisfied as $item) {
                    if (Comparator::greaterThan($item, $currentVersion)) {
                        $newVersion = $item;
                    }
                }
                if (is_null($newVersion)) {
                    $newStatus = 'compatibility breaks';
                    $newVersion = Version::latest($versions);
                }

                if ($currentVersion == $newVersion) {
                    continue;
                }

                $packages[] = compact('package', 'currentVersion', 'currentStatus', 'newVersion', 'newStatus', 'url', 'description', 'priority');
            }
        }

        $handler = self::UP_MONET_HANDLER;
        $this->data = compact('handler', 'packages');
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
