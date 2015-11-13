angular.module('controllers', ['ngTagsInput'])
  .controller('ActivityListCtrl', ['$scope', '$routeParams', '$location', '$questionManage', '$activityManage', '$activityTypeManage', '$mdDialog', 'lodash', '$mdToast',
    function($scope, $routeParams, $location, $questionManage, $activityManage, $activityTypeManage, $mdDialog, lodash, $mdToast) {

      var type_id = $routeParams.type_id;
      $activityManage.listByType(type_id).then(function(data) {
        console.log(data);
        $scope.list = data;

      }, function(err) {
        alert(err);
      });

      // 活动类型列表
      $activityTypeManage.fetch().then(function(data) {
        $scope.activityTypeList = data;
      }, function(err) {
        alert(err);
      });

      // 更新活动类型
      $scope.onTypeChangeClick = function(activity, type_id) {
        var old_type_id = activity.type_id;
        activity.type_id = type_id;
        $activityManage.update(activity.id, activity).then(function(data) {
          $location.path('/activity/list/' + type_id);
        }, function(err) {
          alert(err);
        });
      };

      $scope.onEditStatusChangeClick = function(activity, edit_status) {
        var newActivity = activity;
        newActivity.edit_status = edit_status;
        $activityManage.update(newActivity.id, newActivity).then(function(data) {
          $location.path('/activity/list/' + activity.type_id);
        }, function(err) {
          alert(err);
        })
      };

      // 设置报名表单状态 20关闭 10打开
      $scope.applyStatus = function(entity, status) {
        var new_question = entity.question;
        console.log(new_question);
        new_question.status = status;
        $questionManage.update(entity.question.id, new_question).then(function(data) {
          console.log(data);
        }, function(err) {
          alert(err);
        });
      };

      // 复制一个活动
      $scope.copy = function(entity) {
        console.log(entity);
        var newEntity = entity;
        newEntity.id = null;
        newEntity.title = entity.title + " 副本";
        newEntity.status = 10; //活动状态10为草稿
        $activityManage.create(newEntity).then(function(data){
          $location.path('/activity/list/' + entity.type_id);
          console.log(data);
        },function(err){
          alert(err);
        });
      };

      // 发布活动
      $scope.release = function(entity) {
        var confirm = $mdDialog.confirm()
          .title('确定要发布活动“' + entity.title + '”吗？')
          .ariaLabel('delete activity item')
          .ok('确定发布')
          .cancel('点错了，再看看');

        $mdDialog.show(confirm).then(function() {
          console.log(entity);
          var newEntity = entity;
          newEntity.status = 20;//活动状态20为发布
          $activityManage.update(newEntity.id, newEntity).then(function(data){
            $location.path('/activity/list/' + entity.type_id);
            console.log(data);
            
            $mdToast.show($mdToast.simple()
              .content('活动 “' + entity.title + '” 已发布')
              .hideDelay(5000)
              .position("top right"));

          },function(err){
            $mdToast.show($mdToast.simple()
              .content(err.toString())
              .hideDelay(5000)
              .position("top right"));
          });          
        });        
      }

      // 删除
      $scope.delete = function(entity) {
        var confirm = $mdDialog.confirm()
          .title('确定要删除活动“' + entity.title + '”吗？')
          .ariaLabel('delete activity item')
          .ok('确定删除')
          .cancel('手滑点错了，不删');

        $mdDialog.show(confirm).then(function() {
          $activityManage.delete(entity).then(function(data) {

            lodash.remove($scope.list, function(tmpRow) {
              return tmpRow == entity;
            });

            $mdToast.show($mdToast.simple()
              .content('删除活动类型“' + entity.title + '”成功')
              .hideDelay(5000)
              .position("top right"));

          }, function(err) {
            $mdToast.show($mdToast.simple()
              .content(err.toString())
              .hideDelay(5000)
              .position("top right"));
          });
        });
      };

      // 置顶/取消置顶
      $scope.top = function(entity, is_top) {
        var newEntity = entity;
        newEntity.is_top = is_top > 0 ? 1 : 0; // 是否置顶
        $activityManage.update(entity.id, newEntity).then(function(data) {
          $mdToast.show($mdToast.simple()
            .content('置顶成功')
            .hideDelay(5000)
            .position("top right"));
          $location.path('/activity/list/' + $routeParams.type_id);
        }, function(err) {
          $mdToast.show($mdToast.simple()
            .content(err.toString())
            .hideDelay(5000)
            .position("top right"));
        })
      }

      // 设置问题
      $scope.viewQuestion = function(activity) {
        $location.path('/question/view/' + activity.id);
      }

      // 查看报名
      $scope.viewAnswer = function(activity) {
        $location.path('/answer/' + activity.id);
      }

      // 查看反馈
      $scope.viewFeedback = function(activity) {
        $location.path('/activity-feedback/'+activity.id);
      }

      // 预览问题
      $scope.previewQuestion = function(entity) {
        $location.path('/answer/view/' + entity.id);
      }

      // tab
      $scope.isActive = function(type_id) {
        console.log(type_id);
        var route = "/activity/list/"+type_id
        return route === $location.path();
        // return .indexOf(route) != -1;
      }

      //点击增加类型按钮
      $scope.onTypeAddClicked = function() {
        console.log("type add")
        $scope.showAddForm = true;
      };

      //ng-if会增加新的child，需要设置初始值
      $scope.addForm = {
        newType: ""
      };

      // 取消增加新类型
      $scope.cancelAddType = function() {
        $scope.showAddForm = false;
      };

      var addTypeName = function(data){
        var newEntity = {
          name: data,
          display_order: 3
        };
        $activityTypeManage.create(newEntity).then(function(data) {
          $activityTypeManage.fetch().then(function(data) {
            $scope.activityTypeList = data;
          }, function(err) {
            alert(err);
          });

          $location.path('/activity/list/0');
          $mdToast.show($mdToast.simple()
              .content('添加活动类型成功')
              .hideDelay(5000)
              .position("top right"));
        }, function(err) {
          $mdToast.show($mdToast.simple()
              .content(err.toString())
              .hideDelay(5000)
              .position("top right"));
        });
        $scope.showAddForm = false;
        $scope.addForm = {
          newType: ""
        };
      };

      // 增加新的类型
      $scope.commitTypeName = function(typeName) {

        if(typeName.length == 0){
          $mdToast.show(
            $mdToast.simple()
              .content("分组名称不能为空")
              .hideDelay(5000)
              .position("top right"));
        }else if(typeName.length > 20){
          $mdToast.show(
            $mdToast.simple()
              .content("分组名称不能超过20个字符")
              .hideDelay(5000)
              .position("top right"));
        }else{
          addTypeName(typeName);
        }
        
      };

      // 增加新活动
      $scope.createPage = function() {
        $location.path('/activity/add');
      }

    }
  ])
  .controller('ActivityViewCtrl', ['$scope', '$routeParams', '$location', '$activityManage', '$activityTypeManage', '$qupload', '$qiniuManage', '$mdToast',
    function($scope, $routeParams, $location, $activityManage, $activityTypeManage, $qupload, $qiniuManage, $mdToast) {
      $scope.$parent.pageName = '活动详情';

      $scope.onStartTimeSet = function(newDate, oldDate) {
        $scope.start_time_str = getTimeByTimestamp(getTimestamp(newDate));
        $scope.entity.start_time = getTimestamp(newDate);
      }

      $scope.onStopTimeSet = function(newDate, oldDate) {
        $scope.end_time_str = getTimeByTimestamp(getTimestamp(newDate));
        $scope.entity.end_time = getTimestamp(newDate);
      }

      // 标签
      $scope.tags = [];
      // 标签搜索功能
      $scope.loadTags = function(query) {
        return $activityManage.tags(query);
      };

      // qiniu upload poster start //
      $scope.selectPoster = null;

      var startPoster = function() {
        $qiniuManage.fetchUploadToken().then(function(token) {

          $qupload.upload({
            key: '',
            file: $scope.selectPoster.file,
            token: token
          }).then(function(response) {
            $qiniuManage.completelyUrl(response.key).then(function(url) {
              $scope.poster = url;
            });
          }, function(response) {
            console.log(response);
          }, function(evt) {
            if ($scope.selectPoster !== null) {
              $scope.selectPoster.progress.p = Math.floor(100 * evt.loaded / evt.totalSize);
            }
          });

        });
      };

      $scope.posterAbort = function() {
        $scope.selectPoster.upload.abort();
        $scope.selectPoster = null;
      };

      $scope.onPosterSelect = function($files) {
        $scope.selectPoster = {
          file: $files[0],
          progress: {
            p: 0
          }
        };
        startPoster();
      };
      // qiniu upload poster end //

      // qiniu upload code start //
      $scope.selectCode = null;

      var startCode = function() {
        $qiniuManage.fetchUploadToken().then(function(token) {

          $qupload.upload({
            key: '',
            file: $scope.selectCode.file,
            token: token
          }).then(function(response) {
            $qiniuManage.completelyUrl(response.key).then(function(url) {
              $scope.group_code = url;
            });
          }, function(response) {
            console.log(response);
          }, function(evt) {
            if ($scope.selectCode !== null) {
              $scope.selectCode.progress.p = Math.floor(100 * evt.loaded / evt.totalSize);
            }
          });

        });
      };

      $scope.codeAbort = function() {
        $scope.selectCode.upload.abort();
        $scope.selectCode = null;
      };

      $scope.onCodeSelect = function($files) {
        $scope.selectCode = {
          file: $files[0],
          progress: {
            p: 0
          }
        };
        startCode();
      };
      // qiniu upload group code end //

      var id = $routeParams.id;
      if (id > 0) {
        $activityManage.fetch(id).then(function(data) {
          $scope.entity = data;
          $scope.start_time_str = getTimeByTimestamp(data.start_time);
          $scope.end_time_str = getTimeByTimestamp(data.end_time);
          $scope.poster = data.poster;
          $scope.group_code = data.group_code;

          var tags = [];
          for (var k in data.tags) {
            var tag = data.tags[k].name;
            tags.push(tag);
          }
          $scope.tags = tags;

        }, function(err) {
          $location.path('/activity/list/0');
        });
      }

      // 列表
      $activityTypeManage.fetch().then(function(data) {
        $scope.type_list = data;
      });

      // 取消
      $scope.cancel = function() {
        $location.path('/activity/list/0');
      }

      $scope.save = function() {
        var newEntity = $scope.entity;
        newEntity.start_time = $scope.entity.start_time;
        newEntity.end_time = $scope.entity.end_time;
        newEntity.poster = $scope.poster;
        newEntity.group_code = $scope.group_code;

        var tags = [];
        for (var k in $scope.tags) {
          var tag = $scope.tags[k].text;
          tags.push(tag);
        }
        newEntity.tagNames = tags.join();

        if (newEntity.id > 0) { // 更新活动
          $activityManage.update(newEntity.id, newEntity).then(function(data) {
            $mdToast.show($mdToast.simple()
              .content('活动保存成功')
              .hideDelay(5000)
              .position("top right"));
            $location.path('/activity/list/0');
          }, function(err) {
            $mdToast.show($mdToast.simple()
              .content(err.toString())
              .hideDelay(5000)
              .position("top right"));
          });
        } else { // 添加活动
          $activityManage.create(newEntity).then(function(data) {
            $location.path('/activity/list/0');
            $mdToast.show($mdToast.simple()
              .content('活动添加成功')
              .hideDelay(5000)
              .position("top right"));
          }, function(err) {
            $mdToast.show($mdToast.simple()
              .content(err.toString())
              .hideDelay(5000)
              .position("top right"));
          });
        }
      };

    }
  ]);