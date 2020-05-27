<?php

class Archer extends Personnage
{
    public function __construct(array $donnees)
    {
        parent::__construct($donnees);
        parent::setClasse('Archer');
    }

    public function frapper(Personnage $perso)
    {
        if ($perso->id() == $this->id()) {
            return self::CEST_MOI;
        }

        $this->xp += 25;

        if($perso->classe() === 'Guerrier') 
        {
            $degats = (5 + $this->strength()) * 2;
        } else 
        {
            $degats = 5 + $this->strength();
        }
        // On indique au personnage qu'il doit recevoir des dégâts.
        // Puis on retourne la valeur renvoyée par la méthode : self::PERSONNAGE_TUE ou self::PERSONNAGE_FRAPPE
        return $perso->recevoirDegats($degats);
    }
}
