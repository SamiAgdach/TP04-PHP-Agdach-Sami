# TP 4 – Programmation Orientée Objet en PHP — CORRIGÉ COMPLET

> **Contenu du dossier `tp_poo/`**
>
> | Fichier | Contenu |
> |---|---|
> | `partie0.php` → `partie7.php` | Toutes les parties du TP, TODO complétés, code testé sous **PHP 8.4** |
> | `defi_final.php` | 🏁 Défi final (facultatif) : mini-gestionnaire de bibliothèque |
> | `reponses.md` | Le présent document : réponses à **toutes** les questions |
>
> Chaque fichier s'exécute avec : `php partie0.php` (messages d'affichage via `PHP_EOL`, adaptés au CLI).
> Tous les messages d'erreur cités ci-dessous ont été **réellement observés** à l'exécution.

---

## Partie 0 – La première classe

### Solutions des exercices (voir `partie0.php`)

- **0.1** — Création d'un second objet :

```php
$v2 = new Voiture();          // `new` construit une NOUVELLE instance
$v2->setMarque("Renault");
$v2->afficher();              // Marque : Renault | Couleur : Grise
```

- **0.2** — Ajout de `$couleur` et `setCouleur()`, puis mise à jour de `afficher()` :

```php
public $couleur;

public function setCouleur($c) {
    $this->couleur = $c;
}

public function afficher() {
    echo "Marque : " . $this->marque . " | Couleur : " . $this->couleur . PHP_EOL;
}
```

- **0.3** — **Non**, `$v1` et `$v2` ne partagent pas la valeur de `$marque`.

**Explication** : la **classe** `Voiture` est un *plan* commun, mais chaque appel à `new` alloue une **zone mémoire propre** à l'objet créé. Quand on écrit `$v1->setMarque("Toyota")`, la variable `$this` à l'intérieur de la méthode désigne **l'objet situé à gauche de la flèche** (c'est-à-dire `$v1`) ; l'affectation `$this->marque = $m` modifie donc uniquement la propriété de `$v1`. La preuve à l'exécution :

```text
$v1->marque = Toyota
$v2->marque = Renault
```

Si les deux objets partageaient l'attribut, la seconde affectation aurait écrasé la première et les deux lignes afficheraient « Renault ». On résume cela par : *la classe contient le code partagé, les objets contiennent leurs données propres*.

---

## Partie 1 – Visibilité et encapsulation : la classe `Personne`

### Code complété (voir `partie1.php`)

```php
class Personne {
    // ----- Attributs publics -----
    public string $nom;
    public string $prenom;

    // ----- Attributs privés -----
    private string $dateNaissance;   // format "AAAA-MM-JJ"
    private string $villeNaissance;
    private string $cin;

    public function __construct(
        string $nom, string $prenom, string $dateNaissance,
        string $villeNaissance, string $cin
    ) {
        $this->nom            = $nom;
        $this->prenom         = $prenom;
        $this->dateNaissance  = $dateNaissance;
        $this->villeNaissance = $villeNaissance;
        $this->cin            = $cin;
    }

    public function getName(): string {
        return $this->prenom . " " . $this->nom;   // "Prénom Nom"
    }

    private function getAge(): int {
        $naissance  = new DateTime($this->dateNaissance);
        $aujourdhui = new DateTime();
        return $naissance->diff($aujourdhui)->y;   // propriété « années »
    }
}
```

Sortie du programme de test :

```text
Sara Hachimi
Hachimi
```

### Réponses

**1.1 — Que remarquez-vous ?**

Les trois lignes d'expérimentation provoquent chacune une **erreur fatale** (une `Error` qui arrête le script si on ne l'intercepte pas). Comme la première erreur stoppe tout, on les teste une par une (ou avec `try/catch`, comme dans le fichier) et on obtient :

| Ligne testée | Erreur observée (PHP 8.4) |
|---|---|
| `echo $p->cin;` | `Error: Cannot access private property Personne::$cin` |
| `echo $p->dateNaissance;` | `Error: Cannot access private property Personne::$dateNaissance` |
| `echo $p->getAge();` | `Error: Call to private method Personne::getAge() from global scope` |

On remarque deux familles d'erreurs : **l'accès à une propriété privée** et **l'appel à une méthode privée depuis « global scope »** (l'extérieur de toute classe). La visibilité est donc vérifiée **à l'exécution**, au moment de l'accès.

**1.2 — Pourquoi est-il dangereux de laisser `cin` en `public` ?**

