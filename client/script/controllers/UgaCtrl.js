angular.module('controllers').controller('UgaCtrl', [
    '$scope', '$ugaManage',
    function($scope, $ugaManage) {
    	// 官方问题
        $ugaManage.fetch(10).then(function(data) {
            $scope.officialList = data;
        }, function(err) {
            alert(err);
        });
        // 民间问题
        $ugaManage.fetch(0).then(function(data) {
        	console.log(data);
            $scope.folkList = data;
        }, function(err) {
            alert(err);
        });
    }
])
