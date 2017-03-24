function printable(objet) {
   console.log("AGENor : impression demandée pour "+objet);
   if(objet=="fiche") $("body").toggleClass("printfiche");
}
