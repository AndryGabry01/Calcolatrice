/*
 *   Copyright (c) 2020 
 *   All rights reserved.
 */

//Flag verifica se è attiva una virgola
var alreadyPoint = false;
//Array contenente le parentesi mancanti
var haveParentesi = [];

/*########################################################################*/
/* Funzione Per l'inserimento degli operatori
/*########################################################################*/

function op(operator) {
    disExpOBJ = document.getElementById("disExp");
    var calcOp = "";
    
    lastChar = disExpOBJ.value.charAt(disExpOBJ.value.length - 1);

    if (lastChar != "^" &&lastChar != "." && lastChar != "+" && lastChar != "-" && lastChar != "x" && lastChar != "/" || lastChar == ")") {
        alreadyPoint = false;
        calcOp = "|"+operator+"|";
    }
    display(calcOp)
}

/*########################################################################*/
/* Funzione Per l'inserimento dei numeri
/*########################################################################*/

function num(numero) {
    disExpOBJ = document.getElementById("disExp");
    var calcNum;
    calcNum = "|"+numero+"|";
    display(calcNum)

}

/*########################################################################*/
/* Funzione Per l'inserimento delle funzioni matematiche
/*########################################################################*/

function funzioniBase(funzione) {
    disExpOBJ = document.getElementById("disExp");
    var calcFun;
    alreadyPoint = false;
    calcFun = "|"+funzione+"|";
    haveParentesi.unshift(")");
    display(calcFun)
}

/*########################################################################*/
/* Funzione Per l'inserimento Delle parentesi
/*########################################################################*/
function parentesi(paretesi) {
    disExpOBJ = document.getElementById("disExp");
    var calcPar;
    lastChar = disExpOBJ.value.charAt(disExpOBJ.value.length - 1);

    switch (paretesi) {
        case "(":
            calcPar = "|(|";
            if (haveParentesi[0] == "(") {
                haveParentesi.splice(0,1);
            }else if(haveParentesi[0] == ","){
                haveParentesi.splice(0,1);
                calcPar = "|,|";
            }
            alreadyPoint = false;
            haveParentesi.unshift(")");
            display(calcPar)
            break;
        case ")":
            calcPar = "|)|";
            if (haveParentesi[0] == ")") {
                haveParentesi.splice(0,1);
            }
            alreadyPoint = false;
            display(calcPar)
            break;
        default:
            break;
    }
}

/*########################################################################*/
/* Funzione Per l'inserimento delle potenze
/*########################################################################*/

function potenza(potenza) {
    disExpOBJ = document.getElementById("disExp");
    var calcPo;
    lastChar = disExpOBJ.value.charAt(disExpOBJ.value.length - 1);
    if (lastChar != "." && lastChar != "+" && lastChar != "-" && lastChar != "x" && lastChar != "/") {
        alreadyPoint = false;
        switch (potenza) {
            case "x²":
                var calcPo = "|^|2";
                break;
            case "x^n":
                var calcPo = "|^|";
                break;
            default:
                break;
        }
        display(calcPo);
    }
}

/*########################################################################*/
/* Funzione Per l'inserimento delle radici
/*########################################################################*/

function rad(radice) {
    disExpOBJ = document.getElementById("disExp");
    var calcRad;
    lastChar = disExpOBJ.value.charAt(disExpOBJ.value.length - 1);

    switch (radice) {
        case "√^2":
            alreadyPoint = false;
            calcRad = "|sqrt(|";
            haveParentesi.unshift(")");
            break;
        case "√^n":
            alreadyPoint = false;
            calcRad = "|radn(|";
            haveParentesi.unshift(",");
            break;
        default:
            break;
    }
    display(calcRad)
}

/*########################################################################*/
/* Funzione Per l'inserimento del reciproco
/*########################################################################*/

function reciproco() {
    disExpOBJ = document.getElementById("disExp");
    var calcRec;
    alreadyPoint = false;
    calcRec = "|rec(|";
    haveParentesi.unshift(")");
    display(calcRec)
}

/*########################################################################*/
/* Funzione Per l'inserimento del fattoriale
/*########################################################################*/

function fattoriale() {
    disExpOBJ = document.getElementById("disExp");
    var calcFat;
    lastChar = disExpOBJ.value.charAt(disExpOBJ.value.length - 1);
    if (disExpOBJ.value.length > 0 && !isNaN(lastChar)) {
        calcFat = "|!|";
        alreadyPoint = true;
        display(calcFat)
    }    
}

/*########################################################################*/
/* Funzione Per l'inserimento della virgola
/*########################################################################*/

function point() {
    disExpOBJ = document.getElementById("disExp");
    var calcPoint;
    lastChar = disExpOBJ.value.charAt(disExpOBJ.value.length - 1);

    if (!isNaN(lastChar) && alreadyPoint == false) {
        calcPoint = "|.|";
        alreadyPoint = true;
        display(calcPoint)
    }    
}

