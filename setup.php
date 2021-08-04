<?php
/**
 * Setup
 *
 * @package WikiDocs
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    https://github.com/Zavy86/wikidocs
 */

 // initialize session
 session_start();
 // errors configuration
 error_reporting(E_ALL & ~E_NOTICE);
 ini_set("display_errors",true);
 // definitions
 $errors=false;
 $checks_array=array();
 // acquire variables
 $g_act=$_GET['act'];
 if(!$g_act){$g_act="setup";}
 // include coonfiguration sample
 include("config.sample.inc.php");
 // defines constants
 define('PATH_URI',explode("setup.php",$_SERVER['REQUEST_URI'])[0]);
 // die if configuration already exist
 if(file_exists("config.inc.php")){die("Wiki|Docs is already configured..");}
 // make root dir from given path
 $original_dir=str_replace("\\","/",realpath(dirname(__FILE__))."/");
 $root_dir=substr($original_dir,0,strrpos($original_dir,(string)$_POST['path'])).$_POST['path'];
 // check action
 if($g_act=="check"){
  // reset session setup
  $_SESSION['wikidocs']['setup']=null;
  // check setup
  if(file_exists($root_dir."setup.php")){$checks_array['path']=true;}else{$checks_array['path']=false;$errors=true;}
  if(strlen($_POST['title'])){$checks_array['title']=true;}else{$checks_array['title']=false;$errors=true;}
  if(strlen($_POST['subtitle'])){$checks_array['subtitle']=true;}else{$checks_array['subtitle']=false;$errors=true;}
  if(strlen($_POST['owner'])){$checks_array['owner']=true;}else{$checks_array['owner']=false;$errors=true;}
  if(strlen($_POST['notice'])){$checks_array['notice']=true;}else{$checks_array['notice']=false;$errors=true;}
  if(strlen($_POST['editcode'])){$checks_array['editcode']=true;}else{$checks_array['editcode']=false;$errors=true;}
  if(strlen($_POST['color'])==7 && substr($_POST['color'],0,1)=="#"){$checks_array['color']=true;}else{$checks_array['color']=false;$errors=true;}
  // set session setup
  if(!$errors){$_SESSION['wikidocs']['setup']=$_POST;}
 }
 // conclude action
 if($g_act=="conclude"){
  // build configuration file
  $config="<?php\n";
  $config.="define(\"PATH\",\"".$_SESSION['wikidocs']['setup']['path']."\");\n";
  $config.="define(\"TITLE\",\"".$_SESSION['wikidocs']['setup']['title']."\");\n";
  $config.="define(\"SUBTITLE\",\"".$_SESSION['wikidocs']['setup']['subtitle']."\");\n";
  $config.="define(\"OWNER\",\"".$_SESSION['wikidocs']['setup']['owner']."\");\n";
  $config.="define(\"NOTICE\",\"".$_SESSION['wikidocs']['setup']['notice']."\");\n";
  $config.="define(\"EDITCODE\",\"".md5($_SESSION['wikidocs']['setup']['editcode'])."\");\n";
  $config.="define(\"VIEWCODE\",".($_SESSION['wikidocs']['setup']['viewcode']?"\"".md5($_SESSION['wikidocs']['setup']['viewcode'])."\"":"null").");\n";
  $config.="define(\"COLOR\",\"".$_SESSION['wikidocs']['setup']['color']."\");\n";
  $config.="define(\"DARK\",".(isset($_SESSION['wikidocs']['setup']['dark'])?"true":"false").");\n";
  $config.="define(\"GTAG\",".($_SESSION['wikidocs']['setup']['gtag']?"\"".$_SESSION['wikidocs']['setup']['gtag']."\"":"null").");\n";
  $config.="?>\n";
  // write configuration file
  file_put_contents($root_dir."config.inc.php",$config);
  // build htacess file
  $htaccess="<IfModule mod_rewrite.c>\n";
  $htaccess.="RewriteEngine On\n";
  $htaccess.="RewriteBase ".$_SESSION['wikidocs']['setup']['path']."\n";
  $htaccess.="RewriteCond %{REQUEST_FILENAME} !-f\n";
  $htaccess.="RewriteRule ^(.*)$ index.php?doc=$1 [NC,L,QSA]\n";
  $htaccess.="</IfModule>\n";
  // write htaccess
  file_put_contents($root_dir.".htaccess",$htaccess);
  // check for configuration and htacess files
  if(file_exists($root_dir."config.inc.php") && file_exists($root_dir.".htaccess")){$configured=true;}else{$configured=false;}
  // make default homepage if not exist
  if(!file_exists($root_dir."documents/homepage/content.md")){
   // check for directory or make it
   if(!is_dir($root_dir."documents/homepage")){mkdir($root_dir."documents/homepage",0755,true);}
   // copy readme as default homepage content
   copy($root_dir."README.md",$root_dir."documents/homepage/content.md");
  }
 }
