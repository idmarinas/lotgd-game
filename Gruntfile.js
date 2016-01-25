/*global module:false*/
module.exports = function (grunt) {
  "use strict";
  // Project configuration.
  grunt.initConfig({
    //Información de los paquetes que se usan en el proyecto
    project: grunt.file.readJSON('package.json'),
    //Información extra de la aplicación
    meta: {
      name: "La Leyenda de Ignis",
    },
    //Copiar archivos del proyecto 
    copy: {
      // Para desarrollo (localhost)
      toLocalhost: {
        files: [
          {
            expand: true,
            src: [//Archivos a copiar
              '**',//Todos los archivos y subdirectorios
              '!node_modules/**',//No copiar el directorio de node
              '!bower_components/**',//No copiar el directorio de bower
              '!*.json',//Ignorar archivos .json del directorio principal
              '!Gruntfile.js',//Ignorar archivo Gruntfile
              '!**/*.dist',//Ignorar archivos .dist
              '!**/*.md' // Ignorar archivos .md
            ],
            dest: '/Users/Ivan/Sites/ignis/',
            //filter : 'isFile'
          }
        ]
      },
      //Para publicar
      production: {
        files: [
          {
            expand: true,
            src: [//Archivos a copiar
              '**',//Todos los archivos y subdirectorios
              '!node_modules/**',//No copiar el directorio de node
              '!bower_components/**',//No copiar el directorio de bower
              '!*.json',//Ignorar archivos .json del directorio principal
              '!Gruntfile.js',//Ignorar archivo Gruntfile
              '!**/*.dist',//Ignorar archivos .dist
              '!**/*.md', // Ignorar archivos .md
              '!**/*.phar', // Ignorar los archivos .phar sólo se usa en el desarrollo
              '!**/*.lock' // Ignorar los archivos .lock sólo se usa en el desarrollo
            ],
            dest: '../production',
            //filter : 'isFile'
          }
        ]
      }
    }
  });
    
  // These plugins provide necessary tasks.
  grunt.loadNpmTasks('grunt-contrib-copy');
  // grunt.loadNpmTasks('grunt-contrib-jshint');

  // Default task. Procesa los archivos y luego los copia en la carpeta del servidor local
  grunt.registerTask('default', ['copy:toLocalhost']);
  
  // Copiar a la carpeta de producción para luego subirlo al servidor
  grunt.registerTask('production', ['copy:production']);
};
