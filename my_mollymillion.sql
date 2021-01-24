SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `my_mollymillion` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `my_mollymillion`;

CREATE TABLE IF NOT EXISTS `abilita` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `proprietario` int(255) NOT NULL,
  `descrizione` text NOT NULL,
  `modificatore_caratteristica` varchar(3) NOT NULL,
  `prova_contrapposta` varchar(255) DEFAULT NULL,
  `cd` text NOT NULL,
  `condizioni_aggiuntive` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `abilita` (`id`, `nome`, `proprietario`, `descrizione`, `modificatore_caratteristica`, `prova_contrapposta`, `cd`, `condizioni_aggiuntive`) VALUES
(1, 'Acrobazia', 0, 'Tuffarsi, rotolare, nuotare, fare capriole, salti mortali, intrattenere un pubblico.', 'des', NULL, '[{"cd":"15","azione":"Una caduta \\u00e8 pi\\u00f9 corta di 3 metri"},{"cd":"15","azione":"Salto mortale a met\\u00e0 velocit\\u00e0 come parte del movimento normale, senza attacchi di opportunit\\u00e0"},{"cd":"25","azione":"Salto mortale a met\\u00e0 velocit\\u00e0 all''interno di un''area occupata da nemici, senza attacchi di opportunit\\u00e0"}]', '[{"cd":"+2","azione":"Superficie leggermente ostruita, scivolosa, pendente o angolata"},{"cd":"+5","azione":"Superficie pesantemente ostruita o scivolosa"}]'),
(2, 'Addestrare animali', 0, 'Guidare una pariglia di cavalli che trainano un carro su un terreno impervio, insegnare un cane a fare la guardia o insegnare a un tirannosauro a ruggire a comando.', 'car', NULL, '[{"cd":"10","azione":"Gestire un animale"},{"cd":"25","azione":"Spingere un animale"},{"cd":"20","azione":"Insegnare comandi a un animale o addestrarlo per un compito generico"},{"cd":"20","azione":"Allevare un animale selvatico (prove aggiuntive)"}]', '[{"cd":"20","azione":"Cacciare"},{"cd":"15","azione":"Cavalcare"},{"cd":"20","azione":"Cavalcare in combattimento"},{"cd":"15","azione":"Combattere"},{"cd":"15","azione":"Intrattenere"},{"cd":"15","azione":"Lavori pesanti"},{"cd":"15","azione":"Proteggere"}]'),
(3, 'Artista della fuga', 0, 'Liberarsi da legaccci o manette, sgusciare attraverso spazi stretti e sottrarsi alla presa dei mostri.', 'des', '["Utilizzare corde", "Animare corde", "comandare vegetali", "Controllare vegetali", "Intralciare", "Lottare"]', '[{"cd":"+10","azione":"Corde (Prova contrapposta + 10)"},{"cd":"20","azione":"Rete o incantesimi Animare corde, comandare vegetali, controllare vegetali, intralciare"},{"cd":"23","azione":"Incantesimo Calappio"},{"cd":"30","azione":"Manette o spazio ristretto"},{"cd":"35","azione":"Manette perfette"},{"cd":"0","azione":"Lottatore (prova contrapposta)"}]', NULL),
(4, 'Concentrazione', 1, 'Viene effettuata una prova di concentrazione quando rischia di essere distratto mentre è impegnato in un''azione che richiede attenzione.', 'cos', NULL, '[{"cd":"5","azione":"Tempo atmosferico vigoroso"},{"cd":"10","azione":"Subire un danno o tempo atmosferico violento"},{"cd":"10","azione":"Subire un danno ripetuto"},{"cd":"10","azione":"Movimento vigoroso es. su una cavalcatura in movimento"},{"cd":"15","azione":"Movimento violento es. su una cavalcatura al galoppo, intralciato"},{"cd":"20","azione":"In lotta o immobilizzato"},{"cd":"0","azione":"Lottatore (prova contrapposta)"}]', '[{"cd":"x","azione":"Danno subito"},{"cd":"x\\/2","azione":"Danni subiti"}]'),
(5, 'Osservare', 1, 'Individuare personaggi e creature che si nascondono', 'sag', '["Nascondersi", "Camuffare"]', '[{"cd":"-1","azione":"Ogni 3 metri di distanza"},{"cd":"-5","azione":"Osservatore distratto"}]', NULL);

