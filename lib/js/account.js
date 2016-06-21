var account = angular.module('account', ['ui.router', 'ui.bootstrap']);

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
		.state('eventCreated', {
			url: '/created',
			templateUrl: 'lib/views/created.html'
		});
});



account.controller('eventController', ['$scope', '$http', '$state', function($scope, $http, $state){
	$scope.events = $scope.user.event_codes; //store 'event_codes' from session array

	$scope.changeView = function(view){
		$state.go(view);
	};

    $http.get('lib/events.php')
    	.then(function(response){
    		$scope.eventInfo = response.data;
    	});
}]);

account.controller('eventFormController', ['$scope', '$state', '$http', function($scope, $state, $http) {

$scope.changeView = function(view){
		$state.go(view);
};

	$scope.formData = {}; //Object to collect form data

	$scope.submitForm = function(isValid) {
		if(isValid) {
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
}]);

account.controller('modalController', function($scope, $log, $uibModal){
	$scope.animationsEnabled = true;

	$scope.open = function(size) {

	    var modalInstance = $uibModal.open({
	    	animation	: $scope.animationsEnabled,
	    	templateUrl	: 'lib/views/guestlist.html',
	    	scope		: $scope,
	    	controller	: 'modalInstanceController',
	    	size: size,
	    	resolve: {
		        items: function () {
		          return $scope.eventInfo;
		        }
		      }
	    });
    

	    modalInstance.result.then(function (selectedItem) {
	    	$scope.selected = selectedItem;
	    }, function () {
	    	$log.info('Modal dismissed at: ' + new Date());
	    });
    };
});

account.controller('modalInstanceController', ['$scope', '$http', '$uibModalInstance', function($scope, $http, $uibModalInstance){
	$scope.cancel = function() {
    	$uibModalInstance.dismiss('cancel');
  	};

  	$scope.submitForm = function(isValid) {
		if(isValid) {
			console.log($scope.formData);
			$http({
				method  : 'POST',
				url     : 'lib/add.php',
				data    : $.param($scope.formData),  // pass in data as strings
				headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
			})
			.success(function(data) {
		    	$http.get('lib/events.php')
    				.then(function(response){
    			$scope.eventInfo = response.data;
    			$scope.location.reload(true);
    			});
		    });
		
			
		}
	}
}]);