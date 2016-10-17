var account = angular.module('account', ['ui.router', 'ui.bootstrap', 'smart-table']);

account.config(function($stateProvider, $urlRouterProvider) {

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

account.filter('tel', function () {
    return function (tel) {
        if (!tel) { return ''; }

        var value = tel.toString().trim().replace(/^\+/, '');

        if (value.match(/[^0-9]/)) {
            return tel;
        }

        var country, city, number;

        switch (value.length) {
            case 10: // +1PPP####### -> C (PPP) ###-####
                country = 1;
                city = value.slice(0, 3);
                number = value.slice(3);
                break;

            case 11: // +CPPP####### -> CCC (PP) ###-####
                country = value[0];
                city = value.slice(1, 4);
                number = value.slice(4);
                break;

            case 12: // +CCCPP####### -> CCC (PP) ###-####
                country = value.slice(0, 3);
                city = value.slice(3, 5);
                number = value.slice(5);
                break;

            default:
                return tel;
        }

        if (country == 1) {
            country = "";
        }

        number = number.slice(0, 3) + '-' + number.slice(3);

        return (country + " (" + city + ") " + number).trim();
    };
});


account.controller('eventController', ['$scope', '$http', '$state', function($scope, $http, $state) {
	$scope.events = $scope.user.event_codes; //store 'event_codes' from session array

	$scope.changeView = function(view) {
		$state.go(view);
	};

    $http.get('lib/events.php')
    	.then(function(response) {
    		$scope.eventInfo = response.data;
    	});
}]);

account.controller('eventFormController', ['$scope', '$state', '$http', function($scope, $state, $http) {

	$scope.changeView = function(view){
		$state.go(view);
	};

	//Set $scope.today to today's date
	var today = new Date;
	$scope.today = today.toISOString();

	$scope.formData = {}; //Object to collect form data

	$scope.submitForm = function(isValid) {
		if(isValid) {
			$http({
				method  : 'POST',
				url     : 'lib/create.php',
				data    : $.param($scope.formData),  // pass in data as strings
				headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
			})
			.then(function(response) {
			    if (response.status = '200') {
			        console.log('success');
			    	$state.go('eventCreated');
                } else if (response.status = '400') {
                    conslole.log(response);
                	$scope.showError = true;
                }
		    }, function(response) {
					console.log(response);
					$scope.showError = true;
			});

		}
		
	}
}]);


account.controller('NavCtrl', function($scope, $location){
	$scope.isActive = function(route) {
		return route === $location.path();
	};
});

account.controller('guestListCtrl', function($scope, $http, $state) {

    $scope.formData = {}; //Object to collect form data
	$scope.displayedList = [];

	var getGuestList = function() {
		$scope.events = $scope.user.event_codes; //store 'event_codes' from session array

		$http.get('lib/events.php')
			.then(function(response){
				$scope.eventInfo = response.data;
			});
	};

    var modifyGuestList = function(isValid, $eventCode, $method, $id) {
        $scope.formData.eventCode = $eventCode;
		$scope.formData.method = $method;
		$scope.formData.id = $id;


        if(isValid) {
            console.log($scope.formData);
            $http({
                method  : 'POST',
                url     : 'lib/add.php',
                data    : $.param($scope.formData),  // pass in data as strings
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
            })
                .then(function(data) {
                    $http.get('lib/events.php')
                        .then(function(response){
                            $scope.eventInfo = response.data;
							$eventCode = true;
                        });
                },  function(response) {
					console.log(response);
				});
        }
    };

	//add to the real data holder
	$scope.addGuest = function addGuest(isValid, eventCode, method) {
		modifyGuestList(isValid, eventCode, method);
		$scope.events.push(getGuestList);
        $state.reload('dashboard');
	};

	//remove to the real data holder
	$scope.removeGuest = function removeItem(eventCode, method, id) {
		if (id !== -1) {
			var isValid = true;
			modifyGuestList(isValid, eventCode, method, id);
			$scope.events.push(getGuestList);
			$state.reload('dashboard');
		}
	}

});


account.controller('modalController', function($scope, $log, $uibModal){

	$scope.oneAtATime = true;

	$scope.status = {
		isCustomHeaderOpen: false,
		isFirstOpen: true,
		isFirstDisabled: false
	};

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
    			window.location.reload(true);
    			});
		    });
		
			
		}
	};
}]);