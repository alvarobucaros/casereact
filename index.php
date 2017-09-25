<html>
<head>
<title>CASE PHP</title>
<link href="css/estilo.css" rel="stylesheet" type="text/css"/>
<script src="js/jquery-1.12.0.js" type="text/javascript"></script>
</head>
<?php include_once 'idiomaESP.php'; ?>
<body>
    <div  ID="titulo"><h4><?php echo $titGeneral ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>v.1.0.1 - 2017-08-15</strong></h4></div>
<div id="main">

    <form name="Cabecera"  method="POST">
        <div>
            <label><?php echo $tpFormulario?>&nbsp;&nbsp; :</label>            
            <input type="radio" name="frm" id="frmcrud" value="cr" checked/>&nbsp;&nbsp;&nbsp;CRUD 
            <input type="radio" name="frm" id="frmfrm" value="fr" />&nbsp;&nbsp;&nbsp;<?php echo $formulario ?> <br>                
        </div>
         <br/>
        <div>
            <label><?php echo $conEncabezado?>&nbsp; :</label>            
            <input type="radio" name="hdr" id="hdrsi" value="si" />&nbsp;&nbsp;&nbsp;<?php echo $si?> 
            <input type="radio" name="hdr" id="hdrno" value="no" checked/>&nbsp;&nbsp;&nbsp;<?php echo $no?> <br>                
        </div>        
        <br/>
        <div>
            <label><?php echo $baseDatos ?> </label>
            <span id="ddlBases"></span>
        </div>
        <br/>
        <div id="tablas" style="display: none">
            <label><?php echo $tabla ?></label> 
            <span id="ddlTabla"></span>
        </div>
        <br />
        <div id="columnas" style="display: none">
            <label><?php echo $columnas ?></label>                   
            <div id="detColumnn">           
            </div>             
        </div> 
        <br />

        <div>
            <label><?php echo $prefijo ?></label>
            <input style=" width: 300" type="text" id="prefijo"></input>
        </div>
        <br />
        <div>
            <label><?php echo $usuario ?></label>
            <input  style=" width: 300" type="text" id="user" value = "Alvaro"></input>
        </div>
        <br />
        <div id="ruta">
            <label><?php echo $descarga ?></label> 
            <input  style=" width: 300" type="text" id="ruta" value = "C:/wamp/www/atomIngenieria/"></input>
        </div>
        <br />        
        <div>
            <label><?php echo $ruta ?></label>
            <input  style=" width: 300" type="text" id="lsruta" value = "C:/"></input>
        </div>
        <br />
        <div>
            <a onclick="procesar();" class="button"><img src="img/aprobar.png" alt="procesar" title="Procesa petición" /><?php echo $procesa ?></a>
            <i class="fa fa-question" ></i> <a href="#ayudas" cass="fa fa-question" ><?php echo $ayudas ?> </a>
              
        </div> 
        <br /> 
        <div id="progresos" style="display: none">
         <span id = "resultado"></span>
        </div>
        <br />
        
        <?php 
        echo ' <div id="progreso" style="display: none">';
        echo " <div id='nota'> ";
        echo "   <span>" .$nota ."</span>";      
        echo ' <input   type="text" id="leng" value = "'. $leng .'"></input>';
        echo ' <input   type="text" id="js" value = ""></input>'; 
        echo ' <input   type="text" id="mod" value = ""></input>'; 
        echo ' <input   type="text" id="view" value = ""></input>'; 
        echo '  </div>>/div>';
       ?>
    </form>
</div>
<div id="popup" style="display: none;">
    <div class="content-popup">
        <div class="close"><a href="#" id="close"><img src="img/close.gif"/></a></div>
        <div>
           <h2>Contenido POPUP</h2>
           ...
        </div>
    </div>
</div>

<script type="text/javascript"> 
    $('document').ready(function(){          
        llenaComboBaseDatos(); 
        lectura();
    }); 

function llenaComboBaseDatos()
{    
    condicion='';
    $.post("includes/opcCase.php", {accion:'ddlBasesDatos', condicion:condicion}, function(data){
    $("#ddlBases").html(data); 
    });
}

function cambiaDB(){  
    var tipo  = $("#selDB option:selected" ).text();
    $('#tablas').show();    
    condicion=tipo;
    $.post("includes/opcCase.php", {accion:'ddlTablas', condicion:condicion}, function(data){
    $("#ddlTabla").html(data); 
    });
}

function cambiaTabla(){ 
    var db  = $("#selDB option:selected" ).text();
    var tabla  = $("#selTabla option:selected" ).text(); 
    leng  = $('#leng').val() 
    condicion=db + '||' + tabla+ '||' +leng;
    $.post("includes/opcCase.php", {accion:'grillaTabla', condicion:condicion}, function(data){
    $("#detColumnn").html(data); 
   // alert (data);
    $('#columnas').show();  
    });
}

