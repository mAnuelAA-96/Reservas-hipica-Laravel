Comenzamos con la creación del sitio disponible en nginx mediante el comando: sudo nano /etc/nginx/sites-available/reservas. Este archivo lo tenemos que configurar con los datos de nuestro nuevo proyecto y el dominio que queremos usar. Una vez hecho esto, usamos el comando sudo ln -s /etc/nginx/sites-available/reservas /etc/nginx/sites-enabled/ para habilitar el uso del sitio creado el enlace en el sistema de archivos de nginx.

Una vez hecho esto, podemos ajustar el valor del archivo nginx.conf para establecerlo en un valor fijo y así evitar problemas. Como esto ya lo tenemos hecho de tareas anteriores, no es necesario hacerlo.

Con estas configuraciones hechas, pasamos al siguiente paso. Para crear el certificado https para el dominio creado, usamos el comando sudo certbot --nginx -d reservas.manuelflo.com. Ahora ya tenemos preparado nuestro servidor virtual para poder acceder por https.

Antes de reiniciar nginx, vamos a DigitalOcean y creamos un nuevo registro de tipo A con el dominio usado. En este caso es reservas.manuelflo.com. Para finalizar la configuración, reiniciamos nginx mediante el comando sudo systemctl restart nginx.

Ahora ya tenemos nuestro servidor configurado con nuestro dominio y podemos comenzar a desarrollar la aplicación.

El primer paso es crear la base de datos y el usuario utilizado para ella. Los comandos serán los siguientes:

CREATE DATABASE reservas_hipica;
CREATE USER 'reservasDB'@'localhost' IDENTIFIED BY 'R3serv4s*,';
GRANT ALL PRIVILEGES ON reservas_hipica.* TO 'reservasDB'@'localhost';
FLUSH PRIVILEGES;

Para crear la aplicación, ejecutamos el comando laravel new reservas_hipica y realizamos los pasos de configuración.

Por último, debemos cambiar el propietario de la carpeta storage y bootstrap/cache mediante los siguientes comandos ejecutados desde la carpeta de nuestro proyecto:

sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache



APLICACIÓN WEB LARAVEL

La aplicación web se compone de 4 vistas (a parte de las propias de bienvenida, login y registro) que son las siguientes: MisReservas, Add, Edit y Caballos. Estas se encuentran organizadas en las carpetas reservas y caballos dentro de resources/view. En todas ellas podremos ver una barra de navegación superior para poder cambiar fácilmente entre MisReservas, Añadir reserva y Nuestros caballos.

La vista Caballos es una vista sencilla que simplemente carga los datos del caballo con una foto guardada en el directorio public/images de nuestro proyecto. Las imágenes son nombradas según el nombre del caballo.

La vista MisReservas es la vista inical al realizar el login en la aplicación. En ella aparecerán (si existen) todas las reservas futuras realizadas por el usuario. Cada reserva se compone de la fecha y hora, los comentarios realizados y el nombre del caballo seleccionado con su foto. Además, hay dos botones para editar la reserva o borrarla. Previamente a borrarla nos aparecerá un mensaje de aviso para confirmar la acción. Al pulsar el botón editar, se nos abrirá el formulario correspondiente para editar la reserva, del cual se hablará más adelante.

En la barra de navegación tenemos un botón dedicado a añadir reservas. Esto nos llevará a la vista Add en la que nos encontramos un formulario para realizar la reserva. Podremos seleccionar la fecha mediante un calendario (con las restricciones de la tarea), la hora mediante una lista desplegable, el caballo mediante una lista desplegable (sólo aparecen los caballos que no estén enfermos) y podremos realizar un comentario. Tendremos un botón para confirmar la reserva y otro para cancelarla que nos llevarán, si están los datos correctos en caso de confirmar, a la vista de Mis Reservas. Para controlar que los datos introducidos sean correctos y no estén duplicados con otras reservas se utiliza la clase ReservasController, la cual hará de filtro para comprobar los datos y guardará la reserva en caso de que estén todos los datos correctos o enviará a la vista el error ocurrido. Esto lo hará mediante el método store de esta clase.

Una vez tengamos alguna reserva hecha podremos editarla mediante el botón antes nombrado. Tendremos una vista de formulario exactamente igual al de añadir reservas, con la excepción de que los datos de la reserva están ya establecidos en los campos correspondientes para simplemente tener que modificarlo. Mediante la clase ReservasController se procede a la validación de los datos y a actualizar la reserva mediante el método update. Este método contiene también la forma para borrar la reserva.



ROUTES

En el archivo routes/web.php debemos establecer las rutas que seguirán nuestras vistas para realizar las diferentes acciones. En este archivo debemos definir también las rutas correspondientes a la verificación por email de los usuarios.



ENVÍO DE CORREOS

Para realizar el envío de correos he utilizado postmark. Para que postmark funcione con nuestra aplicación, debemos modificar el archivo .env de la misma, añadiendo los datos correspondientes incluido un token de postmark. Una vez que hemos configurado la aplicación para poder realizar los envíos, tendremos que crear las clases encargadas de realizar el envío. En mi caso, he realizado tres clases diferentes ubicadas en el directorio app/Mail. Estas clases se usan para controlar el envío de un correo diferente en caso de añadir, editar o cancelar una reserva. Además, debemos crear las vistas que enviaremos por correo. Estas vistas se encuentran en views/email, y también tendremos tres vistas diferentes para añadir, editar o cancelar una reserva.



API

Una vez hemos ejecutado el comando php artisan install:api, pasamos a la creación de las clases necesarias para el manejo. Necesitaremos un controlador (ApiController) y un archivo de rutas para el api (api.php). Mediante el archivo api.php, definiremos las rutas que se utilizarán para ejecutar las consultas get y post, y con la clase ApiController controlaremos las funciones que se realizarán, así como enviar una respuesta al cliente.



PANEL DE ADMINISTRACIÓN

Se ha creado un panel de administración con orchid en el que se podrá consultar las reservas realizadas por todos los usuarios, así como añadir y editarlas. También se puede ver la lista de caballos y modificar cualquiera de sus campos, por ejemplo para actualizar la lista de caballos enfermos y sanos.

Hay un apartado para ver el histórico de reservas, el cual contendrá las reservas ya pasadas. Mediante la clase app/Console/Commands/GuardarHistorialReservas.php, se ha realizado una función que realiza una consulta sobre las reservas anteriores al día actual y las añade a la tabla historial borrándolas de la tabla reservas de la base de datos. Así las reservas quedan almacenadas en esta nueva tabla y la tabla reservas queda conteniendo únicamente las reservas futuras.

Para el funcionamiento de esta clase que extiende de la clase Command, se ha utilizado la clase routes/console.php, que contiene el comando a ejecutar.



CAPTURAS

- misreservas.php

![misreservas](asset('images/vistas/misreservas.jpg'))

- add.blade.php

![add](asset('images/vistas/addreserva.jpg'))

- edit.blade.php

![edit](asset('images/vistas/editreserva.jpg'))

- caballos.blade.php

![caballos](asset('images/vistas/caballos.jpg'))

- Panel de administración

![adminpanel](asset('images/vistas/adminpanel.jpg'))



