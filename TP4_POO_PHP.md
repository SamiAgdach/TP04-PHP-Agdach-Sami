# TP 4 – Programmation Orientée Objet en PHP

---

## Objectifs 

À la fin de ce TP, vous serez capable de :

1. Définir une **classe**, instancier des **objets** et utiliser `$this`.
2. Contrôler l'accès aux membres avec la **visibilité** (`public`, `private`, `protected`) et appliquer l'**encapsulation**.
3. Écrire des **constructeurs** (classiques et avec *promotion de propriétés* PHP 8) et un **destructeur**.
4. Utiliser les **méthodes magiques** : `__get`, `__set`, `__isset`, `__unset`, `__toString`, `__serialize`, `__unserialize`.
5. Mettre en œuvre l'**héritage** (`extends`, `parent::`, `final`, `readonly`).
6. Résoudre un **problème concret** (compte bancaire) en modélisant avec des objets.

---

## Préparation de l'environnement

**Version PHP requise : 8.1 ou plus** (nécessaire pour `readonly`).

```bash
php -v                      # vérifier la version
mkdir tp_poo && cd tp_poo   # créer le dossier de travail
```

Créez un fichier par partie (`partie1.php`, `partie2.php`, …) et exécutez-le soit :

- en ligne de commande : `php partie1.php`
- soit via le serveur interne : `php -S localhost:8000` puis ouvrez `http://localhost:8000/partie1.php`

> **Astuce :** en ligne de commande, `<br>` s'affiche tel quel. Utilisez `PHP_EOL` ou `"\n"` si vous ne testez pas dans un navigateur.

### Comment travailler ?

- Les blocs marqués `// TODO` sont **à compléter par vous**.
- Les questions demandent une **réponse écrite** (notez-la dans un commentaire ou dans un fichier `reponses.md`).
---

## Partie 0 – la première classe

### Rappel

| Notion | Rôle |
|---|---|
| **Classe** | Modèle (plan) : liste des propriétés et des méthodes |
| **Objet** | Instance de la classe, créé avec `new` |
| **`$this`** | L'objet qui appelle la méthode |
| **`->`** | Accès à une propriété ou une méthode d'un objet |

### Exemple à recopier et exécuter

```php
<?php
class Voiture {
    public $marque;

    public function setMarque($m) {
        $this->marque = $m;      // $this = l'objet qui appelle la méthode
    }

    public function afficher() {
        echo "Marque : " . $this->marque . PHP_EOL;
    }
}

$v1 = new Voiture();
$v1->setMarque("Toyota");
$v1->afficher();
```

### Exercice 0

- [ ] **0.1** Créez un second objet `$v2` de marque « Renault » et affichez-le.
- [ ] **0.2** Ajoutez une propriété `$couleur` et une méthode `setCouleur()`. Modifiez `afficher()` pour afficher aussi la couleur.
- [ ] **0.3**  `$v1` et `$v2` partagent-ils la même valeur de `$marque` ? Pourquoi ?

---

## Partie 1 – Visibilité et encapsulation : la classe `Personne`

### Rappel

| Visibilité | Accessible depuis… |
|---|---|
| `public` | La classe, les classes filles **et** l'extérieur |
| `protected` | La classe et ses classes filles |
| `private` | **Uniquement** la classe elle-même |

> L'**encapsulation** consiste à masquer les détails internes d'un objet. On préfère donc `private` ou `protected` pour les attributs.

### Énoncé

Définir une classe `Personne` :

1. Deux attributs **publics** : `nom`, `prenom`.
2. Trois attributs **privés** : `dateNaissance` (format `AAAA-MM-JJ`), `villeNaissance`, `cin`.
3. Une méthode **publique** `getName()` qui retourne le nom complet.
4. Une méthode **privée** `getAge()` qui retourne l'âge actuel.

### Code de départ

