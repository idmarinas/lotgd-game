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

//-- Construir la aplicación
gulp.task('copy', function () {
	return gulp.src(filesCopy)
		.pipe(gulp.dest(variables.build_dir))
	;
});

//-- Copiar los archivos a la carpeta de desarrollo
gulp.task('dev-copy', function () {
	return gulp.src(variables.build_dir + '/**')
		.pipe(gulp.dest(variables.development_dir))
	;
});

//-- Copiar los archivos a la carpeta de desarrollo
gulp.task('test-copy-dev', function () {
	return gulp.src(variables.build_dir + '/**')
		.pipe(gulp.dest(variables.development_test_dir))
	;
});

//-- Copiar los archivos a la carpeta de producción
gulp.task('prod-copy', function () {
	return gulp.src(variables.build_dir + '/**')
		.pipe(gulp.dest(variables.production_dir))
	;
});