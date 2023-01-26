<?php
    include_once 'connection.php';

    $id = $_GET['id'];

    $cursor = $velib->findOne(["_id"=>$id]);

    if (isset($_POST['submit'])) {
        $post_data = array();
        $post_data['_id']                           = $_POST['txtid'];
        $post_data['code']                          = $_POST['txtcode'];
        $post_data['name']                          = $_POST['txtname'];
        $post_data['is_installed']                  = $_POST['txtis_installed'];
        $post_data['numdocksavailable']             = $_POST['txtnumdocksavailable'];
        $post_data['numbikesavailable']             = $_POST['txtnumbikesavailable'];
        $post_data['mechanical']                    = $_POST['txtmechanical'];
        $post_data['ebike']                         = $_POST['txtebike'];
        $post_data['capacity']                      = $_POST['txtcapacity'];
        $post_data['is_renting']                    = $_POST['txtis_renting'];
        $post_data['duedate']                       = $_POST['txtduedate'];
        $post_data['nom_arrondissement_communes']   = $_POST['txtnom_arrondissement_communes'];
 
        $result = $velib->updateOne(['_id'=>$post_data['_id']],['$set'=>$post_data],['upsert' => false]);
        header("location: index.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <title>UPDATE VELIB</title>
</head>
<body>
    <nav class="navbar navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">VELIB</a>
            </div>
    </nav>
    <div class="container">
        <div class="card mt-3 mb-2 bg-light">
            <h4 class="card-title mx-auto mt-4">Update Velib</h4>
            <div class="card-body">
                <form method="POST" class="my-3 mx-3">
                    <div class="mb-3">
                        <label for="id" class="form-label">_id</label>
                        <input type="text" readonly value="<?php echo $cursor['_id']; ?>" class="form-control" id="id" name="txtid" aria-describedby="id">
                    </div>
                    <div class="mb-3">
                        <label for="duedate" class="form-label">Actualisation de la donnée</label>
                        <input type="datetime" readonly value=" <?php $dateTime = new DateTime('NOW'); echo $dateTime->format(DateTimeInterface::W3C); ?>" class="form-control" id="duedate" name="txtduedate" aria-describedby="duedate">
                    </div>
                    <div class="mb-3">
                        <label for="code" class="form-label">Identifiant station</label>
                        <input type="number" min="1000" max="99999" value="<?php echo $cursor['code']; ?>" class="form-control" id="code" name="txtcode" aria-describedby="code" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom station</label>
                        <input type="text" minlength="3" value="<?php echo $cursor['name']; ?>" class="form-control" id="name" name="txtname" aria-describedby="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="nom_arrondissement_communes" class="form-label">Nom commune équipée</label>
                        <input type="text" readonly value="<?php echo $cursor['nom_arrondissement_communes']; ?>" class="form-control" id="nom_arrondissement_communes" name="txtnom_arrondissement_communes" aria-describedby="nom_arrondissement_communes" required>
                    </div>
                    <div class="mb-3">
                        <label for="is_installed" class="form-label">Station en fonctionnement</label>
                        <select class="form-select" id="is_installed" name="txtis_installed">
                            <option>OUI</option>
                            <option>NON</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="is_renting" class="form-label">Borne de paiement disponible</label>
                        <select class="form-select" id="is_renting" name="txtis_renting">
                            <option>OUI</option>
                            <option>NON</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="numdocksavailable" class="form-label">Nombre bornettes libres</label>
                        <input type="number" min="0" value="<?php echo $cursor['numdocksavailable']; ?>" class="form-control" id="numdocksavailable" name="txtnumdocksavailable" aria-describedby="numdocksavailable" required>
                    </div>
                    <div class="mb-3">
                        <label for="numbikesavailable" class="form-label">Nombre total vélos disponibles</label>
                        <input type="number" min="0" value="<?php echo $cursor['numbikesavailable']; ?>" class="form-control" id="numbikesavailable" name="txtnumbikesavailable" aria-describedby="numbikesavailable" required>
                    </div>
                    <div class="mb-3">
                        <label for="mechanical" class="form-label">Vélos mécaniques disponibles</label>
                        <input type="number" min="0" value="<?php echo $cursor['mechanical']; ?>" class="form-control" id="mechanical" name="txtmechanical" aria-describedby="mechanical" required>
                    </div>
                    <div class="mb-3">
                        <label for="ebike" class="form-label">Vélos électriques disponibles</label>
                        <input type="number" min="0" value="<?php echo $cursor['ebike']; ?>" class="form-control" id="ebike" name="txtebike" aria-describedby="ebike" required>
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacité de la station</label>
                        <input type="number" min="0" value="<?php echo $cursor['capacity']; ?>" class="form-control" id="capacity" name="txtcapacity" aria-describedby="capacity" required>
                    </div>


                    <div class="d-grid gap-2 col-6 mx-auto">
                        <input type="submit" name="submit" class="btn btn-success" value="Update"/>
                        <a href="index.php" class="btn btn-warning">View Velib</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</body>
</html>