CREATE TABLE IF NOT EXISTS `access` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `access_date` datetime(6) NOT NULL,
  `user` varchar(255) CHARACTER SET utf8 NOT NULL,
  `host` varchar(255) CHARACTER SET utf8 NOT NULL,
  `accepted_language` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_esperanto_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `access_log` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `date_time` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `remote_address` varchar(255) DEFAULT NULL,
  `info` text,
  `last_session_time` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `attacco_tmp` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `giocatore` varchar(255) NOT NULL,
  `caratteristiche` mediumtext,
  `iniziativa` varchar(255) DEFAULT NULL,
  `iniziativa_giro` varchar(255) NOT NULL,
  `ca` varchar(255) NOT NULL DEFAULT '0',
  `pf` mediumtext NOT NULL,
  `armi` mediumtext,
  `incantesimi` mediumtext,
  `abilita` mediumtext,
  `equipaggiamento` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `attacco_tmp` (`id`, `nome`, `giocatore`, `caratteristiche`, `iniziativa`, `iniziativa_giro`, `ca`, `pf`, `armi`, `incantesimi`, `abilita`, `equipaggiamento`) VALUES
(1, 'Troll', 'npc', '{"for":{"base":"17","bonus":"3"},"des":{"base":"13","bonus":"1"},"cos":{"base":"14","bonus":"2"},"car":{"base":"15","bonus":"2"},"int":{"base":"14","bonus":"2"},"sag":{"base":"13","bonus":"1"}}', '10', '21', '25', '40', '["2"]', NULL, NULL, NULL),
(2, 'Alhandra', 'xxxx', '{"for":{"base":"17","bonus":"3"},"des":{"base":"15","bonus":"2"},"cos":{"base":"14","bonus":"2"},"car":{"base":"15","bonus":"2"},"int":{"base":"14","bonus":"2"},"sag":{"base":"13","bonus":"1"}}', NULL, '13', '17', '18', '["1","3"]', NULL, NULL, NULL),
(3, 'Morgan', 'xxxx', '{"for":{"base":"11"},"des":{"base":"18","bonus":"4"},"cos":{"base":"13","bonus":"1"},"car":{"base":"14","bonus":"2"},"int":{"base":"14","bonus":"2"},"sag":{"base":"10"}}', NULL, '12', '10', '14', '["7","8"]', '', '', ''),
(4, 'Erbin', 'xxxx', '{"for":{"base":"10","bonus":"0"},"des":{"base":"12","bonus":"1"},"cos":{"base":"15","bonus":"2"},"car":{"base":"14","bonus":"2"},"int":{"base":"18","bonus":"4"},"sag":{"base":"16","bonus":"3"}}', NULL, '9', '11', '11', '["4","5"]', '["9","3","12","15","29","30"]', NULL, NULL),
(5, 'Troll semplice', 'npc', NULL, '6', '6', '25', '40', '["2"]', NULL, NULL, NULL);

CREATE TABLE IF NOT EXISTS `oggetti` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `proprietario` varchar(255) DEFAULT NULL,
  `presente` int(1) NOT NULL DEFAULT '0',
  `azione` mediumtext,
  `bonus_tiro` varchar(255) DEFAULT NULL,
  `danno` varchar(255) DEFAULT NULL,
  `gittata` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Dungeons & Dragons DO IT YOURSELF' AUTO_INCREMENT=13 ;

