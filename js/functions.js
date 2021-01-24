


$(document).ready(function() {
	
	// test
	$("#test").click(function() {

		$.post("../dispatcher.php", {

			"action" : "test"
			
		}, function(ret) {
			console.log(ret);
		});
		

		
	});
	
	
	
	
	
	$("button, a").click(function() {
		
		
		// preleva id
		var buttonID = $(this).attr("id");

			
		// console.log("pushed " + buttonID);
			
		// solo per i button di azione/riferimento
		if (!buttonID.match("link|btn")) {

			var infoID = "";

			// dati da inviare
			if ($("#text-" + buttonID).val() != "") {
				infoID = $("#text-" + buttonID).val();
			} else {
				infoID = $("span#nome").text();
			}
			
			
			
			/* LOGOUT */
			if (buttonID.match('logout')) {
				
				$.post("../dispatcher.php", {

					"action" : buttonID
					
				}, function() {
					
					// location.href = 'http://localhost/mollymillion@ftp.mollymillion.altervista.org/dungeonsanddragonsdiy/template/home.html';
					// location.href = 'https://mollymillion.altervista.org/dungeonsanddragonsdiy/template/home.html';
					location.href = 'home.html';
				});
				
			}
			
			
			
			/* LOGIN: carica le caratteristiche del giocatore */
			if (buttonID.match('login')) {
				
				$.post("../dispatcher.php", {

					"action" : buttonID,
					"data" : infoID
					
				}, function(ret) {
				
					console.log("return LOGIN: " + ret);
					
					var decodedArray = JSON.parse(ret);
					

					// sitribuisci stats nella pagina
					$.each(decodedArray['data'], function(i,v) {
						
						if (i == 'caratteristiche') {
							
							var k = JSON.parse(v);
	
							$.each(k, function(l,m) {
								
								// console.log(l);
								
								$.each(m, function(p,q) {
									if (p == 'base') $("#" + l).text(q);
									if (p == 'bonus') $("#bonus-" + l).text(q);
								})

							});
							
						} else {
							
							console.log("CASO NON ['caratteristiche']");
							$("#" + i).text(v);
						}
						
					});

				});
				
			}
			
			
			
			// BUTTON per SCEGLIERE ARMA
			if (buttonID.match('carica-armi')) {

				$.post("../dispatcher.php", {
					
					"action" : 'caricaArmi',
					"data" : buttonID.replace("carica-armi-", "")
					
				}, function(ret) {
					
					console.log(ret);
					
				});
			}
			
			
			// BUTTON per SVUOTARE BUFFER COMBATTIMENTO
			if (buttonID == 'attacca-annulla') {
				
				$.post("../dispatcher.php", {
					"action" : 'annullaAttacco'
				}, function() {
					$("#container-icone-personaggi").empty();
				});
				
			}
			
			
			
			// BUTTON per INSERIRE OGGETTI/PERSONAGGI
			if (buttonID.match('inserisci')) {
				
				var action = buttonID.replace('-ok', '').replace('inserisci-', '');
				var modal = buttonID.replace('-ok', '');


				$.post("../dispatcher.php", {
					"action" : 'inserisci' + action,
					"data" : $("#modal-" + modal + " input, #modal-" + modal + " select").serializeArray()
				}, function(ret) {
					$("#modal-inserisci-risultato").empty().html(ret);
					console.log(ret);
				}, );
				
			}
			
			
			// BUTTON per MODIFICARE OGGETTI/PERSONAGGI
			if (buttonID.match('modifica')) {
				
				var action = buttonID.replace('-ok', '').replace('modifica-', '');
				var modal = buttonID.replace('-ok', '');


				$.post("../dispatcher.php", {
					"action" : 'modifica' + action,
					"data" : $("#modal-" + modal + " input, #modal-" + modal + " select").serializeArray()
				}, function(ret) {
					$("#modal-modifica-risultato").empty().html(ret);
					console.log(ret);
				}, );
				
			}
			
			
			// BUTTON ATTACCA
			if (buttonID == 'attacca') {
				
				showModal("nemico");
				
				/* 
				$.post("../dispatcher.php", {
					"action" : 'attacca',
					"data" : ""
				}, function(ret) {
					
					// mostra personaggi
					
					// attacco vero e proprio
					// $("#attacca-risultato-text").html(ret);
					
				});
				 */
			}
			
			
			
			
		// button PERSONAGGI
		} else {
			
			// console.log(buttonID);

			// btn azione x ATTACCO
			if ((buttonID == 'link-attacca-armi') | (buttonID == 'link-attacca-incantesimi')) {
				
				var tipo = buttonID.replace("link-attacca-", "");
				console.log(tipo);
				$.post("../dispatcher.php", {

				"action" : 'scegliOggetto',
				// manca 'data' ???
				"data" : tipo
				
				}, function(ret) {

					$("#lista-" + tipo).html(ret);
					
				});
				
			} 
			
			
			// btn ARMI nella DASHBOARD
			if (buttonID == 'link-armi') {
				
				$.post("../dispatcher.php", {
					
					"action" : 'scegliOggetto',
					"data" : 'armi'
					
				}, function(ret) {
					
					$("#lista-armi").html(ret);
					
				});
				
			}
			
			
			// btn INCANTESIMI nella DASHBOARD
			if (buttonID == 'link-incantesimi') {
				
				$.post("../dispatcher.php", {
					
					"action" : 'scegliOggetto',
					"data" : 'incantesimi'
					
				}, function(ret) {
					
					$("#lista-incantesimi").html(ret);
					
				});
				
			}
			
			
			// btn ABILITA' nella DASHBOARD
			if (buttonID == 'link-abilita') {
				
				$.post("../dispatcher.php", {
					
					"action" : 'scegliAbilita'
					
				}, function(ret) {
					
					$("#lista-abilita").html(ret);
					
				});
				
			}
			
			
			// btn personaggio x ATTACCO
			if (buttonID == 'link-attacca-npc') {
				
				$.post("../dispatcher.php", {

				"action" : 'scegliNemico'
				// manca 'data' ???
				}, function(ret) {
					// metti risultato visibile
					$("#attacca-party-stats").text(ret);

				});
				
			}
			

			// btn FINESTRA ATTACCA
			if (buttonID == 'link-attacca') {
				
				console.log(buttonID);
				
				
				$.post("../dispatcher.php", {

				"action" : 'start'/* ,
				"data" : infoID */
				// manca 'data' ???
				}, function(ret) {
					// metti risultato visibile
					$("#lista-nemico").html(ret);
					$("#container-icone-personaggi").html(ret);
		
				});
			
			} 
			
			
			// btn FINESTRA MODIFICA - DM
			if (buttonID == 'link-modifica') {
				
				
				
				
				$.post("../dispatcher.php", {

				"action" : 'modifica'
				}, function(ret) {
					// metti risultato visibile

					var decodedArray = JSON.parse(ret);

					$.each(decodedArray, function(i, v) {
						
						if (i == 'personaggi') $("#modal-lista-modifica-Personaggio").html('<span class="chiudi" onclick="closeModal(\'lista-modifica-Personaggio\')">X</span>' + v).removeClass("hide");
						if (i == 'oggetti') $("#modal-lista-modifica-Oggetto").html('<span class="chiudi" onclick="closeModal(\'lista-modifica-Oggetto\')">X</span>' + v).removeClass("hide");
						
					});
					

					
				});
				
				
			} 
			
		}

	})

})

	




















