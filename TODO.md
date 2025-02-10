# A tener en cuenta ...

- **5.0.0** El antiguo sistema de módulos está obsoleto
	- Los módulos antiguos seguirán funcionando pero están obsoletos.
	- El nuevo sistema tipo Bundle reemplaza al sistema de módulos antiguos.
	- Se empezará a usar un sistema de módulos tipo Bundle
- **7.0.0** Esta es la última versión compatible con el antiguo sistema de módulos.
	- Se ha cambiado de Fomantic UI a TailwindCSS
- **7.1.0** La versión mínima y máxima de PHP es `7.4`

# Actualmente haciendo esto (7.2.0)

- Se revisa el código para hacer ir limpiando cosas que se van a quitar en la versión 8.0.0
- Limpiando el core, y mejorando el código, para migrar a php 8.0
- Hacer que los modals de Stimulus, se carguen desde un único modal, se puede usar la etiqueta <dialog> de html

# Para la versión 8.0.0

- **BC** esta versión será una Symfony App
- Agregar opción para poder instalar LoTGD en Docker
- El panel de administración del juego estará creado con EasyAdminBundle
- Se elimina https://github.com/Sylius/SyliusThemeBundle y se hace opcional
- Se elimina paquete `laminas/laminas-serializer`
- **BC** se elimina la compatibilidad con el antiguo sistema de módulos.
- Se han migrado todos los cronjob a comandos de consola.
- Revisar plantillas y traducciones (ver si se puede mejorar la estructura de las traducciones)
	- Usar macros y blocks donde se pueda.
- WebpackEncore (Se tiene que revisar como sería con Tailwind)
	- Sustituir WebpackEncore por AssetMapper (¿?)
	- Organizar mejor los archivos js/css
		- El tema se crea en una configuración nueva para personalizar
		- El js se crea en una entry común para todo (app por ejemplo), ya que puede dar problemas
			- webpack.encore.entry.js
			- webpack.encore.theme.js
- **Correos** permitir usar una plantilla para así personalizar los mensajes
	- Se usará `Symfony\Bridge\Twig\Mime\TemplatedEmail` para todos los correos del core.
	- Habrá dos versiones de cada correo predeterminado version html y txt
	- Desde la configuración se podrá decidir si se envían correos en html o txt
	- Agregar opción para que el usuario pueda elegir.
  ```php
      use Symfony\Bridge\Twig\Mime\TemplatedEmail;
  
      $email = (new TemplatedEmail())
      // ...
          // html mail
          ->htmlTemplate('signup.html.twig')
          //-- or only text mail
          ->textTemplate('signup.txt.twig')
      // ...
      ;
  ```
- Se simplifica el sistema de instalación, se elimina el instalador actual
	- Se va a pasar a un sistema de migraciones de "Doctrine migrations"
	- Esto permite que tanto si se crea una versión limpia como si actualizas desde una versión anterior se actualice
	  la base de datos acorde a eso.
	- La instalación via web, ya no será posible, y no se planea ponerla.
- Motd, permitir la traducción, y que las encuestas tengan una configuración fuera de un campo serializado.
	- Poner las opciones de la encuesta en una tabla separada. Permitiendo que las opciones también se puedan traducir.
- Agregar sistema al core, para poder añadir términos y condiciones y política de privacidad, sin necesidad de módulo.
-

# Cosas a mejorar

