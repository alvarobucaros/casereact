<?php

class DBconexion{
	var $conect;  
	var $BaseDatos;
	var $Servidor;
	var $Usuario;
	var $Clave;
        var $mysqli;
        
    public function __construct($BaseDatos, $Servidor, $Usuario, $Clave){
        $this->BaseDatos = $BaseDatos;
        $this->Servidor = $Servidor;
        $this->Usuario = $Usuario;
        $this->Clave = $Clave;
    }      
        
        
        function conectar() {
      
            $mysqli = new mysqli($this->Servidor,$this->Usuario,$this->Clave, $this->BaseDatos);
            if (mysqli_connect_errno()) {
                printf("Conexión fallida: %s\n", mysqli_connect_error());
                return false;
            }  
            else { 
               // mysqli_character_set_name('utf8',$mysqli);
               mysqli_set_charset($mysqli,"utf8");
                return $mysqli;    
            }
	
	}

	function desConectar(){
             mysqli_close($this->conect);
	}
}