/*########################################################################*/
/* Funzione Per l'inserimento di ANS E MEMORIA
/*########################################################################*/

function ansMem(param) {
    disExpOBJ = document.getElementById("disExp");
    var calcAM;
    alreadyPoint = false;
    calcAM = "|"+param+"|";
    display(calcAM)
}

/*########################################################################*/
/* Funzione Per l'inserimento delle parentesi fittizie per ricordare
/* all utente di inserire le parentesi
/*########################################################################*/

function parentesiFittizie(){
    expOBJ = document.getElementById("Exp");
    var par;
    for (i= 0; i < haveParentesi.length; i++) {
        if (haveParentesi[i] == ",") {
            par = "("            
        } else {
            par = haveParentesi[i]; 
        }
        expOBJ.innerHTML +="<span id='fakePar'>"+par+"</span>";
    }
}

/*########################################################################*/
/* Funzione La stampa sul display
/*########################################################################*/

function display(calcExp) {
    calcExpOBJ = document.getElementById("calcExp");
    disExpOBJ = document.getElementById("disExp");
    expOBJ = document.getElementById("Exp");
    document.getElementById("RetExp").style.visibility = "hidden";
    document.getElementById("Errore-Risultato").style.visibility = "hidden";
        
    calcExpOBJ.value += calcExp;
    disExpOBJ.value = StringRefactorDisplay(calcExpOBJ.value);
    expOBJ.innerHTML  = disExpOBJ.value;
    parentesiFittizie();
}

/*########################################################################*/
/* Funzione La l'esecuzione della funzione ce
/*########################################################################*/
function StringRefactorCalc(string) {
    string = string.replaceAll("||","|");
    if (string.charAt(0) == "|") {
        string = string.substring(1);
    }
    if (string.charAt(string.length-1) == "|") {
        string = string.substring(0, string.length-1);
    }
    return string;
}

/*########################################################################*/
/* Funzione la conversione da stringa di calcolo ad stringa da visualizzare
/*########################################################################*/
function StringRefactorDisplay(string) {
    string = string.replaceAll("|","");
    string = string.replaceAll(",","(");
    string = string.replaceAll("radn(","√^");
    string = string.replaceAll("sqrt(","√^2(")
    return string;
}

/*########################################################################*/
/* Funzione CE
/*########################################################################*/
function ce() {
    calcExpOBJ = document.getElementById("calcExp");
    disExpOBJ = document.getElementById("disExp");
    expOBJ = document.getElementById("Exp");
    document.getElementById("RetExp").style.visibility = "hidden";
    document.getElementById("Errore-Risultato").style.visibility = "hidden";

    tempString = StringRefactorCalc(calcExpOBJ.value);
    pipeIndex = tempString.lastIndexOf("|");

    //Verifico se è stata cancellata una parentesi (
    count = (tempString.substring(pipeIndex,tempString.length).match(/\(/g) || []).length;
    if (count>0) {
        i=0;
        while (haveParentesi[i] != ")" && i< haveParentesi.length) {
            i++;
        }
        haveParentesi.splice(i,1);
    }
    //Verifico se è stata cancellata una parentesi ( di una radn
    count = (tempString.substring(pipeIndex,tempString.length).match(/\,/g) || []).length;
    if (count>0) {
        i=0;
        while (haveParentesi[i] != ")" && i< haveParentesi.length) {
            i++;
        }
        haveParentesi.splice(i,1);
    }

    //Verifico se è stata cancellata una parentesi )
    
    count = (tempString.substring(pipeIndex,tempString.length).match(/\)/g) || []).length;
    if (count>0) {
        haveParentesi.unshift(")")
    }

    //verifico se è stato cancellato un .
    count = (tempString.substring(pipeIndex,tempString.length).match(/\./g) || []).length;
    if (count>0) {
        alreadyPoint = false;
    }
    
    
    tempString = tempString.substring(0,pipeIndex);
    calcExpOBJ.value = tempString;

    disExpOBJ.value = StringRefactorDisplay(calcExpOBJ.value);
    if (disExpOBJ.value == "") {
        expOBJ.innerHTML  = "0";
    }else{
        expOBJ.innerHTML  = disExpOBJ.value;
    }
    parentesiFittizie();

}

/*########################################################################*/
/* Funzione C
/*########################################################################*/
function c() {
    calcExpOBJ = document.getElementById("calcExp");
    disExpOBJ = document.getElementById("disExp");
    expOBJ = document.getElementById("Exp");
    document.getElementById("RetExp").style.visibility = "hidden";
    document.getElementById("Errore-Risultato").style.visibility = "hidden";
        
    calcExpOBJ.value = "";
    disExpOBJ.value = "";
    expOBJ.innerHTML  = "0";
}

//numeri by tastiera trigger