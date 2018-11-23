// gulpfile.js
var gulp = require('gulp');
    runSequence  = require('run-sequence'),
    clearnHtml   = require('gulp-cleanhtml'),
    minifyInline = require('gulp-minify-inline-scripts'),
    imagemin = require('gulp-imagemin');
    uglify   = require('gulp-uglify');
    cleanCss = require('gulp-clean-css');
    // rev = require('gulp-rev');
    // 只能替换相对html的资源路径 且文件存在带v=.
    rev = require('gulp-rev-append');
    del = require('del');
    revCollector = require('gulp-rev-collector');
    browserSync  = require('browser-sync').create(),
    reload       = browserSync.reload;

    var src  = './public/static/default';
    var dist = './public/static/dist';
    var src_html  = './application/admin/view/df';
    var dist_html = './application/admin/view/df_dist';

//复制html
// gulp.task('copyHtml', function(){
//    return gulp.src('./bolg/template/**/*.html')
//     .pipe(clearnHtml())
//     .pipe(minifyInline())
//     .pipe(gulp.dest(dir));
// });

//复制Css
// gulp.task('copyCss',function(){
//    return gulp.src(src+'/admin/css/*.css')
//     .pipe(cleanCss({conpatibility: 'ie8'}))
//     .pipe(rev())
//     .pipe(gulp.dest(dist+'/admin/css'))
//     .pipe(rev.manifest())
//     .pipe(gulp.dest(dist+'/manifest/css'));
// });

//复制字体
// gulp.task('copyFont',function(){
//    return gulp.src('./bolg/public/static/assets/fonts/*')
//     .pipe(gulp.dest(dir2+'/fonts'))
// });

//复制JS
// gulp.task('copyJs', function(){
//    return gulp.src(['./bolg/public/static/assets/js/*.js','./bolg/public/static/assets/demo/js/*.js'])
//     .pipe(uglify())
//     .pipe(rev())
//     .pipe(gulp.dest(dir2+'/js'))
//     .pipe(rev.manifest())
//     .pipe(gulp.dest(dir+'/manifest/js'));
// });

//复制图片
// gulp.task('copyImg', function () {
//    return gulp.src('./bolg/public/static/assets/images/**/*')
//     .pipe(imagemin())
//     .pipe(rev())
//     .pipe(gulp.dest(dir2+'/images'))
//     .pipe(rev.manifest())
//     .pipe(gulp.dest(dir+'/manifest/images'));
// });

//对html中的静态资源（css,js,image)进行MD5后的文件引用替换
gulp.task('rev',function(){
    return gulp.src(src_html+'/login/*.html')
     .pipe(rev())
     .pipe(gulp.dest(dist_html+'/login'));
});

//对html中的静态资源（css,js,image)进行MD5后的文件引用替换
// gulp.task('rev', function(){
//     return gulp.src([dist+'/manifest/**/*.json', src_html+'/login/*.html'])     //找到json，和目标html文件路径
//         .pipe(revCollector({
//             replaceReved: true,
//         }))
//         .pipe(gulp.dest(dist_html+'/login'));
//  });

//监视文件，并进行自动操作 task : server
gulp.task('server',function(){
    // browserSync.init({
    //     proxy : 'http://tp51',
    //     notify: false, // 刷新不弹出提示
    // });
    gulp.watch(src + '/**/css/!(_)*.{scss,css}', ['rev']);
    gulp.watch(src + '/**/js/*.js', ['rev']);
});

//设置默认任务 task : default
gulp.task('default', function (done) {
    condition = false;
    //依次顺序执行
    runSequence(
    ['clear'],
    // ['copyImg'],
    // ['copyHtml'],
    // ['copyCss'],
    // ['copyFont'],
    // ['copyJs'],
        ['rev'],
    //     ['server'],
        done);
});

//html
// gulp.task('html', function (done) {
//     condition = false;
//     //依次顺序执行
//     runSequence(
//     ['copyHtml'],
//         ['rev'],
//         ['bwrel'],
//         done);
// });

//css
// gulp.task('css', function (done) {
//     condition = false;
//     //依次顺序执行
//     runSequence(
//     ['clear'],
//     // ['copyCss'],
//         ['rev'],
//         // ['bwrel'],
//         done);
// });

//js
// gulp.task('js', function (done) {
//     condition = false;
//     //依次顺序执行
//     runSequence(
//     ['copyJs'],
//         ['rev'],
//         ['bwrel'],
//         done);
// });

//img
// gulp.task('img', function (done) {
//     condition = false;
//     //依次顺序执行
//     runSequence(
//     ['copyImg'],
//         ['rev'],
//         ['bwrel'],
//         done);
// });

//reload
// gulp.task('bwrel', function(){
//     gulp.watch(dir+'/**/*.html').on("change", reload);
// });

//清除开发文件夹（dist)
gulp.task('clear', function(){
    del(dist_html);
    del(dist);
});