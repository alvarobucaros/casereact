<?php
include_once("clsConection.php");
$objClase=new DBconexion;
date_default_timezone_set("America/Bogota");

if(isset($_POST["accion"])){
   $accion = $_POST["accion"];
   $condicion = $_POST["condicion"];
   
    if ($accion=='ddlBasesDatos'){
        $sql = "SELECT distinct table_schema FROM information_schema.TABLES ".
               "WHERE table_schema NOT LIKE 'information_%' AND table_schema NOT LIKE 'performance_%' ";
       $obj = new DBconexion();
       $con = $obj->conectar();
        $respuesta='<select name="selDB" id="selDB" onChange="cambiaDB()"><option value="-1">Seleccione DB</option>';
        
        $result = mysqli_query($con, $sql);
        while( $reg = mysqli_fetch_array($result, MYSQLI_NUM) ){
            $cod=$reg[0]; 
            $des=$reg[0]; 
                $respuesta .= "<option value='".$cod."'>$des</option>";
        }
        $respuesta .= '</select>';
        echo $respuesta;
        return $respuesta;
    }
 
   
    if ($accion=='ddlTablas'){
       $data = explode('||', $condicion);
       $sql = "SELECT distinct table_name  FROM information_schema.columns WHERE table_schema = '".
               $data[0] . "' order by table_name;";
       $obj = new DBconexion();
       $con = $obj->conectar();
        $respuesta='<select name="selTabla" id="selTabla" onChange="cambiaTabla()"><option value="-1">Seleccione Tabla</option>';
        
        $result = mysqli_query($con, $sql);
        while( $reg = mysqli_fetch_array($result, MYSQLI_NUM) ){
            $cod=$reg[0]; 
            $des=$reg[0]; 
                $respuesta .= "<option value='".$cod."'>$des</option>";
        }
        $respuesta .= '</select>';
        echo $respuesta;
        return $respuesta;      
    }
    
    if ($accion=='grillaTabla'){
       $data = explode('||', $condicion);
       $leng=$data[2];
       include_once '../idioma'.$leng.'.php';    
       $sql = "SELECT column_name,  column_type, column_comment  ".
               "FROM information_schema.columns WHERE table_schema = '".
               $data[0] . "' AND table_name = '" . $data[1] . "' ORDER BY ordinal_position; ";
       $obj = new DBconexion();
       $con = $obj->conectar();
     
        $respuesta='<table id="tabla" class="tablex" border="1"><tr><th>Columna</th><th>tipo</th><th>Nombre</th>'
                . '<th>Index</th><th>Orde n</th><th>Tipo Text</th><th>Valida</th><th>Radio</th><th>Check</th>'.
                '<th>Tabla lista</th><th>CodDetalle</th></tr>';
        $i=0;
        $result = mysqli_query($con, $sql);
        while( $reg = mysqli_fetch_array($result, MYSQLI_ASSOC) ){
            $i+=1;
            if ($reg['column_comment']==''){
                $comen = $reg['column_name'];}
            else
            {
                $comen = $reg['column_comment'];
            }
            $tpTx= $reg['column_type'];
            $tx='N';
            if (strtoupper($tpTx)=='DATE' || strtoupper($tpTx)=='DATETIME'){$tx='D';}
            if (substr(strtoupper($tpTx),0,4)=='CHAR' || substr(strtoupper($tpTx),0,4)=='VARC'){$tx='C';}
            $respuesta .= "<tr>".
            "<td><input class='tip' type='text' size='20' id='co".$i."' value='". $reg['column_name'] ."' /></td>".                    
            "<td><input class='tip' type='text' size='10' id='ti".$i."' value='". $reg['column_type'] ."' /></td>".
            "<td><input class='tip' type='text' size='20' id='no".$i."' value='". $comen ."' /></td>".
            "<td><input type='checkbox' size='5' class='td' name='in".$i."'></input></td>".
            "<td><input type='checkbox' size='5' class='td' name='or".$i."'></input></td>".
            "<td><input class='tip' type='text' size='8' id='tx".$i."' name='tx".$i."' value='". $tx ."' ></input></td>".
            "<td><input type='checkbox' size='5' class='td' name='va".$i."' checked></input></td>".
            "<td><input class='tip' type='text' size='2' maxlength='2' id='Rad".$i."' value='' /></td>".
            "<td><input class='tip' type='text' size='10' maxlength='30' id='Chk".$i."' value='' /></td>".
            "<td><input class='tip' type='text' size='10' maxlength='30' id='Tlis".$i."' value='' /></td>".
            "<td><input class='tip' type='text' size='10' maxlength='30' id='Flis".$i."' value='' /></td></tr>";         
        }
        $respuesta .= '</table>';
        echo $respuesta;
        return $respuesta; 

    } 
    
 /*  
  *     crea los objetos
  */ 
 //0.columna|1.tipo|2.nombre|3.radio|4.indice|5.orden|6.valida|7.check|8.sele file|9.sele data   
    if ($accion=='creaObjetos'){
        $column=array();
        $archTextos=array();
        $fechas=array();
        $i=0;
        $j=0;    
        $data = explode('<>', $condicion); 
        foreach ($data as $valor) {
            $rec = explode('|', $valor);
            if ($i==0){
                $autor = $rec[0];
                $lsruta = $rec[1];
                $ruta = $rec[2];
                $prefijo = $rec[3];
                $indice = $rec[4];
                $orden = $rec[5];
                $tabla = $rec[6];
                $frm = $rec[7];
                $hdr = $rec[8];
                $textArea  = $rec[9];
                $leng  = $rec[10];
                $js  = $rec[11];
                $mod  = $rec[12]; 
                $view  = $rec[13];
                $indiceCmp = $indice; // str_replace($prefijo,'', $indice);
            }else{
                $j=$i-1;
                for ($k=0;$k<=10;$k++){
                    if ($k==0){
                        $column[$j][$k]= $rec[$k]; // str_replace($prefijo,'', $rec[$k]);
                    }
                    else{
                        $column[$j][$k]=$rec[$k];

                    }  
                }    
            }
        $i+=1; 
        }
    }
    $n=0;
    $f=0;
    $err='';
    for($l=0;$l < count($column);$l++) {
        if($column[$l][8]!=''){
            $file=$lsruta.$column[$l][8];
             $pos = strpos($file, ".txt");
             if ($pos ==  true){
                $exists = is_file( $file );
                if(!$exists){
                    $err .=' La tabla lista '.$file . ' no existe \n';
                } 
             }
        }
        if($column[$l][10]=='D'){
            $fechas[$f]=$column[$l][0];
            $f+=1;
        }
    }
  
    if ($err !='')
        {
        echo $err;
        return $err;
        }
//  Modelo
//
    $select = '';
    $fileLista=array();
    $fileCampos=array();
    $l=0;
    for ($x=0;$x<=$j;$x++){
        $select .= $column[$x][0] ;
        if ($x<$j){$select .= ', ';}
        if($column[$x][8]!=''){
            $fileLista[$l] = $column[$x][8];
            $fileCampos[$l] = $column[$x][9];
            $l+=1;
        }
    }

    $directorio = $ruta."modulos";

    if (!is_dir($directorio)) {
        mkdir($directorio, 0777);
    }

$hoy = date("l,M d, Y g:i:s");
$archivo = $directorio.'/mod_'.$tabla.'.php';    
$ar=fopen($archivo,"w") or die("Problemas en la creacion");

  for($n=0;$n < count($archTextos);$n++)
    {
       graba($ar,$archTextos[$n]); 
    }
//
graba($ar,"<?php");
if ($hdr=='si'){
   graba($ar,"include_once(\"clsConection.php\");");
}else
{
   graba($ar,"include_once(\"../bin/cls/clsConection.php\");");
}

$lin='';
graba($ar,"\$objClase = new DBconexion();");
graba($ar,"\$con = \$objClase->conectar();");
graba($ar,"\$data = json_decode(file_get_contents(\"php://input\")); ");
graba($ar,"\$op = mysqli_real_escape_string(\$con, \$data->op);");
graba($ar,"");
graba($ar,"switch (\$op)");
graba($ar,"{");
graba($ar,"    case 'r':");
graba($ar,"        leeRegistros(\$data);");
graba($ar,"        break;");
graba($ar,"    case 'b':");
graba($ar,"        borra(\$data);");
graba($ar,"        break;");
graba($ar,"    case 'a':");
graba($ar,"        actualiza(\$data);");
graba($ar,"        break; ");
graba($ar,"    case 'u':");
graba($ar,"        unRegistro(\$data);");
graba($ar,"        break;"    );
$u=0;
for ($x=0;$x<=$j;$x++){ 
    if ($column[$x][8]!='' && $column[$x][9]!=''){ 
        $table= $column[$x][8];
        $inom= $column[$x][9];
        $pos = strpos($table, ".txt");
        if ($pos == false) {
            graba($ar,"    case '". $u . "':");
            graba($ar,"        lista". $u . "(\$data);");
            graba($ar,"        break;"    );
            $u+=1;
        }
    }                
}

graba($ar,"}");
graba($ar,"  " ); 
graba($ar,"");
graba($ar," ");
graba($ar,"    function  leeRegistros(\$data) ");
graba($ar,"    { ");
graba($ar,"      \$objClase = new DBconexion(); ");
graba($ar,"      \$con = \$objClase->conectar(); ");
graba($ar,"       { ");
graba($ar,"            \$query = \"SELECT  " . $select . "\" ");
graba($ar,"                    . \" FROM " .$tabla . " ORDER BY " .$orden . " \";             ");
graba($ar,"            \$result = mysqli_query(\$con, \$query); ");
graba($ar,"            \$arr = array(); ");
graba($ar,"            if(mysqli_num_rows(\$result) != 0)  ");
graba($ar,"                { ");
graba($ar,"                    while(\$row = mysqli_fetch_assoc(\$result)) { ");
graba($ar,"                        \$arr[] = \$row; ");
graba($ar,"                    } ");
graba($ar,"                } ");
graba($ar,"            echo \$json_info = json_encode(\$arr); ");
graba($ar,"       } ");
graba($ar,"    } ");
graba($ar," " );
graba($ar,"    function borra(\$data)" );
graba($ar,"    { ");
graba($ar,"        \$objClase = new DBconexion(); ");
graba($ar,"        \$con = \$objClase->conectar(); ");
graba($ar,"        \$" . $indice . " = 0; ");
graba($ar,"        \$query = \"DELETE FROM " . $tabla . " WHERE " . $indice . "=\$data->" . $indice . "\"; ");
graba($ar,"        mysqli_query(\$con, \$query); ");
graba($ar,"        echo 'Ok'; ");
graba($ar,"    }" );
graba($ar," ");
graba($ar,"    function actualiza(\$data)" );
graba($ar,"    {     ");
graba($ar,"        \$objClase = new DBconexion(); ");
graba($ar,"        \$con = \$objClase->conectar(); ");
graba($ar,"        \$op =  \$data->op;	 ");
        $insert = '';
        $values = '';
        $update = '';
       
        for ($x=0;$x<=$j;$x++){
            graba($ar,"        \$" . $column[$x][0] . " =  \$data->" .$column[$x][0] . "; ");
            If($column[$x][0] != $indiceCmp){
                $insert .= $column[$x][0];
                $values .=   "\$" . $column[$x][0] ;
                $update .=  $column[$x][0] . " = '\"." . "\$" .  $column[$x][0] . ".\"'";
                 if ($x<$j){$insert .= ', '; $values .= ".\"', '\"." ;$update .= ', ';}
            }
         
        }
        $values .= ".\"')\"; ";  
graba($ar,"   ");
graba($ar,"        if(\$" . $indice ."  == 0) ");
graba($ar,"        { ");
graba($ar,"           \$query = \"INSERT INTO " . $tabla . "(" . $insert . ")\";"); 
graba($ar,"           \$query .= \"  VALUES ('\" . $values ");
graba($ar,"            mysqli_query(\$con, \$query);" );
graba($ar,"            echo 'Ok';");
graba($ar,"        } ");
graba($ar,"        else ");
graba($ar,"        { ");
graba($ar,"            \$query = \"UPDATE " .$tabla ."  SET " . $update . " WHERE " .$indice. " = \".$" .$indice.";");
graba($ar,"            mysqli_query(\$con, \$query); ");
graba($ar,"            echo 'Ok';");
graba($ar,"        } ");
graba($ar," ");
graba($ar,"    } ");
graba($ar," ");
graba($ar,"    function unRegistro(\$data) ");
graba($ar,"    { ");
graba($ar,"        \$objClase = new DBconexion(); ");
graba($ar,"        \$con = \$objClase->conectar();	 ");	
graba($ar,"        \$". $indice . " = \$data->". $indice . ";      " );  
graba($ar,"        \$query = \"SELECT  " . $select . "  \" . ");
graba($ar,"                    \" FROM " . $tabla . "  WHERE " . $indice  . " = \" . \$" . $indice . "  . ");
graba($ar,"                    \" ORDER BY " . $orden . " \"; ");
graba($ar,"        \$result = mysqli_query(\$con, \$query); ");
graba($ar,"        \$arr = array(); ");
graba($ar,"        if(mysqli_num_rows(\$result) != 0)  ");
graba($ar,"        { ");
graba($ar,"            while(\$row = mysqli_fetch_assoc(\$result)) { ");
graba($ar,"                \$arr[] = \$row;");
graba($ar,"           } ");
graba($ar,"        } ");
graba($ar,"        echo \$json_info = json_encode(\$arr); ");
graba($ar," ");
graba($ar,"    } ");
graba($ar," ");
graba($ar,"	 ");
//graba($ar,"}");
$numero = sizeof($fileLista);
if ($numero>0){
    for($i=0;$i<count($fileLista);$i++) {
        $table = $fileLista[$i];
        $campos = $fileCampos[$i];
        $cmpos = explode(',', $campos); 
        $pos = strpos($table, ".txt");
        if ($pos == false) {
            graba($ar,"    function lista".$i."() ");
            graba($ar,"    { ");
            graba($ar,"        \$objClase = new DBconexion(); ");
            graba($ar,"        \$con = \$objClase->conectar();	 ");
            graba($ar,"         \$query = \"SELECT ". $cmpos[0]. ", ". $cmpos[1] . " FROM " . $table . " ORDER BY ". $cmpos[1]. "\";" ); 
            graba($ar,"         \$result = mysqli_query(\$con, \$query); "); 
            graba($ar,"         \$arr = array(); "); 
            graba($ar,"         if(mysqli_num_rows(\$result) != 0)");
            graba($ar,"         { "); 
            graba($ar,"             while(\$row = mysqli_fetch_assoc(\$result)) {"); 
            graba($ar,"                 \$arr[] = \$row;"); 
            graba($ar,"              }"); 
            graba($ar,"         } ");
            graba($ar,"      echo \$json_info = json_encode(\$arr); ");
            graba($ar,"    } ");
            graba($ar," ");            
        }
    }graba($ar," "); 
}
    
}