Le CIN est un **identifiant officiel d'identité**, une donnée personnelle sensible. En `public`, n'importe quel code du projet (ou n'importe quel script qui inclut le fichier) pourrait :

- **le lire** → usurpation d'identité, violation de la confidentialité (et du RGPD/loi 09-08 au Maroc) ;
- **le modifier** → corrompre les données (`$p->cin = "X"` passerait sans contrôle) ;
- **l'afficher** accidentellement (var_dump, debug, sérialisation…).

En `private`, la classe contrôle **tous les accès** : elle décide quelles méthodes exposent quoi. C'est le principe de l'**encapsulation**.

**1.3 — `getAge()` est privée : est-elle donc inutile ?**

**Non.** Une méthode `private` est utilisable par **toutes les autres méthodes de la même classe** : elle sert de *brique interne réutilisable*. Ici, `getAge()` effectue un calcul (différence de dates) qu'on ne veut pas dupliquer ; on l'appellera par exemple depuis `__toString()` (Partie 4) pour afficher « Prénom Nom (âge ans) ». C'est exactement le sens de la remarque *« gardez cette idée pour la Partie 4 »* : la visibilité `private` masque la méthode à l'extérieur **sans** l'empêcher de servir en interne.

**1.4 — Si on change `cin` en `protected`, le comportement depuis l'extérieur change-t-il ?**

**Non.** `protected` = accessible depuis la classe **et ses classes filles** seulement. Depuis l'extérieur (`echo $p->cin;`), l'accès reste interdit avec la même erreur fatale. `protected` ne change que la situation **à l'intérieur de l'héritage** (cf. question 6.4.b).

---

## Partie 2 – Constructeurs et destructeur

### 2.1 — Constructeur avec valeurs par défaut

**2.1.a — Que se passe-t-il avec `new Voiture("Noire")` ? Et `new Voiture(120)` ?**

Observé à l'exécution :

- `new Voiture("Noire")` **fonctionne** : les arguments sont associés **par position** ; `$couleur` reçoit `"Noire"` et `$vitesse` prend sa valeur par défaut `0` → `Couleur : Noire | Vitesse : 0 km/h`.
- `new Voiture(120)` lève une **`TypeError`**, interceptée :

```text
TypeError: Voiture::__construct(): Argument #1 ($couleur) must be of type
string, int given
```

Le typage protège la classe : on ne peut pas passer un `int` là où un `string` est attendu (et l'ordre « couleur puis vitesse » fait que `120` arrive sur `$couleur`).

**2.1.b — Promotion de propriétés (PHP 8) et son avantage :**

```php
public function __construct(
    public string $couleur = "Blanche",
    public int $vitesse = 0
) {}
```

Avantages de cette écriture :

1. **Concision** : la propriété est déclarée, typée, initialisée en **une seule ligne** — plus besoin d'écrire `public string $couleur;` puis `$this->couleur = $couleur;` ;
2. **Moins d'erreurs de recopie** : impossible de se tromper dans le nom de l'attribut ;
3. **Lisibilité** : la signature du constructeur sert de documentation — on voit d'un coup d'œil les attributs, leur type et leur valeur par défaut.

### 2.2 — Le piège des deux constructeurs

**2.2.a — Que remarquez-vous à l'exécution ? Pourquoi ?**

Le script **ne s'exécute même pas** : PHP refuse de compiler le fichier avec :

```text
Fatal error: Cannot redeclare Personne::__construct()
```

**Pourquoi** : contrairement à Java ou C++, PHP **n'autorise pas la surcharge de méthodes** (plusieurs signatures pour un même nom). Une classe ne peut avoir qu'**un seul** `__construct()`.

**2.2.b — Une solution avec un seul constructeur :** les **valeurs par défaut**.

```php
class Personne {
    public function __construct(
        private string $nom = "",
        private string $prenom = ""
    ) {}
}

$a = new Personne();                    // création « vide »
$b = new Personne("Hachimi", "Sara");   // création avec informations
```

Un seul constructeur couvre les deux comportements : chaque paramètre omis prend sa valeur par défaut.

**2.2.c — Bonus : méthode statique de fabrique (*named constructor*).**

Le constructeur devient `private` et on l'appelle via des méthodes statiques **nommées**, qui expliquent comment l'objet est créé :

```php
class PersonneFabrique {
    private function __construct(
        private string $nom = "",
        private string $prenom = ""
    ) {}

    public static function depuisTableau(array $data): self {
        return new self($data['nom'] ?? "", $data['prenom'] ?? "");
    }

    public static function vide(): self {
        return new self();
    }
}

$p = PersonneFabrique::depuisTableau(['nom' => 'Alaoui', 'prenom' => 'Omar']);
```

Avantage : `new PersonneFabrique()` est interdit de l'extérieur ; on ne peut créer l'objet que par des chemins documentés (`depuisTableau`, `vide`, …), chacun validant ses propres données.

### 2.3 — Le destructeur

**2.3.a — Ordre des trois messages avec `unset(...)` :**

```text
Voiture Blanche créée.
Voiture Blanche détruite.     ← unset() déclenche __destruct() immédiatement
Fin du script.
```

**2.3.b — Sans la ligne `unset(...)` :**

```text
Voiture Bleue créée.
Fin du script.
Voiture Bleue détruite.       ← la destruction attend la fin du script
```

Sans `unset`, l'objet reste référencé jusqu'à la fin : PHP appelle alors `__destruct()` pendant l'arrêt du script, **après** la dernière instruction.

**2.3.c — Deux objets `$a` et `$b`, sans `unset` : ordre de destruction ?**

Observé à l'exécution :

```text
Voiture Rouge créée.    ← $a
Voiture Noire créée.    ← $b
Fin du script.
Voiture Noire détruite. ← détruite EN PREMIER
Voiture Rouge détruite.
```

PHP détruit les objets restants **en ordre inverse de leur création** (comportement LIFO, comme une pile). Cela évite notamment qu'un objet détruit en premier ait besoin d'un objet déjà détruit… en pratique il vaut mieux ne jamais dépendre de cet ordre dans `__destruct()`.

**2.3.d — Destructeur dans `Personne` :**

```php
public function __destruct() {
    echo "Détruire " . __CLASS__ . PHP_EOL;
}
```

Résultat observé : `Création de Sara (PersonneDestruct)` puis, après `unset($pd)`, `Détruire PersonneDestruct`. La constante magique `__CLASS__` contient le nom de la classe où **le code est écrit**.

---

## Partie 3 – Surcharge de propriétés : `__get`, `__set`, `__isset`, `__unset`

### 3.1 — Les quatre méthodes complétées (voir `partie3.php`)

```php
class Personne {
    private string $nom = "";
    private string $prenom = "";
    private string $dateNaissance = "";

    public function __set(string $name, mixed $value): void {
        if (!property_exists($this, $name)) {              // propriété inexistante
            echo "Propriété '$name' inexistante : affectation refusée." . PHP_EOL;
            return;
        }
        $this->$name = $value;                             // affectation dynamique
    }

    public function __get(string $name): mixed {
        return property_exists($this, $name) ? $this->$name : null;
    }

    public function __isset(string $name): bool {
        return property_exists($this, $name) && $this->$name !== "";
    }

    public function __unset(string $name): void {
        if (property_exists($this, $name)) {
            $this->$name = "";
        } else {
            echo "Propriété '$name' inexistante : unset refusé." . PHP_EOL;
        }
    }
}
```

Sortie réelle du programme de test :

```text
Hachimi
bool(true)
bool(false)
Propriété 'telephone' inexistante : affectation refusée.
Après tentative : telephone = (null)
```

**Remarque importante** : `$this->$name` (un seul `$` devant `name`) est une **propriété variable** : le nom de l'attribut est contenu dans `$name`. `property_exists($this, $name)` vérifie que l'attribut **existe réellement dans la classe** (y compris s'il est privé), ce qui empêche de créer des propriétés fantômes comme `telephone`.

**3.2 — Sans `__set`, que se passe-t-il avec `$p->nom = "X"` si `nom` est `private` ?**

Sans `__set` : erreur fatale `Error: Cannot modify private property Personne::$nom` — le script s'arrête. Avec `__set` : l'affectation **interdite** est **interceptée** et la méthode magique décide quoi faire (accepter, transformer, refuser, journaliser…). C'est le mécanisme de *property overloading* : il ne rend pas les attributs publics, il **canalise** les accès vers du code de la classe. Idem pour `__get` avec la lecture (`Cannot access private property …`).

**3.3 — `__set` peut-il casser l'encapsulation ? Comment valider ?**

**Oui, s'il accepte n'importe quoi.** Un `__set` naïf (`$this->$name = $value;` sans vérification) ouvre deux brèches :

- on peut écrire des attributs **inexistants** (création de propriétés fantômes) ;
- on peut écrire des valeurs **incohérentes** (`nom` vide, âge négatif…), sans aucune erreur visible.

La parade est de **valider dans `__set`** : vérifier `property_exists()`, puis le type/la longueur/la plage de la valeur, et **refuser** (message ou exception) si elle est invalide. C'est ce que fait la question 3.4.

**3.4 — Validation avec `InvalidArgumentException` :**

```php
if ($name === 'nom') {
    if (!is_string($value) || strlen(trim($value)) < 2) {
        throw new InvalidArgumentException(
            "Le nom doit contenir au moins 2 caractères (reçu : '$value')"
        );
    }
}
```

Test réel :

```text
Exception attendue : Le nom doit contenir au moins 2 caractères (reçu : 'X')
Nom validé : Alaoui
```

---

## Partie 4 – Représentation textuelle : `__toString`

### Code complété (voir `partie4.php`)

```php
public function __toString(): string {
    return "{$this->prenom} {$this->nom} ({$this->getAge()} ans)";
}
```

Résultat :

```text
Sara Hachimi (26 ans)
Fiche : Sara Hachimi (26 ans)
Étudiante : Sara Hachimi (26 ans)
```

**4.1 — `getAge()` est `private`, pourquoi l'appel fonctionne-t-il ?**

Parce que `__toString()` est **dans la même classe** que `getAge()`. La visibilité s'applique à **l'endroit où le code est écrit**, pas à l'objet qui déclenche l'appel : depuis l'extérieur `$p->getAge()` est interdit (`Call to private method … from global scope`), mais **à l'intérieur de la classe**, `$this->getAge()` est un appel normal. C'est la mise en pratique de la réponse 1.3 : une méthode privée est une brique interne réutilisable.

**4.2 — Que se passe-t-il si `__toString()` retourne un entier ?**

Deux comportements selon le mode (testés dans `partie4.php`) :

- **Mode faible** (par défaut, sans `declare(strict_types=1)`) : PHP convertit silencieusement `42` en `"42"` et affiche `42` — *aucune erreur visible*, ce qui est piégeux car le contrat de la méthode n'est pas respecté ;
- **Mode strict** (`declare(strict_types=1)` en tête de fichier) :

```text
TypeError: Return value of PersonneEntier::__toString() must be of the
type string, int returned
```

La règle : `__toString()` **doit** retourner une `string`.

**4.3 — Intérêt de `__toString()` par rapport à `getName()` ? Deux situations pratiques.**

`getName()` ne fournit que le nom et demande d'appeler une méthode précise. `__toString()` définit la **représentation canonique de l'objet entier** :

1. **Affichage en boucle / echo direct** : `foreach ($etudiants as $e) { echo $e; }` — aucun nom de méthode à connaître ; tout composant d'affichage peut imprimer l'objet tel quel ;
2. **Tous les contextes « chaîne »** : concaténation `"Fiche : " . $p`, interpolation `"Étudiante : $p"`, fichiers de log, templates… Et surtout, le **format est centralisé** : si on change le format dans `__toString()`, tous les affichages de l'application changent automatiquement (principe DRY).

---

## Partie 5 – Sérialisation : `__serialize` et `__unserialize`

### Résultats observés (voir `partie5.php`)

**5.1 — Le mot de passe figure-t-il dans la chaîne sérialisée ?**

Avec `__serialize()` :

```text
Sérialisé : O:11:"Utilisateur":2:{s:3:"nom";s:5:"Alice";s:3:"age";i:25;}
Le mot de passe figure-t-il dans la chaîne ? NON (filtré)
```

`O:11:"Utilisateur":2:{…}` = objet de classe `Utilisateur` (nom de 11 caractères) avec **2** propriétés : `nom` et `age`. Le mot de passe a été **exclu** par notre méthode.

**5.2 — Si on met `__serialize()` en commentaire :**

PHP sérialise alors **toutes** les propriétés, y compris `private` :

```text
Sérialisé : O:15:"UtilisateurNaif":3:{s:3:"nom";s:5:"Alice";s:3:"age";i:25;
            s:27:" UtilisateurNaif motDePasse";s:9:"secret123";}
Le mot de passe figure-t-il dans la chaîne ? OUI (!)
```

Le mot de passe apparaît **en clair** (`s:9:"secret123"`). **Risque** : la chaîne est destinée à être stockée quelque part (fichier de session, cache, base de données, log, sauvegarde) ou transitée. Quiconque a accès à cet endroit lit le mot de passe **sans casser aucun chiffrement** — la protection par visibilité `private` ne compte pour rien, car la sérialisation a lieu *depuis l'intérieur de la classe*.

**5.3 — Valeur de `$motDePasse` après `unserialize` ?**

D'après notre `__unserialize()` : `""` (chaîne vide). Le `var_dump` le confirme :

```text
["motDePasse":"Utilisateur":private]=> string(0) ""
```

Dans une vraie application : ne jamais sérialiser de mot de passe ; stocker un **hachage** (`password_hash()`) et non le mot de passe ; et après restauration d'une session, prévoir une **reconnexion / réinitialisation** (l'utilisateur doit ré-authentifier pour les actions sensibles).

**5.4 — Pourquoi ne JAMAIS appeler `unserialize()` sur des données utilisateur ?**

C'est la faille **PHP Object Injection** : `unserialize()` peut **créer des objets** de n'importe quelle classe du projet, avec les propriétés choisies par l'attaquant (il fabrique lui-même la chaîne). Les **méthodes magiques** (`__destruct()`, `__wakeup()`, `__toString()`…) se déclenchent alors pendant la destruction/l'utilisation de ces objets. En chaînant plusieurs classes mal codées (*POP chain*), l'attaquant peut lire/écrire des fichiers, exécuter du code, contourner l'authentification… **sans jamais passer par une validation**.

**Alternative** : `json_encode()` / `json_decode()`. JSON ne peut représenter que des données (tableaux, nombres, chaînes) : `json_decode($json, true)` produit un simple tableau associatif, jamais un objet PHP → aucune méthode magique ne peut être déclenchée. Dans `partie5.php`, le bonus le démontre.

---

## Partie 6 – Héritage : la classe `Etudiant`

### 6.1 — `Animal` / `Chien` / `Chat`

**6.1.a** — Classe `Chat` ajoutée dans `partie6.php` :

```php
class Chat extends Animal {
    public function miauler(): void { echo "Miaou !" . PHP_EOL; }
}
```

**6.1.b — Peut-on appeler `$c->miauler()` ? Pourquoi ?**

**Non**. Observé :

```text
Erreur attendue : Call to undefined method Chien::miauler()
```

`$c` est un `Chien` : il hérite de `manger()` (définie dans `Animal`) mais `miauler()` est définie dans `Chat`. L'héritage descend **du parent vers l'enfant** ; deux classes **sœurs** ne se partagent rien. `Chien` et `Chat` ont chacune leur propre spécialisation.

### 6.2 — `parent::` et redéfinition de méthode

**6.2.a — Si on supprime `parent::__construct($nom)` ?**

Le constructeur de `Personne` n'est **jamais appelé** : PHP **n'appelle pas automatiquement** le constructeur du parent quand l'enfant définit le sien. La propriété `$nom` (promue dans `Personne`) reste **non initialisée** ; ensuite, `presentation()` du parent tente de lire `$this->nom` et PHP lève :

```text
Error: Must not be accessed before initialization
```

La règle : si la classe fille définit `__construct()`, **elle est responsable** d'appeler `parent::__construct(...)`.

**6.2.b — Pourquoi `$nom` est `protected` et non `private` dans `Personne` ?**

Parce que l'enfant doit pouvoir **lire** `$this->nom` (dans `presentation()` et ailleurs). En `private`, la propriété serait invisible dans `Etudiant` (cf. 6.4.b). `protected` = « classe + classes filles » : c'est le niveau adapté aux attributs que l'héritage doit partager, tout en restant masqué de l'extérieur.

### 6.3 — `final` et `readonly`

**6.3.a — Les 3 erreurs observées (code volontairement commenté dans `partie6.php`) :**

| Tentative | Erreur fatale obtenue |
|---|---|
| Redéfinir `afficher()` dans une classe fille de `Base` | `Fatal error: Cannot override final method Base::afficher()` |
| `class SousOutil extends Outil {}` | `Fatal error: Class SousOutil may not inherit from final class (Outil)` |
| `$p->reference = "REF-002";` | `Error: Cannot modify readonly property Produit::$reference` |

- `final` sur une **méthode** → interdit de la redéfinir (le comportement est verrouillé) ;
- `final` sur une **classe** → interdit de l'étendre (nobody hérite d'elle) ;
- `readonly` → la propriété ne peut être écrite **qu'une seule fois** (dans la portée où elle est déclarée) ; toute réécriture est refusée.

**6.3.b — Exemples réels :**

- **`readonly` pertinent** : un **identifiant immuable** — référence produit `REF-001`, CNE étudiant, CIN, numéro de commande, **date de création** d'un compte. Une fois fixé au constructeur, personne (pas même la classe) ne doit pouvoir le modifier : cela garantit l'intégrité des données d'identification.
- **`final` pertinent** : une **classe utilitaire/config** (`Outil`) dont l'API ne doit pas être détournée, ou une **méthode métier sensible** (calcul d'un solde, d'une commission) dont la logique ne doit jamais être changée par une sous-classe, ou encore une méthode appelée **dans le constructeur** (la redéfinition par une fille produirait un objet partiellement initialisé).

### 6.4 — Exercice principal : `Etudiant extends Personne` (voir `partie6.php`)

```php
class Personne {
    public function __construct(
        protected string $nom,
        protected string $prenom,
        private string $dateNaissance      // PRIVÉE
    ) {}

    protected function getAge(): int {     // PROTECTED : utile aux filles
        $naissance  = new DateTime($this->dateNaissance);
        $aujourdhui = new DateTime();
        return $naissance->diff($aujourdhui)->y;
    }

    public function __toString(): string {
        return "{$this->prenom} {$this->nom} ({$this->getAge()} ans)";
    }
}

class Etudiant extends Personne {
    public function __construct(
        string $nom, string $prenom, string $dateNaissance,
        private string $cne
    ) {
        parent::__construct($nom, $prenom, $dateNaissance);
    }

    public function getCne(): string { return $this->cne; }

    public function __toString(): string {
        return parent::__toString() . " – CNE : " . $this->cne;
    }
}

$etudiants = [];
$etudiants[] = new Etudiant("Hachimi", "Sara", "2000-05-14", "R123456789");
$etudiants[] = new Etudiant("Alaoui",  "Omar", "2001-11-02", "R223456789");
$etudiants[] = new Etudiant("Bennani", "Leila","1999-03-27", "R323456789");
$etudiants[] = new Etudiant("Ouazzani","Youssef","2002-07-19","R423456789");
$etudiants[] = new Etudiant("Idrissi", "Nada", "2000-12-08", "R523456789");

foreach ($etudiants as $e) { echo $e . PHP_EOL; }
```

Sortie réelle :

```text
Sara Hachimi (26 ans) – CNE : R123456789
Omar Alaoui (25 ans) – CNE : R223456789
Leila Bennani (27 ans) – CNE : R323456789
Youssef Ouazzani (24 ans) – CNE : R423456789
Nada Idrissi (25 ans) – CNE : R523456789
```

**6.4.a — `$this->getAge()` depuis `Etudiant` alors qu'elle est `private` ?**

Erreur fatale :

```text
Error: Call to private method Personne::getAge() from scope Etudiant
```

`private` = visible uniquement dans la classe qui **déclare**. Pour que la fille puisse l'utiliser, il faut la déclarer **`protected`** (c'est ce qui a été fait : le calcul d'âge est partagé dans la famille `Personne`, mais reste caché de l'extérieur).

