angular.module('controllers')
    .controller('ChannelCtrl', ['$scope', '$location', '$channelManage', '$mdDialog', function($scope, $location, $channelManage, $mdDialog) {
        $channelManage.fetch().then(function(data){
            // console.log(data);
            $scope.channel = data;
        })

        $scope.createChannel = function(){
            $channelManage.create($scope.channel).then(function(data){
                console.log(data);
            });
        }
    }]);