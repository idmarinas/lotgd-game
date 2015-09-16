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
              '!Gruntfile.js',//Ignorar archivo Gruntfile
              '!**/*.less',//Ignorar archivos .less
              '!**/*.dist'//Ignorar archivos .dist
            ],
            dest: 'dist/',
            filter : 'isFile'
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
          },
          {
            src: "bower_components/animate.css/animate.min.css",
            dest: "dist/templates/animate.min.css"
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
      },
      
      //Para copiar los módulos propios
      modules : {//Al local
        expand: true,
        cwd : '/Users/Ivan/Documents/Proyectos Web/Dragón Verde/Módulos/Terminados/',
        src: [//Archivos a copiar
          '**'//Todos los archivos y subdirectorios
        ],
        dest: '/Users/Ivan/Sites/lotgd/modules/',
        filter: 'isFile'
      },
      modulesDist : {//A la carpeta Dist
        expand: true,
        cwd : '/Users/Ivan/Documents/Proyectos Web/Dragón Verde/Módulos/Terminados/',
        src: [//Archivos a copiar
          '**'//Todos los archivos y subdirectorios
        ],
        dest: 'dist/modules/',
        filter: 'isFile'
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
      },
      uikit: {
        options: {
          strictMath: true,
          // sourceMap : true,
          outputSourceFiles: true
        },
        files: {
          "dist/templates/uikit.css": "templates/uikit.less"
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
      },
      uikit: {
        options: {
          keepSpecialComments: '*',
          advanced: false
        },
        files: {
          "dist/templates/uikit.css": "dist/templates/uikit.css"
        }
      }
    },
    uglify: {
      main: {
        files: {
          'dist/templates/dragon.min.js': 
            [
              'bower_components/uikit/js/uikit.js',
              'bower_components/uikit/js/components/tooltip.js',
              'bower_components/uikit/js/components/notify.js'
            ]
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
  grunt.loadNpmTasks('grunt-contrib-uglify');
  // grunt.loadNpmTasks('grunt-contrib-jshint');

  // Default task. Procesa los archivos y luego los copia en la carpeta del servidor local
  grunt.registerTask('default', ['clean', 'copy:main', 'less', 'postcss', 'usebanner', 'cssmin', 'uglify', 'copy:localhost']);
  
  //Copia archivos para luego publicarlo se incluyen los módulos propios
  grunt.registerTask('production', ['clean', 'copy:main', 'less', 'postcss', 'usebanner', 'cssmin', 'uglify', 'copy:modulesDist']);
  
  //Copia sólo los módulos propios al servidor local
  grunt.registerTask('modules', ['copy:modules']);
};
