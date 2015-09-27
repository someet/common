// 引入 gulp 
var gulp = require('gulp');

// 引入组件
var jshint = require('gulp-jshint');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');

// 检查脚本
gulp.task('lint', function() {
  gulp.src('./client/script/*.js')
    .pipe(jshint())
    .pipe(jshint.reporter('default'));
});

// 编译 sass
gulp.task('sass', function() {
  gulp.src(['./client/style/**/*.scss'])
    .pipe(sass())
    .pipe(gulp.dest('./web/static/style'));
});

gulp.task('copy', function(){
  gulp.src(['./bower_components/angular-material/angular-material.min.css'])
   .pipe(gulp.dest('./web/static/style'));

  gulp.src([
      './bower_components/angular/angular.min.js',
      './bower_components/angular-animate/angular-animate.min.js',
      './bower_components/angular-aria/angular-aria.min.js',
      './bower_components/angular-material/angular-material.min.js'
    ])
    .pipe(gulp.dest('./web/static/js'));

  gulp.src('./client/other/*')
    .pipe(gulp.dest('./web/static/other'));
});

// 合并，压缩文件
gulp.task('script', function(){
  gulp.src('./client/script/*.js')
    .pipe(concat('all.js'))
    .pipe(gulp.dest('./web/static/js'))
    .pipe(rename('all.min.js'))
    .pipe(uglify())
    .pipe(gulp.dest('./web/static/js'))
});

// 默认任务
gulp.task('default', function() {
  gulp.run('lint', 'sass', 'script', 'copy');

  gulp.watch(['./client/**/*'], function(){
    gulp.run('lint', 'sass', 'script', 'copy');
  });
})
