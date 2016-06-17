var account = angular.module('account', ['ui.router']);

account.config(function($stateProvider, $urlRouterProvider){

	$urlRouterProvider
		.otherwise('/dashboard');

	$stateProvider
		.state('dashboard', {
			url: '/dashboard',
			templateUrl: 'lib/views/dashboard.html'
		});

	$stateProvider
		.state('create', {
			url: '/create',
			templateUrl: 'lib/views/create.html'
		});

	$stateProvider
		.state('event', {
			url: '/event',
			templateUrl: 'lib/views/event.html'
		});

	$stateProvider
		.state('eventCreated', {
			url: '/created',
			templateUrl: 'lib/views/created.html'
		});
});


account.controller('eventController', ['$scope', '$state', function($scope, $state){
	$scope.events = $scope.user.event_codes;
	$scope.changeView = function(view){
            $state.go(view);
        }
}]);

account.controller('eventFormController', function($scope, $state, $http) {

	$scope.formData = {}; //Object to collect form data

	$scope.submitForm = function(isValid) {
		if(isValid) {
			console.log($scope.formData);
			if($scope.formData.password !== $scope.formData.confirm) { //Check if passwords match
				alert('Passwords do not match');
			} else {
				$http({
					method  : 'POST',
					url     : 'lib/create.php',
					data    : $.param($scope.formData),  // pass in data as strings
					headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
				})
				.success(function(data) {
			    	$state.go('eventCreated');
			    });
			}
			
		}
	}
});