/****************************************************************************************************************************************/
/****************************************************************************************************************************************/
/****************************************************************************************************************************************/
/****************************************************************************************************************************************/



/**
* 
* funzione showHideMove: mostra/nascondi input e pulsante OK
* delle caselle giocatori e PNG
* 

function showHideMove(player) {
	
	$("#" + $(player).attr("id") + " > input[type='text'], #" + $(player).attr("id") + " > button").removeClass("hide").addClass("show");
	
}
*/


/**
* 
* funzione move: cerca coordinate del nome casella inserito nell'input
* e usale per sovrascrivere quelle relative al giocatore
* 
*/

function move(player, type) {
	
	
	// numero player
	var numberId = $(player).attr("id").replace("btn", "");
	var coordN = "coord" + numberId, playerN = type + numberId;
	
	
	
	if ( coordN == "" ) {
	
		alert("Nessuna casella");
		
	} else {
		
		// sempre maiuscolo
		var coords = [], casella = $("#" + coordN).val().toUpperCase();
		
		
		$("area").each(function() {
		
			if ( $(this).attr("alt") == casella ) coords = $(this).attr("coords").split(",");
			
		});
		
		
		$("#" + coordN).val("");
		
		
		
		$("#" + playerN).css({
			"left" : "calc(" + coords[0] + "px + 8px)",
			"top" : "calc(" + (coords[1]) + "px + 228px)"
		});
		

	}

}

/**
* funzione showModal: mostra finestra modale relativa al parametro
* 
* @param 			modal 					string
* 
*/
function showModal(modal) {
	console.log($("#modal-" + modal).html());
	if ((modal.match('attacca')) || 
		(modal.match('inserisci')) || 
		(modal.match('modifica')) || 
		(modal.match('lista-modifica'))) {
		$("#modal-" + modal).removeClass("hide").addClass("show");
	} else {
		$("#modal-" + modal).removeClass("hide-all").addClass("show-all");
	}
	
	if ((modal.match('login'))) $("#text-login").focus();
	
}



/**
* Al click su un elemento con classe ".chiudi" chiudilo
*/
function closeModal(elem) {
	// console.log(elem);
	if ((elem.match('attacca')) || (elem.match('inserisci')) || (elem.match('modifica')) || (elem.match('lista-modifica'))) {
		$("#modal-" + elem).removeClass("show").addClass("hide");
	} else {
		$("#modal-" + elem).removeClass("show-all").addClass("hide-all");
	}
	
	
}


/**
* Svuota form
*/
function emptyForm(form) {
	
	$("#" + form + " input").val("");
	
}







/*
 * Validazione 

function validate(cont) {
	
	
	$("#" + cont + " input").each(function() {
		
		if ($(this).attr("required") == "required") {
			
			console.log("trovato required");
			
			
		}
		
	});
	
	
}
*/




function scegliArma(id) {

	$.post("../dispatcher.php", {
				
		"action" : 'scegliArma',
		"data" : id
		
	}, function(ret) {
		console.log(ret);
		var returned = JSON.parse(ret);
		
		var danno = JSON.parse(returned.danno);
		
 
		$("#attacca-party-stats-text").html("<b>" + returned.nome + "</b><p>danno " + danno.quantita + "d" + danno.dado + "</p>");
		$("#attacca-party-stats-weapon").val(returned.id);
		
	});
	
};

