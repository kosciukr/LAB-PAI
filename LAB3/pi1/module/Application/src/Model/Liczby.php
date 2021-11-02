<?php
namespace Application\Model;

class Liczby
{
public function generuj():array{
    $numArr = array();
    while(count($numArr) < 100){ //only unique 100
        $tmp =mt_rand(0,1000);
        if(!in_array($tmp, $numArr)){
            $numArr[] = $tmp;
        }
    }
    sort($numArr); //sort unique
        return $numArr;
}
}