<?php
ini_set('display_errors', 1);

class Calcolatrice 
{
    private $displayedMathExp;
    private $cleanMathExp; //aggiornare js, per inviare stringa cosi
    private $result;
    private $errorExp;
    private $memory;
    private $ANS;

    public function __construct($postExp =  null, $displayExp = null)
    {
        $this->result = null;
        $this->errorExp = " ";
        $this->ANS = 0;
        $this->memory = null;
        if ($postExp != null && $displayExp != null) {
            $this->cleanMathExp = $this->sanitazeExp($postExp);
            $this->displayedMathExp = $this->sanitazeExp($displayExp);
        }
    }

    /*########################################################################*/
    /* Funzione Sanificare la stringa */
    /*########################################################################*/
    public function sanitazeExp($dirtyString)
    {
        return filter_var($dirtyString,FILTER_SANITIZE_STRING);
    }

    /*########################################################################*/
    /* Funzione per riformattare l'array in una stringa secondo le specifiche */
    /*########################################################################*/
    private function restoreString($expArray)
    {
        $exp="";
        foreach ($expArray as $index =>$part) {
            if ($index == count($expArray)-1 ) {
                $exp .= $part;
            }else {
                $exp .= $part."|";
            }
        }
        return $exp;
    }

    /*########################################################################*/
    /* Funzione che prepara i fattoriali al calcolo */
    /*########################################################################*/
    private function prepareFact()
    {
        $tempExp = $this->cleanMathExp;
        $expArray = explode("|",$tempExp);

        //Calcolo Del Fattoriale
        for ($i=0; $i < count($expArray); $i++) {
            if ($expArray[$i] == "!" && is_numeric($expArray[$i-1])) {
                $fact = 1;
                $num = $expArray[$i-1];

                for ($j=0; $j < $num; $j++) { 
                    $fact *= $num -$j;
                }
                
                //Elimina il numero calcolato dall array
                unset($expArray[$i-1]);

                //Rigenera la stringa di calcolo aggiornata
                $expArray[$i] =  $fact;
                $expArray = array_values($expArray);
                $this->cleanMathExp = $this->restoreString($expArray);   
            }
        }    
    }

    /*########################################################################*/
    /* Funzione che prepara le restanti funzioni al calcolo*/
    /*########################################################################*/
    private function prepareFuncAndNumber()
    {
        $tempExp = $this->cleanMathExp;
        $expArray = explode("|",$tempExp);
        for ($i=0; $i < count($expArray); $i++) {
            
            //Se la funzione dopo è un pigreco e quella attuale non è un + - / * , )
            if ($expArray[$i-1] == "π" && $expArray[$i] != "," && $expArray[$i] != ")" &&$expArray[$i] != "+" && $expArray[$i] != "-" && $expArray[$i] != "*" && $expArray[$i] != "/" ) {
                $expArray[$i] = "|*|".$expArray[$i];
            } 
            if (is_numeric($expArray[$i]) && (substr_count($expArray[$i-1],")") == 1 || substr_count($expArray[$i-1],"ANS") == 1 || substr_count($expArray[$i-1],"M") == 1)) {
                $expArray[$i] = "|*|".$expArray[$i];
            }
            if ($i>0 && 
                  ($expArray[$i] == "(" ||$expArray[$i] == "M" || $expArray[$i] == "ANS" || $expArray[$i] == "cos("|| $expArray[$i] == "sin("|| $expArray[$i] == "tan("|| $expArray[$i] == "cotan(" || $expArray[$i] == "radn(" || $expArray[$i] == "sqrt(" || $expArray[$i] == "rec(") && 
                  ($expArray[$i] != "," && $expArray[$i-1] != "+" && $expArray[$i-1] != "*"&& $expArray[$i-1] != "**"  && $expArray[$i-1] != "/" && $expArray[$i-1] != "-" && $expArray[$i-1] != "(" && substr_count($expArray[$i-1],"(") == 0)
                )
            {
                $expArray[$i] = "*|".$expArray[$i];

            }
            switch ($expArray[$i]) {                
                case 'π':
                    //Se prima del pigreco non ce un segno mette un *
                    if ($i > 0 && substr_count($expArray[$i-1],"(")== 0 &&$expArray[$i-1] != "+" && $expArray[$i-1] != "-" && $expArray[$i-1] != "*" && $expArray[$i-1] != "/") {
                        $expArray[$i] = "*|".round(pi(),2);
                    }else{
                        //seno sostituisce il simbolo con il numero del pigreco
                        $expArray[$i] = round(pi(),2);
                    }
                    //Se dopo il pigreco non ce un segno mette un *
                    if ($i+1 < count($expArray) && $expArray[$i+1] != "," && $expArray[$i+1] != ")" &&$expArray[$i+1] != "+" && $expArray[$i+1] != "-" && $expArray[$i+1] != "*" && $expArray[$i+1] != "/" ) {
                        $expArray[$i] .= "|*|"; 
                    }
                    break;   
                
               case 'sqrt(':
                    // se prima della radice quad non ce un segno, mette un *
                    if ($i > 0 && substr_count($expArray[$i-1],"(")== 0 &&$expArray[$i-1] != "+" && $expArray[$i-1] != "-" && $expArray[$i-1] != "*" && $expArray[$i-1] != "/") {
                        $expArray[$i] = "|*|sqrt(";
                    }else{
                        $expArray[$i] = "sqrt(";
                    }               
                    break;       
                 
                default:
                    break;
            }          
        }  
        $this->cleanMathExp = $this->restoreString($expArray);   
    }

