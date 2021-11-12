README.txt for Jn Pro module
---------------------------

CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation


 INTRODUCTION
 ------------
  - This is a Custom module to add content type and custom block for QR code.


 REQUIREMENTS
 ------------
  - Install codeitnowin/barcode package from packagist.org
   using: composer require codeitnowin/barcode.


 INSTALLATION
 ------------

  - Install the Jn Pro module as you would normally install a
  contributed Drupal module. Visit https://www.drupal.org/node/1897420 for
  further information.

  - Add the below snippet in your main composer.json file.

      "repositories": [
              {
                  "type": "vcs",
                  "url": "git@github.com:saurabhshu/jn_pro"
              }
          ],
 Run the command-
 composer require drupal/jn_pro:dev-main
 to download the module and it's dependencies.
