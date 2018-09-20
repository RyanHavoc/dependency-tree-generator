<template>
    <ul class="dependency-tree__items" :class="{ 'dependency-tree__items--last': last }">
        <li class="dependency-tree__item">
            <div class="dependency-tree__package" v-html="packageLabel"></div>
            <dependency-tree
                v-for="(dependency, index) in dependencies"
                :dependencies="dependency.dependencies"
                :name="dependency.name"
                :version="dependency.version"
                :parent-nest-length="dependencies.length"
                :nest-length="dependency.dependencies.length"
                :index="index"
            >
            </dependency-tree>
        </li>
    </ul>
</template>

<script>
    export default {
        name: 'dependency-tree',

        props: {
            name: String,
            version: String,
            dependencies: Array,
            index: Number,
            parentNestLength: {
                type: Number,
                required: false,
                default: 0
            },
            nestLength: Number
        },

        computed: {
            packageLabel: function() {
                return `${this.name} @ ${this.version}`;
            },
            last: function() {
                return this.nestLength >= this.parentNestLength && (this.index + 1) == this.parentNestLength || this.parentNestLength == (this.index + 1) && this.nestLength > 0;
            }
        }
    }
</script>