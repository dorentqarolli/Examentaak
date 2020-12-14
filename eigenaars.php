<?php
    include 'functions.php';

    $conn = maakConnectie();

    $arrEigenaar = maakArray($conn);

    //Array van dier maken
    function maakArray($conn){
        //data selecteren
        $sql = "SELECT * FROM eigenaars";
        $result = $conn->query($sql);
        $arrEigenaar = array();
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $arrEigenaar[$row["id"]]['volledigenaam'] = $row["volledigenaam"];
                $arrEigenaar[$row["id"]]['adres'] = $row["adres"];
                $arrEigenaar[$row["id"]]['telefoonnummer'] = $row["telefoonnummer"];
                $arrEigenaar[$row["id"]]['email'] = $row["email"];
            }    
        } else {
            echo "0 results";
        }
        return $arrEigenaar;

    }
	
	//Dropdown om mijn dier te selecteren
    function kiesEigenaar($arrEigenaar,$idCurrentEigenaar){
        $returnString = "<div class='row'>
                <div class='col-12'>
                    <div class='form-group'>
                        <label for='idCurrentEigenaar'>Kies een eigenaar</label>
                        <select class='form-control' id='idCurrentEigenaar' name='idCurrentEigenaar' onchange='this.form.submit()'>
                            <option value=''>Kies een nieuwe eigenaar</option>";
        foreach($arrEigenaar as $key => $value){
            $selected = NULL;
            if($key == $idCurrentEigenaar){
                $selected = "SELECTED";
            }
              $returnString .="
                            <option value='$key' $selected >{$value['volledigenaam']}</option>";
        }
        $returnString .= "
                        </select>
                    </div>
                </div>
            </div>
            <hr>";
        return $returnString;
    }
	//Dropdown met de gegevens van mijn eigenaar
    function formEigenaar($arrEigenaar,$idCurrentEigenaar){
        $returnString = NULL;
        if($idCurrentEigenaar != NULL){
            $returnString = PHP_EOL . "
            <div class='row'>
                <div class='col-12'>
                    <h2>Eigenaar</h2>
                </div>
                <div class='col-6'>
                    <div class='form-group'>
                        <label for='volledigenaam'>naam</label>
                        <input type='text' class='form-control' id='volledigenaam' name='volledigenaam' value='{$arrEigenaar[$idCurrentEigenaar]['volledigenaam']}'>
                    </div>
                    <div class='form-group'>
                        <label for='adres'>adres</label>
                        <input type='date' class='form-control' id='adres' name='adres' value='{$arrEigenaar[$idCurrentEigenaar]['adres']}'>
                    </div>
                </div>    
                <div class='col-6'>
                    <div class='form-group'>
                        <label for='telefoonnummer'>telefoonnummer</label>
                        <input type='text' class='form-control' id='telefoonnummer' name='telefoonnummer' value='{$arrEigenaar[$idCurrentEigenaar]['telefoonnummer']}'>
                    </div>
                    <div class='form-group'>
                        <label for='email'>email</label>
                        <input type='text' class='form-control' id='email' name='email' value='{$arrEigenaar[$idCurrentEigenaar]['email']}'>
                    </div>
                </div>
            </div><hr>";
            
        }else{
            $returnString = PHP_EOL . "<div class='row'>
                <div class='col-12'>
                    <h2>Eigenaar</h2>
                </div>
                <div class='col-6'>
                    <div class='form-group'>
                        <label for='volledigenaam'>naam</label>
                        <input type='text' class='form-control' id='volledigenaam' name='volledigenaam' value=''>
                    </div>
                    <div class='form-group'>
                        <label for='adres'>adres</label>
                        <input type='text' class='form-control' id='adres' name='adres' value=''>
                    </div>
                </div>    
                <div class='col-6'>
                    <div class='form-group'>
                        <label for='telefoonnummer'>telefoonnummer</label>
                        <input type='text' class='form-control' id='telefoonnummer' name='telefoonnummer' value=''>
                    </div>
                    <div class='form-group'>
                        <label for='email'>email</label>
                        <input type='text' class='form-control' id='email' name='email' value=''>
                    </div>
                </div>
            </div><hr>";
        }
        return $returnString;
    }
	//Maak de knoppen onderaan het formulier
    function buttonBarEigenaar($idCurrentEigenaar){
        $returnString = NULL;
        if($idCurrentEigenaar==NULL){
            //Knoppen voor een nieuw eigenaar
            $returnString .="
            <div class='row'>
                <div class='col-md-12 text-center'>
                    <div class='btn-group' role='group'>
                      <button type='button' class='btn btn-success' onclick=\"this.form.actie.value='newEigenaar'; this.form.submit()\"><i class='fa fa-plus'></i> Maak nieuwe eigenaar</button>
                      <button type='button' class='btn btn-danger' onclick=\"this.form.actie.value=''; this.form.submit()\"><i class='fa fa-close'></i> Annuleren</button>
                    </div>
                </div>
            </div>";
        }else{
            //Knoppen voor een bestaande eigenaar
            $returnString .="
            <div class='row'>
                <div class='col-md-12 text-center'>
                    <div class='btn-group' role='group'>
                      <button type='button' class='btn btn-success' onclick=\"this.form.actie.value='updateEigenaar'; this.form.submit()\"><i class='fa fa-check'></i> Gegevens actualiseren</button>
                      <button type='button' class='btn btn-danger' onclick=\"this.form.actie.value=''; this.form.submit()\"><i class='fa fa-close'></i> Annuleren</button>
                    </div>
                </div>
            </div>";
        }
        return $returnString;
    }

	$idCurrentEigenaar = NULL;
	if(isset($_GET['idCurrentEigenaar'])){
		$idCurrentEigenaar = $_GET['idCurrentEigenaar'];
	}

	$actie = NULL;

	if(isset($_GET['actie'])){
		$actie = $_GET['actie'];
		$_GET['actie'] = NULL;
	}

	if($idCurrentEigenaar != NULL && $actie=="updateEigenaar"){
		$sql = "UPDATE eigenaars SET 
		volledigenaam = '{$_GET['volledigenaam']}', 
		adres= '{$_GET['adres']}', 
		telefoonnummer = '{$_GET['telefoonnummer']}', 
		email = '{$_GET['email']}'    
		WHERE ID = $idCurrentEigenaar";
		if ($conn->query($sql) === TRUE) {
			$arrEigenaar = maakArray($conn);
		} else {
			 echo "Error updating record: " . $conn->error;
		}
	}elseif(isset($_GET['volledigenaam']) && $actie=="newEigenaar"){
		$sql = "INSERT INTO eigenaars (volledigenaam, adres, telefoonnummer, email)
	VALUES ('{$_GET['volledigenaam']}', '{$_GET['adres']}', '{$_GET['telefoonnummer']}', '{$_GET['email']}')";

		if ($conn->query($sql) === TRUE) {
			$idCurrentEigenaar = $conn->insert_id;
			 $arrEigenaar = maakArray($conn);
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
			}
	}
	
?>

<!doctype html>
<html lang="nl">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/styles.css">

    <title>Eigenaar</title>
  </head>
  <body>
    <form method="GET">
        <input type="hidden" name="actie" value="">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Eigenaar</h1>
                </div>
            </div>
            <hr>
            <?php print kiesEigenaar($arrEigenaar,$idCurrentEigenaar); ?>
            <?php print formEigenaar($arrEigenaar,$idCurrentEigenaar); ?>
            <?php print buttonBarEigenaar($idCurrentEigenaar) ?>
        </div>
		
		<div class="btn-group">
			<a href="index.php" class="btn btn-primary">Home Pagina</a>
			<a href="dieren.php" class="btn btn-primary">nieuwe dieren invoegen</a>
		</div>
		
    </form>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>