angular.module('controllers').controller('UgaCtrl', [
    '$scope', '$ugaManage',
    function($scope, $ugaManage) {
        $ugaManage.data().then(function(data) {
            $scope.data = data;
        }, function(err) {
            alert(err);
        });
    }
])
