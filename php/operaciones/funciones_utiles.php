<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of funciones_utiles
 *
 * @author Jose
 */
class funciones_utiles {
    //put your code here
    static function armarStingAutores($autores){
        $salida="";
        foreach ($autores as $clave=>$valor){

          $salida = $salida. ' / ' . $valor['autor'];
        }
        $salida = substr($salida,2);
        return $salida;
    }
   static function armarStingPalabras($palabras)
    {
        $salida="";
        foreach ($palabras as $clave=>$valor){

            $salida = $salida. ' / ' . $valor['palabra_clave'];
        }
        $salida = substr($salida,2);
        return $salida;
    }
}
