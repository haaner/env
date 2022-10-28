# Env

Eine PHP-Klasse zum Einlesen von *.env.dist* bzw. *.env* - Dateien. Sobald auf eine Konstante bzw. Methode der Klasse zugegriffen wird, werden die Umgebungsvariable initial aus den Dateien eingelesen und entsprechende PHP-defines deklariert.

Damit diese Klasse verwendet werden kann, muss nachfolgende Definition gesetzt worden sein:
 ```
define('PROJECT_PATH', '/var/www/project/');
```