$alvaro = $autor. "   " . $hoy ."  ";
fputs($ar,"// >>>>>>>   Creado por: ".$alvaro." <<<<<<< ");
fputs($ar,"\n");
fclose($ar);
//
//  Controladora
//
$directorio = $ruta."controller";

if (!is_dir($directorio)) {
    mkdir($directorio, 0777);
}
$archivo = $directorio.'/'.$tabla.'.ctrl.js';    
$ar=fopen($archivo,"w") or die("Problemas en la creacion");   
 $puntos='';        
if ($hdr=='si'){
    $puntos='../';
}
graba($ar,"var app = angular.module('app', []);");
graba($ar,"app.controller('mainController',['\$scope','\$http', function(\$scope,\$http){");
graba($ar,"    \$scope.form_title = 'Lista de ". $tabla ."';");
graba($ar,"    \$scope.form_btnNuevo = 'Nuevo registro';");
graba($ar,"    \$scope.form_btnEdita = 'Edita';");
graba($ar,"    \$scope.form_btnElimina = 'Elimina';");
graba($ar,"    \$scope.form_btnAnula = 'Cerrar';");
graba($ar,"    \$scope.form_btnActualiza = 'Actualizar';");
graba($ar,"    \$scope.form_titModal = 'Actualiza lista de registros';");
graba($ar,"    \$scope.form_Phbusca = 'Consulta';");
graba($ar," ");

