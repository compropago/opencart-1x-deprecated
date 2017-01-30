# ComproPago Plugin Opencary 1.5.x

## Descripción
Este módulo provee el servicio de ComproPago para poder generar órdenes de pago dentro de la plataforma de e-commerce 
Magento. Con ComproPago puede recibir pagos en OXXO, 7Eleven más tiendas en todo México.

[Registrarse en ComproPago ] (https://compropago.com)


## Ayuda y Soporte de ComproPago
- [Centro de ayuda y soporte](https://compropago.com/ayuda-y-soporte)
- [Solicitar integración](https://compropago.com/integracion)
- [Guía para comenzar a usar ComproPago](https://compropago.com/ayuda-y-soporte/como-comenzar-a-usar-compropago)
- [Información de contacto](https://compropago.com/contacto)

## Requerimientos
* [PHP >= 5.5](http://www.php.net/)
* [PHP JSON extension](http://php.net/manual/en/book.json.php)
* [PHP cURL extension](http://php.net/manual/en/book.curl.php)

## Instalación:
1. Copiar los directorios **admin** y **catalog** en el mismo orden, en directorio raiz de OpenCart. Asegurate de mantener la 
   estructura en los directorios.

2. Copiar la carpeta vendor dentro de la raiz de opencart al nivel de las carpetas **admin** y **catalog** junto los archivos 
   composer.json y composer.lock al mismo nivel.

3. Ingresar en el panel de admistración a **Extensions > Payments** y dar click en el boton install de **Compropago Payment Method**.


## Configurar ComproPago

1. Para iniciar la configuración ir a **Extensions > Payments**. Dar click en el boton editar de **Edit Compropago**.

2. Dentro de la pestaña **Plugin Configurations** cambiar **Status** a 'Enabled', ingresar las **Claves Publica y Privada** ( Si no conoce sus claves puede verificarlas dentro del panel de administracion de su cuenta en Compropago [https://compropago.com/panel/configuracion](https://compropago.com/panel/configuracion) ), Seleccionar el modo correspondiente a Pruebas o activo. El campo **Sort Order** indicara el lugar en el cual se mostrara Compropago como metodo de pago al realizar una compra, si desa que Compropago sea su metodo de pago por defecto indique **Sort Order** = 1.

3. Dentro de la pestaña **Display Configurations** puede indicar la manera en la cual se mostrara la seleccion de proveedores para realización del pago.
Para mostrar u ocultar los logos de proveedores modifique el campo **Show Logo**, puede tambien agragar una pequeña descripción del apartado con el campo **Description Service**, y por ultimo puede tambien agregar las instrucciones que desee pertinentes para la selección del proveedor en el apartado **Instructions**

4. Dentro de la pestaña **Estatus Configurations** establecer **New Order status** = Processing y **Approve Order Status** = Processed.


## ¿Cómo trabaja el módulo?
Una vez que el cliente sabe que comprar y continúa con el proceso, seleccionará la opción de elegir el método de pago.
Aquí aparecerá la opción de pago con ComproPago, selecciona el establecimiento de su conveniencia y el botón de **continuar**.

Al completar el proceso de compra dentro de la tienda, el sistema proporcionará un recibo de pago,
por lo que solo resta realizar el pago en el establecimiento que seleccionó anteriormente.

Una vez que el cliente generó su órden de pago, dentro del panel de control de ComproPago la orden se muestra como
"PENDIENTE". Sólo resta que el cliente realice el depósito a la brevedad posible.


---

## Configuración del plugin

1. Dirígete a **System -> Configuration -> Sales -> Payment Methods** y selecciona
   **ComproPago**.

   ***Nota:*** La opción de **Habilitar Logos** es para mostrar las imágenes de los establecimientos con
   los que procesamos pagos

2. Agrega la **llave privada** y **llave pública** que se encuentran en el apartado de configuración dentro del
   [panel de control de ComproPago][Compropago-Panel].

   Asegúrate de que las llaves coincidan con el modo activo o modo pruebas según sea el caso.

---

## Sincronización con la notificación Webhook
1. Ingresa al área de [Webhooks][Compropago-Webhooks] en el panel de ComproPago.
2. Introduce la dirección: **http://direcciondetusitio.com/index.php/compropago/webhook/**
3. Selecciona el botón "Probar" y verifica que el servidor de la tienda está respondiendo, debera aparecer un
   mensaje con la leyenda "Probando el WebHook?, Ruta correcta."

Una vez completados estos pasos el proceso de instalación ha finalizado.

## Documentación

### ComproPago Plugin Magento
**[API de ComproPago](https://compropago.com/documentacion/api)**

ComproPago te ofrece un API tipo REST para integrar pagos en efectivo en tu comercio electrónico o tus aplicaciones.


**[General](https://compropago.com/documentacion)**

Información de Comisiones y Horarios, como Transferir tu dinero y la Seguridad que proporciona ComproPAgo


**[Herramientas](https://compropago.com/documentacion/boton-pago)**
* Botón de pago
* Modo de pruebas/activo
* WebHooks
* Librerías y Plugins
* Shopify