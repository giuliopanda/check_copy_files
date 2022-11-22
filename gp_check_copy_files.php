<?php
/** 
 * Verifica se due cartelle sono uguali oppure elenca i file differenti.
 * Genera due file .txt con l'elenco dei file all'interno delle cartelle e il rispettivo md5.
 *
 *   # php gp_check_copy_files.php dir1 dir2   
 *   verifica due cartelle se sono uguali
 * 
 *   #  gp_check_copy_files.php path_md5_checksum1.txt path_md5_chechsum2.txt 
 *    Verifica due file checksum già generato
 * 
 *   # php gp_check_copy_files.php path1   
 *   il percorso della  cartella per cui creare il file checksum
 *   
 *   # php gp_check_copy_files.php -h 
 *   #help
 */

/**
 * --------------
 * - CONTROLLER -
 * --------------
 */ 
array_shift($argv); 
if (count($argv) == 0 || $argv[0] == "-h" || $argv[0] == "-help") {
    help_and_die();
}
if (count($argv) == 1) {
    $files = scanAllDir($argv[0]);
    $name = "md5_check".sanitize_key($argv[0])."_".date('YmdHis').".txt";
    file_put_contents(__DIR__."/".$name , implode("\n", $files));
    echo "Ho salvato il checksum: ".$name1."\n";
    echo "checksum ".$argv[0]." created\n";
} else if (count($argv) == 2 && is_file($argv[0]) && is_file($argv[1])) {
    if (md5_file($argv[0]) == md5_file($argv[1])) {
        echo "I due file  (".$argv[0] .") sono uguali\n";
    } else {
        echo_check_2_md5($argv[0], $argv[1], 'file');
        
    }
} elseif (count($argv) == 2 && is_dir($argv[0]) && is_dir($argv[1])) {
    $files = scanAllDir($argv[0]);
    $name1 = "md5_checksum.".sanitize_key($argv[0])."_".date('YmdHis')."-1.txt";
    echo "Ho salvato il primo checksum: ".$name1."\n";
    file_put_contents(__DIR__."/".$name1, implode("\n", $files));
    $files = scanAllDir($argv[1]);
    $name2 = "md5_check.".sanitize_key($argv[1])."_".date('YmdHis')."-2.txt";
    echo "Ho salvato il secondo checksum: ".$name2."\n";
    file_put_contents(__DIR__."/".$name2, implode("\n", $files));
    echo_check_2_md5($name1, $name2, 'cartelle');
}

/**
 * -------------
 * - FUNCTIONS -
 * -------------
 */


 /**
  * Verifica due checksum e se sono diversi stampa le differenze a video.
  * @param string $arg1 
  * @param string $arg2
  * @param string $what file|dir serve solo il testo da stampare
  */
function echo_check_2_md5($arg1, $arg2, $what) {
    if (md5_file(__DIR__."/".$arg1) == md5_file(__DIR__."/".$arg2)) {
        if ($what == "file") {
            echo "I due file sono uguali\n";
        } else {
            echo "Le due cartelle sono uguali\n";
        }
    } else {
        if ($what == "file") {
            echo "I due file sono diversi\n";
        } else {
            echo "# Le due cartelle sono diverse\n";
            $a = convert_checkfile_to_array(__DIR__."/".$arg1);
            $b = convert_checkfile_to_array(__DIR__."/".$arg2);
            $d = array_diff($b, $a);
            $e = array_diff($a, $b);
            $f = array_merge($d, $e);
            if (count($f) > 0) {
                echo "# Differenze nei contenuti dei file: ".count($f)." \n";
                foreach ($f as $key=>$row) {
                    echo $key."\n";
                }
            }
           
            $d = array_diff_key($b, $a);
            $e = array_diff_key($a, $b);
            $f = array_merge($d, $e);
            if (count($f) > 0) {
                echo "# Differenze nei nomi dei file: ".count($f)."\n";
                foreach ($f as $key=>$row) {
                    echo $key."\n";
                }
            }
            
            
        }
    }
}

/**
 * Stampa il primo set di commenti se non ci sono argomenti passati o se è -h o -help
 */
function help_and_die() {
    $tokens =  token_get_all(file_get_contents(__FILE__));
    echo " ------ HELP ------".PHP_EOL;
    echo " ------------------".PHP_EOL;
    foreach ($tokens as $token) {
        if (is_array($token)) {
            if ( token_name($token[0]) == 'T_DOC_COMMENT') {
                echo str_replace(['/*','*/','*'],'',$token[1]). PHP_EOL;
                break;
            }
        }
    }
    die;
}

/**
 * Legge un file di tipo checksum e ne ritorna l'array 
 * dove i singoli file sono l'indice e l'md5 è il valore
 * @param String $path
 * @return array|false
 */
function convert_checkfile_to_array($path) {
    $a = file_get_contents($path);
    $a = explode("\n", $a);
    $ris = [];
    foreach ($a as $row) {
        $temp = explode(" ", $row, 2);
        if (count ($temp) != 2) return false;
        $ris[$temp[1]] = $temp[0];
    }
    return $ris;
}


/**
 * Scansione ricorsiva di file e verifica con MD5
 * @return array
 */

function scanAllDir($dir, $base_path="/") {
  $result = [];
  foreach(scandir($dir) as $filename) {
    if ($filename[0] === '.' || $filename[0] === '..') continue;
    $filePath = $dir . '/' . $filename;
    if (is_dir($filePath)) {
      foreach (scanAllDir($filePath, $filename) as $childFilename) {
        $result[] = $childFilename;
      }
    } else {
      $md5 = md5_file( $filePath );
      $result[] = $md5 ." ". strtolower(str_replace(["\\","//"],"/", $base_path."/".$filename));
    }
  }
  return $result;
}

/**
 * pulisce una stringa dai caratteri speciali
 * @param string $name
 * @return string
 */
function sanitize_key($name) {
    return preg_replace( '/[^A-Za-z0-9\-_]/', '', $name );
}