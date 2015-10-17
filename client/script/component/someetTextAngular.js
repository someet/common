// custom text angular image insert image link and video insert
angular.module("someetTextAngular", ['textAngular']);
function wysiwygeditor($scope) {
  $scope.orightml = '<h2>Try me!</h2>';
  $scope.htmlcontent = $scope.orightml;
  $scope.disabled = false;
};