for ($x=0;$x<=$j;$x++){
    if($column[$x][3]>0){
    $m=$column[$x][3];
        for ($i=0;$i<$m;$i++){
            graba($ar,"    \$scope.form_Activo".$x.$i. " = ".$i.";");               
        }
    }

    if($column[$x][7]>0){
        $m=$column[$x][7];
        for ($i=0;$i<$m;$i++){
            graba($ar,"    \$scope.form_Activo".$x.$i. " = 'Opcion ".$x.$i."';");               
        }
    } 
}

$numero = sizeof($fileLista);

if ($numero>0){
     for ($x=0;$x<$numero;$x++){
         $tab = $fileLista[$x];
         $xx = strpos($tab, ".txt");
    
         if($xx > 0){
            $tmp= substr($tab, 0, $xx);
            $graba="    \$scope.".$tmp." = {model: null,"; 
            graba($ar,$graba);
            $graba="    availableOptions: ["; 
            graba($ar,$graba);
            
            $tab = $lsruta.$tab;
 
            $fp = fopen($tab, "r"); 
            if ($fp){
            $leer=0;
            while(!feof($fp)) {
                $linea = fgets($fp);
                $rec = explode(',',$linea);
                if ($leer == 0){
                    $cod = strtolower($rec[0]);
                    $det = strtolower($rec[1]);
                }
               else{ 
                   $dosPuntos=":'";
                   $coma=",";
                   if(feof($fp)){ $coma="]}";}
                   $graba = "{".$cod.$dosPuntos.$rec[0]."',".$det .$dosPuntos.trim($rec[1])."'}".$coma;   
                   graba($ar,$graba);
               }
                 $leer +=1;
            }
            fclose($fp);                        
         }
 else {
      graba($ar,'// error no abre el archivo : '.$tab);
 }
         }
     }
}

