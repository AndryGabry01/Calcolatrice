<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <script src="js/Math.js"></script>
    <?php
    require("Calcolatrice.php");
    if (isset($_POST['calcExp']) && isset($_POST['disExp'])) {
        $calcoli = new Calcolatrice($_POST['calcExp'],$_POST['disExp']);
    }else{
        $calcoli = new Calcolatrice();
    }
    
    if (isset($_POST['memory'])) {
        $calcoli->setMemoryExp($_POST['memory']);
    }else{
        $calcoli->setMemoryExp("0");
    }

    if (isset($_POST['risultato'])) {
        $calcoli->setANSExp($_POST['risultato']); 
    }
    if (isset($_POST['calc']) && isset($_POST['calcExp']) && isset($_POST['disExp']) && $_POST['calcExp'] != "" && $_POST['disExp'] != "") {
        $calcoli->makeCalc();
    }
    if (isset($_POST['sto']) && isset($_POST['risultato'])) {
        $calcoli->setMemoryExp($_POST['risultato']);

    }  
    if (isset($_POST['mp']) && isset($_POST['risultato'])) {
        $calcoli->setMp($_POST['risultato']);
    }
    ?>

    <title>WCalc</title>
</head>

<body>
    <form action="" method="POST" id="form">
        <p class="m"><?php 
        if ($calcoli->getMemoryExp() != null && $calcoli->getMemoryExp() != "0") {
           echo "M";
        }else{
            echo "<br>";
        }
        ?></p>
        
        <div class="gridContainer">
            <div id="display">
                <p id="RetExp"> <?php echo $calcoli->getDisplayedMathExp(); ?></p>
                <p id="Errore-Risultato"><?php 
                if ($calcoli->getErrorExp() == null) {
                    echo $calcoli->getResultExp();
                }else {
                    echo $calcoli->getErrorExp();
                }?>
                </p>
                <p id="Exp"></p>
            </div>

            <input type="button" class="button" onclick="rad('√^n')" value="√^n">
            <input type="button" class="button" onclick="num('π')"  value="π">
            <input type="submit" class="button" name="sto" value="STO">
            <input type="button" class="button" onclick="ansMem('M')" value="MEM">
            <input type="submit" class="button" name="mp" value="M+">
            <input type="button" class="blueButton" onclick="ce()" value="CE">
            <input type="button" class="blueButton" onclick="c()" value="C">

            <input type="button" class="button" onclick="rad('√^2')" value="√^2">
            <input type="button" class="button" onclick="funzioniBase('cose(')" value="Cos">
            <input type="button" class="button"  onclick="funzioniBase('sine(')" value="Sin">
            <input type="button" class="whiteButton" onclick="num('7')" value="7">
            <input type="button" class="whiteButton" onclick="num('8')" value="8">
            <input type="button" class="whiteButton" onclick="num('9')" value="9">
            <input type="button" onclick="op('-')" class="button" value="-">

            <input type="button" class="button" onclick="fattoriale()" value="n!">
            <input type="button" class="button" onclick="funzioniBase('tane(')" value="Tan">
            <input type="button" class="button" onclick="funzioniBase('cotan(')" value="CoTan">
            <input type="button" class="whiteButton" onclick="num('4')" value="4">
            <input type="button" class="whiteButton" onclick="num('5')" value="5">
            <input type="button" class="whiteButton" onclick="num('6')" value="6">
            <input type="button" class="button" onclick="op('x')" value="x">

            <input type="button" class="button" onclick="reciproco()" value="1/n">
            <input type="button" class="button" onclick="parentesi('(')" value="(">
            <input type="button" class="button" onclick="parentesi(')')" value=")">
            <input type="button" class="whiteButton" onclick="num('1')" value="1">
            <input type="button" class="whiteButton" onclick="num('2')" value="2">
            <input type="button" class="whiteButton" onclick="num('3')" value="3">
            <input type="button" onclick="op('/')" class="button" value="/">

            <input type="button" class="button" onclick="ansMem('ANS')" value="ANS">
            <input type="button" class="button" onclick="potenza('x²')" onclick="" value="x²">
            <input type="button" class="button" onclick="potenza('x^n')" value="x^n">
            <input type="button" class="whiteButton" onclick="num('0')" id="zeroButton" value="0">
            <input type="button" onclick="point()" class="whiteButton" value=".">
            <input type="submit" class="orangeButton" name="calc" value="=">
            <input type="button" onclick="op('+')" class="button" value="+">


            <input type="hidden" id="calcExp" name="calcExp" value="">
            <input type="hidden" id="disExp"  name="disExp" value="">
            <input type="hidden" id="memory"  name="memory" value="<?php echo $calcoli->getMemoryExp(); ?>">
            <input type="hidden" id="risultato"  name="risultato" value="<?php 
            if ( $calcoli->getResultExp() != null && $calcoli->getResultExp() == 0) {
                echo "0";
            }else if ( $calcoli->getResultExp() !== null){
                echo $calcoli->getResultExp();
            }else if(isset($_POST['risultato'])){
                echo $calcoli->sanitazeExp($_POST['risultato']);
            }else{
                echo "0";
            } ?>">
        </div>
    </form>
</body>
</html>
