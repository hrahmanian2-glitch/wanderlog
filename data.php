<?php


$FICHIER_USERS = __DIR__ . '/data/users.csv';
$FICHIER_TRIPS = __DIR__ . '/data/trips.json';
$DOSSIER_UPLOADS = __DIR__ . '/uploads';




function emailExiste($email) {
    global $FICHIER_USERS;

    if (!file_exists($FICHIER_USERS)) {
        return false;
    }

    $lignes = file($FICHIER_USERS);

    foreach ($lignes as $ligne) {
        $colonnes = explode(',', trim($ligne));
        
        if ($colonnes[0] === $email) {
            return true;
        }
    }

    return false;
}


function connexionOk($email, $mdp) {
    global $FICHIER_USERS;

    if (!file_exists($FICHIER_USERS)) {
        return false;
    }

    $lignes = file($FICHIER_USERS);

    foreach ($lignes as $ligne) {
        $colonnes = explode(',', trim($ligne));

        
        if ($colonnes[0] === $email) {
            if (password_verify($mdp, $colonnes[3])) {
                return true;
            }
        }
    }

    return false;
}



function ajouterUser($prenom, $nom, $email, $mdp) {
    global $FICHIER_USERS;

    if (emailExiste($email)) {
        return false;
    }

    
    $mdpHash = password_hash($mdp, PASSWORD_DEFAULT);


    $ligne = $email . ',' . $prenom . ',' . $nom . ',' . $mdpHash . "\n";

    file_put_contents($FICHIER_USERS, $ligne, FILE_APPEND);

    return true;
}


function getUser($email) {
    global $FICHIER_USERS;

    if (!file_exists($FICHIER_USERS)) {
        return null;
    }

    $lignes = file($FICHIER_USERS);

    foreach ($lignes as $ligne) {
        $colonnes = explode(',', trim($ligne));
        if ($colonnes[0] === $email) {
            return [
                'email'  => $colonnes[0],
                'prenom' => $colonnes[1],
                'nom'    => $colonnes[2]
            
            ];
        }
    }

    return null;
}



function getAllTrips() {
    global $FICHIER_TRIPS;

    if (!file_exists($FICHIER_TRIPS)) {
        return [];
    }

    $contenu = file_get_contents($FICHIER_TRIPS);
    $voyages = json_decode($contenu, true);

    
    if (!is_array($voyages)) {
        return [];
    }

    return $voyages;
}



function ajouterTrip($email, $titre, $histoire, $lat, $lng, $lieu, $date, $photos) {
    global $FICHIER_TRIPS;

    $voyages = getAllTrips();

    
    $nouvelId = 1;
    foreach ($voyages as $v) {
        if ($v['id'] >= $nouvelId) {
            $nouvelId = $v['id'] + 1;
        }
    }

    $nouveauVoyage = [
        'id'         => $nouvelId,
        'email'      => $email,
        'titre'      => $titre,
        'histoire'   => $histoire,
        'lat'        => floatval($lat),
        'lng'        => floatval($lng),
        'lieu'       => $lieu,
        'date'       => $date,
        'photos'     => $photos,
        'created_at' => time()  
    ];

    $voyages[] = $nouveauVoyage;

    
    file_put_contents($FICHIER_TRIPS, json_encode($voyages, JSON_PRETTY_PRINT));

    return $nouvelId;
}



function getTrip($id) {
    $voyages = getAllTrips();
    foreach ($voyages as $v) {
        if ($v['id'] == $id) {
            return $v;
        }
    }
    return null;
}



$FICHIER_LIKES = __DIR__ . '/data/likes.json';


function getAllLikes() {
    global $FICHIER_LIKES;
    if (!file_exists($FICHIER_LIKES)) return [];
    $contenu = file_get_contents($FICHIER_LIKES);
    $likes = json_decode($contenu, true);
    return is_array($likes) ? $likes : [];
}


function compterLikes($tripId) {
    $likes = getAllLikes();
    $n = 0;
    foreach ($likes as $like) {
        if ($like['trip_id'] == $tripId) {
            $n++;
        }
    }
    return $n;
}


function userALike($email, $tripId) {
    $likes = getAllLikes();
    foreach ($likes as $like) {
        if ($like['email'] === $email && $like['trip_id'] == $tripId) {
            return true;
        }
    }
    return false;
}


function toggleLike($email, $tripId) {
    global $FICHIER_LIKES;
    $likes = getAllLikes();


    $nouveauxLikes = [];
    $existait = false;

    foreach ($likes as $like) {
        if ($like['email'] === $email && $like['trip_id'] == $tripId) {
            $existait = true;
        
        } else {
            $nouveauxLikes[] = $like;
        }
    }

    if (!$existait) {
        $nouveauxLikes[] = [
            'email'      => $email,
            'trip_id'    => intval($tripId),
            'created_at' => time()
        ];
    }

    file_put_contents($FICHIER_LIKES, json_encode($nouveauxLikes, JSON_PRETTY_PRINT));

    return !$existait;
}



$FICHIER_COMMENTS = __DIR__ . '/data/comments.json';


function getCommentairesDuTrip($tripId) {
    global $FICHIER_COMMENTS;
    if (!file_exists($FICHIER_COMMENTS)) return [];
    $contenu = file_get_contents($FICHIER_COMMENTS);
    $tous = json_decode($contenu, true);
    if (!is_array($tous)) return [];

    $resultat = [];
    foreach ($tous as $c) {
        if ($c['trip_id'] == $tripId) {
            
            $auteur = getUser($c['email']);
            $c['auteur'] = $auteur ? ($auteur['prenom'] . ' ' . $auteur['nom']) : 'Inconnu';
            $resultat[] = $c;
        }
    }

    return $resultat;
}


function ajouterCommentaire($email, $tripId, $contenu) {
    global $FICHIER_COMMENTS;

    if (!file_exists($FICHIER_COMMENTS)) {
        $tous = [];
    } else {
        $tous = json_decode(file_get_contents($FICHIER_COMMENTS), true);
        if (!is_array($tous)) $tous = [];
    }

    $nouvelId = 1;
    foreach ($tous as $c) {
        if ($c['id'] >= $nouvelId) {
            $nouvelId = $c['id'] + 1;
        }
    }

    $nouveau = [
        'id'         => $nouvelId,
        'email'      => $email,
        'trip_id'    => intval($tripId),
        'contenu'    => $contenu,
        'created_at' => time()
    ];

    $tous[] = $nouveau;
    file_put_contents($FICHIER_COMMENTS, json_encode($tous, JSON_PRETTY_PRINT));


    $auteur = getUser($email);
    $nouveau['auteur'] = $auteur ? ($auteur['prenom'] . ' ' . $auteur['nom']) : 'Inconnu';

    return $nouveau;
}