graba($ar,"");

    for ($x=0;$x<=$j;$x++){
        $colAux=str_replace($prefijo,'', $column[$x][2]);
        graba($ar,"    \$scope.form_" . $column[$x][0] . " = '" . $colAux . "';");
    }
graba($ar,"");        
    for ($x=0;$x<=$j;$x++){
        $colAux=str_replace($prefijo,'', $column[$x][2]);
        graba($ar,"    \$scope.form_Ph" . $column[$x][0] . " = 'Digite " . strtolower($colAux) . "';");
    }

graba($ar,"   ");

graba($ar,"    ");
graba($ar,"    var defaultForm= {");
    $regis="";
    $porDefecto='';
    for ($x=0;$x<=$j;$x++){
        $val = "''";
        $coma = "";
        $regis.= "'" .$column[$x][0] ."':".$column[$x][0]; 
        if ($x<$j){$coma = ",";  $regis.= ", ";}
        if (substr($column[$x][1],0,3)=='int'){$val=0;} 
        graba($ar,"        " . $column[$x][0] . ":".$val.$coma);         
    }
$porDefecto=$regis;
graba($ar,"   };");
graba($ar,"    ");
if (sizeof($fileLista) > 0){
    graba($ar,"    getCombos();");
}
graba($ar,"    ");
graba($ar,"    getInfo();");
graba($ar,"    ");
if($frm=='fr'){
    graba($ar," $('#idForm').slideToggle();");
    graba($ar,"");
    graba($ar,"    function getInfo(){");
    graba($ar,"        \$http.post('".$puntos."modulos/mod_". $tabla . ".php?op=r',{'op':'r'}).success(function(data){");
 //   graba($ar,"    \$scope.registro.empresa_nombre = data[0].empresa_nombre; ");

    $regis="";  
    for ($x=0;$x<=$j;$x++){
        $val = "''";
        $coma = "";
        $regis = "\$scope.registro." .$column[$x][0] ." = data[0].".$column[$x][0]."; "; 
        graba($ar,"        " . $regis );         
    }   
    graba($ar,"        });   ");
    graba($ar,"    }");
    graba($ar,"");        
}
else {
    graba($ar,"    function getInfo(){");
    graba($ar,"        \$http.post('".$puntos."modulos/mod_". $tabla . ".php?op=r',{'op':'r'}).success(function(data){");
    graba($ar,"        \$scope.details = data;");
    graba($ar,"        });       ");
    graba($ar,"    }");
    graba($ar,"");
}