```php
<?php
class Personne {

    // ----- Attributs publics -----
    public string $nom;
    public string $prenom;

    // ----- Attributs privés -----
    private string $dateNaissance;   // format "AAAA-MM-JJ"
    private string $villeNaissance;
    private string $cin;

    // ----- Constructeur -----
    public function __construct(
        string $nom,
        string $prenom,
        string $dateNaissance,
        string $villeNaissance,
        string $cin
    ) {
        // TODO : initialiser les 5 attributs avec $this->
    }

    // ----- Méthode publique -----
    public function getName(): string {
        // TODO : retourner "Prénom Nom"
    }

    // ----- Méthode privée -----
    private function getAge(): int {
        // TODO : calculer l'âge avec DateTime
        // Indice : $naissance = new DateTime($this->dateNaissance);
        //          $aujourdhui = new DateTime();
        //          $naissance->diff($aujourdhui)->y
    }
}
```

### Programme de test

```php
$p = new Personne("Hachimi", "Sara", "2000-05-14", "Marrakech", "EE123456");

echo $p->getName() . PHP_EOL;   // à vous de prédire le résultat
echo $p->nom . PHP_EOL;
```

### Expérimentation

Ajoutez **une par une**, exécutez, et notez le message d'erreur obtenu :

```php
echo $p->cin;
echo $p->dateNaissance;
echo $p->getAge();
```

### Questions

- [ ] **1.1**  Que remarquez-vous ? Quelle ligne provoque quelle erreur ?
- [ ] **1.2** Pourquoi serait-il dangereux de laisser `cin` en `public` ?
- [ ] **1.3** `getAge()` est privée : est-elle donc inutile ? Comment pourrait-elle être utilisée par une autre méthode de la classe ? *(gardez cette idée pour la Partie 4)*
- [ ] **1.4** Changez la visibilité de `cin` en `protected`. Le comportement depuis l'extérieur change-t-il ?

---

## Partie 2 – Constructeurs et destructeur

### Rappel

- `__construct()` est appelé **automatiquement** à chaque `new`.
- Un constructeur peut avoir des paramètres **typés** et des **valeurs par défaut**.
- **PHP 8 – promotion de propriétés** : déclarer et initialiser les attributs directement dans la signature.
- `__destruct()` est appelé quand il n'y a plus de référence vers l'objet (ou à la fin du script, ou après `unset()`).

### 2.1 Constructeur avec valeurs par défaut

```php
<?php
class Voiture {
    public string $couleur;
    public int $vitesse;

    public function __construct(string $couleur = "Blanche", int $vitesse = 0) {
        $this->couleur = $couleur;
        $this->vitesse = $vitesse;
    }

    public function afficher(): void {
        echo "Couleur : {$this->couleur} | Vitesse : {$this->vitesse} km/h" . PHP_EOL;
    }
}

$v1 = new Voiture();               // valeurs par défaut
$v2 = new Voiture("Rouge", 50);    // valeurs personnalisées
$v1->afficher();
$v2->afficher();
```

- [ ] **2.1.a** Que se passe-t-il avec `new Voiture("Noire")` ? Et `new Voiture(120)` ? Testez (attention au typage !).
- [ ] **2.1.b** Réécrivez le constructeur avec la **promotion de propriétés** :

```php
public function __construct(
    public string $couleur = "Blanche",
    public int $vitesse = 0
) {}
```

Quel est l'avantage de cette écriture ?

### 2.2 Le piège des deux constructeurs

Dans votre classe `Personne` de la Partie 1, essayez d'écrire **deux** constructeurs :

```php
public function __construct() {
    echo "Création " . __CLASS__ . PHP_EOL;
}

public function __construct(string $nom, string $prenom) {
    $this->nom = $nom;
    $this->prenom = $prenom;
}
```

