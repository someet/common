angular.module('controllers').controller('UgaAnswerListCtrl', [
    '$scope', '$routeParams', '$ugaAnswerManage',
    function($scope, $routeParams, $ugaAnswerManage) {
        console.log(1);

        	var id = $routeParams.id;
            $ugaAnswerManage.fetch(id).then(function(data) {
                $scope.list = data;
                console.log(data);
            }, function(err) {
                alert(err);
            });


    }
])
