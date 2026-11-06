<?php
declare(strict_types=1);
class Utilisateur
{
    public string $nom;
    public int $age;
    private string $motDePasse;

    public function __construct(string $nom, int $age, string $motDePasse)
    {
        $this->nom         = $nom;
        $this->age         = $age;
        $this->motDePasse  = $motDePasse;
    }

    public function __serialize(): array
    {
        return [
            'nom' => $this->nom,
            'age' => $this->age,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->nom         = $data['nom'];
        $this->age         = $data['age'];
        $this->motDePasse  = "test";   
    }
}
class UtilisateurNaif
{
    public function __construct(
        public string $nom,
        public int $age,
        private string $motDePasse
    ) {}
}
echo "=== 5.1 : sérialisation avec __serialize() ===" . PHP_EOL;

$u = new Utilisateur("Alice", 25, "secret123");
$serialise = serialize($u);
echo "Sérialisé : $serialise" . PHP_EOL;

echo PHP_EOL . "Le mot de passe figure-t-il dans la chaîne ? ";
echo (str_contains($serialise, 'secret123') ? "OUI (!)" : "NON (filtré)") . PHP_EOL;

$restaure = unserialize($serialise);
var_dump($restaure);

echo PHP_EOL . "=== 5.2 : même objet SANS __serialize() ===" . PHP_EOL;

$un = new UtilisateurNaif("Alice", 25, "secret123");
$serialiseNaif = serialize($un);
echo "Sérialisé : $serialiseNaif" . PHP_EOL;

echo PHP_EOL . "Le mot de passe figure-t-il dans la chaîne ? ";
echo (str_contains($serialiseNaif, 'secret123') ? "OUI (!)" : "NON") . PHP_EOL;

