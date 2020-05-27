<?php

class PersonnagesManager
{
    private $db; // Instance de PDO

    public function __construct($db)
    {
        $this->setDb($db);
    }

    public function create(array $donnees, string $classe): Personnage
    {
        return  new $classe($donnees);
    }

    public function add(Personnage $perso)
    {
        $q = $this->db->prepare('INSERT INTO personnages(nom, classe) VALUES(:nom, :classe)');
        $q->bindValue(':nom', $perso->nom());
        $q->bindValue(':classe', $perso->classe());
        $q->execute();

        $perso->hydrate([
            'id' => $this->db->lastInsertId(),
            'degats' => 0,
            'xp' => 0,
            'level' => 0,
            'strength' => 0,
        ]);
    }

    public function count()
    {
        return $this->db->query('SELECT COUNT(*) FROM personnages')->fetchColumn();
    }

    public function delete(Personnage $perso)
    {
        $this->db->exec('DELETE FROM personnages WHERE id = ' . $perso->id());
    }

    public function exists($info)
    {
        if (is_int($info)) // On veut voir si tel personnage ayant pour id $info existe.
        {
            return (bool) $this->db->query('SELECT COUNT(*) FROM personnages WHERE id = ' . $info)->fetchColumn();
        }

        // Sinon, c'est qu'on veut vérifier que le nom existe ou pas.

        $q = $this->db->prepare('SELECT COUNT(*) FROM personnages WHERE nom = :nom');
        $q->execute([':nom' => $info]);

        return (bool) $q->fetchColumn();
    }

    public function get($info)
    {
        if (is_int($info)) {
            $q = $this->db->query('SELECT id, nom, classe, degats, xp, level, strength FROM personnages WHERE id = ' . $info);
            // $donnees = $q->fetch(PDO::FETCH_ASSOC);

            // return new Personnage($donnees);
        } else {
            $q = $this->db->prepare('SELECT id, nom, classe, degats, xp, level, strength FROM personnages WHERE nom = :nom');
            $q->execute([':nom' => $info]);

            // return new Personnage($q->fetch(PDO::FETCH_ASSOC));
        }
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
        // echo '<pre>'.var_export($info, true).'</pre><hr />';
        // echo '<pre>'.var_export($donnees, true).'</pre><hr />';
        return $this->create($donnees, $donnees['classe']);
    }

    public function getList($nom)
    {
        $persos = [];

        $q = $this->db->prepare('SELECT id, nom, classe, degats, xp, level, strength FROM personnages WHERE nom <> :nom ORDER BY nom');
        $q->execute([':nom' => $nom]);

        while ($donnees = $q->fetch(PDO::FETCH_ASSOC)) {
            // $persos[] = new Personnage($donnees);
            $persos[] = $this->create($donnees, $donnees['classe']);
        }

        return $persos;
    }

    public function update(Personnage $perso, $strength = 0)
    {

        if ($perso->xp() >= 100) {
            $perso->setLevel(1);
            $perso->setXp(0);
            $perso->setStrength($perso->level());
        }

        $q = $this->db->prepare('UPDATE personnages SET xp = :xp, level = :level, strength = :strength, degats = :degats WHERE id = :id');

        $q->bindValue(':xp', $perso->xp(), PDO::PARAM_INT);
        $q->bindValue(':degats', ($perso->degats() + $strength), PDO::PARAM_INT);
        $q->bindValue(':level', $perso->level(), PDO::PARAM_INT);
        $q->bindValue(':strength', $perso->strength(), PDO::PARAM_INT);
        $q->bindValue(':id', $perso->id(), PDO::PARAM_INT);

        $q->execute();
    }

    public function setDb(PDO $db)
    {
        $this->db = $db;
    }
}
