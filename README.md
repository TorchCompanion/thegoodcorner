TODO -

~~1-) recupérer le system de filtre dans AnnonceService~~

~~2-) faire un formulaire de filtre qui marche~~

~~3-) ajouter du js pour pas envoyer les champs vides~~

~~4-) ajouter des filtres coherent avec vos entities Annonce~~

~~5-) ajouter une recherche "photo"~~

~~6-) Créer la page profil~~

7-) Afficher un/des formulaire d'édition de profil si $this->getUser() === $userProfile

~~8-) afficher listing annonce where a.owner === $userProfile~~

------------------------------------------------------------------------
CONTROLLER :
----------

- UserController
    - Form adresse
    - Form user
    - Display user

ENTITE :
--------

- Préparer entité Cart

FORMULAIRE :
------------

- Finir "AdressType"
- Ajout suppression annonce (picture premier)
- Ajout modification annonce

TWIG :
------

- Créer User.Display
    - Si Display != User, afficher un bouton pour edit
    - Créer User.Edit :
        - Modification des informations de l'utilisateur
        - Modification de la photo de profil
        - Afficher un formulaire pour edit les informations de l'utilisateur
        - Afficher un formulaire pour edit les annonces de l'utilisateur

------------------------------------------------------------------------

Idées :
-------
Annonce adresse : Champ Utiliser adresse existante->Adresse stocké dans User OU créer une nouvelle adresse


