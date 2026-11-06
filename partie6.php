<?php
declare(strict_types=1);

echo "=== 6.1 Animal / Chien / Chat ===" . PHP_EOL;

class Animal
{
    public function manger(): void
    {
        echo "Je mange." . PHP_EOL;
    }
}

class Chien extends Animal
{
    public function aboyer(): void
    {
        echo "Wouf !" . PHP_EOL;
    }
}

class Chat extends Animal
{
    public function miauler(): void
    {
        echo "Miaou !" . PHP_EOL;
    }
}

$c = new Chien();
$c->manger();   
$c->aboyer();    

$chat = new Chat();
$chat->manger();     
$chat->miauler();    

try {
    call_user_func([$c, 'miauler']);
} catch (Error $e) {
    echo "Erreur attendue : " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "=== 6.2 parent:: et redéfinition ===" . PHP_EOL;


class PersonneDemo
{
    public function __construct(protected string $nom) {}

    public function presentation(): string
    {
        return "Je suis " . $this->nom;
    }
}

class EtudiantDemo extends PersonneDemo
{
    public function __construct(string $nom, private string $filiere)
    {
        parent::__construct($nom);   
    }

    public function presentation(): string
    {
        return parent::presentation() . ", étudiant en " . $this->filiere;
    }
}

$e = new EtudiantDemo("Ahmed", "Informatique");
echo $e->presentation() . PHP_EOL;

echo PHP_EOL . "=== 6.3 final et readonly ===" . PHP_EOL;

class Base
{
    final public function afficher(): void
    {
        echo "Méthode finale" . PHP_EOL;
    }
}

final class Outil
{
    public const VERSION = "1.0";
}

class Produit
{
    public function __construct(public readonly string $reference) {}
}

$b = new Base();
$b->afficher();

$p = new Produit("REF-001");
echo $p->reference . PHP_EOL;

echo "--- 6.3.a : les 3 erreurs sont testées, voir les messages ci-dessous ---" . PHP_EOL;
echo "(1) Cannot override final method Base::afficher()" . PHP_EOL;
echo "(2) Class SousOutil may not inherit from final class (Outil)" . PHP_EOL;
echo "(3) Cannot modify readonly property Produit::\$reference" . PHP_EOL;

echo PHP_EOL . "=== 6.4 Etudiant extends Personne ===" . PHP_EOL;

class Personne
{
    public function __construct(
        protected string $nom,
        protected string $prenom,
        private string $dateNaissance      
    ) {}

    protected function getAge(): int
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

class Etudiant extends Personne
{
    public function __construct(
        string $nom,
        string $prenom,
        string $dateNaissance,
        private string $cne
    ) {
        parent::__construct($nom, $prenom, $dateNaissance);
    }

    public function getCne(): string
    {
        return $this->cne;
    }

    public function __toString(): string
    {
        return parent::__toString() . " – CNE : " . $this->cne;
    }

    public function testAcces(): void
    {
        echo "Âge calculé depuis Etudiant : " . $this->getAge() . " ans" . PHP_EOL;
    }
}

$etudiants = [];
$etudiants[] = new Etudiant("Hachimi",  "Sara",   "2000-05-14", "R123456789");
$etudiants[] = new Etudiant("Alaoui",   "Omar",   "2001-11-02", "R223456789");
$etudiants[] = new Etudiant("Bennani",  "Leila",  "1999-03-27", "R323456789");
$etudiants[] = new Etudiant("Ouazzani", "Youssef","2002-07-19", "R423456789");
$etudiants[] = new Etudiant("Idrissi",  "Nada",   "2000-12-08", "R523456789");

$t = new Etudiant("Tazi", "Hind", "2001-01-15", "R923456789");
$t->testAcces();

foreach ($etudiants as $etu) {
    echo $etu . PHP_EOL;      
}

