<?php

namespace Martsins\UpMonitBundle\Command;

use Guzzle\Http\Exception\ClientErrorResponseException;
use Martsins\UpMonitBundle\DataCollector\UpMonitDataCollector;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Guzzle\Http\Client;
use Symfony\Component\Console\Input\InputArgument;

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
          ->setHelp("...");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $request = new Request();
        $response = new Response();
        /** @var UpMonitDataCollector $collection */
        $collection = $this->getContainer()->get('up_monit.data_collector');

        $collection->collect($request, $response);

        $packages = $collection->getData();
        if (isset($packages['data']) && !empty($packages['data'])) {
            $token = $this->getContainer()->getParameter('up_monit.token');

            preg_match('/^(?:(.+):)?\/\/(?:(.+)(:.+)?@)?([\w\.-]+)(?::(\d+))?(\/.*)/i', $token, $match);
            if (isset($match[1]) && isset($match[2]) && isset($match[4]) && isset($match[6])) {
                $link = $match[1] . '://' . $match[4] . '/api/project' . $match[6] . '/' . $match[2];
            }

            if (isset($link) && !empty($link)) {
                $client = new Client();
                try {
                    $client->post(
                      $link,
                      ['Content-Type' => 'application/json'],
                      json_encode($packages)
                    )->send();
                } catch (ClientErrorResponseException $e) {

                }
                //ToDo: parse response code
            }
        }
    }
}