var signIn = angular.module('signIn', ['ngAnimate', 'ui.router', 'ui.bootstrap', 'validation.match']);

signIn.config(function($stateProvider, $urlRouterProvider){

    $urlRouterProvider
        .otherwise('/register');

    $stateProvider
        .state('register', {
            url: '/register',
            templateUrl: 'lib/views/register-modal.html'
        });

    $stateProvider
        .state('success', {
            url: '/success',
            templateUrl: 'lib/views/regsuccess.html'
        });
});

signIn.directive("phoneValidator", function () {
    return {
        require: "ngModel",
        restrict: "A",
        link: function (scope, elem, attrs, ctrl) {

            var domElement = elem[0]; // Get DOM element
            var phoneNumberRegex = new RegExp("\\d{3}\\-\\d{3}\\-\\d{4}"); // Phone number regex
            var cursorIndex; // Index where the cursor should be

            // Create a parser to alter and validate if our
            // value is a valid phone number
            ctrl.$parsers.push(function (value) {

                // If our value is non-existent, we return undefined
                // WHY?: an angular model value should be undefined if it is empty
                if (typeof value === "undefined" || value === null || value == "") {
                    ctrl.$setValidity('invalidFormat', true); // No invalid format if the value of the phone number is empty
                    return undefined;
                }

                // PARSER LOGIC
                // =compare our value to a modified value after it has
                // been transformed into a "nice" phone number. If these
                // values are different, we set the viewValue to
                // the "nice" phone number. If these values are the same,
                // we render the viewValue (aka. "nice" phone number)
                var prevValue, nextValue;

                prevValue = value;
                nextValue = value.replace(/[\D]/gi, ""); // Strip all non-digits

                // Make the "nice" phone number
                if (nextValue.length >= 4 && nextValue.length <= 6) {
                    nextValue = nextValue.replace(/(\d{3})(\d{3})?/, "$1-$2");
                } else if (nextValue.length >= 7 && nextValue.length <= 10) {
                    nextValue = nextValue.replace(/(\d{3})(\d{3})(\d{4})?/, "$1-$2-$3");
                }

                // Save the correct index where the cursor should be
                // WHY?: we do this here because "ctrl.$render()" shifts
                // the cursor index to the end of the phone number
                cursorIndex = domElement.selectionStart;
                if (prevValue != nextValue) {
                    ctrl.$setViewValue(nextValue); // *Calling this function will run all functions in ctrl.$parsers!
                } else {
                    ctrl.$render(); // Render the new, "nice" phone number
                }

                // If our cursor lands on an index where a dash "-" is,
                // move it up by one
                if (cursorIndex == 4 || cursorIndex == 8) {
                    cursorIndex = cursorIndex + 1;
                }

                var valid = phoneNumberRegex.test(value); // Test the validity of our phone number
                ctrl.$setValidity('invalidFormat', valid); // Set the validity of the phone number field
                domElement.setSelectionRange(cursorIndex, cursorIndex); // Assign the cursor to the correct index

                return value; // Return the updated value
            });
        }
    }
});


signIn.controller('modalController', function($scope, $log, $uibModal, $window){

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
            templateUrl	: 'lib/views/register-modal.html',
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


signIn.controller('modalInstanceController', ['$scope', '$window', '$http', '$uibModalInstance', function($scope, $state, $http, $uibModalInstance){
    $scope.cancel = function() {
        $uibModalInstance.dismiss('cancel');
    };

    $scope.formData = {}; //Object to collect form data

    $scope.checkUser = function (userName) {
        if (userName) {
            $http.get('lib/checkuser.php?username=' + userName)
                .then(function (response) {
                    console.log('response: ' + response.data);
                    if (response.data === "0") {
                        $scope.regForm.username.$setValidity("taken", true);
                    } else {
                        $scope.regForm.username.$setValidity("taken", false);
                    }
                });
        }
    };

    $scope.submitForm = function(isValid) {
        if (isValid) {
            if ($scope.formData.password !== $scope.formData.confirm) { //Check if passwords match
                alert('Passwords do not match');
            } else {
                $http({
                    method: 'POST',
                    url: 'lib/register.php',
                    data: $.param($scope.formData),  // pass in data as strings
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}  // set the headers so angular passing info as form data (not request payload)
                })
                    .then(function(response) {
                        if (response.status = '200') {
                            console.log('success');
                            window.location.href = 'dashboard.php#/dashboard';
                        } else if (response.status = '400') {
                            conslole.log('error');
                            $scope.showError = true;
                        }
                    }, function(response) {
                        console.log('error');
                        $scope.showError = true;
                    });
            }
        }
    };
}]);


