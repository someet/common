angular.module('controllers').controller('UgaCtrl', [
    '$scope', '$ugaManage',
    function($scope, $ugaManage) {

        $scope.isActive = function() {
            var route = "/uga";
            return route === $location.path();
        }

        $ugaManage.data().then(function(data) {
            $scope.data = data;
        }, function(err) {
            alert(err);
        })
    }
])
