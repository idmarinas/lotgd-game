-   Errores
    -   s
-   Añadir
    -   s
-   Cambiar
    -   mail.php
        -   en el inbox cambiar el select para informar de cuantos mensajes tiene cada uno de los remitentes
-   Cambiar como se guarda la configuración del juego
    -   Toda la configuración se guarda en la base de datos
    -   Se hace uso de la cache para reducir las consultas a la base de datos
    -   Aplicar este concepto a los módulos también

# Actualmente haciendo esto
-   Mejorar el sistema de plantillas, para hacer la creación de temas mas sencillo
    -   Mejorar el sistema para aprobechar mejor las capacidades de Twig
-   En batalla: poner la salud de jugador y enemigos en la columna de resultados, del jugador y enemigos, arriba y abajo
    -   Mirar imagen Anotación 2020-09-24 153923
-   Revisar y eliminar uso de DB:: class, se usará Doctrine en su lugar.
-   Usar el zend-http component, para generar la respuesta del servidor (sustituir page_header y page_footer)
    -   Revisar el page_header y page_footer que queden sin sustituir,
    preparar las plantillas para las páginas (faltan por transferir algunas plantillas tanto del core como del theme)
-   Revisar las funciones de Output/Collector para reemplazar todas las funciones.
-   Twig macros, se pueden usar para sustituir bloques que se necesita pasar variables (motd item y similar)

# Para la versión 4.6.0
-   Revisar las plantillas, para seguir simplificando
    -   Crear los bloques en la misma plantilla principal, de la página
-   Helpers de Laminas view en Twig functions....
    -   Agregar el basePath helper
-   ???? Crear el nuevo sistema de módulos
    -   Un sistema compatible con el viejo

# Para la versión 4.7.0
-   Agregar un sistema de publicidad en el core (principalmente el Google Adsense)
-   Revisar plantillas y traducciones (ver si se puede mejorar la estructura de las traducciones)

# Para la versión 5.0.0
-   Un nuevo sistema de instalación, mixto: por consola o por web.
-   Uso de Laminas DB, obsoleto.
    -   Eliminar uso de DB:: class, se usará Doctrine en su lugar.
    -   En las actualizaciones (a la 4.0.0, por ejemplo) se hace uso de Laminas DB
        -   Esto se eliminara en esta versión
-   Eliminar compatibilidad con el uso del viejo sistema de módulos
-   Eliminar compatibilidad con el viejo sistema de traducción.
-   Eliminar compatibilidad con el viejo sistema de creación de formularios.

# Para la versión X.0.0
-   Esta actualización requiere de que se valla migrando ciertos paquetes de Laminas a Symfony
-   Usar un sistema parecido al de Laminas MVC para crear las páginas.
    -   Posibilidad 1 de una migración a Laminas MVC framework (ya que todo el core usa módulos de Laminas)
    -   Posibilidad 2 de una migración a Symfony Framework (Esto seria la opción más lógica en el sentido de que viene con un sitema de plantillas que permite sustituir las de un bundle por otras propias)
        -   Creo que se migrará todo el código a los módulos de Symfony Framework, para así poder aprobechar sus capacidades, que son muy útiles en el LoTGD
        -   Templates: permitir reemplazar una pantilla de un bundle con otra propia, es una forma fácil de personalizar y crear temas propios.

## Cosas pendientes
-   Modificar el sistema de logeo, para usar una clase (principalmente por el tema de la contraseña)
    -   Login
    -   Cambiar la forma en la que se códifican las contraseñas y se hace el login.
    -   Esto obligaría a los usuarios a generar una nueva contraseña.
-   Códigos de color, cambiar y unir todos los códigos de color, (color, negrita, cursiva, etc) en un mismo lugar
    -   IDEA: usar la clase BBCode, ejemplo como se usa en foros y similar
-   Navegación: clase de php ArrayObject, puede servir para agregar la funcionalidad de agregar un link en una posición concreta

### Sólo falta los módulos.
-   Adaptar los formularios a zend-form (Módulos)
    -   Los formularios usan también el sistema de traducción y es necesario actualizarlo
        -   Se ha adaptado el formulario de configuración básica del juego (settings)
        -   Se irán adaptando el resto de formularios con cada actualización
    -   En algunas partes se usa Symfony Form



## Para la versión X

-   Rehacer el sistema de combate, usando el principio del resto del juego y haciendo uso de una factoria.
    -   Hacer el que sistema de combate sea mas personalizable, se pueda extender las clases para añadir más opciones.
-   Rehacer los personajes, para que sean mas sencillos de extender, tambien para que se complemente cono el sistema de combate nuevo.
    -   Se simplifica la forma en la que se calcula las estadísticas del perosnaje, haciendo que tanto los personajes jugador como los creados por el servidor, tengan una forma de creación muy similar.
-   Habilidades y sus buffs. Usar la base de datos para guardar los buffs, y asi poder traducir ciertos campos.
    -   Estos buffs pueden servir para muchas cosas, las monturas por ejemplo.

## Puede ser complicado
-   *

## Complicado
-   Módulos, usar un sistema similar al que usa zenframework con sus módulos. pasar los módulos actuales a un sistema similar al manejado por zenframework.

* * *


## Temas

Los hook admiten incluir plantillas de forma dinamica en los archivos twig.
La forma de hacerlo es incluir en los parametros una key con el nombre del hook en formate kamelCaseTpl donde se incluya la templates a incluir. Es un array para poder incluir más.
Ejemplo:

```php
$params = modulehook('create-form', $params);

//-- Alternativa para las plantilas de los módulos
$params['createFormTpl'] = ['MyModule/template.twig'];
```

Esta plantilla hereda todas las variables de la plantilla padre.

## Menu de navegación

Permitir añadir al principio o al final de una categoria, añadir una categoría al final o al inicio
