# UpMonitBundle

## Installation

Require the `nelmio/cors-bundle` package in your composer.json and update your dependencies.

      "require" : {
        ....
        "martsins/upmonitbundle": "dev-master",
        ....
      }
And add in composer.json
    
      "repositories" : [{
          "type" : "vcs",
          "url" : "https://github.com/martsins/UpMonitBundle.git"
      }]

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
        project_id: "%up_monit_project_id%"
        url: "%up_monit_url%"

And add in parameters.yml

    up_monit_token: your_up_monit_server_token
    up_monit_project_id: your_up_monit_server_project_id
    up_monit_url: your_up_monit_server_url

## License

Released under the MIT License.
