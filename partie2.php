<?php
echo "2.1 Constructeur avec valeurs par défaut" . PHP_EOL;

class Voiture
{
    public string $couleur;
    public int $vitesse;

    public function __construct(string $couleur = "Blanche", int $vitesse = 0)
    {
        $this->couleur = $couleur;
        $this->vitesse = $vitesse;
    }

    public function afficher(): void
    {
        echo "Couleur : {$this->couleur} | Vitesse : {$this->vitesse} km/h" . PHP_EOL;
    }
}

$v1 = new Voiture();                
$v2 = new Voiture("Rouge", 50);    
$v1->afficher();
$v2->afficher();

echo PHP_EOL . "Test 2.1.a:" . PHP_EOL;

$v3 = new Voiture("Noire");        
try {
    $v4 = new Voiture(120);       
} catch (TypeError $e) {
    echo "TypeError attendu: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "Test 2.1.b:" . PHP_EOL;
class VoiturePromo
{
    public function __construct(
        public string $couleur = "Blanche",
        public int $vitesse = 0
    ) {}

    public function afficher(): void
    {
        echo "Couleur : {$this->couleur} | Vitesse : {$this->vitesse} km/h" . PHP_EOL;
    }
}

$vp = new VoiturePromo("Verte", 90);
$vp->afficher();

echo PHP_EOL . "2.2 Le piège des deux constructeurs " . PHP_EOL;
echo "2.2.b" . PHP_EOL;

class Personne
{
    public function __construct(
        private string $nom = "",
        private string $prenom = ""
    ) {}

    public function presentation(): string
    {
        return "Personne : " . $this->prenom . " " . $this->nom;
    }
}

$p1 = new Personne();                    // création « vide »
$p2 = new Personne("Hachimi", "Sara");   // création avec informations
echo $p1->presentation() . PHP_EOL;
echo $p2->presentation() . PHP_EOL;

// ----- 2.2.c : BONUS – méthode statique de fabrique (named constructor) -----
echo PHP_EOL . "2.2.c" . PHP_EOL;

class PersonneFabrique
{
    private function __construct(
        private string $nom = "",
        private string $prenom = ""
    ) {}

    public static function depuisTableau(array $data): self
    {
        return new self($data['nom'] ?? "", $data['prenom'] ?? "");
    }

    public static function vide(): self
    {
        return new self();
    }

    public function presentation(): string
    {
        return "Personne : " . $this->prenom . " " . $this->nom;
    }
}

$p3 = PersonneFabrique::depuisTableau(['nom' => 'Alaoui', 'prenom' => 'Omar']);
$p4 = PersonneFabrique::vide();
echo $p3->presentation() . PHP_EOL;
echo $p4->presentation() . PHP_EOL;


echo PHP_EOL . "2.3 Le destructeur" . PHP_EOL;

class VoitureDestruct
{
    public function __construct(public string $couleur)
    {
        echo "Voiture {$this->couleur} créée." . PHP_EOL;
    }

    public function __destruct()
    {
        echo "Voiture {$this->couleur} détruite." . PHP_EOL;
    }
}

echo "2.3.a" . PHP_EOL;
$maVoiture = new VoitureDestruct("Blanche");
unset($maVoiture);          
echo "Fin du script." . PHP_EOL;



echo PHP_EOL . "2.3.b" . PHP_EOL;
$voitureSansUnset = new VoitureDestruct("Bleue");
echo "Fin du script." . PHP_EOL;




echo PHP_EOL . "2.3.c" . PHP_EOL;
$a = new VoitureDestruct("Rouge");   
$b = new VoitureDestruct("Noire");   
echo "Fin du script." . PHP_EOL;
class PersonneDestruct
{   
    public function __construct(private string $nom)
    {
        echo "Création de " . $this->nom . " (" . __CLASS__ . ")" . PHP_EOL;
    }

    public function __destruct()
    {
        echo "Détruire " . __CLASS__ . PHP_EOL;   
    }
}
echo PHP_EOL . "2.3.d" . PHP_EOL;
$pd = new PersonneDestruct("Sara");
unset($pd);