- [ ] **2.2.a** Que remarquez-vous à l'exécution ? Pourquoi ?
- [ ] **2.2.b** Proposez **une solution** pour obtenir les deux comportements (création « vide » et création avec informations) avec **un seul** constructeur. *(Indice : valeurs par défaut.)*
- [ ] **2.2.c** *Bonus :* cherchez ce qu'est une **méthode statique de fabrique** (*named constructor*), par exemple `Personne::depuisTableau(array $data)`.

### 2.3 Le destructeur

```php
<?php
class Voiture {
    public function __construct(public string $couleur) {
        echo "Voiture {$this->couleur} créée." . PHP_EOL;
    }

    public function __destruct() {
        echo "Voiture {$this->couleur} détruite." . PHP_EOL;
    }
}

$maVoiture = new Voiture("Blanche");
unset($maVoiture);          // appelle __destruct()
echo "Fin du script." . PHP_EOL;
```

- [ ] **2.3.a**  Dans quel ordre les trois messages s'affichent-ils ?
- [ ] **2.3.b**  Supprimez la ligne `unset(...)`. Que change-t-il dans l'ordre des messages ?
- [ ] **2.3.c** Créez deux objets `$a` et `$b`, sans `unset`. Dans quel ordre sont-ils détruits à la fin du script ?
- [ ] **2.3.d** Ajoutez dans `Personne` un destructeur affichant « Détruire `nom_class` » (utilisez `__CLASS__`) puis testez votre programme.

---

## Partie 3 – Surcharge de propriétés : `__get`, `__set`, `__isset`, `__unset`

### Rappel

Ces méthodes magiques sont appelées **automatiquement** lorsqu'on accède à une propriété **inaccessible** (privée/protégée) ou **inexistante**.

| Méthode | Déclenchée par… | Signature |
|---|---|---|
| `__set` | `$obj->prop = valeur` | `public __set(string $name, mixed $value): void` |
| `__get` | `echo $obj->prop` | `public __get(string $name): mixed` |
| `__isset` | `isset($obj->prop)` / `empty(...)` | `public __isset(string $name): bool` |
| `__unset` | `unset($obj->prop)` | `public __unset(string $name): void` |

### Code de départ

Passez **tous les attributs** de `Personne` en `private` puis complétez :

```php
<?php
class Personne {
    private string $nom = "";
    private string $prenom = "";
    private string $dateNaissance = "";

    public function __set(string $name, mixed $value): void {
        // TODO : n'autoriser que les propriétés existantes
        // Indice : property_exists($this, $name)
        // Sinon : afficher un message "Propriété $name inexistante"
    }

    public function __get(string $name): mixed {
        // TODO : retourner la valeur si la propriété existe, sinon null
    }

    public function __isset(string $name): bool {
        // TODO
    }

    public function __unset(string $name): void {
        // TODO : remettre la propriété à "" (chaîne vide)
    }
}

$p = new Personne();
$p->nom = "Hachimi";             // → __set
echo $p->nom . PHP_EOL;         // → __get
var_dump(isset($p->nom));       // → __isset
unset($p->nom);                 // → __unset
var_dump(isset($p->nom));
$p->telephone = "0600000000";   // propriété inexistante : que se passe-t-il ?
```

### Questions

- [ ] **3.1** Complétez les quatre méthodes et vérifiez les résultats attendus.
- [ ] **3.2**  Sans `__set`, que se passerait-il en écrivant `$p->nom = "X"` si `nom` est `private` ? Et avec `__set` ?
- [ ] **3.3**  `__set` peut-il casser l'encapsulation ? Que faire pour **valider** la valeur (ex. refuser un `nom` vide) ?
- [ ] **3.4** Dans `__set`, ajoutez une validation : le nom ne doit pas être vide et doit contenir au moins 2 caractères (`strlen`). Sinon, lancez `throw new InvalidArgumentException("...")`.

---

## Partie 4 – Représentation textuelle : `__toString`

### Rappel

