<?php

// On enregistre notre autoload.
spl_autoload_register(function (string $class): bool {

    $base_dir = 'class/';


    $file = $base_dir . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (is_file($file)) {
        require $file;
        return true;
    }
    return false;
});




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
    <div class="nes-container is-centered is-rounded with-title">
        <h1 class="title">
            simplon-tp Fight !
        </h1>
        <p class="nes-text">
            <?= $manager->count() ?> personnages déjà dans l'arène.
        </p>
    </div>
    <br>
    <?php
    // On a un message à afficher ?
    if (isset($message)) {
        echo '<div class="nes-balloon from-left"><p class="nes-text  is-error">'
            . $message
            . '</p></div>';
    }
    // Si on utilise un personnage (nouveau ou pas).
    if (isset($perso)) {
        switch ($perso->classe()) {
            case 'Guerrier':
                $couleur_classe = 'is-error';
                break;
            case 'Archer':
                $couleur_classe = 'is-success';
                break;
            case 'Magicien':
                $couleur_classe = 'is-primary';
                break;
            default:
                $couleur_classe = 'is-warning';
                break;
        }
    ?>
        <section class="nes-container with-title is-rounded">
            <!-- <h3 class="title"> My Character </h3> -->
            <div class="title">
                <div class="nes-badge">
                    <span class="name <?= $couleur_classe ?>">
                        <?= htmlspecialchars($perso->nom()) ?>
                    </span>
                </div>
            </div>
            Dégâts : 
            <progress 
                class="nes-progress is-error is-inline" 
                value="<?= $perso->degats() ?>" 
                max="100">
            </progress>
            <ul class="nes-list is-disc">
                <li>Level :</li>
                    <?php
                for ($i = $perso->level(); $i > 0; $i--) {
                    echo '<i class="nes-icon is-medium trophy"></i>';
                }
                ?>
                <li>Force :</li>
                <?php
                for ($i = $perso->strength(); $i > 0; $i--) {
                    echo '<i class="nes-icon is-medium star"></i>';
                }
                ?>
            </ul>
            <p><a href="?deconnexion=1">Déconnexion</a></p>
        </section>

        <fieldset>
            <legend>Qui frapper ?</legend>
            <div class="nes-container">
                <?php
                $persos = $manager->getList($perso->nom());
                if (empty($persos)) {
                    echo 'Personne à frapper !';
                } else {
                    foreach ($persos as $unPerso) {
                        switch ($unPerso->classe()) {
                            case 'Guerrier':
                                $couleur_classe = 'is-error';
                                break;
                            case 'Archer':
                                $couleur_classe = 'is-success';
                                break;
                            case 'Magicien':
                                $couleur_classe = 'is-primary';
                                break;
                            default:
                                $couleur_classe = 'is-warning';
                                break;
                        }
                        echo '<a href="?frapper=' . $unPerso->id() . '">'
                            .'<div class="ennemy nes-container with-title is-rounded">'
                            . '<div class="title nes-badge">'
                            . '<span class="' . $couleur_classe . '">'
                            . htmlspecialchars($unPerso->nom())
                            . '</span></div>'

                            .'<progress '
                            .'class="nes-progress is-error is-inline" '
                            .'value="'. $unPerso->degats() .'"'
                            .'max="100">'
                            .'</progress>';

                            echo '<p>';
                            for ($i = $unPerso->level(); $i > 0; $i--) {
                                echo '<i class="nes-icon is-medium trophy"></i>';
                            }
                            echo '</p><p>';
                            for ($i = $unPerso->strength(); $i > 0; $i--) {
                                echo '<i class="nes-icon is-medium star"></i>';
                            }
                            
                            echo '</p></div></a>';
                    }
                }
                ?>
            </div>
        </fieldset>
    <?php
    }
    // Sinon on affiche le formulaire de création de personnage
    else {
    ?>
        <form action="" method="post" class="nes-container with-title is-rounded">
            <p>
                Nom : <input type="text" name="nom" maxlength="50" class="nes-input" />
                <!-- <div class="nes-select">
                    <select required id="error_select">
                        <option value="" disabled selected hidden>Select...</option>
                        <option value="0">guerrier</option>
                        <option value="1">archer</option>
                        <option value="2">magicien</option>
                    </select>
                </div> -->
            </p>
            <input type="submit" value="Guerrier" name="creer" class="nes-btn is-error" />
            <input type="submit" value="Archer" name="creer" class="nes-btn is-success" />
            <input type="submit" value="Magicien" name="creer" class="nes-btn is-primary" />
            <input type="submit" value="Déjà dans l'arène" name="utiliser" class="nes-btn is-warning" />
        </form>

    <?php } 
    // echo '<pre>'.var_export($GLOBALS, true).'</pre><hr />';
    ?>

</body>

</html>
<?php
// Si on a créé un personnage, on le stocke dans une variable session afin d'économiser une requête SQL.
if (isset($perso)) {
    $_SESSION['perso'] = $perso;
}
