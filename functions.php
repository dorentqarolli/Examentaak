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
                eigenaars.volldeigenaam as naamEigenaar
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
                ON aandoeningen.id=behandelingen.id_ziekte
                WHERE behandelingen.id_dier =".$row["id"];
                
                $rstEigenaar = $conn->query($sqlEigenaar);
                if ($rstEigenaar->num_rows > 0) {
                    while($rowEigenaar = $rstEigenaar->fetch_assoc()) {
                        $arrDier[$row["id"]]['eigenaars'][$rowEigenaar["idEigenaar"]] = array(
                            "naamEigenaar" => $rowEigenaar["naamEigenaar"]);
                    }
                }

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
?>