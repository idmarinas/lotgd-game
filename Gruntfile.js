/*global module:false*/
module.exports = function (grunt) {
  "use strict";
  // Project configuration.
  grunt.initConfig({
    //Información de los paquetes que se usan en el proyecto
    project: grunt.file.readJSON('package.json'),
    //Información extra de la aplicación
    meta: {
      name: "La Leyenda del Dragón Verde",
    },
    //Información de Copyright de todos los paquetes usados en el proyecto
    banners: {
      //Información del Copyright del proyecto
      project: '/*! This file is part of the web "<%= meta.name %>"' +
      '\n * ' +
      '\n * @link      <%= project.homepage %>' +
      '\n * ' +
      '\n * @copyright 2015-<%= grunt.template.today("yyyy") %> (r)' +
      '\n * @author    <%= project.author %>' +
      '\n * ' +
      '\n * @version   <%= project.version %>' +
      '\n*/\n',
    },
    //Borrar los archivos previos
    clean: ['dist'],
    //Copiar archivos del proyecto (para publicar)
    copy: {
      main: {
        files: [
          {
            expand: true,
            src: [//Archivos a copiar
              '**',//Todos los archivos y subdirectorios
              '!node_modules/**',//No copiar el directorio de node
              '!bower_components/**',//No copiar el directorio de bower
              '!*.json',//Ignorar archivos .json del directorio principal
              '!**/*.less',//Ignorar archivos .less
              '!**/*.dist'//Ignorar archivos .dist
            ],
            dest: 'dist/'
          },
          {
            src: "bower_components/font-awesome/css/font-awesome.min.css",
            dest: "dist/templates/font-awesome.min.css"
          },
          {
            expand: true,
            cwd: 'bower_components/font-awesome/fonts',
            src: ["**"],
            dest: "dist/fonts/"
          }
        ]
      },
      localhost : {
        files : [
          {
            expand: true,
            cwd : 'dist',
            src : ['**'],
            dest : '/Users/Ivan/Sites/lotgd/'
          }
        ]
      }
    },
    less: {
      compileCore: {
        options: {
          strictMath: true,
          // sourceMap : true,
          outputSourceFiles: true
        },
        files: {
          "dist/templates/dragon_verde.css": "templates/dragon_verde.less"
        }
      }
    },
    postcss: {
      options: {
        processors: [
          require('autoprefixer-core')({ browsers: 'last 10 versions, > 1%' }), // add vendor prefixes
        ]
      },
      dist: {
        src: 'dist/templates/dragon_verde.css'
      }
    },
    usebanner: {
      options: {
        position: 'top',
        banner: '<%= banners.project %>'
      },
      files: {
        src: 'dist/templates/dragon_verde.css'
      }
    },
    cssmin: {
      main: {
        options: {
          keepSpecialComments: '*',
          advanced: false
        },
        files: {
          "dist/templates/dragon_verde.css": "dist/templates/dragon_verde.css"
        }
      }
    }
  });
    
  // These plugins provide necessary tasks.
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-banner');
  grunt.loadNpmTasks('grunt-postcss');
  // grunt.loadNpmTasks('grunt-contrib-uglify');
  // grunt.loadNpmTasks('grunt-contrib-jshint');

  // Default task.
  grunt.registerTask('default', ['clean', 'copy:main', 'less', 'postcss', 'usebanner', 'cssmin', 'copy:localhost']);
};
