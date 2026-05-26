<?php
    namespace anorrl;
    use anorrl\Universe;

    class Datastore {

        private Universe $universe;

        function __construct(Universe $universe) {
            $this->universe = $universe;
        }

        function get(bool $sorted = false) {}
        function set() {}
        function increment() {}

    }