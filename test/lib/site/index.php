<?php
ini_set('always_populate_raw_post_data', 1);
ini_set('filter.default', FILTER_SANITIZE_STRIPPED);
require_once(dirname(__FILE__).'/limonade/limonade.php');

/**
 * This is the site configuration file
 *
 * @return void
 * @author Christopher Cowan
 **/
function configure()
{  
  # Setup options for Limonade
  $root_dir                  = dirname(__FILE__).'/../';
  option('root_dir',           $root_dir); // this folder contains your main application file
  option('base_path',          dirname(__FILE__));
  option('base_uri',           '/'); // set it manually if you use url_rewriting
  option('limonade_dir',       dirname(__FILE__).'/limonade'); // this folder contains the limonade.php main file
  option('limonade_views_dir', dirname(__FILE__).'/limonade/limonade/views/');
  option('limonade_public_dir',dirname(__FILE__).'/limonade/limonade/public/');
  option('error_views_dir',    option('limonade_views_dir'));
  option('env',                ENV_DEVELOPMENT);
  option('debug',              true);
  option('session',            'Yummy_Plus3_Session_Cookie'); // true, false or the name of your session
  option('encoding',           'utf-8');
} // END function configure()

# the index request
dispatch('/', function() {
    return 'hello world';
});

dispatch('/phpinfo', function() {
    phpinfo();
    return;
});

$echo = function() {
    setcookie('example', 'test');
    $response = array();
    foreach($GLOBALS as $key=>$data) {
        if($key != 'GLOBALS') {
            $response[$key] = $data;
        }
    }
    $response['_HEADERS'] = http_get_request_headers();
    return json($response);
};

dispatch_get('/echo', $echo);
dispatch_post('/echo', $echo);
dispatch_put('/echo', $echo);
dispatch_delete('/echo', $echo);

run();