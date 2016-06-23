angular.module('controllers')
    .controller('WechatCtrl', ['$scope', '$location', '$wechatManage', '$mdDialog', function($scope, $location, $wechatManage, $mdDialog) {
    	$wechatManage.fetch().then(function(data){
    		// console.log(data);
    		$scope.wechatReply = data;
    	})

    	$scope.createWechatReply = function(){
    		// console.log(data);
    		$wechatManage.create($scope.wechatReply).then(function(data1){
    			console.log(data1);
    		});
    	}
}]);