**6.4.b — Pourquoi `$this->dateNaissance` est-il inaccessible depuis `Etudiant` alors que `Etudiant` *est une* `Personne` ?**

Parce que `private` signifie « visible **uniquement dans le code de la classe qui l'a déclarée** ». La relation d'héritage (« Est une ») permet de **réutiliser le comportement public/protected**, mais **ne casse pas l'encapsulation** : `Etudiant` reste un code différent de `Personne`, et le concepteur de `Personne` a réservé `dateNaissance` à sa propre implémentation. Si l'accès direct était nécessaire dans les filles, l'attribut devrait passer en `protected`. (Ici ce n'est pas utile : `getAge()` protégée suffit à exploiter la date.)

**6.4.c — Avantage de réutiliser `parent::__toString()` :**

- **Moins de code** : on n'écrit que l'ajout (« – CNE : … ») ;
- **Une seule source de vérité** : le format « Prénom Nom (âge ans) » est défini **une seule fois** dans `Personne` ; si on le modifie (ex. « nom, prénom (âge) »), l'affichage d'`Etudiant` suit **automatiquement** ;
- **Cohérence** : impossible d'avoir un décalage entre l'affichage de `Personne` et celui d'`Etudiant` (principe **DRY** : *Don't Repeat Yourself*).

