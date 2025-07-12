-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 29-Jun-2025 às 23:00
-- Versão do servidor: 9.1.0
-- versão do PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `clubemasterbd`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `atletas`
--

DROP TABLE IF EXISTS `atletas`;
CREATE TABLE IF NOT EXISTS `atletas` (
  `id_atleta` int NOT NULL AUTO_INCREMENT,
  `cc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `codigo_clube` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nome` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `data_nascimento` date NOT NULL,
  `posicao` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fotografia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'no_image.png',
  `pe_preferido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `escalao` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sub_escalao` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `altura` decimal(3,2) DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `nacionalidade` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `morada` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `data_inscricao` date DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `codigo_postal` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `localidade` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telemovel` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` int DEFAULT NULL,
  `nif` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_atleta`),
  KEY `codigo_clube` (`codigo_clube`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `atletas`
--

INSERT INTO `atletas` (`id_atleta`, `cc`, `codigo_clube`, `nome`, `data_nascimento`, `posicao`, `fotografia`, `pe_preferido`, `escalao`, `sub_escalao`, `altura`, `peso`, `nacionalidade`, `telefone`, `email`, `morada`, `data_inscricao`, `ativo`, `codigo_postal`, `localidade`, `telemovel`, `status`, `nif`) VALUES
(2, '23456789', '123456', 'Miguel Costa', '2014-07-22', 'Médio', '686002c2ddc15_no_image.png', 'Esquerdo', 'Benjamins', 'Sub-11', 1.45, 38.20, 'Portuguesa', '923456789', 'miguel@email.com', 'Avenida Central, 45', '2025-01-20', 1, NULL, NULL, NULL, NULL, '6754'),
(3, '34567890', '123456', 'João Ferreira', '2010-05-10', 'Extremo-Esquerdo', '686002b31a987_17495017601748710440Cristiano_Ronaldo.jpg', 'Direito', 'Veteranos', '', 1.55, 45.00, 'Portuguesa', '934567890', 'joao@email.com', 'Rua do Comércio, 78', '2025-02-05', 1, NULL, NULL, NULL, NULL, '7898909'),
(4, '45678901', '123456', 'André Freitas', '2008-11-30', 'Guarda-Redes', '686002a9a8374_68448bb50ef2e.jpg', 'Direito', 'Veteranos', '', 1.40, 55.30, 'Portuguesa', '945678901', 'andre@email.com', 'Praça Central, 12', '2025-02-10', 1, NULL, NULL, NULL, NULL, '42354356'),
(5, '435346', '123456', 'gui', '2007-02-22', 'Médio-Ofensivo', '17511221181748710440Cristiano_Ronaldo.jpg', 'Direito', 'Veteranos', '', 9.99, 45.00, NULL, NULL, 'gui@ggggggg', 'rua de ggujhjhg', NULL, 1, '4520-256', 'porto', '57567858', 0, '43254345'),
(6, 'fdhdsghfgh', '123456', 'ricardo', '2222-02-22', 'Médio-Defensivo', '686002cd88d01_17511221181748710440Cristiano_Ronaldo.jpg', 'Direito', 'Infantis', 'Sub-12', 9.99, 11.00, NULL, NULL, 'fh@hjdfjfjd', 'jdfjfdhj', NULL, 1, 'jjghjghj', 'fhjfdjdfjfh', 'jfgjfdjfhhf', 0, '7567'),
(8, '543536', '123456', 'gaspar', '2025-11-22', 'Extremo-Direito', '686002a2f29c7_no_image.png', 'Esquerdo', 'Juniores', 'Sub-19', 9.99, 22.00, NULL, NULL, 'uuutyuuuuuyt@fdgdsfh', 'tgruyhdfuy', NULL, 1, 'hfydudfyuy', 'uhdyrtfyhtyu', 'hyujtyujdtyuyu', 0, '564645643');

-- --------------------------------------------------------

--
-- Estrutura da tabela `clube`
--

DROP TABLE IF EXISTS `clube`;
CREATE TABLE IF NOT EXISTS `clube` (
  `codigo` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nome_clube` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nome_utilizador` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `morada` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `data_registo` date NOT NULL,
  `imagem` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'no_image.png',
  `logo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo_utilizador` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'admin',
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `clube`
--

INSERT INTO `clube` (`codigo`, `password`, `nome_clube`, `nome_utilizador`, `morada`, `email`, `data_registo`, `imagem`, `logo`, `telefone`, `tipo_utilizador`) VALUES
('123', '$2y$10$dJe.yMFw6TJWL8uxn9y3Buw9AWZOFkikwF1CM5y/QRfcngSTXoRWy', '123', '123', '123', '123@123', '0000-00-00', '17512359561744805036Real_Madrid.png', NULL, NULL, 'admin'),
('1234', '$2y$10$dgCHolAwDonlDKfWcdipzeom/ksBJMiTdLkmhoKpav7z.S8zS/QkC', '1234', '1234', '1234', '1234@1234', '0000-00-00', '1751235971clube.png', NULL, NULL, 'admin'),
('12345', '$2y$10$HJbuZEgxQbgDklPK/tvs.e3a7IdNMs0jgL7cWMcXbh9H5sNytJ496', '12345', '12345', '12345', '12345@12345', '0000-00-00', '17512359881744805036Real_Madrid.png', NULL, NULL, 'admin'),
('123456', '$2y$10$ppfDSReh0OJpiHhFYeooFeZq93v0Xad5KXhmolS1W2GQ2bdgJ0AIm', 'Clube 1', 'Presidente', 'Rua X', 'clube1@gmail.com', '2025-04-15', '17512358771744805036Real_Madrid.png', 'logo_clube.png', '912345678', 'admin'),
('club1', '$2y$10$0CaWJBpR7zlg3H27vVimr.ibEXjThmzJj58BbQGF8s.AsJLWu8Rs.', 'club1', 'club1', 'club1', 'club1@club1', '0000-00-00', '17512360641744805036Real_Madrid.png', NULL, NULL, 'admin');

-- --------------------------------------------------------

--
-- Estrutura da tabela `consultas_medicas`
--

DROP TABLE IF EXISTS `consultas_medicas`;
CREATE TABLE IF NOT EXISTS `consultas_medicas` (
  `id_consulta` int NOT NULL AUTO_INCREMENT,
  `id_atleta` int NOT NULL,
  `codigo_clube` varchar(255) NOT NULL,
  `data_consulta` date NOT NULL,
  `medico` varchar(255) NOT NULL,
  `tipo_consulta` varchar(255) NOT NULL,
  `diagnostico` text,
  `tratamento` text,
  `recomendacoes` text,
  `data_proxima_consulta` date DEFAULT NULL,
  `apto_treinar` tinyint(1) DEFAULT '0',
  `apto_competir` tinyint(1) DEFAULT '0',
  `observacoes` text,
  PRIMARY KEY (`id_consulta`),
  KEY `id_atleta` (`id_atleta`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `contactos`
--

DROP TABLE IF EXISTS `contactos`;
CREATE TABLE IF NOT EXISTS `contactos` (
  `id_contacto` int NOT NULL AUTO_INCREMENT,
  `codigo_clube` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nome` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `assunto` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mensagem` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `data_envio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `respondido` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_contacto`),
  KEY `codigo_clube` (`codigo_clube`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `contactos_internos`
--

DROP TABLE IF EXISTS `contactos_internos`;
CREATE TABLE IF NOT EXISTS `contactos_internos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo_clube` varchar(50) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `funcao` varchar(50) NOT NULL,
  `telemovel` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `contactos_internos`
--

INSERT INTO `contactos_internos` (`id`, `codigo_clube`, `nome`, `funcao`, `telemovel`, `email`, `data_criacao`) VALUES
(1, '123456', 'António Silva', 'Presidente', '912345678', 'presidente@clube.pt', '2025-06-20 18:41:09'),
(2, '123456', 'Manuel Santos', 'Diretor Desportivo', '923456789', 'diretor@clube.pt', '2025-06-20 18:41:09'),
(3, '123456', 'João Ferreira', 'Treinador Principal', '934567890', 'treinador@clube.pt', '2025-06-20 18:41:09'),
(4, '123456', 'Ana Pereira', 'Secretaria', '945678901', 'secretaria@clube.pt', '2025-06-20 18:41:09'),
(5, '123456', 'Carlos Oliveira', 'Médico', '956789012', 'medico@clube.pt', '2025-06-20 18:41:09'),
(6, '123456', 'ghgfhg', 'Diretor Desportivo', '775675', 'diretordesportivo@com', '2025-06-28 16:55:42'),
(7, '123456', 'cristovao', 'Médico', '789569569', 'cristovao123@gmail.com', '2025-06-28 16:57:10'),
(8, '123456', 'arnaldo', 'Presidente', '54675', 'arnaldo@gmail.com', '2025-06-28 17:04:16'),
(9, '123456', 'bnkdfgh', 'Treinador Principal', 'fgdhh', 'fghdfj@ghrfjghj', '2025-06-28 17:04:35'),
(10, '123456', 'bnkdfgh', 'Treinador Principal', 'fgdhh', 'fghdfj@ghrfjghj', '2025-06-28 17:05:21'),
(11, '123456', 'hgjghj', 'Diretor Desportivo', 'ghfgujfgy', 'fghhf@bvbfgn', '2025-06-28 17:05:32'),
(12, '123456', 'hgjghj', 'Diretor Desportivo', 'ghfgujfgy', 'fghhf@bvbfgn', '2025-06-28 17:06:52');

-- --------------------------------------------------------

--
-- Estrutura da tabela `convocatorias_jogo`
--

DROP TABLE IF EXISTS `convocatorias_jogo`;
CREATE TABLE IF NOT EXISTS `convocatorias_jogo` (
  `id_convocatoria` int NOT NULL AUTO_INCREMENT,
  `id_jogo` int NOT NULL,
  `id_atleta` int NOT NULL,
  `data_convocatoria` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_convocatoria`),
  KEY `id_jogo` (`id_jogo`),
  KEY `id_atleta` (`id_atleta`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `convocatorias_jogo`
--

INSERT INTO `convocatorias_jogo` (`id_convocatoria`, `id_jogo`, `id_atleta`, `data_convocatoria`) VALUES
(5, 3, 3, '2025-06-06 19:55:00'),
(6, 3, 4, '2025-06-06 19:55:00'),
(9, 9, 4, '2025-06-28 16:16:40'),
(10, 9, 8, '2025-06-28 16:16:40'),
(11, 9, 5, '2025-06-28 16:16:40'),
(12, 9, 3, '2025-06-28 16:16:40');

-- --------------------------------------------------------

--
-- Estrutura da tabela `convocatorias_treino`
--

DROP TABLE IF EXISTS `convocatorias_treino`;
CREATE TABLE IF NOT EXISTS `convocatorias_treino` (
  `id_convocatoria` int NOT NULL AUTO_INCREMENT,
  `id_treino` int NOT NULL,
  `id_atleta` int NOT NULL,
  `data_convocatoria` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_convocatoria`),
  KEY `id_treino` (`id_treino`),
  KEY `id_atleta` (`id_atleta`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `convocatorias_treino`
--

INSERT INTO `convocatorias_treino` (`id_convocatoria`, `id_treino`, `id_atleta`, `data_convocatoria`) VALUES
(2, 1, 2, '2025-06-06 19:55:00'),
(4, 2, 2, '2025-06-06 19:55:00'),
(5, 3, 3, '2025-06-06 19:55:00'),
(6, 3, 4, '2025-06-06 19:55:00'),
(7, 4, 3, '2025-06-06 19:55:00'),
(8, 4, 4, '2025-06-06 19:55:00'),
(17, 5, 4, '2025-06-28 15:47:52'),
(18, 5, 8, '2025-06-28 15:47:52'),
(19, 5, 5, '2025-06-28 15:47:52'),
(20, 5, 3, '2025-06-28 15:47:52'),
(21, 5, 2, '2025-06-28 15:47:52'),
(22, 5, 6, '2025-06-28 15:47:52'),
(23, 15, 4, '2025-06-28 16:17:27'),
(24, 15, 8, '2025-06-28 16:17:27'),
(25, 15, 5, '2025-06-28 16:17:27'),
(26, 15, 3, '2025-06-28 16:17:27'),
(27, 15, 2, '2025-06-28 16:17:27'),
(28, 15, 6, '2025-06-28 16:17:27');

-- --------------------------------------------------------

--
-- Estrutura da tabela `documentos`
--

DROP TABLE IF EXISTS `documentos`;
CREATE TABLE IF NOT EXISTS `documentos` (
  `id_documento` int NOT NULL AUTO_INCREMENT,
  `id_atleta` int NOT NULL,
  `codigo_clube` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nome_documento` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_documento` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nome_arquivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `caminho_arquivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `data_upload` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observacoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id_documento`),
  KEY `id_atleta` (`id_atleta`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `documentos`
--

INSERT INTO `documentos` (`id_documento`, `id_atleta`, `codigo_clube`, `nome_documento`, `tipo_documento`, `nome_arquivo`, `caminho_arquivo`, `data_upload`, `observacoes`) VALUES
(2, 2, '', '', 'Atestado Médico', 'atestado_miguel.pdf', 'uploads/documentos/atestado_miguel.pdf', '2025-06-06 19:55:00', 'Atestado de aptidão física para a prática desportiva'),
(3, 5, '123456', 'clube', 'Identificação', '', 'documentos/1751121902_1744805036Real_Madrid.png', '2025-06-28 14:45:02', '');

-- --------------------------------------------------------

--
-- Estrutura da tabela `escaloes`
--

DROP TABLE IF EXISTS `escaloes`;
CREATE TABLE IF NOT EXISTS `escaloes` (
  `id_escalao` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `codigo_clube` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_escalao`),
  KEY `codigo_clube` (`codigo_clube`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `escaloes`
--

INSERT INTO `escaloes` (`id_escalao`, `nome`, `codigo_clube`, `ativo`) VALUES
(1, 'Pré-Petizes', '123456', 1),
(2, 'Petizes', '123456', 1),
(3, 'Traquinas', '123456', 1),
(4, 'Benjamins', '123456', 1),
(5, 'Infantis', '123456', 1),
(6, 'Iniciados', '123456', 1),
(7, 'Juvenis', '123456', 1),
(8, 'Juniores', '123456', 1),
(9, 'Sub-23', '123456', 1),
(10, 'Seniores', '123456', 1),
(11, 'Veteranos', '123456', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `estatisticas_jogo`
--

DROP TABLE IF EXISTS `estatisticas_jogo`;
CREATE TABLE IF NOT EXISTS `estatisticas_jogo` (
  `id_estatistica` int NOT NULL AUTO_INCREMENT,
  `id_jogo` int NOT NULL,
  `id_atleta` int NOT NULL,
  `gols` int NOT NULL DEFAULT '0',
  `assistencias` int NOT NULL DEFAULT '0',
  `cartoes_amarelos` int NOT NULL DEFAULT '0',
  `cartoes_vermelhos` int NOT NULL DEFAULT '0',
  `minutos_jogados` int NOT NULL DEFAULT '0',
  `observacoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id_estatistica`),
  KEY `id_jogo` (`id_jogo`),
  KEY `id_atleta` (`id_atleta`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `estatisticas_jogo`
--

INSERT INTO `estatisticas_jogo` (`id_estatistica`, `id_jogo`, `id_atleta`, `gols`, `assistencias`, `cartoes_amarelos`, `cartoes_vermelhos`, `minutos_jogados`, `observacoes`) VALUES
(5, 3, 3, 0, 0, 1, 0, 90, 'Recebeu cartão amarelo por falta tática'),
(6, 3, 4, 0, 0, 0, 0, 90, 'Boa atuação no gol, apesar dos gols sofridos');

-- --------------------------------------------------------

--
-- Estrutura da tabela `estatisticas_treino`
--

DROP TABLE IF EXISTS `estatisticas_treino`;
CREATE TABLE IF NOT EXISTS `estatisticas_treino` (
  `id_estatistica` int NOT NULL AUTO_INCREMENT,
  `id_atleta` int NOT NULL,
  `codigo_clube` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `mes` int NOT NULL,
  `ano` int NOT NULL,
  `total_treinos` int NOT NULL DEFAULT '0',
  `presencas` int NOT NULL DEFAULT '0',
  `faltas` int NOT NULL DEFAULT '0',
  `faltas_justificadas` int NOT NULL DEFAULT '0',
  `avaliacao_media` decimal(3,2) DEFAULT NULL,
  PRIMARY KEY (`id_estatistica`),
  KEY `id_atleta` (`id_atleta`),
  KEY `codigo_clube` (`codigo_clube`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `estatisticas_treino`
--

INSERT INTO `estatisticas_treino` (`id_estatistica`, `id_atleta`, `codigo_clube`, `mes`, `ano`, `total_treinos`, `presencas`, `faltas`, `faltas_justificadas`, `avaliacao_media`) VALUES
(2, 2, '123456', 6, 2025, 2, 2, 0, 0, 4.50),
(3, 3, '123456', 6, 2025, 2, 2, 0, 0, 3.50),
(4, 4, '123456', 6, 2025, 2, 1, 1, 1, 4.00);

-- --------------------------------------------------------

--
-- Estrutura da tabela `ficha_clinica`
--

DROP TABLE IF EXISTS `ficha_clinica`;
CREATE TABLE IF NOT EXISTS `ficha_clinica` (
  `id_ficha` int NOT NULL AUTO_INCREMENT,
  `id_atleta` int NOT NULL,
  `codigo_clube` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_sangue` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alergias` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `medicacao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `doencas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `restricoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `contato_emergencia` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefone_emergencia` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `observacoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `data_criacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `grupo_sanguineo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `doencas_cronicas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `medicacao_regular` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `cirurgias_anteriores` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `lesoes_anteriores` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `restricoes_alimentares` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `contato_emergencia_nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contato_emergencia_telefone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contato_emergencia_relacao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_ficha`),
  UNIQUE KEY `id_atleta` (`id_atleta`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `ficha_clinica`
--

INSERT INTO `ficha_clinica` (`id_ficha`, `id_atleta`, `codigo_clube`, `tipo_sangue`, `alergias`, `medicacao`, `doencas`, `restricoes`, `contato_emergencia`, `telefone_emergencia`, `observacoes`, `data_criacao`, `data_atualizacao`, `grupo_sanguineo`, `doencas_cronicas`, `medicacao_regular`, `cirurgias_anteriores`, `lesoes_anteriores`, `restricoes_alimentares`, `contato_emergencia_nome`, `contato_emergencia_telefone`, `contato_emergencia_relacao`) VALUES
(2, 2, '', 'O-', 'Nenhum', 'Nenhum', 'Nenhum', 'Nenhum', 'António Costa', '923456780', 'Ficha clínica inicial', '2025-06-06 19:55:00', '2025-06-06 19:55:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 5, '123456', NULL, 'jhgk', NULL, NULL, NULL, NULL, NULL, 'kggfg', '2025-06-28 14:36:50', '2025-06-28 14:38:58', 'B+', 'kghjh', 'khhj', 'kjhkhjk', 'hjkhjk', 'hjkhhjkh', 'fkkjjh', 'kjghf', 'hhfjk');

-- --------------------------------------------------------

--
-- Estrutura da tabela `historico`
--

DROP TABLE IF EXISTS `historico`;
CREATE TABLE IF NOT EXISTS `historico` (
  `id_historico` int NOT NULL AUTO_INCREMENT,
  `id_atleta` int NOT NULL,
  `codigo_clube` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_evento` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `data_evento` date NOT NULL,
  `registrado_por` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `observacoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `data_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_historico`),
  KEY `id_atleta` (`id_atleta`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `historico`
--

INSERT INTO `historico` (`id_historico`, `id_atleta`, `codigo_clube`, `tipo_evento`, `data_evento`, `registrado_por`, `descricao`, `observacoes`, `data_registro`) VALUES
(2, 2, '', 'Convocatória Seleção', '2025-06-01', '', 'Convocado para a seleção distrital Sub-11', 'Participação em estágio de 3 dias', '2025-06-06 19:55:00'),
(3, 5, '123456', 'Transferência', '2025-11-22', 'fernando', 'fghfgj', NULL, '2025-06-28 14:45:07'),
(4, 5, '123456', 'Lesão', '2024-06-11', 'rt', 'treyet', NULL, '2025-06-28 14:47:18'),
(5, 5, '123456', 'Avaliação', '2026-12-26', 'yhtyt', 'fgyh', NULL, '2025-06-28 14:47:35'),
(10, 2, '123456', 'Inscrição', '2222-02-22', 'gjfj', 'dfgsgj', NULL, '2025-06-28 15:46:01'),
(13, 2, '123456', 'Prémio', '0002-02-22', '2222', '222', NULL, '2025-06-28 15:46:30'),
(14, 2, '123456', 'Transferência', '2222-02-22', '222', '222', NULL, '2025-06-28 15:46:38'),
(15, 2, '123456', 'Renovação', '2222-02-22', '222', '222', NULL, '2025-06-28 15:46:47'),
(16, 2, '123456', 'Outro', '0222-02-22', '2222', '222', NULL, '2025-06-28 15:46:55');

-- --------------------------------------------------------

--
-- Estrutura da tabela `jogos`
--

DROP TABLE IF EXISTS `jogos`;
CREATE TABLE IF NOT EXISTS `jogos` (
  `id_jogo` int NOT NULL AUTO_INCREMENT,
  `codigo_clube` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `local` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `adversario` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `escalao` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sub_escalao` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo_jogo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Amigável',
  `resultado` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gols_favor` int DEFAULT '0',
  `gols_contra` int DEFAULT '0',
  `plano_jogo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `observacoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id_jogo`),
  KEY `codigo_clube` (`codigo_clube`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `jogos`
--

INSERT INTO `jogos` (`id_jogo`, `codigo_clube`, `data`, `hora`, `local`, `adversario`, `escalao`, `sub_escalao`, `tipo_jogo`, `resultado`, `gols_favor`, `gols_contra`, `plano_jogo`, `observacoes`) VALUES
(3, '123456', '2025-06-15', '11:30:00', 'Campo Municipal', 'Porto FC', 'Petizes (Sub-7)', 'Sub-13', 'Torneio', '0-3', 2, 3, 'Formação 11x11\r\nFoco na organização defensiva\r\nTrabalhar saída de bola sob pressão', 'Jogo contra equipa mais forte para desenvolvimento'),
(5, '123456', '2025-06-13', '11:01:00', 'sdfgsdh', 'fghffg', 'Traquinas (Sub-9)', NULL, 'Campeonato', NULL, 0, 0, NULL, ''),
(6, '123456', '2025-06-19', '12:20:00', 'fgrfgfdg', 'fguykgf', 'Pré-Petizes (Sub-5)', NULL, 'Campeonato', NULL, 0, 0, NULL, ''),
(8, '123456', '2025-06-26', '12:11:00', 'edftr', 'ytyrty', 'Juniores (Sub-19)', NULL, 'Torneio', NULL, 0, 0, NULL, ''),
(9, '123456', '2025-06-29', '11:01:00', 'reyteyeyt', 'ghfhfg', 'Veteranos', NULL, 'Campeonato', NULL, 0, 0, NULL, 'fghf'),
(10, '123456', '2025-07-02', '11:01:00', 'cghfvhg', 'ghdt', 'Veteranos', NULL, 'Amigável', NULL, 0, 0, NULL, '');

-- --------------------------------------------------------

--
-- Estrutura da tabela `mensagens_internas`
--

DROP TABLE IF EXISTS `mensagens_internas`;
CREATE TABLE IF NOT EXISTS `mensagens_internas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo_clube` varchar(50) NOT NULL,
  `remetente` varchar(100) NOT NULL,
  `destinatario` varchar(100) NOT NULL,
  `assunto` varchar(200) NOT NULL,
  `mensagem` text NOT NULL,
  `lida` tinyint(1) DEFAULT '0',
  `data_envio` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `mensagens_internas`
--

INSERT INTO `mensagens_internas` (`id`, `codigo_clube`, `remetente`, `destinatario`, `assunto`, `mensagem`, `lida`, `data_envio`) VALUES
(1, '123456', '', 'António Silva', 'trg', 'ghf', 0, '2025-06-28 16:55:11'),
(2, '123456', '', 'ghgfhg', 'fhg', 'ghhfghfg', 0, '2025-06-28 16:56:01'),
(3, '123456', '', 'Carlos Oliveira', 'fgjuyhfuj', 'yujtujtji', 0, '2025-06-28 17:00:26'),
(4, '123456', '', 'Carlos Oliveira', 'fgjuyhfuj', 'yujtujtji', 0, '2025-06-28 17:01:49'),
(5, '123456', '', 'hgjghj', 'fgufh', 'ujhgfy', 0, '2025-06-28 17:07:00'),
(6, '123456', '', 'hgjghj', 'fgufh', 'ujhgfy', 0, '2025-06-28 17:10:26'),
(7, '123456', '', 'hgjghj', 'fgufh', 'ujhgfy', 0, '2025-06-28 17:11:57'),
(8, '123456', 'Desconhecido', 'hgjghj', 'gfhg', 'hfjghjkgh', 0, '2025-06-28 17:19:58'),
(9, '123456', 'Desconhecido', 'hgjghj', 'fgufh', 'ujhgfy', 0, '2025-06-28 17:20:55'),
(10, '123456', 'Desconhecido', 'hgjghj', 'dsfgfgfg', 'gfgfghf', 0, '2025-06-28 17:21:02'),
(11, '123456', 'Desconhecido', 'hgjghj', 'dsfgfgfg', 'gfgfghf', 0, '2025-06-28 17:45:14'),
(12, '123456', 'Desconhecido', 'hgjghj', 'fgufh', 'ujhgfy', 0, '2025-06-28 17:45:44'),
(13, '123456', 'Desconhecido', 'hgjghj', 'fgufh', 'ujhgfy', 0, '2025-06-28 17:46:14'),
(14, '123456', 'Desconhecido', 'Manuel Santos', 'jyhj', 'jhkkk', 0, '2025-06-29 22:58:37');

-- --------------------------------------------------------

--
-- Estrutura da tabela `presencas_jogo`
--

DROP TABLE IF EXISTS `presencas_jogo`;
CREATE TABLE IF NOT EXISTS `presencas_jogo` (
  `id_presenca` int NOT NULL AUTO_INCREMENT,
  `id_jogo` int NOT NULL,
  `id_atleta` int NOT NULL,
  `presente` tinyint(1) NOT NULL DEFAULT '0',
  `justificacao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `data_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_presenca`),
  KEY `id_jogo` (`id_jogo`),
  KEY `id_atleta` (`id_atleta`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `presencas_jogo`
--

INSERT INTO `presencas_jogo` (`id_presenca`, `id_jogo`, `id_atleta`, `presente`, `justificacao`, `data_registro`) VALUES
(5, 3, 3, 1, NULL, '2025-06-06 19:55:00'),
(6, 3, 4, 1, NULL, '2025-06-06 19:55:00'),
(11, 8, 4, 1, 'tyiuju', '2025-06-28 15:50:51'),
(12, 8, 8, 1, '', '2025-06-28 15:50:51'),
(13, 8, 5, 1, '', '2025-06-28 15:50:51'),
(14, 8, 3, 1, 'ikuikuyi', '2025-06-28 15:50:51'),
(15, 8, 2, 1, 'uyiyi', '2025-06-28 15:50:51'),
(16, 8, 6, 1, '', '2025-06-28 15:50:51'),
(17, 6, 4, 1, '', '2025-06-28 15:50:59'),
(18, 6, 8, 1, '', '2025-06-28 15:50:59'),
(19, 6, 5, 1, '', '2025-06-28 15:50:59'),
(20, 6, 3, 0, '', '2025-06-28 15:50:59'),
(21, 6, 2, 1, '', '2025-06-28 15:50:59'),
(22, 6, 6, 0, '', '2025-06-28 15:50:59'),
(23, 5, 4, 1, '', '2025-06-28 15:51:08'),
(24, 5, 8, 0, '', '2025-06-28 15:51:08'),
(25, 5, 5, 0, '', '2025-06-28 15:51:08'),
(26, 5, 3, 0, '', '2025-06-28 15:51:08'),
(27, 5, 2, 0, '', '2025-06-28 15:51:08'),
(28, 5, 6, 0, '', '2025-06-28 15:51:08');

-- --------------------------------------------------------

--
-- Estrutura da tabela `presencas_treino`
--

DROP TABLE IF EXISTS `presencas_treino`;
CREATE TABLE IF NOT EXISTS `presencas_treino` (
  `id_presenca` int NOT NULL AUTO_INCREMENT,
  `id_treino` int NOT NULL,
  `id_atleta` int NOT NULL,
  `presente` tinyint(1) NOT NULL DEFAULT '0',
  `justificacao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `data_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `avaliacao` int DEFAULT NULL,
  `observacoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id_presenca`),
  KEY `id_treino` (`id_treino`),
  KEY `id_atleta` (`id_atleta`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `presencas_treino`
--

INSERT INTO `presencas_treino` (`id_presenca`, `id_treino`, `id_atleta`, `presente`, `justificacao`, `data_registro`, `avaliacao`, `observacoes`) VALUES
(2, 1, 2, 1, NULL, '2025-06-06 19:55:00', 5, 'Excelente participação em todos os exercícios'),
(5, 3, 3, 1, NULL, '2025-06-06 19:55:00', 3, 'Dificuldades nos exercícios de resistência'),
(6, 3, 4, 1, NULL, '2025-06-06 19:55:00', 4, 'Bom desempenho físico geral'),
(7, 4, 3, 1, NULL, '2025-06-06 19:55:00', 4, 'Boa participação no jogo formal'),
(8, 4, 4, 0, 'Lesão no tornozelo', '2025-06-06 19:55:00', NULL, NULL),
(10, 2, 2, 1, 'null', '2025-06-20 19:13:10', NULL, NULL),
(11, 5, 4, 0, '', '2025-06-22 18:52:33', NULL, NULL),
(12, 5, 3, 0, '', '2025-06-22 18:52:33', NULL, NULL),
(13, 5, 2, 0, '', '2025-06-22 18:52:33', NULL, NULL),
(15, 8, 4, 1, '', '2025-06-22 21:54:33', NULL, NULL),
(16, 8, 5, 0, '', '2025-06-22 21:54:33', NULL, NULL),
(17, 8, 3, 0, '', '2025-06-22 21:54:33', NULL, NULL),
(18, 8, 2, 0, '', '2025-06-22 21:54:33', NULL, NULL),
(19, 9, 4, 0, '', '2025-06-22 21:54:36', NULL, NULL),
(20, 9, 5, 0, '', '2025-06-22 21:54:36', NULL, NULL),
(21, 9, 3, 0, '', '2025-06-22 21:54:36', NULL, NULL),
(22, 9, 2, 0, '', '2025-06-22 21:54:36', NULL, NULL),
(27, 7, 4, 1, '', '2025-06-22 21:56:31', NULL, NULL),
(28, 7, 5, 1, '', '2025-06-22 21:56:31', NULL, NULL),
(29, 7, 3, 0, '', '2025-06-22 21:56:31', NULL, NULL),
(30, 7, 2, 0, '', '2025-06-22 21:56:31', NULL, NULL),
(31, 14, 4, 1, 'dfhgdjhjf', '2025-06-28 15:48:07', NULL, NULL),
(32, 14, 8, 1, '', '2025-06-28 15:48:07', NULL, NULL),
(33, 14, 5, 1, 'hfghjghjghj', '2025-06-28 15:48:07', NULL, NULL),
(34, 14, 3, 1, 'jhgjh', '2025-06-28 15:48:07', NULL, NULL),
(35, 14, 2, 1, 'hjjhj', '2025-06-28 15:48:07', NULL, NULL),
(36, 14, 6, 1, '', '2025-06-28 15:48:07', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `sub_escaloes`
--

DROP TABLE IF EXISTS `sub_escaloes`;
CREATE TABLE IF NOT EXISTS `sub_escaloes` (
  `id_sub_escalao` int NOT NULL AUTO_INCREMENT,
  `id_escalao` int NOT NULL,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `codigo_clube` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_sub_escalao`),
  KEY `id_escalao` (`id_escalao`),
  KEY `codigo_clube` (`codigo_clube`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `sub_escaloes`
--

INSERT INTO `sub_escaloes` (`id_sub_escalao`, `id_escalao`, `nome`, `codigo_clube`, `ativo`) VALUES
(1, 1, 'Sub-5', '123456', 1),
(2, 2, 'Sub-6', '123456', 1),
(3, 2, 'Sub-7', '123456', 1),
(4, 3, 'Sub-8', '123456', 1),
(5, 3, 'Sub-9', '123456', 1),
(6, 4, 'Sub-10', '123456', 1),
(7, 4, 'Sub-11', '123456', 1),
(8, 5, 'Sub-12', '123456', 1),
(9, 5, 'Sub-13', '123456', 1),
(10, 6, 'Sub-14', '123456', 1),
(11, 6, 'Sub-15', '123456', 1),
(12, 7, 'Sub-16', '123456', 1),
(13, 7, 'Sub-17', '123456', 1),
(14, 8, 'Sub-18', '123456', 1),
(15, 8, 'Sub-19', '123456', 1),
(16, 9, 'B', '123456', 1),
(17, 10, '', '123456', 1),
(18, 11, '', '123456', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `treinos`
--

DROP TABLE IF EXISTS `treinos`;
CREATE TABLE IF NOT EXISTS `treinos` (
  `id_treino` int NOT NULL AUTO_INCREMENT,
  `codigo_clube` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `data` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  `local` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `escalao` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sub_escalao` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo_treino` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `plano_treino` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `objetivos` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `observacoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id_treino`),
  KEY `codigo_clube` (`codigo_clube`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `treinos`
--

INSERT INTO `treinos` (`id_treino`, `codigo_clube`, `data`, `hora_inicio`, `hora_fim`, `local`, `escalao`, `sub_escalao`, `tipo_treino`, `plano_treino`, `objetivos`, `observacoes`) VALUES
(1, '123456', '2025-06-10', '18:00:00', '19:30:00', 'Campo Municipal', 'Sub-23 (B)', 'Sub-10', 'Técnico', 'Aquecimento (15 min)\r\nExercícios de passe (20 min)\r\nExercícios de finalização (25 min)\r\nJogo reduzido (25 min)\r\nRetorno à calma (5 min)', 'Melhorar a técnica de passe e finalização', 'Treino focado em aspectos técnicos'),
(2, '123456', '2025-06-12', '18:00:00', '19:30:00', 'Campo Municipal', 'Iniciados (Sub-14)', 'Sub-10', 'Tático', 'Aquecimento (15 min)\r\nPosicionamento defensivo (25 min)\r\nTransições defesa-ataque (20 min)\r\nJogo condicionado (25 min)\r\nRetorno à calma (5 min)', 'Melhorar o posicionamento defensivo e as transições', 'Treino focado em aspectos táticos'),
(3, '123456', '2025-06-19', '13:30:00', '19:00:00', 'Campo Municipal', 'Iniciados (Sub-14)', 'Sub-13', 'Normal', 'Aquecimento (15 min)\r\nCircuito de resistência (20 min)\r\nExercícios de velocidade (20 min)\r\nJogo condicionado (20 min)\r\nRetorno à calma (15 min)', 'Melhorar a condição física geral', 'Treino focado em aspectos físicos'),
(4, '123456', '2025-06-13', '18:30:00', '22:30:00', 'Campo Municipal', 'Seniores', 'Sub-13', 'Normal', 'Aquecimento (15 min)\r\nExercícios de posse de bola (20 min)\r\nSituações de jogo (20 min)\r\nJogo formal (30 min)\r\nRetorno à calma (5 min)', 'Preparação para o jogo do fim de semana', 'Treino focado na preparação competitiva'),
(5, '123456', '2222-02-22', '11:01:00', '11:01:00', 'dfghdhfg', 'Traquinas (Sub-9)', NULL, 'Técnico', NULL, NULL, ''),
(7, '123456', '2025-06-08', '11:11:00', '15:15:00', 'sfdhtrhrf', 'Juniores (Sub-19)', NULL, 'Normal', NULL, NULL, ''),
(8, '123456', '2025-06-18', '11:11:00', '11:01:00', 'dfgfdh', 'Traquinas (Sub-8)', NULL, 'Normal', NULL, NULL, 'etge'),
(9, '123456', '2025-06-18', '22:22:00', '22:02:00', 'fggrfg', 'Pré-Petizes (Sub-5)', NULL, 'Normal', NULL, NULL, ''),
(14, '123456', '2025-06-26', '11:01:00', '11:02:00', 'luz', 'Seniores', NULL, 'Normal', NULL, NULL, ''),
(15, '123456', '2025-06-29', '11:11:00', '11:11:00', 'rtgfdh', 'Veteranos', NULL, 'Normal', NULL, NULL, '');

-- --------------------------------------------------------

--
-- Estrutura da tabela `utilizadores`
--

DROP TABLE IF EXISTS `utilizadores`;
CREATE TABLE IF NOT EXISTS `utilizadores` (
  `id_utilizador` int NOT NULL AUTO_INCREMENT,
  `codigo_clube` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nome` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cargo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `permissoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `data_criacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acesso` datetime DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_utilizador`),
  KEY `codigo_clube` (`codigo_clube`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `utilizadores`
--

INSERT INTO `utilizadores` (`id_utilizador`, `codigo_clube`, `nome`, `email`, `password`, `cargo`, `permissoes`, `data_criacao`, `ultimo_acesso`, `ativo`) VALUES
(1, '123456', 'Administrador', 'admin@clube1.pt', '$2y$10$oG.TGsSu65kDTgtA0oNZr.9btw6lz0v5kaCDfXgCgFo7LEJ2DPOxO', 'Presidente', 'all', '2025-06-06 19:55:00', '2025-06-06 19:55:00', 1),
(2, '123456', 'João Silva', 'joao@clube1.pt', '$2y$10$oG.TGsSu65kDTgtA0oNZr.9btw6lz0v5kaCDfXgCgFo7LEJ2DPOxO', 'Diretor Desportivo', 'atletas,treinos,jogos', '2025-06-06 19:55:00', '2025-06-06 19:55:00', 1),
(3, '123456', 'Ana Costa', 'ana@clube1.pt', '$2y$10$oG.TGsSu65kDTgtA0oNZr.9btw6lz0v5kaCDfXgCgFo7LEJ2DPOxO', 'Treinador', 'atletas,treinos', '2025-06-06 19:55:00', '2025-06-06 19:55:00', 1);

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `atletas`
--
ALTER TABLE `atletas`
  ADD CONSTRAINT `atletas_ibfk_1` FOREIGN KEY (`codigo_clube`) REFERENCES `clube` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `contactos`
--
ALTER TABLE `contactos`
  ADD CONSTRAINT `contactos_ibfk_1` FOREIGN KEY (`codigo_clube`) REFERENCES `clube` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `convocatorias_jogo`
--
ALTER TABLE `convocatorias_jogo`
  ADD CONSTRAINT `convocatorias_jogo_ibfk_1` FOREIGN KEY (`id_jogo`) REFERENCES `jogos` (`id_jogo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `convocatorias_jogo_ibfk_2` FOREIGN KEY (`id_atleta`) REFERENCES `atletas` (`id_atleta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `convocatorias_treino`
--
ALTER TABLE `convocatorias_treino`
  ADD CONSTRAINT `convocatorias_treino_ibfk_1` FOREIGN KEY (`id_treino`) REFERENCES `treinos` (`id_treino`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `convocatorias_treino_ibfk_2` FOREIGN KEY (`id_atleta`) REFERENCES `atletas` (`id_atleta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`id_atleta`) REFERENCES `atletas` (`id_atleta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `escaloes`
--
ALTER TABLE `escaloes`
  ADD CONSTRAINT `escaloes_ibfk_1` FOREIGN KEY (`codigo_clube`) REFERENCES `clube` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `estatisticas_jogo`
--
ALTER TABLE `estatisticas_jogo`
  ADD CONSTRAINT `estatisticas_jogo_ibfk_1` FOREIGN KEY (`id_jogo`) REFERENCES `jogos` (`id_jogo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `estatisticas_jogo_ibfk_2` FOREIGN KEY (`id_atleta`) REFERENCES `atletas` (`id_atleta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `estatisticas_treino`
--
ALTER TABLE `estatisticas_treino`
  ADD CONSTRAINT `estatisticas_treino_ibfk_1` FOREIGN KEY (`id_atleta`) REFERENCES `atletas` (`id_atleta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `estatisticas_treino_ibfk_2` FOREIGN KEY (`codigo_clube`) REFERENCES `clube` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `ficha_clinica`
--
ALTER TABLE `ficha_clinica`
  ADD CONSTRAINT `ficha_clinica_ibfk_1` FOREIGN KEY (`id_atleta`) REFERENCES `atletas` (`id_atleta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `historico`
--
ALTER TABLE `historico`
  ADD CONSTRAINT `historico_ibfk_1` FOREIGN KEY (`id_atleta`) REFERENCES `atletas` (`id_atleta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `jogos`
--
ALTER TABLE `jogos`
  ADD CONSTRAINT `jogos_ibfk_1` FOREIGN KEY (`codigo_clube`) REFERENCES `clube` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `presencas_jogo`
--
ALTER TABLE `presencas_jogo`
  ADD CONSTRAINT `presencas_jogo_ibfk_1` FOREIGN KEY (`id_jogo`) REFERENCES `jogos` (`id_jogo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `presencas_jogo_ibfk_2` FOREIGN KEY (`id_atleta`) REFERENCES `atletas` (`id_atleta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `presencas_treino`
--
ALTER TABLE `presencas_treino`
  ADD CONSTRAINT `presencas_treino_ibfk_1` FOREIGN KEY (`id_treino`) REFERENCES `treinos` (`id_treino`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `presencas_treino_ibfk_2` FOREIGN KEY (`id_atleta`) REFERENCES `atletas` (`id_atleta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `sub_escaloes`
--
ALTER TABLE `sub_escaloes`
  ADD CONSTRAINT `sub_escaloes_ibfk_1` FOREIGN KEY (`id_escalao`) REFERENCES `escaloes` (`id_escalao`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_escaloes_ibfk_2` FOREIGN KEY (`codigo_clube`) REFERENCES `clube` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `treinos`
--
ALTER TABLE `treinos`
  ADD CONSTRAINT `treinos_ibfk_1` FOREIGN KEY (`codigo_clube`) REFERENCES `clube` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  ADD CONSTRAINT `utilizadores_ibfk_1` FOREIGN KEY (`codigo_clube`) REFERENCES `clube` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
