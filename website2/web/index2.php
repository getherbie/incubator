<?php

ini_set('display_errors', 1);

require_once(__DIR__ . '/../vendor/autoload.php');

define('HERBIE_DEBUG', true);

use Herbie2\Benchmark;
use Herbie2\Menu\Builder;

Benchmark::mark();


$test = file_scan_directory(__DIR__ . '/../site/pages', '/\.md$/');
echo"<pre>";print_r($test);echo"</pre>";


function file_scan_directory($dir, $mask, $options = array(), $depth = 0) {
    // Merge in defaults.
    $options += array(
        'nomask' => '/(\.\.?|CVS)$/',
        'callback' => 0,
        'recurse' => TRUE,
        'key' => 'uri',
        'min_depth' => 0,
    );

    $options['key'] = in_array($options['key'], array('uri', 'filename', 'name')) ? $options['key'] : 'uri';
    $files = array();
    if (is_dir($dir) && $handle = opendir($dir)) {
        while (FALSE !== ($filename = readdir($handle))) {
            if (!preg_match($options['nomask'], $filename) && $filename[0] != '.') {
                $uri = "$dir/$filename";
                #$uri = file_stream_wrapper_uri_normalize($uri);
                if (is_dir($uri) && $options['recurse']) {
                    // Give priority to files in this folder by merging them in after any subdirectory files.
                    $files = array_merge(file_scan_directory($uri, $mask, $options, $depth + 1), $files);
                }
                elseif ($depth >= $options['min_depth'] && preg_match($mask, $filename)) {
                    // Always use this match over anything already set in $files with the
                    // same $$options['key'].
                    $file = new stdClass();
                    $file->uri = $uri;
                    $file->filename = $filename;
                    $file->name = pathinfo($filename, PATHINFO_FILENAME);
                    $key = $options['key'];
                    $files[$file->$key] = $file;
                    if ($options['callback']) {
                        $options['callback']($uri);
                    }
                }
            }
        }

        closedir($handle);
    }

    return $files;
}


$time = Benchmark::mark();
echo sprintf("<br>Generated in %s seconds", $time);
