<?php

namespace App\Http\Validation;

use Illuminate\Contracts\Validation\Rule;

class ValidPIVA implements Rule
{
    protected  $message;
    public function __construct($nazione)
    {
        return $this->nazione = $nazione;
    }


    public function passes($attribute, $value)
    {

        if ($this->nazione != 'Italia'){

            return true;
        }else{

            if (strlen($value) > 0){
                if ($this->ControllaPIVA($value) == 'true')
                    return true;
                else
                    $this->message = $this->ControllaPIVA($value);
                    return false;
            }
            return true;
        }
    }

    public function message()
    {
        return $this->message;
    }

    protected function ControllaPIVA($pi)
    {
        if( strlen($pi) != 11 )
            return  "La lunghezza della partita IVA non &egrave;\n"
                ."corretta: la partita IVA dovrebbe essere lunga\n"
                ."esattamente 11 caratteri.\n";
        if( preg_match("/^[0-9]+\$/", $pi) != 1 )
            return "La partita IVA contiene dei caratteri non ammessi:\n"
                ."la partita IVA dovrebbe contenere solo cifre.\n";
        $s = 0;
        for( $i = 0; $i <= 9; $i += 2 )
            $s += ord($pi[$i]) - ord('0');
        for( $i = 1; $i <= 9; $i += 2 ){
            $c = 2*( ord($pi[$i]) - ord('0') );
            if( $c > 9 )  $c = $c - 9;
            $s += $c;
        }
        if( ( 10 - $s%10 )%10 != ord($pi[10]) - ord('0') )
            return "La partita IVA non &egrave; valida:\n"
                ."il codice di controllo non corrisponde.";
        return true;
    }


}