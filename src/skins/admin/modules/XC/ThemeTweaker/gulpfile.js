/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Gulpfile
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var gulp = require('gulp');
var uglify = require('gulp-uglify');
var notify = require('gulp-notify');

gulp.task('default', function () {
    return gulp.src('codemirror/lib/codemirror.js')
        .pipe(uglify())
        .pipe(gulp.dest('codemirror/lib/min'))
        .pipe(notify({ message: 'Finished minifying codemirror/lib/codemirror.js'}));
});
