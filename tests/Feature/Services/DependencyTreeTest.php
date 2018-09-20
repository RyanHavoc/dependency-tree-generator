<?php

namespace Tests\Feature\Services;

use App\Package;
use App\Services\DependencyTree;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DependencyTreeTest extends TestCase
{
    /** @test */
    public function it_gets_a_full_package() 
    {
        // Setup
        $dependencyTree = new DependencyTree();
        
        // Testing
        $result = $dependencyTree->getPackage('express');
        
        //Results
        $this->assertArrayHasKey('dist-tags', $result);
        $this->assertArrayHasKey('versions', $result);
    }
    
    /** @test */
    public function it_gets_the_latest_package() 
    {
        // Setup
        $dependencyTree = new DependencyTree();

        // Testing
        $latestVersion = $dependencyTree->getPackage('express')['dist-tags']['latest'];
        $result = $dependencyTree->getPackage('express', 'latest');

        //Results
        $this->assertEquals($latestVersion, $result['version']);
    }
    
    /** @test */
    public function it_gets_a_specific_package() 
    {
        // Setup
        $dependencyTree = new DependencyTree();

        // Testing
        $result = $dependencyTree->getPackage('express', '4.16.1');

        //Results
        $this->assertEquals('4.16.1', $result['version']);
    }
    
    /** @test */
    public function it_gets_a_dependency_tree_for_a_package() 
    {
        // Setup
        $dependencyTree = new DependencyTree();

        // Testing
        $result = $dependencyTree->getDependencyTree('express');

        //Results
        $this->assertEquals('express', $result['name']);
        $this->assertTrue(is_array($result['dependencies']));
    }

    /** @test */
    public function it_removes_superfluous_comparisons_from_start_of_version_string()
    {
        // Setup
        $dependencyTree = new DependencyTree();

        // Testing & Results
        $this->assertEquals('1.2.0', $dependencyTree->parseVersion('statuses', '=1.2.0'));
        $this->assertEquals('1.2.0', $dependencyTree->parseVersion('statuses', '~1.2.0'));
        $this->assertEquals('1.2.0', $dependencyTree->parseVersion('statuses', '^1.2.0'));
    }

    /** @test */
    public function it_converts_single_number_versions_into_a_proper_format()
    {
        // Setup
        $dependencyTree = new DependencyTree();

        // Testing & Results
        $this->assertEquals('1.0.0', $dependencyTree->parseVersion('statuses','1'));
        $this->assertEquals('13.0.0', $dependencyTree->parseVersion('statuses','13'));
    }

    /** @test */
    public function it_returns_the_latest_version_to_match_an_range_expression()
    {
        // Setup
        $dependencyTree = new DependencyTree();

        // Testing & Results
        $this->assertEquals('1.5.0', $dependencyTree->parseVersion('statuses', '>=1.3.0 < 2'));
        $this->assertEquals('1.4.0', $dependencyTree->parseVersion('statuses', '>=1.3.0 < 1.5.0'));
    }
    
    /** @test */
    public function it_caches_a_package() 
    {
        // Setup
        $dependencyTree = new DependencyTree();
        
        // Testing
        $result = $dependencyTree->getPackage('express', '4.16.1');
        
        //Results
        $this->assertDatabaseHas('packages', [
            'name' => $result['name'],
            'version' => $result['version']
        ]);
    }

    /** @test */
    public function it_avoids_duplicate_caching_of_a_package()
    {
        // Setup
        $dependencyTree = new DependencyTree();

        // Testing
        $dependencyTree->getPackage('express', '4.16.1');
        $dependencyTree->getPackage('express', '4.16.1');

        //Results
        $this->assertEquals(1, Package::where('name', 'express')->where('version', '4.16.1')->count());
    }
    
    /** @test */
    public function it_gets_a_cached_package() 
    {
        // Setup
        $dependencyTree = new DependencyTree();
        $dependencyTree->getPackage('express', '4.16.1');

        // Testing
        $result = $dependencyTree->getCachedPackage('express', '4.16.1');
        
        //Results
        $this->assertEquals('express', $result->name);
        $this->assertEquals('4.16.1', $result->version);
    }
    
    /** @test */
    public function it_gets_the_parsed_data_for_a_cached_package()
    {
        // Setup
        $dependencyTree = new DependencyTree();
        $dependencyTree->getPackage('express', '4.16.1');

        // Testing
        $result = $dependencyTree->getCachedPackageData('express', '4.16.1');

        //Results
        $this->assertTrue(is_array($result));
        $this->assertEquals('express', $result['name']);
        $this->assertEquals('4.16.1', $result['version']);
    }
}
