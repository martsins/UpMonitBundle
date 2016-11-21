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

Add the MartsinsUpMonitBundle to your application's kernel:

    public function registerBundles()
    {
        $bundles = array(
            ...
            new Martsins\UpMonitBundle\MartsinsUpMonitBundle(),
            ...
        );
        ...
    }

## Configuration

    martsins_upmonit_bundle:
        token: seacret
        project_id: some_project_id
        url: server_url

## License

Released under the MIT License.
