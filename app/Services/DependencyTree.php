<?php

namespace App\Services;

use App\Package;
use Exception;
use GuzzleHttp\Client;
use vierbergenlars\SemVer\expression; // <= Argh! Inconsistent naming convention.
use vierbergenlars\SemVer\version;    // <= Argh! Inconsistent naming convention.

class DependencyTree
{
    /**
     * Get packaged determined by parameters.
     *
     * @param $name
     * @param null $version
     * @return array|mixed|null
     */
    public function getPackage($name, $version = null)
    {
        // If version is null fetch the package.
        if (is_null($version)) return $this->fetchPackage($name);

        // If version is latest fetch the latest package version.
        if ($version === 'latest') return $this->fetchLatestPackageVersion($name);

        // Otherwise fetch the package version passed.
        return $this->fetchPackageVersion($name, $version);
    }

    /**
     * Fetch specific package version from npm registry.
     *
     * @param $name
     * @param string $version
     * @return array|mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchPackageVersion($name, $version = 'latest')
    {
        $version = $this->parseVersion($name, $version);

        // Check for cached package data and return.
        $cachedPackageData = $this->getCachedPackageData($name, $version);
        if ($cachedPackageData) {
            return $cachedPackageData;
        }

        try {
            $client = new Client();
            $response = $client->request('GET', "https://registry.npmjs.org/{$name}/{$version}");
            $package = $this->cachePackage($response->getBody());
            return json_decode($package->data, true);
        } catch(Exception $e) {
            return [
                'name' => $name,
                'version' => $version,
                'error' => [
                    'message' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Fetch package from npm registry.
     *
     * @param $name
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchPackage($name)
    {
        try {
            $client = new Client();
            $response = $client->request('GET', "https://registry.npmjs.org/{$name}");

            return json_decode($response->getBody(), true);
        } catch(Exception $e) {
            return [
                'name' => $name,
                'error' => [
                    'message' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Fetch the latest package version from npm registry.
     *
     * @param $name
     * @return array|mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchLatestPackageVersion($name)
    {
        $package = $this->fetchPackage($name);
        return $this->fetchPackageVersion($name, $package['dist-tags']['latest']);
    }

    /**
     * Generate dependency tree for package.
     *
     * @param $name
     * @param string $version
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDependencyTree($name, $version = 'latest')
    {
        if ($version === 'latest') {
            $package = $this->fetchLatestPackageVersion($name);
            $version = $package['version'];
        }

        $package = $this->fetchPackageVersion($name, $this->parseVersion($name, $version));

        $tree = [
            'name' => $package['name'],
            'version' => $package['version'],
            'dependencies' => []
        ];

        if (array_key_exists('error', $package)) array_push($tree, $package['error']);

        if (!empty($package['dependencies'])) {
            foreach($package['dependencies'] as $dependencyName => $dependencyVersion) {
                $dependency = $this->fetchPackageVersion($dependencyName, $this->parseVersion($dependencyName, $dependencyVersion));

                array_push($tree['dependencies'], $this->getDependencyTree($dependency['name'], $dependency['version']));
            }
        }

        return $tree;
    }

    /**
     * Parse version number for processing.
     *
     * @param $name
     * @param $version
     * @return bool|int|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function parseVersion($name, $version) {

        // Strip ~, ^ and = from start of string.
        $version = in_array(substr($version, 0, 1), ['~','^','=']) ? substr($version, 1, strlen($version)) : $version;

        // If version is a digit e.g. 2
        if (is_numeric($version)) return "{$version}.0.0";

        if (strpos($version, '>') !== false || strpos($version, '<') !== false) {
            $latestPackage = $this->fetchPackage($name);
            $versions = $latestPackage['versions'];
            krsort($versions);

            $range = new expression($version);

            foreach($versions as $key => $value) {
                if ($range->satisfiedBy(new version($key))) {
                    return $key;
                }
            }
        }

        return $version;
    }

    /**
     * Cache package.
     *
     * @param $package
     * @return mixed
     */
    public function cachePackage($package)
    {
        $data = json_decode($package, true);

        return $this->getCachedPackage($data['name'], $data['version']) ?:
            Package::create([
                'name' => $data['name'],
                'version' => $data['version'],
                'data' => $package
            ]);
    }

    /**
     * Get cached package.
     *
     * @param $name
     * @param $version
     * @return mixed
     */
    public function getCachedPackage($name, $version)
    {
        return Package::where('name', $name)
            ->where('version', $version)
            ->first();
    }

    /**
     * Get package date from cached package.
     *
     * @param $name
     * @param $version
     * @return mixed|null
     */
    public function getCachedPackageData($name, $version)
    {
        $cachedPackage = $this->getCachedPackage($name, $version);
        return $cachedPackage ? json_decode($cachedPackage->data, true) : null;
    }
}