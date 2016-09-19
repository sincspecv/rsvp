

var regForm = angular.module('regForm', ['ngAnimate', 'ui.router', 'validation.match']);

regForm.config(function($stateProvider, $urlRouterProvider){

	$urlRouterProvider
		.otherwise('/register');

	$stateProvider
		.state('register', {
			url: '/register',
			templateUrl: 'lib/views/regform.html'
		});

	$stateProvider
		.state('success', {
			url: '/success',
			templateUrl: 'lib/views/regsuccess.html'
		});
});

regForm.controller('formController', function($scope, $state, $http) {

	$scope.formData = {}; //Object to collect form data

	$scope.submitForm = function(isValid) {
		if(isValid) {
			console.log($scope.formData);
			if($scope.formData.password !== $scope.formData.confirm) { //Check if passwords match
				alert('Passwords do not match');
			} else {
				$http({
					method  : 'POST',
					url     : 'lib/register.php',
					data    : $.param($scope.formData),  // pass in data as strings
					headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
				})
				.success(function(data) {
			    	$state.go('success');
			    });
			}
			
		}
	}
});
