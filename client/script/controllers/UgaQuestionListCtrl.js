angular.module('controllers').controller('UgaQuestionListCtrl', [
'$scope', '$ugaManage', function ($scope, $ugaManage) {
		console.log(1);
        $ugaManage.fetch().then(function (data) {
          $scope.list = data;
          console.log(data);
        }, function (err) {
          alert(err);
        });
	}
])