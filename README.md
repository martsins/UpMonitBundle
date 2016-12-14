# UpMonitBundle

## Installation

    composer require martsins/upmonitbundle

or

Require the `martsins/upmonitbundle` package in your composer.json and update your dependencies.

      "require" : {
        ....
        "martsins/upmonitbundle": "dev-master",
        ....
      }

Add the UpMonitBundle to your application's kernel:

    public function registerBundles()
    {
        $bundles = array(
            ...
            new Martsins\UpMonitBundle\UpMonitBundle(),
            ...
        );
        ...
    }

## Configuration

    up_monit:
        token: "%up_monit_token%"

And add in parameters.yml

    up_monit_token: your_up_monit_server_token

## Command

    php bin/console upmonit:check-status

## License

Released under the MIT License.
