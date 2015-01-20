var app = angular.module( 'app', ['nouislider'] );

app.controller( 'Root', function( $scope ) {
    $scope.age = {'from': 15, 'to': 35};
});

