<?php
declare(strict_types=1);


class Personne
{
    private string $nom = "";
    private string $prenom = "";
    private string $dateNaissance = "";

    public function __set(string $name, mixed $value): void
    {
        if (!property_exists($this, $name)) {
            echo "Propriété '$name' inexistante : affectation refusée." . PHP_EOL;
            return;
        }

        if ($name === 'nom') {
            if (!is_string($value) || strlen(trim($value)) < 2) {
                throw new InvalidArgumentException(
                    "Le nom doit contenir au moins 2 caractères (reçu : '$value')"
                );
            }
        }

        $this->$name = $value;
    }

    public function __get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;    
    }

    public function __isset(string $name): bool
    {
        return property_exists($this, $name) && $this->$name !== "";
    }

    public function __unset(string $name): void
    {
        if (property_exists($this, $name)) {
            $this->$name = "";    
        } else {
            echo "Propriété '$name' inexistante : unset refusé." . PHP_EOL;
        }
    }
}

$p = new Personne();

$p->nom = "Hachimi";             
echo $p->nom . PHP_EOL;          
var_dump(isset($p->nom));       
unset($p->nom);                   
var_dump(isset($p->nom));        

$p->telephone = "0600000000";     
echo "Après tentative : telephone = " . ($p->telephone ?? "(null)") . PHP_EOL;

echo PHP_EOL . "--- Test 3.4 : validation du nom ---" . PHP_EOL;

try {
    $p->nom = "X";                
} catch (InvalidArgumentException $e) {
    echo "Exception attendue : " . $e->getMessage() . PHP_EOL;
}

$p->nom = "Alaoui";              
echo "Nom validé : " . $p->nom . PHP_EOL;

