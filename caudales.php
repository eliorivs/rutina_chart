<?php
  error_reporting(0);
  require ('conexion/conexion.php');
  mysqli_set_charset($conn, "utf8");

 
  function fechas()
  {
    global $conn; 
     $data =  array();
     $table =  "historico_caudales_recuperacion";    
     $sql = " select fecha from ".$table." order by fecha "; 
     $result = $conn->query($sql);
     while ($row = $result->fetch_array(MYSQLI_ASSOC)) { 
             
            $data[] = $row['fecha'];
    }
    return $data;          

  }

  function GetCaudales()
  {
       global $conn; 
       $fechas = fechas();     
       $pozos = PozosBombeo();
       $table =  "historico_caudales_recuperacion"; 
       $acum = 0; 
       foreach($fechas as $fecha)
       {   
          
           foreach($pozos as $pozo)
           {
                $sql = "select ".$pozo." as value from ".$table." where fecha ='".$fecha."'";
                $result = $conn->query($sql);
                while ($row = $result->fetch_array(MYSQLI_ASSOC))
                { 
                    $value = $row['value'];
                }
                $acum = $acum + $value;
               
           }
           if($acum!=0)
           {
            $data[] = [ConvertMilis($fecha),$acum];
            $acum = 0;
           }
          
       }
       return($data);
    

  }

  function PozosBombeo()
  {
      global $conn;
      $pozos = ['PRLB_01','PRLB_02','PRLB_03','PRLB_04','PRLB_05','PRLB_06','PRLB_07','PRLB_08','PRLB_09','PRLB_10','PRLB_11','PRLB_12','PRLB_13','PRLB_14'];
      return array_reverse($pozos, true);

  }
 
?>