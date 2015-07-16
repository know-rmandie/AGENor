<?php
/******************************************************************************/
/*                                                                            */
/*                       __        ____                                       */
/*                 ___  / /  ___  / __/__  __ _____________ ___               */
/*                / _ \/ _ \/ _ \_\ \/ _ \/ // / __/ __/ -_|_-<               */
/*               / .__/_//_/ .__/___/\___/\_,_/_/  \__/\__/___/               */
/*              /_/       /_/                                                 */
/*                                                                            */
/*                                                                            */
/******************************************************************************/
/*                                                                            */
/* Titre          : Case à cocher, formulaire complet avec récupération des...*/
/*                                                                            */
/* URL            : http://www.phpsources.org/scripts102-PHP.htm              */
/* Auteur         : PHP Sources                                               */
/* Date édition   : 04 Déc 2004                                               */
/*                                                                            */
/******************************************************************************/


if ($_POST['envoie']) {

echo $_POST['id_1'].'<br />';
echo $_POST['id_2'].'<br />';
echo $_POST['id_3'].'<br />';

// ou

print_r($_POST);

      }
?>



<form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="post">
<input type="checkbox" name="id_1" value="1" <?php if ($_POST['id_1']) echo 
'checked="checked"'; ?>>1<br />
<input type="checkbox" name="id_2" value="2" <?php if ($_POST['id_2']) echo 
'checked="checked"'; ?>>2<br />
<input type="checkbox" name="id_3" value="3" <?php if ($_POST['id_3']) echo 
'checked="checked"'; ?>>3<br />
<input type="submit" name="envoie" value="Envoyer">
</form>