graba($ar,"    function getCombos(){");
$numero = sizeof($fileLista);
if ($numero>0){
    $u=0;
    for($i=0;$i<$numero;$i++) {
        $table = $fileLista[$i];
        $pos = strpos($table, ".txt");
        if ($pos==0){
           $idSel=  substr($table, 0, $pos);
        graba($ar,"          \$http.post('".$puntos."modulos/mod_". $tabla . ".php?op=".$u."',{'op':'".$u."'}).success(function(data){");      
        graba($ar,"         \$scope.operators".$u." = data;");      
        graba($ar,"         });");
        $u+=1;
        }
    }
}
graba($ar,"} ");
graba($ar," ");

graba($ar,"\$scope.show_form = true;");
graba($ar,"// Function to add toggle behaviour to form");
graba($ar,"\$scope.formToggle =function(){");
graba($ar,"\$('#idForm').slideToggle();");
graba($ar,"//\$scope.registro = '';");
graba($ar,"\$scope.". $indice ."=0;");
graba($ar,"// \$scope.grupo_activo='A';");
graba($ar,"// \$scope.grupoactivo = true;");
graba($ar,"\$('#idForm').css('display', 'none');");
graba($ar,"");
graba($ar,"};");
graba($ar,"");
graba($ar,"\$scope.show_form = true;");
graba($ar,"// Function to add toggle behaviour to form");
graba($ar,"\$scope.formToggle =function(){");
graba($ar,"\$('#idForm').slideToggle();");
graba($ar,"        \$scope.formato.\$setPristine();");
graba($ar,"        \$scope.registro = angular.copy(defaultForm);");
graba($ar,"");
graba($ar,"};");
graba($ar,"");
graba($ar,"\$scope.registro = function(info){ alert ('inserta');};");
graba($ar,"");
graba($ar,"");
graba($ar,"    \$scope.registro =function(info){ ");
graba($ar,"            alert ('actualiza');   ");
graba($ar,"            \$http.post('".$puntos."modulos/mod_". $tabla . ".php?op=a',{'op':'a', " . $porDefecto . "}).success(function(data){");
graba($ar,"");
graba($ar,"            \$scope.show_form = true;");
graba($ar,"            alert(data);");
graba($ar,"            if (data === true) {");
graba($ar,"            getInfo();");
graba($ar,"            }");
graba($ar,"            });");
graba($ar,"     };");
graba($ar,"");
graba($ar,"    \$scope.registro = {};");
graba($ar,"    ");
graba($ar,"    \$scope.editInfo =function(info)");
graba($ar,"    {  ");
graba($ar,"        \$scope.registro =  info;  ");
graba($ar,"        \$('#idForm').slideToggle();");
graba($ar,"       // if(registro.grupo_activo=='A'){registro.grupoactivo=true;}");
graba($ar,"       // else{registro.grupoinactivo=true;}");
graba($ar,"");
graba($ar,"    };");
graba($ar,"");
graba($ar,"    \$scope.deleteInfo =function(info)");
graba($ar,"    { ");
graba($ar,"        if (confirm('Desea borrar el registro con nombre : '+info." . $orden . "+' ?')) {  ");
graba($ar,"            \$http.post('".$puntos."modulos/mod_". $tabla . ".php?op=b',{'op':'b', '" .$indice ."':info." .$indice ."}).success(function(data){");
graba($ar,"            if (data === 'Ok') {");
graba($ar,"            getInfo();");
graba($ar,"            alert ('Registro Borrado ');");
graba($ar,"            }");
graba($ar,"            });");
graba($ar,"         }");
graba($ar,"    };");
graba($ar,"");
graba($ar,"    \$scope.updateInfo =function(info)");
graba($ar,"    {");
    $regis="";  
    for ($x=0;$x<=$j;$x++){
        $regis.= "'" .$column[$x][0] ."':info.".$column[$x][0]; 
        if ($x<$j){$regis.= ", ";}      
    }
graba($ar,"        er='';");

    for ($x=0;$x<=$j;$x++){
        if ($column[$x][6]!=''){
graba($ar,"        if(\$('#".$column[$x][0]."').val()===''){er+='falta ". strtolower($column[$x][2])."\\n';}");            
        }  
        else {
           graba($ar,$x.'  '.$column[$x][6]); 
        }
    }

