<?php 

require_once ('config.php');

class Database 
{
     private static $connection = null;
     private static $statement = null;
     private static $error = null;
 
     /*
      *   metodo para ejecutar sentencias SQL de tipo INSERT, UPDATE y DELETE.
      */
     public static function executeRow($query, $values)
     {
         try {
             // Se crea la conexión mediante la clase PDO con el controlador para MariaDB.
             self::$connection = new PDO('mysql:host=' . SERVER . ';dbname=' . DATABASE, USERNAME, PASSWORD);
             // Se prepara la sentencia SQL.
             self::$statement = self::$connection->prepare($query);
             // Se ejecuta la sentencia preparada y se retorna el resultado.
             return self::$statement->execute($values);
         } catch (PDOException $error) {
             // Se obtiene el código y el mensaje de la excepción para establecer un error personalizado.
             self::setException($error->getCode(), $error->getMessage());
             return false;
         }
     }


 
     /*
        *   Método para obtener el último ID de un registro insertado en la base de datos. esto se ocupa cuando se inserta
        *   un registro en la base de datos y se necesita obtener el ID del registro insertado. para insertar otro registro 
        *   en otra tabla que dependa del ID del registro insertado anteriormente.
      */

     public static function getLastRow($query, $values)
     {
         if (self::executeRow($query, $values)) {
             $id = self::$connection->lastInsertId();
         } else {
             $id = 0;
         }
         return $id;
     }


 
     /*
      *  Método para obtener un registro de una sentencia SQL tipo SELECT. para que me devuelva los registros
      */
     public static function getRow($query, $values = null)
     {
         if (self::executeRow($query, $values)) {
             return self::$statement->fetch(PDO::FETCH_ASSOC);
         } else {
             return false;
         }
     }
 
     /*
      *   Método para obtener varios registros de una sentencia SQL tipo SELECT.
      */
     public static function getRows($query, $values = null)
     {
         if (self::executeRow($query, $values)) {
             return self::$statement->fetchAll(PDO::FETCH_ASSOC);
         } else {
             return false;
         }
     }
 
     /*
      * Metodo para obtener el codigo de error de sql y dependiendo a eso se asigna un mensaje personalizado
      * puedo ocuparlo para la parte de la base de datos ocupando sqlMessage
      */
     private static function setException($code, $message)
     {

        //modo desarrollador 
        $activado_modo_dev = 1;

         // Se asigna el mensaje del error original por si se necesita.
         self::$error = $message . PHP_EOL;
         // Se compara el código del error para establecer un error personalizado.
         if ($activado_modo_dev == 0){
            switch ($code) {
                case '2002':
                    self::$error = 'Servidor desconocido';
                    break;
                case '1049':
                    self::$error = 'Base de datos desconocida';
                    break;
                case '1045':
                    self::$error = 'Acceso denegado';
                    break;
                case '42S02':
                    self::$error = 'Tabla no encontrada';
                    break;
                case '42S22':
                    self::$error = 'Columna no encontrada';
                    break;
                case '23000':
                    self::$error = 'Violación de restricción de integridad';
                    break;
                default:
                    self::$error = 'Ocurrió un problema en la base de datos';
            }
         }else {
            switch ($code) {
                case '2002':
                    self::$error = $message;
                    break;
                case '1049':
                    self::$error = $message;
                    break;
                case '1045':
                    self::$error =  $message;
                    break;
                case '42S02':
                    self::$error = $message;
                    break;
                case '42S22':
                    self::$error =  $message;
                    break;
                case '23000':
                    self::$error =  $message;
                    break;
                default:
                    self::$error =  $message;
            }
         }
         
     }
 
     /*
      *   Método para obtener un error personalizado cuando ocurre una excepción.
      *   Parámetros: ninguno.
      *   Retorno: error personalizado.
      */
     public static function getException()
     {
         return self::$error;
     }
}

?>