?>
<!DOCTYPE html>
<html>
 <head>
  <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" media="screen,projection"/>
  <link type="text/css" rel="stylesheet" href="helpers/materialize-1.0.0/css/materialize.min.css" media="screen,projection"/>
  <link type="text/css" rel="stylesheet" href="css/styles.css" media="screen,projection"/>
  <link  type="image/png" rel="icon" href="images/favicon.png" sizes="any"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="theme-color" content="#4CAF50">
  <style>:root{--theme-color:#4CAF50;}</style>
  <title>Setup - Wiki|Docs</title>
 </head>
 <body>
  <div class="container">
   <div class="row">
    <div class="col s12">
     <h1>Wiki|Docs</h1>
     <p>Just a databaseless markdown flat-file wiki engine..</p>
    </div><!-- /col -->
<?php if($g_act=="setup"){ ?>
    <div class="col s12">
     <h2>Configuration</h2>
     <p>Setup your wiki engine..</p>
     <form action="setup.php?act=check" method="post">
      <div class="row">
       <div class="input-field col s12">
         <input type="text" name="path" class="validate" value="<?php echo PATH_URI; ?>" required>
         <label for="path"><span class="green-text">Pfad</span></label>
       </div>
      </div>
      <div class="row">
       <div class="input-field col s12 m5">
         <input type="text" name="title" class="validate" value="<?php echo TITLE; ?>" required>
         <label for="title"><span class="green-text">Titel</span></label>
       </div>
       <div class="input-field col s12 m7">
         <input type="text" name="subtitle" class="validate" value="<?php echo SUBTITLE; ?>" required>
         <label for="subtitle"><span class="green-text">Untertitel</span></label>
       </div>
      </div>
      <div class="row">
       <div class="input-field col s12 m5">
         <input type="text" name="owner" class="validate" placeholder="Betreiber" required>
         <label for="title"><span class="green-text">Betreiber</span></label>
       </div>
       <div class="input-field col s12 m7">
         <input type="text" name="notice" class="validate" placeholder="Copyright Hinweis" required>
         <label for="subtitle"><span class="green-text">Copyright Hinweis</span></label>
       </div>
      </div>
      <div class="row">
       <div class="input-field col s12 m5">
         <input type="text" name="editcode" class="validate" placeholder="w&auml;hle einen starken Login-Code .." required>
         <label for="subtitle"><span class="green-text">Login-Code eingeben</span></label>
       </div>
       <div class="input-field col s12 m7">
         <input type="text" name="viewcode" class="validate" placeholder="leer lassen, wenn der Inhalt &ouml;ffentlich sichtbar sein soll">
         <label for="title"><span class="green-text">Login-Code f&uuml;r Betrachter</span></label>
       </div>
      </div>
      <div class="row">
       <div class="input-field col s6 m3">
         <input type="text" name="color" class="validate" placeholder="Farbgestaltung w&auml;hlen.. (#4CAF50)" value="#4CAF50" required>
         <label for="subtitle"><span class="green-text">Farbe</span></label>
       </div>
       <div class="input-field col s6 m2">
        <label for="check-dark">
         <input type="checkbox" name="dark" id="check-dark">
         <span class="black-text">Dark Mode</span>
        </label>
       </div>
       <div class="input-field col s12 m7">
         <input type="text" name="gtag" class="validate" placeholder="Google Analytics tag.. (z.B UA-123456789-1)">
         <label for="subtitle"><span class="green-text">Google Analytics tag</span></label>
       </div>
      </div>
      <div class="row">
       <div class="input-field col s12 m12">
        <button type="submit" class="btn btn-block waves-effect waves-light green right">weiter<i class="material-icons right">keyboard_arrow_right</i></button>
       </div>
      </div>
     </form>
    </div><!-- /col -->
<?php
 }
 if($g_act=="check"){
  // define checks
  $check_ok="<span class='secondary-content'><i class='material-icons green-text'>check_circle</i></span>";
  $check_ko="<span class='secondary-content'><i class='material-icons red-text'>cancel</i></span>";
?>
    <div class="col s12">
     <h2>Konfiguration</h2>
     <p>Konfiguration wurde best&auml;tigt...</p>
     <ul class="collection">
      <li class="collection-item"><div>PATH: <?php echo $_POST['path'].($checks_array['path']?$check_ok:$check_ko); ?></div></li>
      <li class="collection-item"><div>TITLE: <?php echo $_POST['title'].($checks_array['title']?$check_ok:$check_ko); ?></div></li>
      <li class="collection-item"><div>SUBTITLE: <?php echo $_POST['subtitle'].($checks_array['subtitle']?$check_ok:$check_ko); ?></div></li>
      <li class="collection-item"><div>OWNER: <?php echo $_POST['owner'].($checks_array['owner']?$check_ok:$check_ko); ?></div></li>
      <li class="collection-item"><div>NOTICE: <?php echo $_POST['notice'].($checks_array['notice']?$check_ok:$check_ko); ?></div></li>
      <li class="collection-item"><div>EDITCODE: <?php echo $_POST['editcode'].($checks_array['editcode']?$check_ok:$check_ko); ?></div></li>
      <li class="collection-item"><div>VIEWCODE: <?php echo ($_POST['viewcode']?$_POST['viewcode']:"PUBLIC").$check_ok; ?></div></li>
      <li class="collection-item"><div>COLOR: <?php echo $_POST['color'].($checks_array['color']?$check_ok:$check_ko); ?></div></li>
      <li class="collection-item"><div>DARK: <?php echo (strlen($_POST['dark'])?"true":"false").$check_ok; ?></div></li>
      <li class="collection-item"><div>GTAG: <?php echo ($_POST['gtag']?$_POST['gtag']:null).$check_ok; ?></div></li>
     </ul>
     <div class="input-field col s12">
<?php if($errors){ ?>
      <button onClick="javascript:window.history.back();" class="btn btn-block waves-effect waves-light green lighten-2">Konfiguration bearbeiten<i class="material-icons left">keyboard_arrow_left</i></button>
<?php }else{ ?>
       <a href="setup.php?act=conclude" class="waves-effect waves-light btn green white-text right">weiter<i class="material-icons right">keyboard_arrow_right</i></a>
<?php } ?>
     </div>
    </div><!-- /col -->
<?php
 }
 if($g_act=="conclude"){
?>
    <div class="col s12">
     <h2>Konfiguration gespeichert</h2>
<?php if($configured){ ?>
     <p>Deine Konfiguration wurde gespeichert..</p>
     <p><a href="<?php echo $_SESSION['wikidocs']['setup']['path']; ?>">weiter</a> zum wiki!</p>
     <i class="material-icons small green-text">check_circle</i>
<?php }else{ ?>
     <p class="red-text">Ein Fehler ist beim Speichern der Konfiguration aufgetreten!</p>
     <i class="material-icons small red-text">cancel</i>
<?php } ?>
<?php } ?>
   </div><!-- /row-->
  </div><!-- /container-->
  <script type="text/javascript" src="helpers/jquery-3.3.1/js/jquery.min.js"></script>
  <script type="text/javascript" src="helpers/materialize-1.0.0/js/materialize.min.js"></script>
 </body>
</html>
