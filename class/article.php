
<?php
class Article {
    public $id;
    public $nom;
    public $description;
    public $prix;
    public $lien;
    public $categorie;
    public $image;
    public $est_bloque;
    public $username;
    public $utilisateur_id;

    public function __construct($id, $nom, $description, $prix, $lien, $categorie, $image, $est_bloque, $utilisateur_id, $username) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->prix = $prix;
        $this->lien = $lien;
        $this->categorie = $categorie;
        $this->image = $image;
        $this->est_bloque = $est_bloque;
        $this->username = $username;
        $this->utilisateur_id = $utilisateur_id;
    }
}

?>