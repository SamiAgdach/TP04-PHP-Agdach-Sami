<?php
declare(strict_types=1);
class Voiture
{
    public $marque;
    public $couleur;
    public function setMarque($m)
    {
        $this->marque = $m;
    }
    public function setCouleur($c)
    {
        $this->couleur = $c;
    }
    public function afficher()
    {
        echo "Marque : " . $this->marque . " | Couleur : " . $this->couleur . PHP_EOL;
    }
}

$v1 = new Voiture();
$v1->setMarque("Toyota");
$v1->setCouleur("Bleue");
$v1->afficher();

$v2 = new Voiture();
$v2->setMarque("Renault");
$v2->setCouleur("Grise");
$v2->afficher();

echo PHP_EOL . "Test" . PHP_EOL;
echo '$v1->marque = ' . $v1->marque . PHP_EOL;
echo '$v2->marque = ' . $v2->marque . PHP_EOL;
?>