<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Dependency Tree</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">

        <!-- Styles -->
        {{ Narum::loadAsset('css', 'site') }}
    </head>
    <body>
        <div class="site-container" data-app="dependencyTree">
            <header class="site-header">
                <h1 class="site-header__title">Dependency Tree Generator</h1>
            </header>
            <div class="search">
                <header class="search__header">
                    <form class="search__form" @submit="submit">
                        <input type="text" placeholder="Enter an npm package name..." v-model="packageName">
                        <div class="select" v-if="packageVersions">
                            <select v-model="packageVersion" @change="changeVersion">
                                <option v-for="version in packageVersions" v-html="version"></option>
                            </select>
                        </div>
                    </form>
                    <div class="search__examples">For example: <a @click="changePackage('express')">express</a>, <a @click="changePackage('async')">async</a>, <a @click="changePackage('vue')">vue</a>, <a @click="changePackage('astrum')">astrum</a></div>
                </header>
                <div class="dependency-tree" v-if="loading || notFound || dependencyTree">
                    <div class="dependency-tree__status" v-if="loading">
                        <div class="folding-cube">
                            <div class="cube1 cube"></div>
                            <div class="cube2 cube"></div>
                            <div class="cube4 cube"></div>
                            <div class="cube3 cube"></div>
                        </div>
                        <span>Analysing dependency tree...</span>
                    </div>
                    <div class="dependency-tree__status" v-if="notFound">
                        <span>No package found.</span>
                    </div>
                    <dependency-tree
                        v-if="dependencyTree && !loading"
                        :name="dependencyTree.name"
                        :version="dependencyTree.version"
                        :dependencies="dependencyTree.dependencies"
                        :nest-length="dependencyTree.dependencies.length"
                        :index="0"
                    ></dependency-tree>
                </div>
            </div>
            <footer class="site-footer">
                <p class="copyright">This service was created by <a href="mailto:ryan@nodividestudio.com">Ryan Taylor</a> in 2018 as an exercise and experiment with the NPM registry to construct a package dependency tree.</p>
            </footer>
        </div>

        {{ Narum::loadAsset('js', 'site') }}
    </body>
</html>