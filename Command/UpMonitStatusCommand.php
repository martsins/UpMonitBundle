<?php

namespace Martsins\UpMonitBundle\Command;

use Martsins\UpMonitBundle\DataCollector\UpMonitDataCollector;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Guzzle\Http\Client;

class UpMonitStatusCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
          // the name of the command (the part after "bin/console")
          ->setName('upmonit:check-status')

          // the short description shown while running "php bin/console list"
          ->setDescription('Send data.')

          // the full command description shown when running the command with
          // the "--help" option
          ->setHelp("...")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $request = new Request();
        $response = new Response();
        /** @var UpMonitDataCollector $collection */
        $collection = $this->getContainer()->get('up_monit.data_collector');
        $collection->collect($request, $response);
        $packages = $collection->getPackages();
        if (isset($packages['data']) && !empty($packages['data'])) {
            $token = $this->getContainer()->getParameter('up_monit.token');
            $project_id = $this->getContainer()->getParameter('up_monit.project_id');
            $url = $this->getContainer()->getParameter('up_monit.url');

            $link = "$url/api/project/$project_id/$token";

            $client = new Client();
            $r = $client->request('POST', $link, [
              'body' => serialize($packages)
            ]);

        }
    }
}