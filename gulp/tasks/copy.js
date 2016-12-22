var gulp = require('gulp');
var variables = require('../variables');

var filesCopy = [
	//-- Todos los archivos y subdirectorios
	'**/**',
	//-- Ignorar archivos que solo se usan en el desarrollo
	'!gulp{,/**}',
	'!assets{,/**}',
	'!build{,/**}',
	'!node_modules{,/**}',
	'!bower_components{,/**}',
	'!**/*.dist',
	'!**/*.md',
	'!**/*.lock',
	'!*.json',
	'!gulpfile.js',
	//-- Ignorar archivos de instalación
	// '!lib/installer{,/**}',
	// '!installer.php',
	'!INSTALL.TXT',
	//-- Otros archivos
	'!CHANGELOG.txt',
	'!QUICKINSTALL.TXT',
	'!README_FIRST.txt',
	'!README.txt'
];

//-- Copy files of game
gulp.task('copy-main', function () {
	return gulp.src(filesCopy)
		.pipe(gulp.dest(variables.build.dir))
	;
});

//-- Copy to localhost beta
gulp.task('copy-beta', function () {
	return gulp.src(variables.build.dir + '{/**,/**/.*}')
		.pipe(gulp.dest(variables.development.dir))
	;
});

//-- Copy to localhost alpha
gulp.task('copy-alpha', function () {
	return gulp.src(variables.build.dir + '{/**,/**/.*}')
		.pipe(gulp.dest(variables.development.alpha))
	;
});

//-- Copy to production directory
gulp.task('copy-prod', function () {
	return gulp.src(variables.build.dir + '{/**,/**/.*}')
		.pipe(gulp.dest(variables.production.dir))
	;
});