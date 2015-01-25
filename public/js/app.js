;(function ($, window, undefined) {

    'use strict';

    var app = window.app = angular.module('app', ['ngResource', 'ngTagsInput', 'nouislider']);

    app.factory('Search', ['$resource', function($resource) {
        return $resource( '/search/:query',
            { query: '@query' }
        );
    }]);

    app.controller('MainCtrl', function ($q, $scope, Search) {
        $scope.age = {'from': 15, 'to': 35};

        $scope.searchFilterTags = [
            { text: 'Tag1', class: 'tag-ethnicity' },
            { text: 'Tag2', class: 'tag-location' },
            { text: 'Tag3', class: 'tag-gender' },
            { text: 'Tafsdfdsf ,sdf3', class: 'tag' }
        ];

        $scope.$watch('[age.from, age.to]', function () {
            // update filter
        });

        $scope.$watch('searchFilterTags', function() {
            // update filter
        }, true);

        $scope.loadTags = function(query) {
            var deferred = $q.defer();

            Search.query({query: query},
                function success(data) {
                    deferred.resolve(data);
                },
                function error() {
                }
            );

            return deferred.promise;
        };

    });

})(jQuery, this);