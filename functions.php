<?php
function maakConnectie(){
        //connectie met databank
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "dierenarts";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $database);

        // Check connection
        if ($conn -> connect_error) {
            die("Connection failed: " . $conn -> connect_error);
        }
        return $conn;
    }
	
	function maakArray($conn){
        //data selecteren
        $sql = "SELECT * FROM dieren";
        $result = $conn->query($sql);
        $arrDier = array();
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $arrDier[$row["id"]]['naam'] = $row["naam"];
                $sqlEigenaar = "SELECT 
                eigenaars.id as idEigenaar,
                eigenaars.volledigenaam as naamEigenaar
                FROM eigenaar_dier
                INNER JOIN eigenaars 
                ON eigenaar_dier.id_eigenaar=eigenaars.id
                WHERE eigenaar_dier.id_dier =".$row["id"];

                $sqlAandoening = "SELECT 
                aandoeningen.id as idAandoening,
                aandoeningen.aandoening as aandoening,
                aandoeningen.beschrijving as beschrijvingAandoening
                FROM aandoening_dier
                INNER JOIN aandoeningen
                ON aandoening_dier.id_aandoening=aandoeningen.id
                WHERE aandoening_dier.id_dier =".$row["id"];
				
                $sqlBehandeling = "SELECT
                behandelingen.id as idBehandeling,
                behandelingen.datum as datumBehandeling,
                behandelingen.behandeling as behandeling
                FROM aandoeningen
                INNER JOIN behandelingen
                ON aandoeningen.id=behandelingen.id
                WHERE behandelingen.id_dier =".$row["id"];
                
                $rstEigenaar = $conn->query($sqlEigenaar);
                if ($rstEigenaar->num_rows > 0) {
                    while($rowEigenaar = $rstEigenaar->fetch_assoc()) {
                        $arrDier[$row["id"]]['eigenaars'][$rowEigenaar["idEigenaar"]] = array(
                            "naamEigenaar" => $rowEigenaar["naamEigenaar"]);
                    }
                }
//die($sqlAandoening);
                $rstAandoening = $conn->query($sqlAandoening);
                if ($rstAandoening->num_rows > 0) {
                    while($rowAandoening = $rstAandoening->fetch_assoc()) {
                        $arrDier[$row["id"]]['aandoeningen'][$rowAandoening["idAandoening"]] = array(
                            "aandoening" => $rowAandoening["aandoening"],
                            "beschrijvingAandoening" => $rowAandoening["beschrijvingAandoening"]);
                    }
                }

                $rstBehandeling = $conn->query($sqlBehandeling);
                if ($rstBehandeling->num_rows > 0) {
                    while($rowBehandeling = $rstBehandeling->fetch_assoc()) {
                        $arrDier[$row["id"]]['behandelingen'][$rowBehandeling["idBehandeling"]] = array(
                            "datumBehandeling" => $rowBehandeling["datumBehandeling"],
                            "behandeling" => $rowBehandeling["behandeling"]);
                    }
                }
            }
            
        } else {
            echo "0 results";
        }
        return $arrDier;

    }
	
	 function kiesDier($arrDier,$idCurrentDier){
        $returnString = "<div class='row'>
                <div class='col-12'>
                    <div class='form-group'>
                        <label for='idCurrentDier'>Kies een dier</label>
                        <select class='form-control' id='idCurrentDier' name='idCurrentDier' onchange='this.form.submit()'>
                            <option value=''>---NIEUW DIER---</option>";
        foreach($arrDier as $key => $value){
            $selected = NULL;
            if($key == $idCurrentDier){
                $selected = "SELECTED";
            }
              $returnString .="
                            <option value='$key' $selected >{$value['naam']}</option>";
        }
        $returnString .= "
                        </select>
                    </div>
                </div>
            </div>
            <hr>";
        return $returnString;
    }
	
	function formDier($arrDier,$idCurrentDier){
        $returnString = NULL;
        if($idCurrentDier != NULL){
            $returnString = PHP_EOL . "
            <div class='row'>
                <div class='col-12'>
                    <h2>Dier</h2>
                </div>
                <div class='col-6'>
                    <div class='form-group'>
                        <label for='naam'>naam</label>
                        <input type='text' class='form-control' id='naam' name='naam' value='{$arrDier[$idCurrentDier]['naam']}'>
                    </div>
                </div>
                <div class='col-12'>

                    <h2>Eigenaars</h2>

                </div>
                <div class='col-6'>
                    <div class='form-group'>";
                    foreach ($arrDier[$idCurrentDier]['eigenaars'] as $idEigenaar => $dataEigenaar) {
                        $returnString .= "<label for='naamEigenaar'>Naam</label>

                        <input type='text' class='form-control' id='eigenaar' name='eigenaar' value='{$dataEigenaar['naamEigenaar']}'>";

                    };
                $returnString .= "</div>
                </div>
                <div class='col-12'>
                    <h2>Aandoening</h2>
                </div>
                <div class='col-6'>
                    <div class='form-group'>";
                    foreach ($arrDier[$idCurrentDier]['aandoeningen'] as $idAandoening => $dataAandoening) {
                        $returnString .= "<label for='aandoening'>Aandoening</label>

                        <input type='text' class='form-control' id='aandoening' name='aandoening' value='{$dataAandoening['aandoening']}'>
                        <label for='beschrijvingAandoening'>Beschrijving</label>
                        <input type='text' class='form-control' id='beschrijving' name='beschrijving' value='{$dataAandoening['beschrijvingAandoening']}'>";

                    };
                    $returnString .= "</div>
                </div>
                <div class='col-12'>
                    <h2>Behandeling</h2>
                </div>
                <div class='col-10'>
                    <div class='form-group'>";
                    foreach ($arrDier[$idCurrentDier]['behandelingen'] as $idBehandeling => $dataBehandeling) {
                        $returnString .= "<label for='datumBehandeling'>Datum</label>

                        <input type='text' class='form-control' id='datum' name='datum' value='{$dataBehandeling['datumBehandeling']}'>
                        <label for='behandeling'>Behandeling</label>
                        <input type='text' class='form-control' id='behandeling' name='behandeling' value='{$dataBehandeling['behandeling']}'>";

                    };
                    $returnString .= "</div>
                </div>
            </div><hr>";
            
        }else{
            $returnString = PHP_EOL . "<div class='row'>
			<div class='col-12'>
				<h2>Dier</h2>
			</div>
                <div class='col-6'>
                    <div class='form-group'>
                        <label for='naam'>naam</label>
                        <input type='text' class='form-control' id='naam' name='naam' value=''>
                    </div>
                </div>
            <div class='col-12'>
                <h2>Eigenaar</h2>
            </div>
                <div class='col-6'>
                    <div class='form-group'>";
                    foreach ($arrDier[$idCurrentDier]['eigenaars'] as $idEigenaar => $dataEigenaar) {
                        $returnString .= "<label for='naamEigenaar'>Naam</label>

                        <input type='text' class='form-control' id='eigenaar' name='eigenaar' value=''>";

                    };
                $returnString .= "</div>
                </div>
            <div class='col-12'>
                <h2>Aandoening</h2>
            </div>
                <div class='col-6'>
                    <div class='form-group'>";
                    foreach ($arrDier[$idCurrentDier]['aandoeningen'] as $idAandoening => $dataAandoening) {
                        $returnString .= "<label for='aandoening'>Aandoening</label>

                        <input type='text' class='form-control' id='aandoening' name='aandoening' value=''>
                        <label for='beschrijvingAandoening'>Beschrijving</label>
                        <input type='text' class='form-control' id='beschrijving' name='beschrijving' value=''>";

                    };
                    $returnString .= "</div>
                </div>
            <div class='col-12'>
                <h2>Behandeling</h2>
            </div>
                <div class='col-10'>
                    <div class='form-group'>";
                    foreach ($arrDier[$idCurrentDier]['behandelingen'] as $idBehandeling => $dataBehandeling) {
                        $returnString .= "<label for='datumBehandeling'>Datum</label>

                        <input type='text' class='form-control' id='datum' name='datum' value=''>
                        <label for='behandeling'>Behandeling</label>
                        <input type='text' class='form-control' id='behandeling' name='behandeling' value=''>";
                    };
                    $returnString .= "</div>
            </div><hr>";
        }
        return $returnString;
    }
	function buttonBar($idCurrentDier){
        $returnString = NULL;
        if($idCurrentDier==NULL){
            //Knoppen voor een nieuw dier
            $returnString .="
            <div class='row'>
                <div class='col-md-12 text-center'>
                    <div class='btn-group' role='group'>
                      <button type='button' class='btn btn-success' onclick=\"this.form.actie.value='newDier'; this.form.submit()\"><i class='fa fa-plus'></i> Maak nieuw dier</button>
                      <button type='button' class='btn btn-danger' onclick=\"this.form.actie.value=''; this.form.submit()\"><i class='fa fa-close'></i> Annuleren</button>
                    </div>
                </div>
            </div>";
        }else{
            //Knoppen voor een bestaand dier
            $returnString .="
            <div class='row'>
                <div class='col-md-12 text-center'>
                    <div class='btn-group' role='group'>
                      <button type='button' class='btn btn-success' onclick=\"this.form.actie.value='updateDier'; this.form.submit()\"><i class='fa fa-check'></i> Gegevens actualiseren</button>
                      <button type='button' class='btn btn-danger' onclick=\"this.form.actie.value=''; this.form.submit()\"><i class='fa fa-close'></i> Annuleren</button>
                    </div>
                </div>
            </div>";
        }
        return $returnString;
    }
?>