---

## Partie 7 – Mise en pratique : le compte bancaire

### Méthode de résolution (démarche générale)

1. **Lire l'énoncé et extraire le modèle** : une classe `Compte`, deux attributs `private` (`montant`, `interet`), un constructeur, deux méthodes (`get_montant()`, `un_an()`) ;
2. **Traduire la notion mathématique** : un intérêt annuel de *t* % multiplie le capital par (1 + t/100) → **intérêts composés** au fil des ans ;
3. **Exécuter le code fourni pour voir les erreurs** → repérer l'erreur de **syntaxe**, puis la formule, puis la cohérence de l'énoncé ;
4. **Corriger, typer, tester** contre les valeurs de contrôle fournies par l'énoncé (1 037,50 et 4 072,24 DH) : si le résultat correspond, la formule est validée.

### Réponses

**7.1 — L'erreur de syntaxe dans `un_an()` :**

```php
$this->montant = $this->montant * ($this->montant 1 * $this->interet / 100);
//                                                            ^ "1" isolé : opérateur manquant
```

Le `1` flotte entre deux expressions sans opérateur → **`Parse error: syntax error, unexpected integer "1"`** : le fichier ne s'exécute pas du tout. L'auteur voulait écrire `(1 + …)`.

**7.2 — La bonne formule après un an avec un intérêt de t % :**

