<?php


class Personne
{
    public function __construct(
        private string $nom = "",
        private string $prenom = "",
        private string $dateNaissance = ""
    ) {}

    private function getAge(): int
    {
        $naissance  = new DateTime($this->dateNaissance);
        $aujourdhui = new DateTime();
        return $naissance->diff($aujourdhui)->y;
    }

    public function __toString(): string
    {
        return "{$this->prenom} {$this->nom} ({$this->getAge()} ans)";
    }
}

$p = new Personne("Hachimi", "Sara", "2000-05-14");
echo $p . PHP_EOL;                

echo "Fiche : " . $p . PHP_EOL;            
$entete = "Étudiante : $p";                
echo $entete . PHP_EOL;

echo PHP_EOL . "--- Test 4.2 : __toString() qui retourne un int ---" . PHP_EOL;

class PersonneEntier
{
    public function __toString(): string
    {
        return 42;      
    }
}

echo "Mode faible : " . new PersonneEntier() . PHP_EOL;
