<?php
/**
 * Update
 *
 * @package WikiDocs
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    https://github.com/Zavy86/wikidocs
 */

 // include functions
 require_once("functions.inc.php");
 // mode definition
 define("MODE","engine");
 // check authentication
 if(wdf_authenticated()!=2){
  wdf_dump("Login notwendig!",null,null,true);
  die();
 }
 // check for git
 if(is_dir(DIR.".git")){
  wdf_dump("Update per git!",null,null,true);
  // check for localhost
  if(in_array($_SERVER['HTTP_HOST'],array("localhost","127.0.0.1"))){
   wdf_dump("Update per git nicht m&ouml;glich!",null,null,true);
   die();
  }else{
   // make command
   $command="cd ".DIR." ; pwd ; git stash 2>&1 ; git stash clear ; git pull 2>&1 ; chmod 755 -R ./";
   // exec shell commands
   $shell_output=exec('whoami')."@".exec('hostname').":".shell_exec($command);
   // debug
   wdf_dump($shell_output,$command,null,true);
  }
 }else{
  // zip based update
  wdf_dump("Update per zip!",null,null,true);
  $update=file_get_contents("https://github.com/youmakemyday/WikiDocs/archive/master.zip");
  $bytes=file_put_contents(DIR."update.zip",$update);
  if($bytes>0){
   wdf_dump("Download des Updates erfolgreich!",null,null,true);
  }else{
   wdf_dump("Download des Updates fehlgeschlagen!",null,null,true);
  }
  // backup current version
  $backup_name=date("Ymd_His").".zip";
  $command="cd ".DIR." ; pwd ; zip -r ./backups/".$backup_name." ./documents";
  $shell_output=exec('whoami')."@".exec('hostname').":".shell_exec($command);
  wdf_dump($shell_output,$command,null,true);
  // check if previous update was deleted
  if(file_exists(DIR."backups/".$backup_name)){wdf_dump("Backup erstellt!",null,null,true);}
  else{die("Fehler beim Erstellen des Backups aufgetreten!");}
  // check for old update directory
  if(is_dir(DIR."update")){
   // delete previous update directories
   $command="cd ".DIR." ; pwd ; rm -R ./update";
   $shell_output=exec('whoami')."@".exec('hostname').":".shell_exec($command);
   wdf_dump($shell_output,$command,null,true);
   // check if previous update was deleted
   if(is_dir(DIR."update")){wdf_dump("Letzes Update entfernt!",null,null,true);}
   else{die("Fehler beim Entfernen des letzten Updates aufgetreten");}
  }
  // unzip update
  $command="cd ".DIR." ; pwd ; unzip update.zip -d ./update";
  $shell_output=exec('whoami')."@".exec('hostname').":".shell_exec($command);
  wdf_dump($shell_output,$command,null,true);
  // check for new version update
  if(file_exists(DIR."update/WikiDocs-master/VERSION.txt")){
   wdf_dump("Update entpackt!",null,null,true);
  }else{
   die("An error occured unzipping the update!");
  }
  // copy configuration into update
  $command="cd ".DIR." ; pwd ; cp ./config.inc.php ./update/WikiDocs-master/config.inc.php";
  $shell_output=exec('whoami')."@".exec('hostname').":".shell_exec($command);
  wdf_dump($shell_output,$command,null,true);
  // check for configuration
  if(file_exists(DIR."update/WikiDocs-master/config.inc.php")){wdf_dump("config.inc.php copied!",null,null,true);}
  else{die("Fehler beim Kopieren der Konfiguration aufgetreten!");}
  // copy .htaccess into update
  $command="cd ".DIR." ; pwd ; cp ./.htaccess ./update/WikiDocs-master/.htaccess";
  $shell_output=exec('whoami')."@".exec('hostname').":".shell_exec($command);
  wdf_dump($shell_output,$command,null,true);
  // check for htaccess
  if(file_exists(DIR."update/WikiDocs-master/.htaccess")){wdf_dump(".htaccess kopiert!",null,null,true);}
  else{die("Fehler beim Kopieren der .htaccess aufgetreten!");}
  // copy documents into update
  $command="cd ".DIR." ; pwd ; cp -R ./documents ./update/WikiDocs-master/";
  $shell_output=exec('whoami')."@".exec('hostname').":".shell_exec($command);
  wdf_dump($shell_output,$command,null,true);
  // check for htaccess
  if(file_exists(DIR."update/WikiDocs-master/documents/homepage/content.md")){wdf_dump("Inhalte erfolgreich kopiert!",null,null,true);}
  else{die("Fehler beim Kopieren der Inhalte aufgetreten!");}
  // overwrite with update
  $command="cd ".DIR." ; pwd ; cp -R ./update/WikiDocs-master/* ./";
  $shell_output=exec('whoami')."@".exec('hostname').":".shell_exec($command);
  wdf_dump($shell_output,$command,null,true);
  // check for update
  if(file_exists(DIR."VERSION.txt")){wdf_dump("Update &uuml;berschrieben",null,null,true);}
  else{die("Fehler beim Aktualisieren aufgetreten!");}
  // delete update directory
  $command="cd ".DIR." ; pwd ; rm -R ./update";
  $shell_output=exec('whoami')."@".exec('hostname').":".shell_exec($command);
  wdf_dump($shell_output,$command,null,true);
  // check if previous update was deleted
  if(!is_dir(DIR."update")){wdf_dump("Update Verzeichnis geleert!",null,null,true);}
  else{die("Fehler beim Leeren des Update-Verzeichnisses aufgetreten!");}
  // delete update zip
  $command="cd ".DIR." ; pwd ; rm ./update.zip";
  $shell_output=exec('whoami')."@".exec('hostname').":".shell_exec($command);
  wdf_dump($shell_output,$command,null,true);
  // check if previous update was deleted
  if(!file_exists(DIR."update.zip")){wdf_dump("Update zip entfernt!",null,null,true);}
  else{die("Es ist ein Fehler beim Entfernen der Update zip aufgetreten");}
 }
?>