`__toString(): string` définit comment l'objet se comporte quand il est **traité comme une chaîne** (`echo $obj`).

### Énoncé

1. **Supprimez** la méthode `getName()`.
2. Définissez `__toString()` qui retourne : `Prénom Nom (âge ans)`.
3. Testez : `echo $p;`

```php
public function __toString(): string {
    // TODO : utiliser $this->getAge() (méthode privée !)
}
```

- [ ] **4.1**  `getAge()` est `private`, pourtant elle fonctionne ici. Pourquoi ? (cf. question 1.3)
- [ ] **4.2** Que se passe-t-il si `__toString()` retourne un entier au lieu d'une chaîne ? Testez.
- [ ] **4.3** *Bonus :* c'est quoi l'intérêt de `__toString()` par rapport à `getName()` ? Listez deux situations où il est pratique.

---

## Partie 5 – Sérialisation : `__serialize` et `__unserialize`

### Rappel

- `serialize($obj)` transforme un objet en chaîne (pour le stocker dans un fichier, une session…).
- `unserialize($chaine)` reconstruit l'objet.
- `__serialize(): array` choisit **quelles données** sont sérialisées.
- `__unserialize(array $data): void` **reconstruit** l'objet à partir de ces données.
- `__sleep()` / `__wakeup()` sont les anciennes versions : si `__serialize()` existe, `__sleep()` est ignorée (idem `__unserialize()` / `__wakeup()`).

### Code de départ

```php
<?php
class Utilisateur {
    public string $nom;
    public int $age;
    private string $motDePasse;

    public function __construct(string $nom, int $age, string $motDePasse) {
        $this->nom = $nom;
        $this->age = $age;
        $this->motDePasse = $motDePasse;
    }

    public function __serialize(): array {
        return [
            'nom' => $this->nom,
            'age' => $this->age,
            // $motDePasse est volontairement exclu pour des raisons de sécurité
        ];
    }

    public function __unserialize(array $data): void {
        $this->nom = $data['nom'];
        $this->age = $data['age'];
        $this->motDePasse = "";   // à réinitialiser
    }
}

$u = new Utilisateur("Alice", 25, "secret123");
$serialise = serialize($u);
echo "Sérialisé : $serialise" . PHP_EOL;

$restaure = unserialize($serialise);
var_dump($restaure);
```

- [ ] **5.1** Exécutez et observez la chaîne sérialisée. Le mot de passe y figure-t-il ?
- [ ] **5.2**  Mettez `__serialize()` en commentaire et relancez. Que constatez-vous dans la chaîne ? Quel risque cela représente-t-il ?
- [ ] **5.3**  Après `unserialize`, quelle est la valeur de `$motDePasse` ? Que faudrait-il prévoir dans une vraie application ?
- [ ] **5.4**  **Réflexion sécurité :** pourquoi ne faut-il **jamais** appeler `unserialize()` sur des données provenant de l'utilisateur (cookie, formulaire…) ? Cherchez le terme *PHP Object Injection*. Quelle alternative utiliser pour échanger des données (indice : `json_encode` / `json_decode`) ?

---

## Partie 6 – Héritage : la classe `Etudiant`

### Rappel

| Mot-clé | Rôle |
|---|---|
| `extends` | Définit une classe dérivée (fille) |
| `parent::` | Accède au comportement de la classe mère |
| `final` | Empêche la redéfinition d'une méthode, ou l'extension d'une classe |
| `readonly` | Propriété modifiable **une seule fois** (initialisation) – PHP ≥ 8.1 |

### 6.1 Découverte avec `Animal` et `Chien`

```php
<?php
class Animal {
    public function manger(): void {
        echo "Je mange." . PHP_EOL;
    }
}

class Chien extends Animal {
    public function aboyer(): void {
        echo "Wouf !" . PHP_EOL;
    }
}

$c = new Chien();
$c->manger();    // héritée d'Animal
$c->aboyer();    // propre à Chien
```

