var installForm = angular.module('installForm', ['ngAnimate', 'ui.bootstrap', 'validation.match']);

installForm.controller('installCtrl', function($scope, $http) {

    $scope.formData = {}; //Object to collect form data


    $scope.submitForm = function (isValid) {
        if (isValid) {
            $http({
                method: 'POST',
                url: 'install.php',
                data: $.param($scope.formData),  // pass in data as strings
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}  // set the headers so angular passing info as form data (not request payload)
            })
                .then(function(response) {
                    if (response.status = '200') {
                        console.log('success');

                    } else if (response.status = '400') {
                        conslole.log('error');
                        $scope.showError = true;
                    }
                }, function(response) {
                    console.log(response);
                    $scope.showError = true;
                });

        }
    }

});