graba($ar,"        if (er==''){");
graba($ar,"        \$http.post('".$puntos."modulos/mod_". $tabla . ".php?op=a',{'op':'a', ".$regis."}).success(function(data){");
graba($ar,"        if (data === 'Ok') {");
graba($ar,"            getInfo();");
graba($ar,"            alert ('Registro Actualizado ');");
graba($ar,"            \$('#idForm').slideToggle();");
graba($ar,"        }");
graba($ar,"        });");
graba($ar,"   }else{alert (er);}  ");
graba($ar,"    };");
graba($ar,"    ");
graba($ar,"    \$scope.clearInfo =function(info)");
graba($ar,"    {");
graba($ar,"        console.log('empty');");
graba($ar,"        \$('#idForm').slideToggle();");
graba($ar,"    };");
graba($ar,"");
graba($ar,"}]);");
graba($ar,"	 ");
$alvaro = $autor. "   " . $hoy ."  ";
fputs($ar,"// >>>>>>>   Creado por: ".$alvaro." <<<<<<< ");
fputs($ar,"\n");
fclose($ar);


//
//    Vista - formulario  + + + + + + + + + + + + + +  F O R M U L A R I O
//


$directorio = $ruta."views";

if (!is_dir($directorio)) {
    mkdir($directorio, 0777);
}
$tit="Formulario ";
if($frm=='cr'){$tit="Admin ";}
$tit.=$tabla;
$archivo = $directorio."/frm_".$tabla.'.php';    
$ar=fopen($archivo,"w") or die("Problemas en la creacion");   

