<?php

namespace App\Http\Controllers;

use App\DependencyTree\DependencyTree;

class NPMController extends Controller
{
    protected $dependencyTree;

    public function __construct(DependencyTree $dependencyTree) {
        $this->dependencyTree = $dependencyTree;
    }

    public function getPackage($name, $version = 'latest')
    {
        return $this->dependencyTree->getPackage($name, $version);
    }

    public function getDependencyTree($name, $version = 'latest')
    {
        return $this->dependencyTree->getDependencyTree($name, $version);
    }
}