```text
M(année suivante) = M(année en cours) × (1 + t / 100)
```

La formule du code de départ, même débarrassée de l'erreur de syntaxe, serait fausse : elle mélange le montant et le taux au carré. Un taux de 10 % fait passer 400 → 400 × 1,10 = 440 DH (et non 400 × 400 × 0,10). C'est la formule des **intérêts composés** : appliquée 10 fois, elle donne M₀ × (1 + t/100)^10.

**7.3 — L'ordre des paramètres du constructeur est-il cohérent avec l'énoncé ?**

**Non.** L'énoncé dit : *« un constructeur prenant en paramètres un **montant initial** et un **intérêt** »*, mais le code déclare `__construct($interet, $montantInitial)` — l'ordre est **inversé**. **Risque à l'appel** : les deux paramètres sont du même type (nombre) ; un développeur qui écrit naturellement `new Compte(400, 10)` obtient un compte de **10 DH à 400 %** — le programme **fonctionne sans aucune erreur** et produit un résultat **faux silencieusement**, le pire type de bug. La règle : l'ordre des paramètres doit suivre l'énoncé (et quand deux paramètres sont de même type, on préfère des types distincts ou des paramètres nommés : `new Compte(montantInitial: 400, interet: 10)`).

**7.4 — Code corrigé, typé, avec affichage formaté (voir `partie7.php`) :**