if ($hdr=='si'){
    graba($ar,"<html>");
    graba($ar,"<head>");
    graba($ar,"  <meta charset=\"utf-8\">");
    graba($ar,"  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">");
    graba($ar,"  <title>" . $tit. "</title>");
    graba($ar,"  <meta content=\"width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no\" name=\"viewport\">");
    graba($ar,"  <link href=\"../css/bootstrap.min.css\" rel=\"stylesheet\" type=\"text/css\"/>");
    graba($ar,"  <link href=\"../css/font-awesome.min.css\" rel=\"stylesheet\" type=\"text/css\"/>");
    graba($ar,"  <link href=\"../css/atom.css\" rel=\"stylesheet\" type=\"text/css\"/>");
    graba($ar,"</head>");
    graba($ar,"<body class=\"hold-transition skin-blue sidebar-mini\"   ng-app=\"app\" >"); 
}
graba($ar,"");
graba($ar,"    <div class=\"container \"  ng-controller=\"mainController\">");
graba($ar,"        <h3 class=\"text-left\">{{form_title}}</h3>");

// para el control de busqueda de registros
if($frm=='cr'){
    graba($ar,"        <nav class=\"navbar navbar-default navbar-mm col-md-8 col-md-offset-1\">");
    graba($ar,"            <div class=\"navbar-header\">");
    graba($ar,"                <div class=\"alert alert-default navbar-brand search-box\">");
    graba($ar,"                    <button class=\"btn btn-primary btn-xs\" ng-show=\"show_form\" ");
    graba($ar,"                    ng-click=\"formToggle()\">{{form_btnNuevo}}<span class=\"glyphicon\" aria-hidden=\"true\"></span></button>");
    graba($ar,"                </div>");
    graba($ar,"                <div class=\"alert alert-default input-group search-box\">");
    graba($ar,"                    <span class=\"input-group-btn\">");
    graba($ar,"                        <input type=\"text\" class=\"form-control busca-mm\" placeholder=\"{{form_Phbusca}}\" ng-model=\"search_query\" required>");
    graba($ar,"                    </span>");
    graba($ar,"                </div>");
    graba($ar,"            </div>");
    graba($ar,"        </nav>");
}


graba($ar,"        <div class=\"col-md-8 col-md-offset-1\">");
graba($ar,"");
graba($ar,"            <form class=\"form-horizontal alert alert-mm color-palette-set\" name=\"formato\" id=\"idForm\"");
graba($ar,"                  ng-submit=\"insertInfo(registro);\" hidden=\"\">");
graba($ar,"");
graba($ar,"   ");             

graba($ar,"");
//0.columna|1.tipo|2.nombre|3.radio|4.indice|5.orden|6.valida|7.check|8.sele file|9.sele data
$sel=0;
$u=0;
for ($x=0;$x<=$j;$x++){
    if($column[$x][0]!=$indiceCmp ){   
        graba($ar,"                <div class=\"form-group\">");
        graba($ar,"                    <label class=\"control-label col-md-4\" for=\"".$column[$x][0]."\">{{form_".$column[$x][0]."}}</label>");
         if($column[$x][3]==0 && $column[$x][7]==0 ){  ///  Botones radio y check
          if ($column[$x][8]==''){ 
            $text = strtoupper($column[$x][10]); 

            graba($ar,"                   <div class=\"col-md-6\">");
            switch ($text) {
                case 'T':  // text area
                    graba($ar,"                    <textarea  class=\"form-control\"  cols=\"60\" rows=\"4\" id=\"".$column[$x][0]."\" name=\"".$column[$x][0]."\"");
                    graba($ar,"                         ng-model=\"registro.".$column[$x][0]."\" required Placeholder=\"{{form_Ph".$column[$x][0]."}}\" ");
                    graba($ar,"                         value=\"{{registro.".$column[$x][0]."}}\">");  
                    graba($ar,"                    </textarea>");  
                    break;
                case 'P': // password
                    graba($ar,"                    <input type=\"password\" class=\"form-control\" id=\"".$column[$x][0]."\" name=\"".$column[$x][0]."\"");
                    graba($ar,"                        ng-model=\"registro.".$column[$x][0]."\" required Placeholder=\"{{form_Ph".$column[$x][0]."}}\" ");
                    graba($ar,"                       value=\"{{registro.".$column[$x][0]."}}\" />"); 
                    break;
                case 'M': //email
                    graba($ar,"                    <input type=\"email\" class=\"form-control\" id=\"".$column[$x][0]."\" name=\"".$column[$x][0]."\"");
                    graba($ar,"                         ng-model=\"registro.".$column[$x][0]."\" required Placeholder=\"{{form_Ph".$column[$x][0]."}}\" ");
                    graba($ar,"                         pattern=\"[a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*@[a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{1,5}\"  value=\"{{registro.".$column[$x][0]."}}\" />");
                    break;
                case 'D': //date             
                    graba($ar,"                    <input type=\"date\" width=\"12\" class=\"form-control fa fa-calendar fa-lg\" id=\"".$column[$x][0]."\" name=\"".$column[$x][0]."\"");
                    graba($ar,"                         ng-model=\"registro.".$column[$x][0]."\" required Placeholder=\"{{form_Ph".$column[$x][0]."}}\" ");
                    graba($ar,"                         value=\"{{registro.".$column[$x][0]."}}\"   />");                      
                    break;
                
                default:                       
                    graba($ar,"                    <input type=\"text\" class=\"form-control\" id=\"".$column[$x][0]."\" name=\"".$column[$x][0]."\"");
                    graba($ar,"                         ng-model=\"registro.".$column[$x][0]."\" required Placeholder=\"{{form_Ph".$column[$x][0]."}}\" ");
                    graba($ar,"                         value=\"{{registro.".$column[$x][0]."}}\" />");

            }
            graba($ar,"                    </div>");
        }else{
            $table= $column[$x][8];
            $inom= $column[$x][9];
          //  $table = $fileLista[$i];
            $pos = strpos($table, ".txt");
            $idSel=  substr($table, 0, $pos);
            $ngModel = "registro.".$column[$x][0];
            

//                    <select id='embrque_Pais' name='embrque_Pais' ng-model='registro.embrque_Pais' >
//                     <option ng-repeat='operator0 in operators0' value = " {{operator0.codigo}}">{{operator0.nombre}}</option>
        
            
            if ($pos == false) {
                $cols=explode(',',$inom);
                graba($ar,"                    <div class=\"col-md-6\">");
                graba($ar,"                    <select id='". $column[$x][0] . "' name='". $column[$x][0] . "' ng-model='". $ngModel . "' >");
                graba($ar,"                     <option ng-repeat='operator".$sel . " in operators" . $sel . "' value = \" {{operator".$sel."." . $cols[0] . "}}\">{{operator".$sel.".".$cols[1]."}}</option>");
               graba($ar,"                    </select>"); 
            }
            else {
                graba($ar,"                    <div class=\"col-md-6\">");
                graba($ar,"                    <select id='". $idSel . "' name='". $idSel . "' ng-model='". $ngModel . "' >");
                graba($ar,"                     <option ng-repeat='".$idSel . " in " . $idSel . ".availableOptions' value = \" {{" . $idSel . ".codigo}}\">{{".$idSel.".detalle}}</option>");
                 graba($ar,"                    </select>"); 
            }
            graba($ar,"                    </div>");     
 
            
            $sel +=1;  
        }
    }else {
            if($column[$x][3]>0){
            graba($ar,"                    <div class=\"btn-group  col-md-6\"  data-toggle=\"buttons\">");
            $m=$column[$x][3];
            for ($i=0;$i<$m;$i++){
             graba($ar,"                   <label>");
             graba($ar,"                      <input type=\"radio\" name =\"".$column[$x][0] ."\" ng-model=\"registro.".$column[$x][0]."\" value=\"".$i."\" >{{form_Activo".$x.$i."}}");    
             graba($ar,"                   </label>");
             }
            graba($ar,"                    </div>");
            }
            
            if($column[$x][7]>0){
            graba($ar,"                    <div class=\"btn-group  col-md-6\"  data-toggle=\"chkbox\">");
            $m=$column[$x][7];
            for ($i=0;$i<$m;$i++){
             graba($ar,"                   <label>");
             graba($ar,"                      <input type=\"checkbox\" id=\"".$column[$x][0].$i."\" ng-model=\"registro.".$column[$x][0].$x.$i."\" value=\"Check ".$i."\" >{{form_Activo".$x.$i."}}");    
             graba($ar,"                   </label>");
             }
            graba($ar,"                    </div>");
            }            
        }
    graba($ar,"                </div> ");
    graba($ar,"");         
    }
}
//<label><input type="checkbox" id="cbox1" value="first_checkbox"> Este es mi primer checkbox</label><br>
       
graba($ar,"                <div class=\"form-group\">");
graba($ar,"                    <div class=\"col-md-5\">");
graba($ar,"                        <button type=\"button\" value=\"Actualizar\" class=\"btn btn-custom pull-right btn-xs\" ");
graba($ar,"                                 ng-click=\"updateInfo(registro)\" id=\"send_btn\">{{form_btnActualiza}}</button>");
graba($ar,"                     </div>  ");
if($frm=='cr'){
    graba($ar,"                    <div class=\"col-md-1\">");
    graba($ar,"                        <button type=\"button\" value=\"Cerrar\" class=\"btn btn-custom pull-right btn-xs\" ");
    graba($ar,"                                 ng-click=\"clearInfo(registro)\" ");
    graba($ar,"                                 id=\"send_btn\">{{form_btnAnula}}</button> ");
    graba($ar,"                    </div>");
}
graba($ar,"                </div>       ");         
graba($ar,"                <div style='display: none'>");
graba($ar,"                <input type=\"text\"	 ng-model=\"registro.". $indice . "\" id ='". $indice . "'  name ='". $indice . "' value=\"{{registro.". $indice . "}}\"/>");
//graba($ar,"                <input type=\"text\"  ng-model=\"registro.". $empresa . "\" id ='". $empresa . "' value=\"{{registro.". $empresa . "}}\"/>");
graba($ar,"");               
graba($ar,"   ");       
graba($ar,"                </div>");
graba($ar,"            </form>");
graba($ar,"	</div>");
if($frm=='cr'){
    graba($ar,"	<div class=\"clearfix\"></div>");
    graba($ar,"        <div class=\"col-md-10\">");
    graba($ar,"            <!-- Table to show employee detalis -->");
    graba($ar,"            <div class=\"table-responsive\">");
    graba($ar,"                <table class=\"table table-hover\">");
    graba($ar,"                    <tr>");
            for ($x=0;$x<=$j;$x++){
               graba($ar,"                        <th>".$column[$x][2]."</th>");
            }
    graba($ar,"                    </tr>");
    graba($ar,"                   ");
    graba($ar,"                    <tr ng-repeat=\"detail in details| filter:search_query\">");
            for ($x=0;$x<=$j;$x++){
               graba($ar,"                    <td>{{detail.".$column[$x][0]."}}</td>");
            }
    graba($ar,"                    <td>");
    graba($ar,"                    <button class=\"btn btn-warning btn-xs\" ng-click=\"editInfo(detail)\" title=\"{{form_btnEdita}}\"><span class=\"glyphicon glyphicon-edit\"></span></button>");
    graba($ar,"                    </td>");
    graba($ar,"                    <td>");
    graba($ar,"                    <button class=\"btn btn-danger btn-xs\" ng-click=\"deleteInfo(detail)\" ");
    graba($ar,"                            confirm=\"Está seguro ?, {{form_btnElimina}}?\" title=\"{{form_btnElimina}}\"><span class=\"glyphicon glyphicon-trash\"></span></button>");
    graba($ar,"                    </td>");
    graba($ar,"                    </tr>");
    graba($ar,"                </table>");
    graba($ar,"            </div>");
    graba($ar,"        </div>");    
}

graba($ar,"</div>");
graba($ar,"");

$puntos="";

if ($hdr=='si'){
    $puntos="../";
    graba($ar,"</body>");
    graba($ar,"<script src=\"../js/jQuery-2.2.0.min.js\" type=\"text/javascript\"></script>");
    graba($ar,"<script src=\"http://cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.1/angular.min.js\" type=\"text/javascript\"></script>");
    graba($ar,"<script src=\"../js/bootstrap.js\" type=\"text/javascript\"></script>");
    graba($ar,"<script src=\"../js/angular-script_1.js\" type=\"text/javascript\"></script>");
    graba($ar,"<script src=\"../js/angular-script.js\" type=\"text/javascript\"></script>");
}    
graba($ar,"<script src=\"".$puntos."controller/".$tabla.".ctrl.js\" type=\"text/javascript\"></script>");

graba($ar,"	 ");
if ($hdr=='si'){
    graba($ar,"</html>");
}
$alvaro = $autor. "   " . $hoy ."  ";
fputs($ar,"<!-- >>>>>>>   Creado por: ".$alvaro." <<<<<<< -->");
fputs($ar,"\n");
fclose($ar);
//

$msg = "Se han creado los modulos.";
echo $msg;
return $msg;


   
 function graba($ar,$ln){
    str_replace('|','"',$ln);
    fputs($ar,$ln);
    fputs($ar,"\n");
}
    