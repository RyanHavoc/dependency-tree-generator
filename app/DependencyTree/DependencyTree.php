<?php

namespace App\DependencyTree;

use App\Package;
use Exception;
use GuzzleHttp\Client;
use vierbergenlars\SemVer\expression; // <= Argh! Inconsistent naming convention.
use vierbergenlars\SemVer\version;    // <= Argh! Inconsistent naming convention.

class DependencyTree
{
    public function getPackage($name, $version = 'latest')
    {
        // Fetch the latest package version.
        if ($version === 'latest') {
            return $this->fetchLatestPackageVersion($name);
        }

        // Otherwise fetch the package version passed.
        return $this->fetchPackageVersion($name, $version);
    }

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

    public function fetchLatestPackageVersion($name)
    {
        $package = $this->fetchPackage($name);
        return $this->fetchPackageVersion($name, $package['dist-tags']['latest']);
    }

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

    public function getCachedPackage($name, $version)
    {
        return Package::where('name', $name)
            ->where('version', $version)
            ->first();
    }

    public function getCachedPackageData($name, $version)
    {
        $cachedPackage = $this->getCachedPackage($name, $version);
        return $cachedPackage ? json_decode($cachedPackage->data, true) : null;
    }
}