```php
class Compte {
    private float $montant;
    private float $interet;   // annuel, en %

    public function __construct(float $montantInitial, float $interet) {
        $this->montant = $montantInitial;
        $this->interet = $interet;
    }

    public function un_an(): void {
        $this->montant = $this->montant * (1 + $this->interet / 100);
    }

    public function get_montant(): float {
        return $this->montant;
    }
}

$c1 = new Compte(400, 10);
$c2 = new Compte(2500, 5);

for ($i = 1; $i <= 10; $i++) {
    $c1->un_an();
    $c2->un_an();
}

echo '<p>Valeur du compte = ' . number_format($c1->get_montant(), 2, ',', ' ') . ' dirhams</p>';
echo '<p>Valeur du compte = ' . number_format($c2->get_montant(), 2, ',', ' ') . ' dirhams</p>';
```

### Vérification des résultats (sortie réelle)

```text
Valeur du compte = 1 037,50 dirhams
Valeur du compte = 4 072,24 dirhams
```

| Compte | Montant initial | Taux | Montant après 10 ans (attendu) | Obtenu |
|---|---|---|---|---|
| `$c1` | 400 DH | 10 % | ≈ 1 037,50 DH | ✔ **1 037,50 DH** |
| `$c2` | 2 500 DH | 5 % | ≈ 4 072,24 DH | ✔ **4 072,24 DH** |