- Bundles
	- ¿? `Special Events`: usando el componente even-dispatcher crear uno para los eventos especiales:
		- Ya creado, falta mejorarlo.
		- El sistema se llama `Occurrence`, en referencia a que ha sucedido algo.
			- Este sistema no me convence, por lo que en la versión 8.0.0 planeo hacer otra cosa:
				- Crear bundles de cada zona:
					- `village`, `forest`, `shop`...
					- Con estos bundles se podría usar el sistema de event dispatcher de symfony para activar estos
					  tipos de eventos especiales.
						- Por ejemplo con el evento `request` antes de procesar toda la petición.
		- Agregar opción para priorizar eventos.
			- Agregar la prioridad a los eventos esto es útil para los eventos que no tienen menú de opciones y los que
			  sí
				- Se permitiría pasar antes los eventos que no tienen menús para así no entrar en conflicto con los que
				  tienen menú
		- Se agregará unas constantes con diferentes prioridades:
			- Se establecen en orden de prioridad
				- `priority_info` Para los eventos que no necesitan una respuesta y no son interactivos, solo son
				  informativos de que ha pasado algo.
					- Este evento permite que se ejecute los otros dos.
				- `priority_interactive` Para eventos que no necesitan respuesta, pero son interactivos, por ejemplo, se
				  puede comentar (ejemplo es la pradera donde puede comer la montura y se puede escribir un comentario,
				  pero no hace falta una respuesta)
					- Este evento impide que se ejecute otro evento con la misma prioridad, pero puede ejecutarse un
					  evento de la prioridad anterior.
				- `priority_needs_response` Para eventos que necesitan una respuesta por parte del usuario (tienen un
				  menú de navegación)
					- Este tipo de evento impide que se ejecuten los otros dos eventos.
		- `En estudio`
			- Convertir en un bundle que gestione todo el evento.
				- La idea es encapsular el evento dentro de un bundle que controle toda la lógica de dicho evento.
				- De esta forma se puede controlar todo el evento y volver a la página que lanzó el evento más
				  fácilmente.
				- Se puede usar la sesión para pasar datos de una petición a otra (request)
					- De esta forma se omite usar la query param

# Futuras versiones

## **BC** Para la versión X.0.0

- lotgd_core_paypal_currency para poner la moneda que se usa en el servidor para las donaciones por paypal (como en
  bundle core)
- `src/core/Controller/CreateController.php`
	- Recrearlo para usar el Symfony form.
	- Permitir la personalización de los datos con el dispatcher.
- Crear un service para el newday runonce (generar un nuevo día)
- Sustituir la función lotgd_mail por Symfony mailer
	- **lib/lotgd_mail.php** Function `lotgd_mail` is deprecated and removed in future versions.
		- Use `Symfony mailer` instead.
- `dragonpoints` para los puntos de dragón asignados actualmente es un array serializado
	- Se registra los valores que se han aumentado al personaje mediante las iniciales del atributo.
	  ```php
		  $points = [
			  'str',
			  'con',
			  'int',
			  'ff',
			  'dex',
			  'str',
		  ];
	  ```
	- Pasarlo a una tabla independiente para registrar los valores que se aumentan del personaje.
	  ```
		  Posible estructura de la tabla
		  'attribute'  El atributo que se esta mejorando del personaje. Ejem: `strength`, siempre con el nombre que aparece en la tabla.
		  'value' Valor es la cantidad que se añade de la mejora, puede ser positivo o negativo 
		  'createAt' Fecha en la que se añadio esta mejora
	  ```
- Crear bundle:
	- `Settings` para poder usarlo en sustitución de las `prefs` para usuario
	- `Mail`
		- Continuar con el que ya tengo iniciado `https://github.com/idmarinas/MessageBundle`
		- En el inbox cambiar el select para informar de cuantos mensajes tiene cada uno de los remitentes
	- `Energy` un bundle que permite determinar el tipo de sistema que se usa para las acciones turnos/stamina
		- Se puede elegir el mínimo de energía y el máximo que puede tener el personaje
		- Se puede hacer que depende de algún atributo. (que tenga bono)
		- Tendrá funciones para poder aumentar y disminuir la energía.

## Para la versión X.Y.Z

- **BC** Hacer el que sistema de combate sea más personalizable, se pueda extender las clases para añadir más opciones.
- **BC** Rehacer los personajes, para que sean más sencillos de extender, también para que se complemente como el
  sistema de combate nuevo.
	- Se simplifica la forma en la que se calcula las estadísticas del personaje, haciendo que tanto los personajes
	  jugador como los creados por el servidor, tengan una forma de creación muy similar.
- **BC** Habilidades y sus buffs. Usar la base de datos para guardar los buffs, y asi poder traducir ciertos campos.
	- Estos buffs pueden servir para muchas cosas, las monturas por ejemplo.
- Crear el bundle de energía, que permita poner energía o un sistema por turnos.

## Cosas pendientes

- Añadir un check para comprobar si se han usado las funciones obligatorias (copyright(), game_version() ... )
	- Compatible con el sistema simple (los tipo Google AdSense)
- Códigos de color, cambiar y unir todos los códigos de color, (color, negrita, cursiva, etc.) en un mismo lugar
	- IDEA: usar la clase BBCode, ejemplo como se usa en foros y similar
- ¿? Permitir que en los eventos, el chance pueda ser superior a 100, para priorizar que un evento pueda pasar con más
  frecuencia.
	- Usar otra forma
