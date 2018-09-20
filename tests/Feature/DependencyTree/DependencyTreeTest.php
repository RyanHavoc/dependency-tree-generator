<?php

namespace Tests\Feature;

use App\Services\DependencyTree;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DependencyTreeTest extends TestCase
{


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
}
