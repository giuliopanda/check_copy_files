# check_copy_files
piccola utility da linea di comando per verificare con prove che due cartelle siano uguali o mostrare le differenze.
Genera due file .txt con l'elenco dei file all'interno delle cartelle e il rispettivo md5.

# Come si usa
Dalla shall digito php gp_check_copy_files.php

```txt
# php gp_check_copy_files.php dir1 dir2   
verifica due cartelle se sono uguali

# gp_check_copy_files.php path_md5_checksum1.txt path_md5_chechsum2.txt 
Verifica due file checksum già generato

# php gp_check_copy_files.php path1   
il percorso della  cartella per cui creare il file checksum

php gp_check_copy_files.php -h 
help
```
