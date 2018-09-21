<?php

namespace App\Http\Controllers;

use App\Services\DependencyTree;

class NPMController extends Controller
{
    protected $dependencyTree;

    public function __construct(DependencyTree $dependencyTree) {
        $this->dependencyTree = $dependencyTree;
    }

    public function fetchPackage($name)
    {
        return $this->dependencyTree->fetchPackage($name);
    }

    public function getPackage($name, $version = null)
    {
        return $this->dependencyTree->getPackage($name, $version);
    }

    public function getDependencyTree($name, $version = 'latest')
    {
        return $this->dependencyTree->getDependencyTree($name, $version);
    }
}