    /*########################################################################*/
    /* Funzione effetua il calcolo*/
    /*########################################################################*/
    public function makeCalc()
    {
        //Configurazione Gestore errori per segnalare come Exception la divisione per zero
        function e($errno, $errstr, $errfile, $errline) {
            if ($errstr == "Division by zero") {
                throw new Exception($errstr, $errno);
            }
        }
        set_error_handler('e');

        //Creazione funzioni matematiche utili mancanti
        //Funzione cotangente
        function cotan($tan)
        {
            return 1/(tan($tan));
        }
        //Funzione radice alla n
        function radn($elev,$radic)
        {
            return pow($radic,1/($elev));
        }
        function rec($numero)
        {
            return 1/($numero);
        }

        
        //Prevenzione errore formatazione stringa  
        $this->cleanMathExp = str_replace ("||", "|", $this->cleanMathExp);
        $this->cleanMathExp = ltrim($this->cleanMathExp, "|");
        $this->cleanMathExp = rtrim($this->cleanMathExp, "|");

        //Conversione ^ in ** (Potenza)
        $this->cleanMathExp = str_replace ("^", "**", $this->cleanMathExp);
        $this->cleanMathExp = str_replace ("x", "*", $this->cleanMathExp);

        //Formattazione Stringa in base alle necessita delle restanti funzioni matematiche
        $this->prepareFuncAndNumber();
        $this->prepareFact();
        $this->cleanMathExp = str_replace ("ANS", $this->ANS, $this->cleanMathExp);
        $this->cleanMathExp = str_replace ("M", $this->memory, $this->cleanMathExp);


        //Creazione Stringa finale di calcolo
        $tempExp = $this->cleanMathExp;
        $expArray = explode("|",$tempExp);
        $finalExp = "";
        foreach ($expArray as $part) {
                $finalExp .=$part;
        }

        //echo $finalExp; //TEST


        //Calcolo e Gestione errori
        try {
            $result = "";
            eval('$result = '. $finalExp .';');
            $this->result = $result;
        } catch (\Throwable $th) {
            $this->errorExp = $th->getMessage();
        }
        //Verifica se Presente un errore nel calcolo
        $this->defineErrorEpx(); 
    }
    
    
    /*########################################################################*/
    /* Getter & Setter Attributi*/
    /*########################################################################*/
    public function getErrorExp()
    {        
        return $this->errorExp;
    }
    public function getMemoryExp()
    {        
        return $this->memory;
    }
    public function getDisplayedMathExp()
    {        
        return $this->displayedMathExp;
    }
    public function getResultExp()
    {       
        if ($this->result !== null) {
            return round($this->result,5);
        }
    }

    public function setMemoryExp($mem)
    {        
        $this->memory = $this->sanitazeExp($mem);
    }
    public function setANSExp($ans)
    {
        $this->ANS = $this->sanitazeExp($ans);

    }
    
    public function setMp($mem)
    {
        $this->memory =  $this->memory +  $this->sanitazeExp($mem);
    }

    /*########################################################################*/
    /* Funzione che verifica la presenza di errori dopo il calcolo*/
    /*########################################################################*/
    private function defineErrorEpx()
    {
        if (substr_count($this->errorExp,"syntax error")>0 ) {
            $this->errorExp =  "Syntax error";
            $this->result = null;
        }else if (substr_count($this->errorExp,"Call to undefined")>0 ) {
            $this->errorExp =  "Syntax error";
            $this->result = null;
        }else if($this->errorExp == "Division by zero"){
            $this->errorExp = "Indefinito";
            $this->result = null;
        }else if(is_nan($this->result)){
            $this->errorExp = "Math error";
            $this->result = null;
        }else if(is_infinite($this->result)){
            $this->errorExp = "Infinity or Memory Exceeds";
            $this->result = null;
        }else {
            $this->errorExp = null;
        }
    }
}




?>