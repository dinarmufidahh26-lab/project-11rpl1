<?php$angka=[
    [21,22,23,24,25],
    [31,32,33,34,35],
    [41,42,43,44,45],
];

$i=0;
while($i<count($angka)){
    $j=0;
    while($j<count($angka[$i])){
        echo $angka[$i][$j];
        if($j<count($angka[$i])-1){
            echo ",";
        }
       $j++; 
    }
    echo"<br>";
    $i++;
}
?>