- [ ] **6.1.a** Ajoutez une classe `Chat` qui hérite d'`Animal` avec une méthode `miauler()`.
- [ ] **6.1.b**  Peut-on appeler `$c->miauler()` ? Pourquoi ?

### 6.2 `parent::` et redéfinition de méthode

```php
<?php
class Personne {
    public function __construct(protected string $nom) {}

    public function presentation(): string {
        return "Je suis " . $this->nom;
    }
}

class Etudiant extends Personne {
    public function __construct(string $nom, private string $filiere) {
        parent::__construct($nom);   // appelle le constructeur du parent
    }

    public function presentation(): string {
        return parent::presentation() . ", étudiant en " . $this->filiere;
    }
}

$e = new Etudiant("Ahmed", "Informatique");
echo $e->presentation() . PHP_EOL;
```

- [ ] **6.2.a** Supprimez l'appel `parent::__construct($nom)`. Que se passe-t-il ? Pourquoi ?
- [ ] **6.2.b** Pourquoi `$nom` est-il `protected` et non `private` dans `Personne` ?

### 6.3 `final` et `readonly`

```php
<?php
class Base {
    final public function afficher(): void {
        echo "Méthode finale" . PHP_EOL;
    }
}

final class Outil {
    public const VERSION = "1.0";
}

class Produit {
    public function __construct(public readonly string $reference) {}
}

$p = new Produit("REF-001");
echo $p->reference . PHP_EOL;
```

- [ ] **6.3.a**  Essayez successivement : (1) redéfinir `afficher()` dans une classe `Fille extends Base` ; (2) créer `class SousOutil extends Outil {}` ; (3) exécuter `$p->reference = "REF-002";`. Notez les 3 erreurs.
- [ ] **6.3.b**  Donnez un exemple réel où `readonly` est pertinent (identifiant, date de création…) et un où `final` est pertinent.

### 6.4 Exercice principal : `Etudiant extends Personne`

Reprenez la classe `Personne` des parties 1 à 4 (avec `__toString()`).

1. Définissez `Etudiant` qui **hérite** de `Personne` et ajoute un attribut **privé** `cne`.
2. Définissez un **assesseur** (`getCne()`) pour cet attribut.
3. Définissez `__toString()` qui affiche nom, prénom, âge **et** CNE.
4. Déclarez un tableau `$etudiants`, ajoutez-y **5 étudiants**, puis affichez-les avec une boucle `foreach`.

```php
<?php
class Etudiant extends Personne {
    private string $cne;

    public function __construct(/* TODO : paramètres de Personne + $cne */) {
        // TODO : parent::__construct(...) puis initialiser $cne
    }

    public function getCne(): string {
        // TODO
    }

    public function __toString(): string {
        // TODO : réutiliser parent::__toString() et ajouter le CNE
    }
}

$etudiants = [];
// TODO : ajouter 5 étudiants avec $etudiants[] = new Etudiant(...)

foreach ($etudiants as $e) {
    echo $e . PHP_EOL;
}
```

- [ ] **6.4.a**  Dans `Etudiant`, essayez d'appeler `$this->getAge()` (privée dans `Personne`). Que se passe-t-il ? Quelle visibilité faut-il donner à `getAge()` pour que ça fonctionne ?
- [ ] **6.4.b**  Essayez d'accéder à `$this->dateNaissance` depuis `Etudiant`. Pourquoi est-ce impossible alors que `Etudiant` *est une* `Personne` ?
- [ ] **6.4.c**  Quel est l'avantage de réutiliser `parent::__toString()` plutôt que de tout réécrire ?

---

## Partie 7 – Mise en pratique : le compte bancaire

### Énoncé

