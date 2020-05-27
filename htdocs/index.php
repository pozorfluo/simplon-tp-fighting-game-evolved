<?php

// On enregistre notre autoload.
/* function chargerClasse($classname) {
    require $classname.'.php';
  }

  spl_autoload_register('chargerClasse');*/




// On fait appel à la classe Personnage
require 'class/Personnage.php';
// On fait appel à la classe PersonnagesManager
require 'class/PersonnagesManager.php';

session_start(); // On appelle session_start() 

if (isset($_GET['deconnexion'])) {
    session_destroy();
    header('Location: .');
    exit();
}

// On fait appel à la connexion à la bdd
require 'config/init.php';

// On fait appel à le code métier
require 'combat.php';
?>
<!DOCTYPE html>
<html>

<head>
<link href="https://fonts.googleapis.com/css?family=Press+Start+2P" rel="stylesheet">
    <link href="https://unpkg.com/nes.css@2.3.0/css/nes.min.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
    <title>🥋Vs🥋 Fight ! </title>

    <meta charset="utf-8" />
</head>

<body>
    <p>Nombre de personnages créés : <?= $manager->count() ?></p>
    <?php
    // On a un message à afficher ?
    if (isset($message)) {
        echo '<b>', $message, '</b>'; // Si oui, on l'affiche.
    }
    // Si on utilise un personnage (nouveau ou pas).
    if (isset($perso)) {
    ?>
        <p><a href="?deconnexion=1">Déconnexion</a></p>

        <fieldset class="nes-field">
            <legend>Mes informations</legend>
            <p class="nes-input is-success">
                Nom : <?= htmlspecialchars($perso->nom()) ?><br />
                Dégâts : <?= $perso->degats() ?><br />
                Level : <?= $perso->level() ?><br />
                Force : <?= $perso->strength() ?><br />

            </p>
        </fieldset>

        <fieldset>
            <legend>Qui frapper ?</legend>
            <p>
                <?php
                $persos = $manager->getList($perso->nom());
                if (empty($persos)) {
                    echo 'Personne à frapper !';
                } else {
                    foreach ($persos as $unPerso) {
                        echo '<a href="?frapper=', $unPerso->id(), '">',
                            htmlspecialchars($unPerso->nom()),
                            '</a> (dégâts : ',
                            $unPerso->degats(),
                            ' level : ',
                            htmlspecialchars($unPerso->level()),
                            ' force : ',
                            htmlspecialchars($unPerso->strength()),
                            ')<br />';
                    }
                }
                ?>
            </p>
        </fieldset>
    <?php
    }
    // Sinon on affiche le formulaire de création de personnage
    else {
    ?>
        <form action="" method="post">
            <p>
                Nom : <input type="text" name="nom" maxlength="50" />
                <input type="submit" value="Créer ce personnage" name="creer" />
                <input type="submit" value="Utiliser ce personnage" name="utiliser" />
            </p>
        </form>

    <?php } ?>

</body>

</html>
<?php
// Si on a créé un personnage, on le stocke dans une variable session afin d'économiser une requête SQL.
if (isset($perso)) {
    $_SESSION['perso'] = $perso;
}
