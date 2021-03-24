<html>
<head>
<title>CASE VUE</title>
<link href="css/estilo.css" rel="stylesheet" type="text/css"/>
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<script src="js/jquery-1.12.0.js" type="text/javascript"></script>
</head>
<?php include_once 'idiomaENG.php'; $leng=''; ?>
<body>
<div>  <h4 class="tit"><?php echo $titGeneral ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $version ?></strong>
      </h4>
</div>
<div id="main" class="col-sm-12">

    <form name="Cabecera"  method="POST">

   
        <div class="row">
            <div class="col-sm-3"><label><label><?php echo $baseDatos ?>&nbsp;:</label> </div>
            <div class="col-sm-4"><span id="ddlBases"></span></div>
        </div>
        
        <div class="row" id="tablas" style="display: none">
            <div class="col-sm-3"><label><label><?php echo $tabla ?>&nbsp;:</label> </div>
            <div class="col-sm-4"><span id="ddlTabla"></span></div>
        </div>        
        
        <div id="columnas" style="display: none">
            <label><?php echo $columnas ?></label>                   
            <div id="detColumnn">           
            </div>             
        </div> 

        <div class="row">
            <div class="col-sm-3"><label><label><?php echo $prefijo ?>&nbsp;:</label> </div>
            <div class="col-sm-4"><input style=" width: 300" type="text" id="prefijo" value="pr_"></input></div>
        </div>        

        <div class="row">
            <div class="col-sm-3"><label><label><?php echo $usuario ?>&nbsp;:</label> </div>
            <div class="col-sm-4"><input style=" width: 300" type="text" id="user"></input></div>
        </div> 
        <div class="row">
            <div class="col-sm-3"><label><label><?php echo $proyecto ?>&nbsp;:</label> </div>
            <div class="col-sm-4"><input style=" width: 300" type="text" id="proyecto" value="test"></input></div>
        </div>

        <div class="row">
            <div class="col-sm-3"><label><label><?php echo $descarga ?>&nbsp;:</label> </div>
            <div class="col-sm-4"><input style=" width: 300" type="text" id="ruta"></input></div>
        </div>         
        
        <div class="row">
            <div class="col-sm-3"><label><label><?php echo $ruta  ?>&nbsp;:</label> </div>
            <div class="col-sm-4"><input style=" width: 300" type="text" id="lsruta"></input></div>
        </div>         
        
        <br />
        <div>
            <a onclick="procesar();" class="button"><img src="img/aprobar.png" alt="procesar" title="Procesa petición" /><?php echo $procesa ?></a>
            <a href="#ayudas" cass="fa fa-question button" id="open" ><?php echo $ayudas ?> </a>
              
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
        echo ' <input   type="text" id="base" value = ""></input>'; 
        echo '  </div></div>';
       ?>
    </form>
</div>
<div id="popup" style="display: none;">
    <div class="content-popup">
        <div class="close"><a href="#" id="close"><img src="img/close.gif"/></a></div>
        <div>
           <h2><?php echo $lecturas ?></h2>
           <a href="Doc01.pdf"><?php echo $manual?></a>
           <p class="cierraText">Seg&uacute;n su navegador, abre o descarga el texto</p>
           <p class="cierraText">Para cerrar la lectura utilice el regreso del navegador</p>
        </div>
    </div>
</div>

<script type="text/javascript"> 
    $('document').ready(function(){          
        llenaComboBaseDatos(); 
        lectura();
    }); 

function esFrm(){
  $("#hdrsi").prop("checked", true); 
  $("#expno").prop("checked", true); 
}

function esCrud(){
  $("#hdrno").prop("checked", true); 
  $("#expsi").prop("checked", true); 
}
  
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
    $('#base').val(tipo);
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
    frm = 'cr';
    hdr = 'no';
    exp = 'si';
    tabla =  $("#selTabla option:selected" ).text();
    usuario = $('#user').val();
    proyecto = $('#proyecto').val()
    ruta = $('#ruta').val()
    proyecto = $('#proyecto').val()
    lsruta = $('#lsruta').val()
    leng  = $('#leng').val() 
    js   = $('#js').val()
    mod  = $('#mod').val() 
    view = $('#view').val()
    base = $('#base').val();
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
                +'|' + Check + '|' + tablaLis + '|' + campoLis + '|' +textArea ;     
    }
         i+=1;
    });
    fila =  usuario+'|'+lsruta+'|'+ruta+'|'+prefijo+'|'+capoIndice+'|'+campoOrden +
            '|'+tabla+ '|'+ frm + '|'+ hdr + '|'+ campoEmpresa+ '|'+ leng  +
            '|'+ js  + '|'+ mod  + '|'+ view + '|'+ exp + '|'+ base + '|' + proyecto + '|' + fila;
    if (capoIndice === '' ||  campoOrden === '' ||  usuario === ''){
        nota = $('#nota span').text();        
        alert (nota);
    }
    else{
        if(err==''){
            alert(fila);
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