1. Définir une classe `Compte` représentant un compte bancaire.
2. Elle contient deux attributs **privés** : `montant` et `interet` (annuel, en %).
3. Un **constructeur** prenant en paramètres un montant initial et un intérêt.
4. Une méthode `get_montant()` retournant le montant du compte.
5. Une méthode `un_an()` modifiant le montant au bout d'un an en fonction de l'intérêt.
6. Créer **deux comptes** : 400 dirhams à 10 % et 2 500 dirhams à 5 %.
7. Afficher le montant de chaque compte au bout de **10 ans**.

### Code à déboguer

Un étudiant a écrit ce code. Il contient **plusieurs problèmes**. Retrouvez-les et corrigez-les.

```php
<?php
class Compte {
    private $montant;
    private $interet;   // en pourcentage

    public function __construct($interet, $montantInitial) {
        $this->montant = $montantInitial;
        $this->interet = $interet;
    }

    public function un_an() {
        $this->montant = $this->montant * ($this->montant 1 * $this->interet / 100);
    }

    public function get_montant() {
        return $this->montant;
    }
}

$c1 = new Compte(10, 400);
$c2 = new Compte(5, 2500);

for ($i = 1; $i <= 10; $i++) {
    $c1->un_an();
    $c2->un_an();
}

echo '<p> Valeur du compte = ' . $c1->get_montant() . ' dirhams </p>';
echo '<p> Valeur du compte = ' . $c2->get_montant() . ' dirhams </p>';
```

- [ ] **7.1** Repérez l'erreur de **syntaxe** dans `un_an()`.
- [ ] **7.2** Même sans erreur de syntaxe, la formule est **fausse mathématiquement**. Écrivez la bonne formule du montant après un an avec un intérêt de `t` %.
- [ ] **7.3** L'ordre des paramètres du constructeur (`$interet, $montantInitial`) est-il cohérent avec l'énoncé (« montant initial et un intérêt ») ? Quel risque cela crée-t-il à l'appel ?
- [ ] **7.4** Corrigez le code, ajoutez les **types** (`float`) et les **types de retour**, puis affichez les montants avec `number_format($montant, 2, ',', ' ')`.

### Vérification de vos résultats

Après 10 ans, vous devez obtenir environ :

| Compte | Montant initial | Taux | Montant après 10 ans |
|---|---|---|---|
| `$c1` | 400 DH | 10 % | **≈ 1 037,50 DH** |
| `$c2` | 2 500 DH | 5 % | **≈ 4 072,24 DH** |

> Si vos valeurs sont très différentes, revoyez la formule de la question 7.2.

---

## 🏁 Défi final (facultatif) : mini-gestionnaire

Concevez, en binôme, un mini-programme **en POO** qui gère une **bibliothèque** :

- Classe `Livre` (titre, auteur, ISBN en `readonly`, disponibilité).
- Classe `Membre` (hérite de `Personne`).
- Méthodes `emprunter()` et `rendre()`, avec validation.
- `__toString()` pour afficher livres et membres.
- Un tableau de livres et une boucle d'affichage.

Critères : encapsulation respectée, constructeurs typés, au moins un héritage, aucune propriété `public` inutile.

---


### Questions de synthèse

1. Quelle est la différence entre une **classe** et un **objet** ?
2. Citez trois avantages de la POO par rapport à la programmation procédurale.
3. Pourquoi est-il recommandé de déclarer les attributs en `private` ou `protected` ?
4. Quelle différence entre `private` et `protected` quand il y a de l'héritage ?
5. Une méthode magique doit-elle être déclarée `public` ? Quelles sont les exceptions tolérées ?

---

## 📦 Livrables

- Un dossier `tp_poo/` contenant les fichiers `partie0.php` à `partie7.php`.
- Un fichier `reponses.md` contenant vos réponses aux questions 🤔.
- Respectez les conventions : classes en `PascalCase`, méthodes et attributs en `camelCase`, indentation de 4 espaces.

>  **Pour aller plus loin :** documentation officielle PHP – <https://www.php.net/manual/fr/language.oop5.php>
