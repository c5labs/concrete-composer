<?php
/**
 * Concrete5 Core Proxy.
 *
 * concrete directly references many resources within it's core directory. 
 * In an effort to enable the CMS to be used as a composer dependency and also 
 * improve security of the composer vendor directory, these resources have been 
 * moved outside of the 'public' webroot. This class proxies requests to 
 * these resources.
 *
 * @author   Oliver Green <oliver@c5dev.com>
 * @license  See attached license file
 *
 * @link https://c5dev.com
 */
class ConcreteCoreProxy
{
    /**
     * Instance of the request args.
     * 
     * @var array
     */
    protected $request;

    /**
     * Base paths to proxy.
     * 
     * @var arrya
     */
    protected $proxy_paths = [
        '/vendor/concrete5/concrete5/',
        '/application/',
        '/packages/',
    ];

    /**
     * Custom mime types.
     * 
     * @var array
     */
    protected $custom_mimes = ['css' => 'text/css'];

    /**
     * Constructor.
     * 
     * @param array $request     $_SERVER
     * @param array $proxy_paths
     * @param  array $custom_mimes 
     */
    public function __construct($request, $proxy_paths = null, $custom_mimes = null)
    {
        $this->request = $request;

        if (is_array($proxy_paths)) {
            $this->proxy_paths = $proxy_paths;
        }

        if (is_array($custom_mimes)) {
            $this->custom_mimes = $custom_mimes;
        }
    }

    public function getBasePath()
    {
        $path = dirname($this->request['SCRIPT_NAME']);

        return '/' === $path ? '' : $path;
    }

    public function translatePath($path)
    {
        return str_replace($this->getBasePath(), '', $path);
    }

    /**
     * Get whether a request uri should be proxied.
     * 
     * @param  string $request_uri
     *
     * @return bool
     */
    protected function shouldProxy($request_uri)
    {
        foreach ($this->proxy_paths as $path) {
            if ($path === substr($request_uri, strlen($this->getBasePath()), strlen($path))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove a query string from a string.
     * 
     * @param  string $uri 
     *
     * @return string     
     */
    protected function removeQueryString($uri)
    {
        $parts = explode('?', $uri);

        return $parts[0];
    }

    /**
     * Detect a given paths MIME type.
     * 
     * @param  string $path
     *
     * @return string
     */
    protected function detectMime($path)
    {
        foreach ($this->custom_mimes as $extension => $mime) {
            if (substr($path, -strlen($extension)) === $extension) {
                return $mime;
            }
        }

        return mime_content_type($path);
    }

    /**
     * Proxy the current request.
     */
    public function handle()
    {
        $request_uri = $this->removeQueryString(
            $this->request['REQUEST_URI']
        );

        if ($this->shouldProxy($request_uri)) {
            $real_path = realpath('../'.$this->translatePath($request_uri));
            if (!is_dir($real_path) && is_readable($real_path)) {
                header('Content-Type: '.$this->detectMime($real_path));
                readfile($real_path);
                die;
            }
        }
    }
}