INSERT INTO `oggetti` (`id`, `nome`, `proprietario`, `presente`, `azione`, `bonus_tiro`, `danno`, `gittata`) VALUES
(1, 'Spada lunga', '1', 1, 'armi', '5', '{"quantita":"1","dado":"8"}', NULL),
(2, 'Randello', '2', 1, 'armi', '0', '{"quantita":"1","dado":"4"}', NULL),
(3, 'Arco lungo', '1', 1, 'armi', '4', '{"quantita":"1","dado":"8"}', '40'),
(4, 'Bastone', '3', 1, 'armi', '0', '{"quantita":"1","dado":"6"}', NULL),
(5, 'Pugnale', '3', 1, 'armi', '4', '{"quantita":"1","dado":"4"}', '20'),
(6, 'Dardo incantato', '3', 1, 'incantesimi', '1', '{"quantita":"1","dado":"4"}', NULL);

CREATE TABLE IF NOT EXISTS `personaggi` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `giocatore` varchar(255) DEFAULT NULL,
  `presente` int(1) unsigned NOT NULL DEFAULT '0',
  `classe` varchar(255) DEFAULT NULL,
  `razza` varchar(255) NOT NULL,
  `livello` varchar(255) DEFAULT NULL,
  `caratteristiche` mediumtext,
  `iniziativa` varchar(255) DEFAULT NULL,
  `ca` varchar(255) DEFAULT NULL,
  `pf` varchar(255) DEFAULT NULL,
  `pe` varchar(255) DEFAULT NULL,
  `denaro` mediumtext,
  `armi` mediumtext,
  `incantesimi` mediumtext,
  `abilita` mediumtext,
  `equipaggiamento` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Dungeons & Dragons DO IT YOURSELF' AUTO_INCREMENT=6 ;

INSERT INTO `personaggi` (`id`, `nome`, `giocatore`, `presente`, `classe`, `razza`, `livello`, `caratteristiche`, `iniziativa`, `ca`, `pf`, `pe`, `denaro`, `armi`, `incantesimi`, `abilita`, `equipaggiamento`) VALUES
(1, 'Alhandra', 'xxxx', 1, 'Guerriero', 'Umano', '3', '{"for":{"base":"17","bonus":"3"},"des":{"base":"15","bonus":"2"},"cos":{"base":"14","bonus":"2"},"car":{"base":"15","bonus":"2"},"int":{"base":"14","bonus":"2"},"sag":{"base":"13","bonus":"1"}}', NULL, '17', '18', '4820', '100', '["1","3"]', NULL, '["4", "5"]', NULL),
(2, 'Troll', 'npc', 1, 'Guerriero', 'Troll', '1', '{"for":{"base":"17","bonus":"3"},"des":{"base":"13","bonus":"1"},"cos":{"base":"14","bonus":"2"},"car":{"base":"15","bonus":"2"},"int":{"base":"14","bonus":"2"},"sag":{"base":"13","bonus":"1"}}', '10', '25', '40', '1500', NULL, '["2"]', NULL, NULL, NULL),
(3, 'Erbin', 'xxxx', 1, 'Mago', 'Umano', '3', '{"for":{"base":"10","bonus":"0"},"des":{"base":"12","bonus":"1"},"cos":{"base":"15","bonus":"2"},"car":{"base":"14","bonus":"2"},"int":{"base":"18","bonus":"4"},"sag":{"base":"16","bonus":"3"}}', NULL, '11', '11', '4820', '100', '["4","5"]', '["9","3","12","15","29","30"]', NULL, NULL),
(4, 'Troll semplice', 'npc', 1, 'Guerriero', 'Troll', '1', NULL, '6', '25', '40', '1500', NULL, '["2"]', NULL, NULL, NULL),
(5, 'Morgan', 'xxxx', 1, 'Ladro', 'Elfo', '3', '{"for":{"base":"11"},"des":{"base":"18","bonus":"4"},"cos":{"base":"13","bonus":"1"},"car":{"base":"14","bonus":"2"},"int":{"base":"14","bonus":"2"},"sag":{"base":"10"}}', NULL, '10', '14', '4820', '100', '["7","8"]', '', '', '');
