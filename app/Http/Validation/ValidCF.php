<?php

namespace App\Http\Validation;

use Illuminate\Contracts\Validation\Rule;

class ValidCF implements Rule
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
                return dd($this->ControllaCF($value) == 'true' OR $this->ControllaPIVA($value));
                if ($this->ControllaCF($value) == 'true' OR $this->ControllaPIVA($value))
                    return true;

                else
                    $this->message = $this->ControllaCF($value);
                return false;
            }
            return true;
        }
    }

    public function message()
    {
        return $this->message;
    }

    protected function ControllaCF($cf)
    {
        if( strlen($cf) != 16 )
            return "La lunghezza del codice fiscale non &egrave;\n"
                ."corretta: il codice fiscale dovrebbe essere lungo\n"
                ."esattamente 16 caratteri.";
        $cf = strtoupper($cf);
        if( preg_match("/^[A-Z0-9]+\$/", $cf) != 1 ){
            return "Il codice fiscale contiene dei caratteri non validi:\n"
                ."i soli caratteri validi sono le lettere e le cifre.";
        }
        $s = 0;
        for( $i = 1; $i <= 13; $i += 2 ){
            $c = $cf[$i];
            if( strcmp($c, "0") >= 0 and strcmp($c, "9") <= 0 )
                $s += ord($c) - ord('0');
            else
                $s += ord($c) - ord('A');
        }
        for( $i = 0; $i <= 14; $i += 2 ){
            $c = $cf[$i];
            switch( $c ){
                case '0':  $s += 1;  break;
                case '1':  $s += 0;  break;
                case '2':  $s += 5;  break;
                case '3':  $s += 7;  break;
                case '4':  $s += 9;  break;
                case '5':  $s += 13;  break;
                case '6':  $s += 15;  break;
                case '7':  $s += 17;  break;
                case '8':  $s += 19;  break;
                case '9':  $s += 21;  break;
                case 'A':  $s += 1;  break;
                case 'B':  $s += 0;  break;
                case 'C':  $s += 5;  break;
                case 'D':  $s += 7;  break;
                case 'E':  $s += 9;  break;
                case 'F':  $s += 13;  break;
                case 'G':  $s += 15;  break;
                case 'H':  $s += 17;  break;
                case 'I':  $s += 19;  break;
                case 'J':  $s += 21;  break;
                case 'K':  $s += 2;  break;
                case 'L':  $s += 4;  break;
                case 'M':  $s += 18;  break;
                case 'N':  $s += 20;  break;
                case 'O':  $s += 11;  break;
                case 'P':  $s += 3;  break;
                case 'Q':  $s += 6;  break;
                case 'R':  $s += 8;  break;
                case 'S':  $s += 12;  break;
                case 'T':  $s += 14;  break;
                case 'U':  $s += 16;  break;
                case 'V':  $s += 10;  break;
                case 'W':  $s += 22;  break;
                case 'X':  $s += 25;  break;
                case 'Y':  $s += 24;  break;
                case 'Z':  $s += 23;  break;
                /*. missing_default: .*/
            }
        }
        if( chr($s%26 + ord('A')) != $cf[15] )
            return "Il codice fiscale non &egrave; corretto:\n"
                ."il codice di controllo non corrisponde.";
        return true;
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