---

## 🏁 Défi final : mini-gestionnaire de bibliothèque (voir `defi_final.php`)

**Conception retenue** :

- **`Livre`** : constructeur avec promotion — `public readonly string $isbn` (identifiant immuable), `private string $titre`, `private string $auteur`, `private ?Membre $emprunteur = null` (une propriété **nulle = disponible**, c'est l'état de disponibilité, aucun attribut `disponible` redondant à synchroniser) ;
- **`Personne`** (mère) : `nom`, `prenom` protégés, `__toString()` ;
- **`Membre extends Personne`** : tableau **privé** `$emprunts` (le lien inverse existe aussi côté livre), constante `MAX_EMPRUNTS = 2` ;
- **Validations par exceptions** :
  - `Membre::emprunter()` refuse si le quota est atteint ; le livre refuse (`Livre::reserverPour()`) s'il est déjà emprunté ;
  - `Membre::rendre()` refuse (`InvalidArgumentException`) si le membre n'a pas ce livre ;
- **`__toString()`** sur les deux classes pour l'affichage direct.

Extraits de la sortie réelle :

```text
[978-2-07-041300-0] Le Petit Prince — A. de Saint-Exupéry (emprunté par Sara Hachimi)
Refusé : Sara Hachimi a déjà atteint son quota (2 livres).
Refusé : Le livre « Le Petit Prince » est déjà emprunté.
Refusé : Omar Alaoui n'a pas emprunté « PHP 8 – Développez ».
```

