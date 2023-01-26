<?php

    include_once 'connection.php';

    // Affichage de la Base de données
    $filter = [];
    $options = ['sort' => ['duedate' => -1]];
    $cursor = $velib -> find($filter, $options);

    // Live
    // Initialiser CURL
    $curl_velib = curl_init("https://opendata.paris.fr/api/records/1.0/search/?dataset=velib-disponibilite-en-temps-reel&q=&rows=500&sort=duedate&facet=name&facet=is_installed&facet=is_renting&facet=is_returning&facet=nom_arrondissement_communes&facet=duedate&refine.nom_arrondissement_communes=Paris&refine.is_installed=OUI&timezone=Europe%2FParis");
    $curl_meteo = curl_init("https://api.tutiempo.net/json/?lan=fr&apid=zsTzaXzqqqXbxsh&lid=39720");

    // Définir les options de CURL
    curl_setopt_array($curl_velib, [
        CURLOPT_RETURNTRANSFER      => true,
        CURLOPT_TIMEOUT             => 1
    ]);

    curl_setopt_array($curl_meteo, [
        CURLOPT_RETURNTRANSFER      => true,
        CURLOPT_TIMEOUT             => 1
    ]);

    // Exécuter la requête
    $data       = curl_exec($curl_velib);
    $data_meteo = curl_exec($curl_meteo);

    
    if ($data === false) {
        var_dump (curl_error($curl_velib));
    }
    else {
        if ($data_meteo === false) {
            var_dump (curl_error($curl_meteo));
        }
        else {
            if (curl_getinfo ($curl_velib, CURLINFO_HTTP_CODE) === 200) {
                if (curl_getinfo ($curl_meteo, CURLINFO_HTTP_CODE) === 200) {
                    
                    $data = json_decode ($data, true);
                    $data_meteo = json_decode ($data_meteo, true);
        
                    for ($i = 0; $i < 500; $i++) {
                            
                        if (isset($_POST['submit'])) {
                            $post_data = array();
                            $post_data['_id']                           = $data['records'][$i]['recordid'];            
                            $post_data['code']                          = $data['records'][$i]['fields']['stationcode'];
                            $post_data['name']                          = $data['records'][$i]['fields']['name']; 
                            $post_data['is_installed']                  = $data['records'][$i]['fields']['is_installed'];
                            $post_data['numdocksavailable']             = $data['records'][$i]['fields']['numdocksavailable'];
                            $post_data['numbikesavailable']             = $data['records'][$i]['fields']['numbikesavailable'];
                            $post_data['mechanical']                    = $data['records'][$i]['fields']['mechanical'];
                            $post_data['ebike']                         = $data['records'][$i]['fields']['ebike'];
                            $post_data['capacity']                      = $data['records'][$i]['fields']['capacity'];
                            $post_data['is_renting']                    = $data['records'][$i]['fields']['is_renting'];
                            $post_data['duedate']                       = $data['records'][$i]['fields']['duedate'];
                            $post_data['nom_arrondissement_communes']   = $data['records'][$i]['fields']['nom_arrondissement_communes'];

                            $post_data['temperature']                   = $data_meteo['hour_hour']['hour1']['temperature']; 
                            $post_data['ciel']                          = $data_meteo['hour_hour']['hour1']['text'];
                            $post_data['humidity']                      = $data_meteo['hour_hour']['hour1']['humidity'];
                            $post_data['wind']                          = $data_meteo['hour_hour']['hour1']['wind'];   
                            $post_data['date']                          = $data_meteo['hour_hour']['hour1']['date'];  
                            $post_data['heure']                         = $data_meteo['hour_hour']['hour1']['hour_data'];


                            $existing = $velib->findOne(['$and' => [["code" => $data['records'][$i]['fields']['stationcode']], ["duedate" => $data['records'][$i]['fields']['duedate']]]]);

                            // Mise à jour ou insertion de l'élément
                            if ($existing) {
                                $velib->updateOne(["_id" => md5(uniqid())], ['$set' => $post_data]);
                                header("Refresh:0");
                            }                                          
                            else {
                                $velib->insertOne($post_data);
                                header("Refresh:0");
                            } 
                            
                                
                        }
                    }
                }                
            }
        }
        
    }
    
    // Fermer la session CURL
    curl_close($curl_velib);

    

    if(isset($_GET['valider']) && !empty(trim($_GET['searchcode']))) {
        $searchcode = $_GET['searchcode'];
        // Agrégation pour effectuer la recherche par code
        $result = $velib->aggregate([
            ['$match' => [
                'code' => $searchcode
                ]
            ],
            ['$sort' => [
                'duedate' => -1
                ]
            ]
        ]);
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- JavaScript Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="style.css">

        <title>VELIB</title>
    </head>


    <body>
        <nav class="navbar navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">VELIB</a>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="card mt-3 mb-3 bg-light">
                <div class="card-header text-center">
                    <h5>All Velibs : <?php echo ($velib->count()); ?> Lignes </h5>
                </div>
                
                <form method="GET" class="d-flex">
                    <input class="form-control me-2" type="text" name="searchcode" placeholder="Search by code">
                    <input type="submit" name="valider" class="btn btn-primary" value="SEARCH"/>
                </form>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mt-0">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col" style="display:none">_id</th>
                                    <th scope="col">code</th>
                                    <th scope="col">Nom</th>
                                    <th scope="col">En fonction</th>
                                    <th scope="col">Bornette libre</th>
                                    <th scope="col">Velo dispo</th>
                                    <th scope="col">Velo mécan. dispo</th>
                                    <th scope="col">Velo élect. dispo</th>
                                    <th scope="col">Capacité</th>
                                    <th scope="col">Paiement dispo</th>
                                    <th scope="col">MAJ</th>
                                    <th scope="col">Nom arrondissement</th>
                                    <th scope="col">Temperature</th>
                                    <th scope="col">Ciel</th>
                                    <th scope="col">Humidite</th>
                                    <th scope="col">Vent</th>
                                    <th scope="col" style="text-align:right"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (empty($result)) {

                                    foreach($cursor as $document) {?>
                                        <tr>
                                            <th scope="row" style="display:none"><?php echo $document['_id']; ?></th>
                                            <td><?php echo $document['code']; ?></td>
                                            <td><?php echo $document['name']; ?></td>
                                            <td><?php echo $document['is_installed']; ?></td>
                                            <td><?php echo $document['numdocksavailable']; ?></td>
                                            <td><?php echo $document['numbikesavailable']; ?></td>
                                            <td><?php echo $document['mechanical']; ?></td>
                                            <td><?php echo $document['ebike']; ?></td>
                                            <td><?php echo $document['capacity']; ?></td>
                                            <td><?php echo $document['is_renting']; ?></td>
                                            <td><?php echo $document['duedate']; ?></td>
                                            <td><?php echo $document['nom_arrondissement_communes']; ?></td>
                                            <td><?php echo $document['temperature']; ?></td>
                                            <td><?php echo $document['ciel']; ?></td>
                                            <td><?php echo $document['humidity']; ?></td>
                                            <td><?php echo $document['wind']; ?></td>
                                            <!-- Button -->
                                            <td style="text-align:right">
                                                <a href="delete.php?id=<?php echo $document['_id']; ?>" class="btn btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                <a href="update.php?id=<?php echo $document['_id']; ?>" class="btn btn-warning"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                            </td>
                                        </tr>
                                    <?php } 
                                }
                                else {
                                    foreach($result as $res) { ?>
                                        <tr>
                                            <th scope="row" style="display:none"><?php echo $document['_id']; ?></th>
                                            <td><?php echo $res['code']; ?></td>
                                            <td><?php echo $res['name']; ?></td>
                                            <td><?php echo $res['is_installed']; ?></td>
                                            <td><?php echo $res['numdocksavailable']; ?></td>
                                            <td><?php echo $res['numbikesavailable']; ?></td>
                                            <td><?php echo $res['mechanical']; ?></td>
                                            <td><?php echo $res['ebike']; ?></td>
                                            <td><?php echo $res['capacity']; ?></td>
                                            <td><?php echo $res['is_renting']; ?></td>
                                            <td><?php echo $res['duedate']; ?></td>
                                            <td><?php echo $res['nom_arrondissement_communes']; ?></td>
                                            <td><?php echo $res['temperature']; ?></td>
                                            <td><?php echo $res['ciel']; ?></td>
                                            <td><?php echo $res['humidity']; ?></td>
                                            <td><?php echo $res['wind']; ?></td>
                                            <!-- Button -->
                                            <td style="text-align:right">
                                                <a href="delete.php?id=<?php echo $res['_id']; ?>" class="btn btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                <a href="update.php?id=<?php echo $res['_id']; ?>" class="btn btn-warning"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                            </td>
                                        </tr>
                                    <?php }
                                } 
                                ?>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" class="my-3 mx-3">
                    <div class="d-grid gap-2 col-6 mx-auto float">
                        <input type="submit" name="submit" class="btn btn-primary" value="LIVE"/>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>