function procesar(){
    $('#progreso').show();
    frm = $("input[name='frm']:checked").val(); 
    hdr = $("input[name='hdr']:checked").val(); 
    tabla =  $("#selTabla option:selected" ).text();
    usuario = $('#user').val();
    ruta = $('#ruta').val()
    lsruta = $('#lsruta').val()
    leng  = $('#leng').val() 
    js   = $('#js').val()
    mod  = $('#mod').val() 
    view = $('#view').val()
    i = 0;
    fila = '';
    capoIndice = ''; 
    campoOrden = '';
    campoEmpresa='';
    prefijo = $('#prefijo').val();
    tabla =  $("#selTabla option:selected" ).text();
    $('#tabla tr').each(function () { 
    err='';
    if (i>0){            
        Columna = $('#co'+i).val();
        tipo = $('#ti'+i).val();
        nomes = $('#no'+i).val(); 
        nombre = nomes.replace(prefijo, "");
        nombre = nombre.toUpperCase();
        radio = $('#Rad'+i).val();
        if(radio===''){radio=0;}
        Check = $('#Chk'+i).val();
        if(Check===''){Check=0;}
        tablaLis = $('#Tlis'+i).val();
        campoLis = $('#Flis'+i).val();
        indice = $('input[name=in'+i+']:checked').val();
        if ($('input[name=in'+i+']:checked').val()) {capoIndice = Columna;} else {indice = '';}
        orden = $('input[name=or'+i+']:checked').val();
        if ($('input[name=or'+i+']:checked').val()) {campoOrden = Columna;} else {orden = '';}
        areaTex = $('input[name=tx'+i+']:checked').val();
        textArea = $('#tx'+i).val();
        valida = $('input[name=va'+i+']:checked').val();
        if ($('input[name=va'+i+']:checked').val()) { valida = $('input[name=va'+i+']:checked').val();} else {valida = '';}
        fila += '<>' + Columna + '|'+ tipo + '|' + nombre + '|' + radio +  '|'+ indice + '|' + orden + '|' + valida
                +'|' + Check + '|' + tablaLis + '|' + campoLis + '|' +textArea;     
    }
         i+=1;
    });
    fila =  usuario+'|'+lsruta+'|'+ruta+'|'+prefijo+'|'+capoIndice+'|'+campoOrden +
            '|'+tabla+ '|'+ frm + '|'+ hdr + '|'+ campoEmpresa+ '|'+ leng  + '|'+ js  + '|'+ mod  + '|'+ view + fila;
    if (capoIndice === '' ||  campoOrden === '' ||  usuario === ''){
        nota = $('#nota span').text();        
        alert (nota);
    }
    else{
alert(fila);
        if(err==''){
            $.post("includes/opcCase.php", {accion:'creaObjetos', condicion:fila}, function(data){
                alert(data);
            $('#progresos span').val(data);
            $('#progresos').show();
        });   
        }

    }
    $('#progreso').hide();
}

function lectura()
{
var xmlDoc = cargarXMLDoc('config.xml');
if (xmlDoc != null)
{

 var links_tag = xmlDoc.getElementsByTagName("parametros")[0].getElementsByTagName("parametro");

 for (var i = 0; i < links_tag.length; i++)
 {
  var llave = links_tag[i].getElementsByTagName("llave")[0].childNodes[0].nodeValue;
  var dato = links_tag[i].getElementsByTagName("dato")[0].childNodes[0].nodeValue;
  
  $('#'+llave).val(dato);
 }
}
}

function cargarXMLDoc(archivoXML) 
{
 var xmlDoc;
 if (window.XMLHttpRequest)
   {
    xmlDoc = new window.XMLHttpRequest();
    xmlDoc.open("GET", archivoXML, false);
    xmlDoc.send("");
    return xmlDoc.responseXML;
   }
 // para IE 5 y IE 6
 else if (ActiveXObject("Microsoft.XMLDOM"))
   {
    xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
    xmlDoc.async = false;
    xmlDoc.load(archivoXML);
    return xmlDoc;
   }
 alert("Error cargando el documento.");
 return null;
}
 
</script>

<script type="text/javascript">
$(document).ready(function(){
  $('#open').click(function(){
        $('#popup').fadeIn('slow');
        $('.popup-overlay').fadeIn('slow');
        $('.popup-overlay').height($(window).height());
        return false;
    });
    
    $('#close').click(function(){
        $('#popup').fadeOut('slow');
        $('.popup-overlay').fadeOut('slow');
        return false;
    });
});
</script>
</body>
</html>