**Critères du défi respectés** : encapsulation (aucune propriété `public` inutile — seul l'ISBN est `public readonly`), constructeurs typés avec promotion, héritage `Membre extends Personne`, `readonly` utilisé à bon escient, validations avec exceptions, affichage via `__toString()`.

---

## Questions de synthèse

**1. Quelle est la différence entre une classe et un objet ?**

La **classe** est le **modèle** : elle définit, dans le code, les propriétés (données) et les méthodes (comportements) qu'auront ses instances. Elle n'existe qu'une fois et ne « vit » pas. L'**objet** est une **instance** de la classe, créée par `new` : il possède sa **propre copie** des propriétés (ses valeurs) et vit en mémoire. Exemple du TP : une seule classe `Voiture`, mais `$v1` (Toyota, bleue) et `$v2` (Renault, grise) sont deux objets indépendants (question 0.3). Analogie : la classe est le plan d'architecte, l'objet est la maison construite.

**2. Citez trois avantages de la POO par rapport à la programmation procédurale.**

1. **Encapsulation** : les données sensibles sont `private` et ne sont accessibles que par des méthodes qui valident — impossible de mettre un CIN en vrac ou un montant négatif sans que la classe l'ait décidé (Parties 1, 3, 7) ;
2. **Réutilisation par héritage** : `Etudiant` réutilise tout `Personne` (`extends`, `parent::`) sans recopier une ligne ; en procédural il faudrait dupliquer les fonctions (Partie 6) ;
3. **Maintenabilité / centralisation** : un comportement est défini à un seul endroit (`__toString()`, `getAge()`) et tout le programme en profite ; les objets regroupent données + comportements, ce qui limite les effets de bord et facilite les évolutions (principe DRY).

**3. Pourquoi est-il recommandé de déclarer les attributs en `private` ou `protected` ?**

Pour appliquer l'**encapsulation** : la classe garde le **contrôle total** de ses données. (a) Un attribut masqué ne peut pas être modifié sans passer par les méthodes qui **valident** (question 3.4) ; (b) les données sensibles (CIN, mot de passe) ne fuient pas (Parties 1 et 5) ; (c) le représentation interne peut **changer** sans casser le code extérieur, puisqu'il ne dépend que des méthodes publiques ; (d) `protected` prolonge ce contrôle aux classes filles sans exposer au public (Partie 6).

**4. Quelle différence entre `private` et `protected` quand il y a de l'héritage ?**

- `private` : accessible **uniquement dans la classe qui déclare** l'attribut/méthode — même ses classes filles ne le voient pas (`Etudiant` ne peut ni lire `dateNaissance` ni appeler `getAge()` si elles sont privées → questions 6.4.a/6.4.b) ;
- `protected` : accessible dans la classe déclarante **et toutes ses classes filles**, mais toujours **caché de l'extérieur**. C'est le niveau « famille » : on le choisit quand l'héritage a besoin d'un membre, sans l'ouvrir au public.

**5. Une méthode magique doit-elle être déclarée `public` ? Quelles exceptions sont tolérées ?**

**Oui, en règle générale** : `__get`, `__set`, `__isset`, `__unset`, `__toString()`, `__serialize()`, `__unserialize()` doivent être `public`, car PHP doit pouvoir les appeler **depuis l'extérieur** au moment de l'accès qui les déclenche. Les **exceptions tolérées** :

- `__construct()` : `public`, mais souvent `private`/`protected` **volontairement** pour interdire `new` et imposer des fabriques statiques (question 2.2.c) ou un Singleton ;
- `__destruct()` : peut être `private` (usage rare, déconseillé) ; PHP l'appelle depuis le contexte de l'objet ;
- `__debugInfo()` et `__callStatic()`… : `public` requis, sauf `__call()`/`__callStatic()` qui peuvent dans certains contextes être protégés selon l'usage interne (toléré par le moteur).
  En pratique pour le TP : **toutes les méthodes magiques de surcharge et `__toString()` s'écrivent `public`** ; seul `__construct()` se permet `private` pour un effet de conception voulu.

---

## 📦 Livrables produits

- Dossier `tp_poo/` : `partie0.php`, `partie1.php`, `partie2.php`, `partie3.php`, `partie4.php`, `partie5.php`, `partie6.php`, `partie7.php` **+ bonus `defi_final.php`** ;
- Fichier `reponses.md` (le présent document) ;
- Conventions respectées : classes en `PascalCase`, méthodes/attributs en `camelCase` (sauf `get_montant()`/`un_an()` imposés par l'énoncé de la Partie 7), indentation de 4 espaces, `declare(strict_types=1)` partout sauf `partie4.php` (nécessaire en mode faible pour la question 4.2).

> **Pour aller plus loin** : documentation officielle PHP — <https://www.php.net/manual/fr/language.oop5.php>
