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
     * File extensions we are allowed to proxy for.
     */
    protected $allowed_extensions = [
        'css', 'js', 'png', 'jpg', 'svg', 'eot', 'woff2', 'woff', 'ttf',
    ];

    /**
     * Constructor.
     *
     * @param array $request
     * @param array $proxy_paths
     * @param  array $custom_mimes
     * @param  array $allowed_extensions
     */
    public function __construct($request, $proxy_paths = null, $custom_mimes = null, $allowed_extensions = null)
    {
        $this->request = $request;

        if (is_array($proxy_paths)) {
            $this->proxy_paths = $proxy_paths;
        }

        if (is_array($custom_mimes)) {
            $this->custom_mimes = $custom_mimes;
        }

        if (is_array($allowed_extensions)) {
            $this->allowed_extensions = $allowed_extensions;
        }
    }

    /**
     * Get the base path of the request.
     *
     * @return string
     */
    public function getBasePath()
    {
        $path = dirname($this->request['SCRIPT_NAME']);

        return '/' === $path ? '' : $path;
    }

    /**
     * Removes the base path from a uri.
     *
     * @param  srting $uri
     *
     * @return string
     */
    protected function translateUri($uri)
    {
        return str_replace($this->getBasePath(), '', $uri);
    }

    /**
     * Get a request_uris actual path in the filesystem.
     *
     * @param  string $request_uri
     *
     * @return string
     */
    protected function getRealPath($request_uri)
    {
        return realpath(__DIR__.'/../'.$this->translateUri($request_uri));
    }

    /**
     * Check that a requested URI actually exists in the filesystem.
     *
     * @param  string  $uri
     *
     * @return bool
     */
    protected function isValidFileUri($uri)
    {
        $path = $this->getRealPath($uri);

        return file_exists($path) && !is_dir($path) && is_readable($path);
    }

    /**
     * Checks whether a URI has an allowed extension.
     *
     * @param  string  $request_uri
     *
     * @return bool
     */
    protected function hasAllowedExtension($request_uri)
    {
        $uri = $this->removeQueryString($request_uri);

        foreach ($this->allowed_extensions as $extension) {
            if (substr($uri, -strlen($extension)) === $extension) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get whether a request uri should be proxied.
     *
     * @param  string $uri
     *
     * @return bool
     */
    protected function shouldProxy($uri)
    {
        // Validate the file exists, is readable, is not a directory and has a valid extension.
        if ($this->isValidFileUri($uri) && $this->hasAllowedExtension($uri)) {

            // Check that the requested URI is within our allowed paths.
            foreach ($this->proxy_paths as $path) {

                // Extract the comparable part of the uri. If we're running in
                // a sub-directory we remove that from the leading part of the uri. Then
                // we take the same number characters from the start of the remaing uri
                // portion as the number in the path we're checking it against.
                $uri_base_path = substr(
                    $uri,
                    strlen($this->getBasePath()), // end of subdirectory (if any)
                    strlen($path) // length of the current path we are checking
                );

                // If we have a match, we allow proxying of the path.
                if ($path === $uri_base_path) {
                    return true;
                }
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
     *
     * @return  bool
     */
    public function handle()
    {
        $request_uri = $this->removeQueryString(
            $this->request['REQUEST_URI']
        );

        if ($this->shouldProxy($request_uri)) {
            $path = $this->getRealPath($request_uri);
            header('Content-Type: '.$this->detectMime($path));
            readfile($path);
            die;
        }

        return false;
    }
}
