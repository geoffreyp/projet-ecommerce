Cart
    # user_id
    # product_id
    quantity / integer

Table de relation entre Cart et Product

Category
    name / string 
    description / text
    
Product
    name / string
    description / text 
    status / boolean
    stock / integer
    price / float
    # category_id
    
User
    username / string
    email / string
    address / text 
    zip / string
    city / string 
    country / string

Photo
    product_id
    path / string
    
Comment
    title 
    content
    username
    created_at
    Relation avec la table Product
    
# Fonctionnalités
    
    Page d'accueil
        liste des 5 derniers produits 
        menu avec toutes les catégories
        Lien sur chaque produit 
        Afficher le menu sur toutes les pages 
        Liste aléatoire
        Internationalisation
        Formulaires (avis)
        Page de contact 
        Prix HT / Prix TTC 
        Articles les plus vus
        Fonction twig pour la TVA 
        Sécurité
        Panier
        
        Tests unitaires
        Tests fonctionnels