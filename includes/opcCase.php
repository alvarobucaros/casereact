<?php
include_once("clsConection.php");
$obj = new DBconexion("atominge","127.0.0.1","root","");
//$objClase=new DBconexion;
date_default_timezone_set("America/Bogota");

if(isset($_POST["accion"])){
   $accion = $_POST["accion"];
   $condicion = $_POST["condicion"];
   
    if ($accion=='ddlBasesDatos'){
        $sql = "SELECT distinct table_schema FROM information_schema.TABLES ".
               "WHERE table_schema NOT LIKE 'information_%' AND table_schema NOT LIKE 'performance_%' ";
      // $obj = new DBconexion();
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
      // $obj = new DBconexion();
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
      // $obj = new DBconexion();
       $con = $obj->conectar();
     
        $respuesta='<table id="tabla" class="tablex" border="1"><tr><th>Columna</th><th>tipo</th><th>Nombre</th>'
                . '<th>Index</th><th>Orde n</th><th>Tipo Text</th><th>Empresa</th><th>Radio</th><th>Check</th>'.
                '<th>Tabla o lista</th><th>Cod y Detalle</th></tr>';
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
            "<td><input type='checkbox' size='6' class='td' name='or".$i."'></input></td>".
            "<td><input class='tip' type='text' size='8' id='tx".$i."' name='tx".$i."' value='". $tx ."' ></input></td>".
            "<td><input type='checkbox' size='5' class='td' name='va".$i."' ></input></td>".
            "<td><input class='tip' type='text' size='2' maxlength='2' id='Rad".$i."' value='' /></td>".
            "<td><input class='tip' type='text' size='2' maxlength='30' id='Chk".$i."' value='' /></td>".
            "<td><input class='tip' type='text' size='8' maxlength='50' id='Tlis".$i."' value='' /></td>".
            "<td><input class='tip' type='text' size='11' maxlength='50' id='Flis".$i."' value='' /></td></tr>";         
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
                $prefijo = str_replace("_","", ucfirst($rec[3]));
                $indice = $rec[4];
                $orden = $rec[5];                
                $tabla = strtolower($rec[6]);
                $tablaSinS = $tabla;
                $m=strlen ($tabla);
                if(substr($tablaSinS,$m-1,1)==='s'){
                    $tablaSinS=substr($tablaSinS,0,$m-1);
                }
                $tablaPrimeraMayuscula = ucfirst($tablaSinS);
                $tablaPrefijo = $prefijo;                
                $prefijo = $rec[3];
                $frm = $rec[7];
                $hdr = $rec[8];
                $textArea  = $rec[9];
                $leng  = $rec[10];
                $js  = $rec[11];
                $mod  = $rec[12]; 
                $view  = $rec[13];
                $exp =$rec[14];
                $base =$rec[15];
                $indiceCmp = $indice; 
                
            }else{
                $j=$i-1;
                for ($k=0;$k<=10;$k++){
                    if ($k==0){
                        $column[$j][$k]= strtolower($rec[$k]); 
                    }
                    else{
                        $column[$j][$k]=strtolower($rec[$k]);
                    }  
                }    
            }
        $i+=1; 
        }
    }
    $n=0;
    $f=0;
    $err='';
    $empresa='';
    $orden='';
    $index='';
    $reqBody='';
    $reqBodyId='';
    for($l=0;$l < count($column);$l++) {
        if($column[$l][8]!=''){
            $file=$lsruta.'/'.$column[$l][8];
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
    $selectSinId = '';
    $update = '';
    $comasSelect='';
    $fileLista=array();
    $fileCampos=array();
    $l=0;
    for ($x=0;$x<=$j;$x++){
        $select .= $column[$x][0] ;
        if ($x<$j){
            $select .= ', '; 
        }

        if($column[$x][8]!=''){
            $fileLista[$l] = $column[$x][8];
            $fileCampos[$l] = $column[$x][9];
            $l+=1;
        }
        if($column[$x][4]==='on'){
            $index=$column[$x][0];
            $reqBodyId='req.body.'.$index;
        } else{
           $update .= $column[$x][0] .' = ?';
           $selectSinId .=$column[$x][0];;
           $reqBody .= 'req.body.'.$column[$x][0];
           if ($x<$j){
                $reqBody .= ', ';
                $selectSinId.= ', ';
                $update .= ', ';
                $comasSelect .=' ?,';
           }else{
               $comasSelect .=' ?';
           }
        }       
        if($column[$x][5]==='on'){
            $orden=$column[$x][0];
        } 
        if($column[$x][6]==='on'){
            $empresa=$column[$x][0];
        }        
    }

    //
    //  SERVER
    //
    $hoy = date("l,M d, Y g:i:s");   
    $alvaro = $autor. "   " . $hoy ."  ";
    
    // Ruter
    
    $directorio = $ruta."/server/routes";
    if (!is_dir($directorio)) { 
        mkdir($directorio, 0777,true);
    }
   
    $archivo = $directorio.'/'.$tabla.'_route.js';    
    $ar=fopen($archivo,"w") or die("Problemas en la creacion");

    graba($ar,"const express = require('express');");
    graba($ar,"const router = express.Router();");
    graba($ar,"");
    graba($ar,"const controller".$tablaPrefijo." = require('../controller/".$tabla."Controller');");
    graba($ar,"");
    graba($ar,"router.get('/lee".$tablaPrimeraMayuscula.":id',controller".$tablaPrefijo.".lee".$tablaPrimeraMayuscula.");");
    graba($ar,"router.get('/trae".$tablaPrimeraMayuscula."',controller".$tablaPrefijo.".trae".$tablaPrimeraMayuscula.");");
    graba($ar,"router.post('/update".$tablaPrimeraMayuscula."',controller".$tablaPrefijo.".update".$tablaPrimeraMayuscula.");");
    graba($ar,"router.get('/delete".$tablaPrimeraMayuscula.":id', controller".$tablaPrefijo.".delete".$tablaPrimeraMayuscula.");");
    graba($ar,"");
    graba($ar,"module.exports = router; ");
    fputs($ar,"// >>>>>>>   Creado por: ".$alvaro." <<<<<<< ");
    fputs($ar,"\n");
    fclose($ar);

 
    //  Controladora sentencias SQL
   
    $directorio = $ruta."/server/controller";
    if (!is_dir($directorio)) { 
        mkdir($directorio, 0777,true);
    }
    $archivo = $directorio.'/'.$tabla.'Controller.js'; 
    $ar=fopen($archivo,"w") or die("Problemas en la creacion");
    
    graba($ar,"const controller = {};");
    graba($ar,"");
    graba($ar,"	controller.lee".$tablaPrimeraMayuscula." = (req, res) => {");
    graba($ar,"		const data = req.body;");
    graba($ar,"		var { id } = req.params;   "); 
    graba($ar,"		let parts = id.split(':');");
    graba($ar,"		empresa = parts[1];");
    graba($ar,"		req.getConnection((err, conn) => {");
    graba($ar,"");
    graba($ar,"		var sql = 'SELECT ".$select."';");
    graba($ar,"		sql += ' FROM ".$tabla." ';");
    graba($ar,"		sql += ' WHERE ".$empresa." = ' + empresa;");
    graba($ar,"		sql += ' ORDER BY ".$orden."';");
    graba($ar,"	//	console.log(sql);");
    graba($ar,"		conn.query(sql, (err, respuesta)=> { ");
    graba($ar,"				if (respuesta.length>0){ ");
    graba($ar,"					res.send(respuesta); ");
    graba($ar,"				} ");
    graba($ar,"				else { ");
    graba($ar,"				  res.json(err); ");
    graba($ar,"				} ");
    graba($ar,"			}); ");
    graba($ar,"		}); ");
    graba($ar,"	};");
    graba($ar,"");
       
    graba($ar,"   controller.trae".$tablaPrimeraMayuscula." = (req, res) => {   ");
    graba($ar,"     req.getConnection((err, conn) => {");
    graba($ar,"		var { id } = req.params;    ");
    graba($ar,"		let parts = id.split(':');");
    graba($ar,"		id = parts[1];");
    graba($ar,"		var sql= 'SELECT ". $index .", ". $orden . "'");
    graba($ar,"		sql += ' FROM conceptos WHERE ".$empresa." =  ? AND ESTADO = \"A\"';");
    graba($ar,"		sql += ' ORDER BY ". $orden . "' ");
    graba($ar,"	//	console.log(sql);");
    graba($ar,"		conn.query(sql,[id], (err, respuesta)=> { ");
    graba($ar,"		if (respuesta.length>0){ ");
    graba($ar,"		  res.send(respuesta); ");
    graba($ar,"		} ");
    graba($ar,"		else { ");
    graba($ar,"			res.json(err); ");
    graba($ar,"		} ");
    graba($ar,"		 }); ");
    graba($ar,"	}); ");
    graba($ar,"	}; ");
    graba($ar," "); 
    
    graba($ar,"    controller.update".$tablaPrimeraMayuscula." = (req, res) => {");
    graba($ar,"    req.getConnection((err, conn) => {");
    graba($ar,"      a='".$reqBody."';");
    graba($ar,"      console.log(a);");
    graba($ar,"     if(".$reqBodyId."==='0')  {");
    graba($ar,"      var sql= 'INSERT INTO " .$tabla . "(" . $selectSinId . ") '; "); 
    graba($ar,"      sql += ' VALUES ( ". $comasSelect. " ) ' ;");
    graba($ar,"   //   console.log(sql+' insert');");
    graba($ar,"      conn.query(sql, [". $reqBody . "], ");
    graba($ar,"        (err, rows) => {");
    graba($ar,"        res.err;");
    graba($ar,"      });");
    graba($ar,"    }");
    graba($ar,"    else{");
    graba($ar,"      var sql= 'UPDATE " .$tabla . "  SET ". $update ."' ;");
    graba($ar,"      sql += ' WHERE ". $index ." = ? '");
    graba($ar,"  //    console.log(sql+' update');");
    graba($ar,"        conn.query(sql, [". $reqBody .", ". $reqBodyId. "], ");
    graba($ar,"        (err, rows) => {");
    graba($ar,"     res.err;");
    graba($ar,"      });    ");  
    graba($ar,"    }");
    graba($ar,"    })  ");
    graba($ar,"   }; ");
    graba($ar,"  ");  

    graba($ar,"    controller.delete".$tablaPrimeraMayuscula." = (req, res) => {");
    graba($ar,"        var { id } = req.params;    ");
    graba($ar,"        let parts = id.split(':');");
    graba($ar,"        id = parts[1];");
    graba($ar,"        req.getConnection((err, conn) => {");
    graba($ar,"           var sql = 'DELETE FROM " .$tabla. " WHERE ".$index." = ?'");
    graba($ar,"           conn.query(sql,[id], ");
    graba($ar,"           (err, rows) => {");
    graba($ar,"            res.err;");
    graba($ar,"           });");
    graba($ar,"         })");
    graba($ar,"    };");
    graba($ar,"");
    graba($ar,"     module.exports = controller;");
    graba($ar,"");
 
    fputs($ar,"// >>>>>>>   Creado por: ".$alvaro." <<<<<<< ");
    fputs($ar,"\n");
    fclose($ar);
    
    //
    //  CLIENTE
    //    
    $directorio = $ruta."/client/src/views";
    if (!is_dir($directorio)) { 
        mkdir($directorio, 0777,true);
    }
    //
    //    Vista - formulario  + + + + + + + + + + + + + +  F O R M U L A R I O
    //
   $archivo = $directorio.'/'.$tabla.'.js'; 
   $ar=fopen($archivo,"w") or die("Problemas en la creacion");
   
    graba($ar,"import React, { Fragment, useEffect, useState} from 'react';");
    graba($ar,"import {useForm} from 'react-hook-form';");
    graba($ar,"import Pagination from 'react-paginate';");
    graba($ar,"import Axios from 'axios';");
    graba($ar,"import Modal from 'react-modal';");
    graba($ar,"import '../App.css';");
    graba($ar,"");
    graba($ar,"const Mas". $tablaPrimeraMayuscula ." = () => {");
    graba($ar,"");
    graba($ar,"    // formato de  la modal de la tabla : ".$tabla."");
    graba($ar,"    const customStylesForm = {");
    graba($ar,"        content : {");
    graba($ar,"        top                   : '40%',");
    graba($ar,"        left                  : '50%',");
    graba($ar,"        right                 : 'auto',");
    graba($ar,"        bottom                : 'auto',");
    graba($ar,"        marginRight           : '-50%',");
    graba($ar,"        transform             : 'translate(-50%, -50%)'");
    graba($ar,"        }");
    graba($ar,"    };");
    graba($ar,"");
    graba($ar,"    // formato de  la ventada modal de borarado");
    graba($ar,"    const customStylesDelete = {");
    graba($ar,"        content : {");
    graba($ar,"        top                   : '50%',");
    graba($ar,"        left                  : '50%',");
    graba($ar,"        right                 : 'auto',");
    graba($ar,"        bottom                : 'auto',");
    graba($ar,"        marginRight           : '-50%',");
    graba($ar,"        transform             : 'translate(-50%, -50%)'");
    graba($ar,"        }");
    graba($ar,"    }; ");
    graba($ar," ");

    graba($ar,"       // Registro de lectura tabla principal y del select fijo y variable");
    graba($ar,"   const {register, errors, handleSubmit} = useForm();");
    graba($ar,"   const [" . $tablaSinS . "Datos, set" . $tablaPrimeraMayuscula . "Datos] = useState([]);");
    graba($ar,"   const [offset, setOffset] = useState(0);");
    for ($x=0;$x<=$j;$x++){
        if($column[$x][8]!='' && $column[$x][9]===''){
           graba($ar,"   const [".$column[$x][0]."Fijo, set".ucfirst($column[$x][0])."Fijo] = useState([]);");
            $mo=0;
            $selectF='';           
            $fil = $lsruta.'/'.$column[$x][8];
            $file = fopen($fil, "r") or exit("Unable to open file!");
            while(!feof($file))
            {
             $t =  fgets($file);             
             $rec = explode(";", $t); 
          //    echo ($rec[0].' '.$rec[1]);
              if($mo===0){ 
                  $selectF='[';                
              }else{
                $selectF .= "{id:'".$rec[0]."',detalle:'".$rec[1]."'},";
              }
             $mo+=1;
            }

            if($mo>0){ $selectF .=']';
                graba($ar,"      set".ucfirst($column[$x][0])."Fijo(".$selectF.");");      
            }           
        }
        if($column[$x][8]!='' && $column[$x][9]!=''){
           graba($ar,"   const [".$column[$x][0]."Vble, set".ucfirst($column[$x][0])."Vble] = useState([]);");
        }
    }
    graba($ar,"");
    graba($ar,"   const[codBorrado, setCodBorrado] = useState('');");
    graba($ar,"   const[idBorrar, setIdBorrar] = useState(0);");
    graba($ar,"   const[empresa, setEmpresa] = useState(1);");
 
    // Calcula si hay Botones radio y/o check box   
    $radio='';
    $check='';
    $datosView='';
    $miTxt="";
    $xt=0;
    for ($x=0;$x<=$j;$x++){
        if($column[$x][3]!='0'){ 
            if(is_numeric($column[$x][3])){
                $xt=$column[$x][3]-1;
                $miTxt="        const btnRadio".ucfirst($column[$x][0]). " = [";
                for ($m=0;$m < $column[$x][3]; $m++){
                    $miTxt .= "{id:'" . $m . "', detalle:'Estado de " . $m . "'}";
                    if($m<$xt){
                        $miTxt .= ", ";
                    }
                }
                 $miTxt .= "];";
            }
            graba($ar,$miTxt);
        }
        if($column[$x][7]!='0'){
            if($check != ''){$check.='<>';}
            $check.=$column[$x][0].'||'.$column[$x][7];
        }
        $miTxt="''";
      
        if(strtoupper($column[$x][10])==='N'){
            $miTxt="'0'";
        }    
        if(strtoupper($column[$x][10])==='D'){
            $miTxt = 'isoDate';
        }
        $datosView .= $column[$x][0].':'.$miTxt;
        if ($x<$j){
            $datosView .= ", ";
        }
    }
    
        // Tabla principal valores iniciales
    graba($ar,"    var d = new Date();");
    graba($ar,"    let isoDate=fecha(d.toISOString());");
    graba($ar,"    var [".$tablaSinS. "Select, set".ucfirst($tablaSinS). "Select] = useState({");
    graba($ar,$datosView);
    graba($ar,"    });");

    graba($ar,"    // Validación");
    graba($ar,"    const selecciona".$tablaPrimeraMayuscula ."=(elemento, caso)=>{");
    graba($ar,"        set".$tablaPrimeraMayuscula ."Select(elemento);");
    graba($ar,"        (caso === 'Editar')&&setIsOpen(true)");
    graba($ar,"    }");
    graba($ar,"");
    graba($ar,"    // metodos de la Modal general y del boton delete");
    graba($ar,"    const [modalIsOpen,setIsOpen] = React.useState(false);");
    graba($ar,"    const [modalDeleteIsOpen,setDeleteIsOpen] = React.useState(false);");
    graba($ar," ");
    graba($ar,"    function openModal() {");
    graba($ar,"        setIsOpen(true);");
    graba($ar,"    }");
    graba($ar,"");
    graba($ar,"    function afterOpenModal() {");
    graba($ar,"   }");
    graba($ar,"");
    graba($ar,"    function closeModal(){");
    graba($ar,"        setIsOpen(false);");
    graba($ar,"   }");
    graba($ar,"");
    graba($ar,"    function openModalDelete() {");
    graba($ar,"        setDeleteIsOpen(true);");
    graba($ar,"    }");
    graba($ar,"");
    graba($ar,"    function closeModalDelete(){");
    graba($ar,"        setDeleteIsOpen(false);");
    graba($ar,"    }");
    graba($ar,"");

    
    graba($ar,"      // Fecha de ISO a amd");
    graba($ar,"    function fecha(fch){");
    graba($ar,"        if (fch != null && fch !== undefined) {");
    graba($ar,"           fch = fch.split('T')[0]");
    graba($ar,"        }else{");
    graba($ar,"            fch=new Date();");
    graba($ar,"        }");
    graba($ar,"       return fch;");
    graba($ar,"    }");
    graba($ar,"");
    graba($ar,"    //  Registro Nuevo valores iniciales");
    graba($ar,"    function recordNuevo(){");
    graba($ar,"        var d = new Date();");
    graba($ar,"        let isoDate=fecha(d.toISOString());");
    for ($x=0;$x<=$j;$x++){
        $miTxt="''";
       // echo($column[$x][10]);
        if(strtoupper($column[$x][10])==='N'){
            $miTxt="'0'";
        }    
        if(strtoupper($column[$x][10])==='D'){
            $miTxt = 'isoDate';
        }
        $text=$tablaSinS."Select.".$column[$x][0]."=".$miTxt.";";
        graba($ar,"     ".$text);
    }
    graba($ar,"        openModal();");
    graba($ar,"    }");  
    graba($ar," // botones de la tabla");
    graba($ar,"    function cambiaRec(txt){");
    graba($ar,"        openModal();");
    graba($ar,"    }");
    graba($ar,"");
    for ($x=0;$x<=$j;$x++){
        if($column[$x][3]>'0' || $column[$x][7]>'0'){
            graba($ar,"    function changeEstado". ucfirst($column[$x][0])."(estado){");
            graba($ar,"        ".$tablaSinS."Select.".$column[$x][0]."Estado = estado;");
            graba($ar,"    }");
        }
    }
    graba($ar," ");   
    graba($ar,"    function confirmaBorraRec(txt){");
    graba($ar,"        Axios.get('http://localhost:3001/delete".$tablaPrimeraMayuscula.":'+ idBorrar )");
    graba($ar,"        .then( alert('Registro borrado'),");
    graba($ar,"            response=>{");           
    graba($ar,"            handlePageClick()");
    graba($ar,"        .catch((err) => console.error(err));");
    graba($ar,"        });");
    graba($ar,"        remove(idBorrar)");
    graba($ar,"        setDeleteIsOpen(false);");
    graba($ar,"    }");
    graba($ar,"");
    graba($ar,"    function borraRec(txt){");
    graba($ar,"        let i = txt.id");
    graba($ar,"        setCodBorrado(txt.".$index."+' '+txt.".$orden.");");
    graba($ar,"        setIdBorrar(txt.id)");
    graba($ar,"        openModalDelete();");
    graba($ar,"");
    graba($ar,"    } ");   
    graba($ar,"  ");  

    graba($ar,"    // remueve de la lista traida de la base de datos"); 
    graba($ar,"    const remove = (id) => {"); 
    graba($ar,"        ". $tablaSinS. "Datos.splice(". $tablaSinS. "Datos.findIndex(txt => txt.id === id), 1);"); 
    graba($ar,"        set". ucfirst($tablaSinS). "Datos([...". $tablaSinS. "Datos]);"); 
    graba($ar,"    };"); 
    graba($ar,""); 
    graba($ar,"    const handleChangeSelectF = (e) => {"); 
    graba($ar,"        ". $tablaSinS. "Select.". $tablaSinS. "SelectF=e.target.value"); 
    graba($ar,"    }"); 
    graba($ar,""); 
    graba($ar,"    const handleChangeSelectV = (e) => {"); 
    graba($ar,"        ". $tablaSinS. "Select.". $tablaSinS. "SelectV=e.target.value"); 
    graba($ar,"    }"); 
    graba($ar,"    // va a actualizar la información"); 
    graba($ar,"    const submit".$tablaPrimeraMayuscula." = (data, event) =>{  ");      
    graba($ar,"        Axios.post('http://localhost:3001/update".$tablaPrimeraMayuscula."', data )"); 
    graba($ar,"        .then( alert('Información actualizada'),()=>{"); 
    graba($ar,"            console.log(data)"); 
    graba($ar,"        .catch((err) => console.error(err));"); 
    graba($ar,"        })"); 
    graba($ar,"        event.target.reset()"); 
    graba($ar,"        remove(idBorrar) ");        
    graba($ar,"        setIsOpen(false);"); 
    graba($ar,"    }"); 
    graba($ar,""); 
    graba($ar,"    const [pageCount, setPageCount] = useState(0)"); 
    graba($ar,"    const recordPerPage = 8;"); 
    graba($ar,"    const [ activePage, setCurrentPage ] = useState( 1 );"); 

    graba($ar," ");
    graba($ar,"   // trae datos de la tabla principal");
    graba($ar,"     useEffect(()=>{");
    graba($ar,"         Axios.get('http://localhost:3001/lee".$tablaPrimeraMayuscula.":'+empresa)");
    graba($ar,"         .then(res=>{");
    graba($ar,"             var ". $tablaSinS. "Datos = res.data");
    graba($ar,"             const slice = ". $tablaSinS. "Datos.slice(offset, offset + recordPerPage)");
    graba($ar,"             const indexOfLastRec  = activePage * recordPerPage;");
    graba($ar,"             const indexOfFirstRec = indexOfLastRec - recordPerPage;");
    graba($ar,"             const currentRec     = ". $tablaSinS. "Datos.slice( indexOfFirstRec, indexOfLastRec );");
    graba($ar,"             set". ucfirst($tablaSinS). "Datos(slice)");
    graba($ar,"             setPageCount(Math.ceil(". $tablaSinS. "Datos.length / recordPerPage))   ");        
    graba($ar,"         })");
    graba($ar,"     },[offset])");
    graba($ar," ");   
    
    for ($x=0;$x<=$j;$x++){
          if($column[$x][8]!='' && $column[$x][9]!=''){
        
           $rec = explode(',', $column[$x][9]);
           
   // trae la lista dsplegable dinamica
    graba($ar,"    useEffect(()=>{");
    graba($ar,"        Axios.get('http://localhost:3001/trae".ucfirst($column[$x][8]).":'+empresa)");
    graba($ar,"        .then((res)=>{");
    graba($ar,"           var data=res.data");
    graba($ar,"            if (data != null && data !== undefined){");
    graba($ar,"            const cp = data.map((txt, key) =>   ");             
    graba($ar,"            <option key={txt.".$rec[0]."} value={txt.".$rec[0]."}>{txt.".$rec[1]."}   ");                
    graba($ar,"            </option>  ");              
    graba($ar,"            )");
    graba($ar,"            set".ucfirst($column[$x][0])."Vble(cp);");
    graba($ar,"            }");
    graba($ar,"        }) ");
    graba($ar,"    },[])  ");         
    graba($ar,"  ");           
        }       
    }

    graba($ar,"    //  Paginación");  
    graba($ar,"");  
    graba($ar,"    const handlePageClick = (e) => {");  
    graba($ar,"        const selectedPage = e.selected;");  
    graba($ar,"        alert(selectedPage)");  
    graba($ar,"        setOffset(selectedPage + 1)");  
    graba($ar,"    };");  
    graba($ar,"");  

    
    
graba($ar,"    //  Parte principal: formulario y ventana modal");  
graba($ar,"    return(   ");     
graba($ar,"        <Fragment>      ");        
graba($ar,"            <div>");  
graba($ar,"                <h2>TABLA DE ". strtoupper($tabla) . " </h2>");  
graba($ar,"                <button onClick={recordNuevo}>Nuevo registro</button>");  
graba($ar,"            </div> ");                 
graba($ar,"            <Modal");  
graba($ar,"            isOpen={modalIsOpen}");  
graba($ar,"            ariaHideApp={false} ");  
graba($ar,"            onAfterOpen={afterOpenModal}");  
graba($ar,"            onRequestClose={closeModal}");  
graba($ar,"            style={customStylesForm}");  
graba($ar,"            contentLabel='".$tabla ."'");  
graba($ar,"            >");  
graba($ar,"");  
graba($ar,"                <div className='form'>");  
graba($ar,"                    <div className='content'>");  
graba($ar,"                        <form className='form-horizontal' onSubmit={handleSubmit(submit".$tablaPrimeraMayuscula.")}>");  
graba($ar,"                            <div className='laModal'>");  
graba($ar,"                                <div className='miModalTit'>");  
graba($ar,"                                    <h3>Actualiza ".$tabla ."</h3>");  
graba($ar,"                                </div>");  
graba($ar,"");  
for ($x=0;$x<=$j;$x++){
    $radio='';
    if($column[$x][3]!='0' OR $column[$x][7]!='0'){ $radio="className='radio'";}    
    graba($ar,"                                <div ".$radio.">");  
    graba($ar,"                                 <label className='label'>".ucfirst(str_replace($prefijo,'',$column[$x][0]))."</label>"); 
    
    if($column[$x][8]===''){    
       if(strtoupper($column[$x][10])==='N' OR strtoupper($column[$x][10])==='C'){
           if ($column[$x][3]==='0' && $column[$x][7]==='0'){
                graba($ar,"                                     <input type='text' defaultValue={".$tablaSinS ."Select.".$column[$x][0]."}");  
                graba($ar,"                                     name='".$column[$x][0]."' placeholder='contraseña obligatorio'");  
                graba($ar,"                                     ref={register({");  
                graba($ar,"                                          required:{value:true, message:'Campo obligatorio'}");  
                graba($ar,"                                      })} />");  
           }
           else{
               for($m=0;$m<$column[$x][3];$m++){
                    graba($ar,"                                     <input type='checkbox' name = '".$column[$x][0]."' defaultChecked={".$tabla ."Select.".$column[$x][0]." === '".$m."'}");  
                    graba($ar,"                                     onChange={() => changeEstado".ucfirst($column[$x][0])."('".$m."')} ref={register()}");  
                    graba($ar,"                                     defaultValue={".$tabla ."Select.".$column[$x][0]."='".$m."'}  /> check  : ".$m);  
               }
                for($m=0;$m<$column[$x][7];$m++){
                    graba($ar,"                                     <input type='radio' name = '".$column[$x][0]."' defaultChecked={".$tabla ."Select.".$column[$x][0]." === '".$m."'}");  
                    graba($ar,"                                     onChange={() => changeEstado".ucfirst($column[$x][0])."('".$m."')} ref={register()}");  
                    graba($ar,"                                     defaultValue={".$tabla ."Select.".$column[$x][0]."='".$m."'}  /> Estado : ".$m);  
               }
           }
       }  
       if(strtoupper($column[$x][10])==='P'){
           graba($ar,"                                     <input type='Password' defaultValue={".$tabla ."Select.".$column[$x][0]."}");  
           graba($ar,"                                     name='".$column[$x][0]."' placeholder='contraseña obligatorio'");  
           graba($ar,"                                     ref={register({");  
           graba($ar,"                                          required:{value:true, message:'Contraseña obligatoria'}");  
           graba($ar,"                                      })} />");  
       }    
       if(strtoupper($column[$x][10])==='D'){
           graba($ar,"                                     <input type='Date' defaultValue={".$tabla ."Select.".$column[$x][0]."}");  
           graba($ar,"                                     name='".$column[$x][0]."' placeholder='AAAA/MM/DD'");  
           graba($ar,"                                     ref={register({");  
           graba($ar,"                                          required:{value:true, message:'Fecha obligatoria'}");  
           graba($ar,"                                      })} />");          
       }
       if(strtoupper($column[$x][10])==='M'){
           graba($ar,"                                     <input type='email' defaultValue={".$tabla ."Select.".$column[$x][0]."}");  
           graba($ar,"                                     name='".$column[$x][0]."' placeholder='Correo electrónico'");  
           graba($ar,"                                     ref={register({");  
           graba($ar,"                                          required:{value:true, message:'Correo obligatorio'}");  
           graba($ar,"                                      })} />");          
       }
       if(strtoupper($column[$x][10])==='T'){
           graba($ar,"                                     <input type='textarea' defaultValue={".$tabla ."Select.".$column[$x][0]."}");  
           graba($ar,"                                     name='".$column[$x][0]."'  rows={2} placeholder='Correo electrónico'");  
           graba($ar,"                                     ref={register({");  
           graba($ar,"                                          required:{value:true, message:'Fecha obligatoria'}");  
           graba($ar,"                                      })} />");          
       }    
       graba($ar,"                                </div>");  
   }
   else{
        if($column[$x][9]===''){
 // graba($ar,"   const [".$column[$x][0]."Fijo, set".ucfirst($column[$x][0])."Fijo] = useState([]);");
 graba($ar,"                           <select  name='".$column[$x][0]."'  defaultValue={".$tabla ."Select.".$column[$x][0]."}");
 graba($ar,"                           onChange={e=>handleChangeSelectF(e)}  ");                  
 graba($ar,"                               ref={register({");
 graba($ar,"                                   required:{value:true, message:'Campo obligatorio'}");
 graba($ar,"                               })} > ");                        
 graba($ar,"                               { ".$column[$x][0]."Fijo.map((txt, key) => ");               
 graba($ar,"                               <option  value={txt.".$index."}>{txt.".$orden. "}   ");                
 graba($ar,"                               </option> ) }");
 graba($ar,"                           </select>");
 graba($ar,"                        </div>  ");
 graba($ar,"");
       }else{
 graba($ar,"");
 graba($ar,"                           <select  name='".$column[$x][0]."' defaultValue={".$tabla ."Select.".$column[$x][0]."}");
 graba($ar,"                           onChange={handleChangeSelectV} ");                    
 graba($ar,"                               ref={register({");
 graba($ar,"                                   required:{value:true, message:'Campo obligatorio'}");
 graba($ar,"                               })} >");
 graba($ar,"                               {".$column[$x][0]."Vble}");
 graba($ar,"                           </select>");
 graba($ar,"                       </div>   ");         
 
 graba($ar,"                                <div>"); 
   }
}
}
graba($ar,"                                    <button>Aceptar</button>");  
graba($ar,"                                    <button onClick={closeModal}>Anula</button>");  
graba($ar,"                                </div>");  
graba($ar,"                                <div  style={{visibility : 'hidden' }}>");  
graba($ar,"                                    <input type='text'  name ='id'");   
graba($ar,"                                    defaultValue={".$tablaSinS ."Select.id}");  
graba($ar,"                                    ref={ register({value:0})}/>  ");    
graba($ar,"                                </div>");  
graba($ar,"                            </div>");  
graba($ar,"                        </form> ");  
graba($ar,"                    </div>");  
graba($ar,"                </div>");  
graba($ar,"            </Modal> ");   
graba($ar,"");  
graba($ar,"            <Modal");  
graba($ar,"            isOpen={modalDeleteIsOpen}");  
graba($ar,"            ariaHideApp={false}");   
graba($ar,"            onRequestClose={closeModalDelete}");  
graba($ar,"            style={customStylesDelete}");  
graba($ar,"            >");  
graba($ar,"                <div className='laModal'>");  
graba($ar,"                    <span>Quiere Borrar a {codBorrado}</span>");  
graba($ar,"                    <div className='tabla'> ");     
graba($ar,"                        <button className='btn btn-sm btn-primary mr-2'onClick={confirmaBorraRec}>Si</button>");  
graba($ar,"                        <button className='btn btn-sm btn-secondary' onClick={closeModalDelete}>No</button>");  
graba($ar,"                    </div>");  
graba($ar,"                </div>");  
graba($ar,"            </Modal>"); 
graba($ar,"");
graba($ar,"            <div className='table-responsive tabla'>");
graba($ar,"                <table className='table table-bordered table-hover table-sm'> ");
graba($ar,"                    <thead>  ");                      
graba($ar,"                        <tr>");
for ($x=0;$x<=$j;$x++){
  graba($ar,"                            <th>".ucfirst(str_replace($prefijo,'',$column[$x][0]))."</th>");  
}
graba($ar,"                            <th colSpan='2'>COMANDOS</th>");
graba($ar,"                        </tr> ");
graba($ar,"                     ");
graba($ar,"                    </thead>  ");
graba($ar,"					<tbody>");        
graba($ar,"					{  ". $tabla."Datos.map((txt, key) => ");              
graba($ar,"					<tr key={txt.".$index."}>");
for ($x=0;$x<=$j;$x++){
graba($ar,"						<td>{txt.".$column[$x][0]."}</td>");
}

graba($ar,"						<td><button onClick={() =>  selecciona". ucfirst($tablaSinS)."(txt,'Editar')} className='btn btn-sm btn-primary '>Cambia</button></td>");
graba($ar,"						<td><button onClick={() => borraRec(txt)} className='btn btn-sm btn-danger '>Anula</button></td>");
graba($ar,"					</tr>    ");                          
graba($ar,"					)}");
graba($ar,"					</tbody> ");                
graba($ar,"                </table>");
graba($ar,"                    <div className='pagination'>");
graba($ar,"                    <Pagination");
graba($ar,"                    previousLabel={'anterior'}");
graba($ar,"                    nextLabel={'siguiente'}");
graba($ar,"                    breakLabel={'...'}");
graba($ar,"                    breakClassName={'break-me'}");
graba($ar,"                    pageCount={pageCount}");
graba($ar,"                    marginPagesDisplayed={2}");
graba($ar,"                    pageRangeDisplayed={8}");
graba($ar,"                    onPageChange={handlePageClick}");
graba($ar,"                    containerClassName={'pagination'}");
graba($ar,"                    subContainerClassName={'pages pagination'}");
graba($ar,"                    activeClassName={'active'}/>   ");               
graba($ar,"                </div>");
graba($ar,"            </div>");
graba($ar,"");
graba($ar,"");

graba($ar,"        </Fragment>");  
graba($ar,"    )");  
graba($ar,"}  ");                       
    
graba($ar,""); 
graba($ar,""); 
graba($ar," export default Mas".$tablaPrimeraMayuscula.";");
graba($ar,""); 
fputs($ar,"//   Creado por: ".$alvaro." ");
fputs($ar,"\n");
fclose($ar);
//

$msg = "Se han creado los modulos.";
echo $msg;
return $msg;
}


 function graba($ar,$ln){
    str_replace('|','"',$ln);
    fputs($ar,$ln);
    fputs($ar,"\n");
}
   