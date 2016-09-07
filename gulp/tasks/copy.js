var gulp = require('gulp');
var variables = require('../variables');

var filesCopy = [
	//-- Todos los archivos y subdirectorios
	'**/**',
	//-- Ignorar archivos de ejecución de tareas
	'!gulp',
	'!gulp/**',
	'!gulpfile.js',
	'!Gruntfile.js',
	//-- Ignorar archivos que solo se usan en el desarrollo
	'!assets',
	'!assets/**',
	'!dist',
	'!dist/**',
	'!node_modules',
	'!node_modules/**',
	'!bower_components',
	'!bower_components/**',
	'!**/*.dist',
	'!**/*.md',
	'!**/*.lock',
	'!*.json',
	//-- Ignorar archivos de instalación
	'!lib/installer',
	'!lib/installer/**',
	'!installer.php',
	'!INSTALL.TXT',
	//-- Otros archivos
	'!CHANGELOG.txt',
	'!QUICKINSTALL.TXT',
	'!README_FIRST.txt',
	'!README.txt'
];

//-- Construir la aplicación
gulp.task('build', function () {
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

//-- Copiar los archivos a la carpeta de producción
gulp.task('prod-copy', function () {
	return gulp.src(variables.build_dir + '/**')
		.pipe(gulp.dest(variables.production_dir))
	;
});