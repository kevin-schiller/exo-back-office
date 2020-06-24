<?php

// activation du système d'autoloading de Composer
require __DIR__.'/../vendor/autoload.php';

// instanciation du chargeur de templates
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../templates');

// instanciation du moteur de template
$twig = new \Twig\Environment($loader, [
    // activation du mode debug
    'debug' => true,
    // activation du mode de variables strictes
    'strict_variables' => true,
]);

// chargement de l'extension Twig_Extension_Debug
$twig->addExtension(new \Twig\Extension\DebugExtension());

// chargement des données de l'utilisateur
$articles = require __DIR__.'/articles-data.php';

// valeurs par defaut du formulaire
$formData = [
    'name' => '',
    'description' => '',
    'price' => '',
    'quantity' => '',
];

// le tableau contenant la liste des erreurs
$errors = [];
// le tableau contenant les messages d'erreurs
$messages = [];

// Vérification de présence de données envoyées par l'utilisateur
if ($_POST) {
    
    // remplacement des valeurs par défault par les valeurs envoyé par l'utilisateur
    if (isset($_POST['name'])) {
        $formData['name'] = $_POST['name'];
    }
    if (isset($_POST['description'])) {
        $formData['description'] = $_POST['description'];
    }
    if (isset($_POST['price'])) {
        $formData['price'] = $_POST['price'];
    }
    if (isset($_POST['quantity'])) {
        $formData['quantity'] = $_POST['quantity'];
    }

    // validation des données du champ name
    if (!isset($_POST['name']) || empty($_POST['name'])) {
        $errors['name'] = true;
        $messages['name'] = 'The text field must not be empty';
    } elseif (strlen($_POST['name']) < 2) {
        $errors['name'] = true;
        $messages['name'] = 'The text field must at least 2 characters';
    } elseif (strlen($_POST['name']) > 100) {
        $errors['name'] = true;
        $messages['name'] = 'The text field must at most 100 characters';
    }

    // validation des données du champ description
    if (
        strpos($_POST['description'], '<')
        || strpos($_POST['description'], '>')
    ) {
        $errors['description'] = true;
        $messages['description'] = 'Description contains prohibited character(s) < or >';
    }

    // validation des données du champ price
    if (!isset($_POST['price']) || empty($_POST['price'])) {
        $errors['price'] = true;
        $messages['price'] = 'The text field must not be empty';
    } elseif (!is_numeric($_POST['price'])) {
        $errors['price'] = true;
        $messages['price'] = 'The price must be a numerical value';
    }
    
    // validation des données du champ quantity
    if (!isset($_POST['quantity']) || empty($_POST['quantity'])) {
        $errors['quantity'] = true;
        $messages['quantity'] = 'The text field must not be empty';
    } elseif (!is_numeric($_POST['quantity'])) {
        $errors['quantity'] = true;
        $messages['quantity'] = 'The quantity must be an integer number';
    } elseif ((!is_int(0 + $_POST['quantity']))) {
        $errors['quantity'] = true;
        $messages['quantity'] = 'The quantity must be an integer number';
    }


    if (!$errors) {
        // enregistrement de données dans la variable de session
        $_SESSION['name'] = $articles['name'];
        $_SESSION['description'] = $articles['description'];
        $_SESSION['price'] = $articles['price'];
        $_SESSION['quantity'] = $articles['quantity'];

        // redirection vers la page des articles
        $url = 'articles.php';
        header("Location: {$url}", true, 302);
        exit();
    }
}


// affichage du rendu d'un template
echo $twig->render('article-new.html.twig', [
    // transmission de données au template
    'formData' => $formData,
    'errors' => $errors,
    'messages' => $messages,
]);