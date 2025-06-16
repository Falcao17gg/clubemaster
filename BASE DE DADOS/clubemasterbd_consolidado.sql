-- SQL Consolidado para ClubeMaster
-- Este ficheiro contém todas as tabelas e dados necessários para o projeto ClubeMaster.
-- Inclui DROP TABLE IF EXISTS para evitar erros de importação.

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

-- Desativar verificação de chaves estrangeiras para permitir importação sem erros
SET FOREIGN_KEY_CHECKS=0;

--
-- Drop de tabelas existentes
--
DROP TABLE IF EXISTS `contactos`;
DROP TABLE IF EXISTS `historico`;
DROP TABLE IF EXISTS `ficha_clinica`;
DROP TABLE IF EXISTS `documentos`;
DROP TABLE IF EXISTS `estatisticas_jogo`;
DROP TABLE IF EXISTS `presencas_jogo`;
DROP TABLE IF EXISTS `convocatorias_jogo`;
DROP TABLE IF EXISTS `estatisticas_treino`;
DROP TABLE IF EXISTS `presencas_treino`;
DROP TABLE IF EXISTS `convocatorias_treino`;
DROP TABLE IF EXISTS `jogos`;
DROP TABLE IF EXISTS `treinos`;
DROP TABLE IF EXISTS `atletas`;
DROP TABLE IF EXISTS `sub_escaloes`;
DROP TABLE IF EXISTS `escaloes`;
DROP TABLE IF EXISTS `utilizadores`;
DROP TABLE IF EXISTS `clube`;

--
-- Estrutura da tabela `clube`
--

CREATE TABLE IF NOT EXISTS `clube` (
  `codigo` int NOT NULL,
  `password` text NOT NULL,
  `nome_clube` text NOT NULL,
  `nome_utilizador` text NOT NULL,
  `morada` text NOT NULL,
  `email` text NOT NULL,
  `data_registo` date NOT NULL,
  `imagem` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'no_image.png',
  `logo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `tipo_utilizador` varchar(50) DEFAULT 'admin',
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `clube`
--

INSERT INTO `clube` (`codigo`, `password`, `nome_clube`, `nome_utilizador`, `morada`, `email`, `data_registo`, `imagem`, `logo`, `telefone`, `website`, `tipo_utilizador`) VALUES
(123456, '$2y$10$oG.TGsSu65kDTgtA0oNZr.9btw6lz0v5kaCDfXgCgFo7LEJ2DPOxO', 'Clube 1', 'Presidente', 'Rua X', 'clube1@gmail.com', '2025-04-15', '1744750858clube.png', 'logo_clube.png', '912345678', 'www.clube1.pt', 'admin');

--
-- Estrutura da tabela `utilizadores`
--

CREATE TABLE IF NOT EXISTS `utilizadores` (
  `id_utilizador` int NOT NULL AUTO_INCREMENT,
  `codigo_clube` int NOT NULL,
  `nome` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `cargo` varchar(100) NOT NULL,
  `permissoes` text NOT NULL,
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
(1, 123456, 'Administrador', 'admin@clube1.pt', '$2y$10$oG.TGsSu65kDTgtA0oNZr.9btw6lz0v5kaCDfXgCgFo7LEJ2DPOxO', 'Presidente', 'all', '2025-06-06 19:55:00', '2025-06-06 19:55:00', 1),
(2, 123456, 'João Silva', 'joao@clube1.pt', '$2y$10$oG.TGsSu65kDTgtA0oNZr.9btw6lz0v5kaCDfXgCgFo7LEJ2DPOxO', 'Diretor Desportivo', 'atletas,treinos,jogos', '2025-06-06 19:55:00', '2025-06-06 19:55:00', 1),
(3, 123456, 'Ana Costa', 'ana@clube1.pt', '$2y$10$oG.TGsSu65kDTgtA0oNZr.9btw6lz0v5kaCDfXgCgFo7LEJ2DPOxO', 'Treinador', 'atletas,treinos', '2025-06-06 19:55:00', '2025-06-06 19:55:00', 1);

--
-- Estrutura da tabela `escaloes`
--

CREATE TABLE IF NOT EXISTS `escaloes` (
  `id_escalao` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `codigo_clube` int NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_escalao`),
  KEY `codigo_clube` (`codigo_clube`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `escaloes`
--

INSERT INTO `escaloes` (`id_escalao`, `nome`, `codigo_clube`, `ativo`) VALUES
(1, 'Pré-Petizes', 123456, 1),
(2, 'Petizes', 123456, 1),
(3, 'Traquinas', 123456, 1),
(4, 'Benjamins', 123456, 1),
(5, 'Infantis', 123456, 1),
(6, 'Iniciados', 123456, 1),
(7, 'Juvenis', 123456, 1),
(8, 'Juniores', 123456, 1),
(9, 'Sub-23', 123456, 1),
(10, 'Seniores', 123456, 1),
(11, 'Veteranos', 123456, 1);

--
-- Estrutura da tabela `sub_escaloes`
--

CREATE TABLE IF NOT EXISTS `sub_escaloes` (
  `id_sub_escalao` int NOT NULL AUTO_INCREMENT,
  `id_escalao` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `codigo_clube` int NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_sub_escalao`),
  KEY `id_escalao` (`id_escalao`),
  KEY `codigo_clube` (`codigo_clube`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `sub_escaloes`
--

INSERT INTO `sub_escaloes` (`id_sub_escalao`, `id_escalao`, `nome`, `codigo_clube`, `ativo`) VALUES
(1, 1, 'Sub-5', 123456, 1),
(2, 2, 'Sub-6', 123456, 1),
(3, 2, 'Sub-7', 123456, 1),
(4, 3, 'Sub-8', 123456, 1),
(5, 3, 'Sub-9', 123456, 1),
(6, 4, 'Sub-10', 123456, 1),
(7, 4, 'Sub-11', 123456, 1),
(8, 5, 'Sub-12', 123456, 1),
(9, 5, 'Sub-13', 123456, 1),
(10, 6, 'Sub-14', 123456, 1),
(11, 6, 'Sub-15', 123456, 1),
(12, 7, 'Sub-16', 123456, 1),
(13, 7, 'Sub-17', 123456, 1),
(14, 8, 'Sub-18', 123456, 1),
(15, 8, 'Sub-19', 123456, 1),
(16, 9, 'B', 123456, 1),
(17, 10, '', 123456, 1),
(18, 11, '', 123456, 1);

--
-- Estrutura da tabela `atletas`
--

CREATE TABLE IF NOT EXISTS `atletas` (
  `id_atleta` int NOT NULL AUTO_INCREMENT,
  `cc` text NOT NULL,
  `codigo_clube` int NOT NULL,
  `nome` varchar(200) NOT NULL,
  `data_nascimento` date NOT NULL,
  `posicao` varchar(100) NOT NULL,
  `fotografia` varchar(100) NOT NULL DEFAULT 'no_image.png',
  `pe_preferido` varchar(100) NOT NULL,
  `escalao` varchar(100) NOT NULL,
  `sub_escalao` varchar(100) DEFAULT NULL,
  `altura` decimal(3,2) DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `nacionalidade` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `morada` text,
  `encarregado_educacao` varchar(200) DEFAULT NULL,
  `telefone_encarregado` varchar(20) DEFAULT NULL,
  `data_inscricao` date DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_atleta`),
  KEY `codigo_clube` (`codigo_clube`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `atletas`
--

INSERT INTO `atletas` (`id_atleta`, `cc`, `codigo_clube`, `nome`, `data_nascimento`, `posicao`, `fotografia`, `pe_preferido`, `escalao`, `sub_escalao`, `altura`, `peso`, `nacionalidade`, `telefone`, `email`, `morada`, `encarregado_educacao`, `telefone_encarregado`, `data_inscricao`, `ativo`) VALUES
(1, '12345678', 123456, 'Pedro Santos', '2015-03-15', 'Avançado', 'jogador1.jpg', 'Direito', 'Benjamins', 'Sub-10', 1.40, 35.50, 'Portuguesa', '912345678', 'pedro@email.com', 'Rua das Flores, 123', 'Maria Santos', '912345679', '2025-01-15', 1),
(2, '23456789', 123456, 'Miguel Costa', '2014-07-22', 'Médio', 'jogador2.jpg', 'Esquerdo', 'Benjamins', 'Sub-11', 1.45, 38.20, 'Portuguesa', '923456789', 'miguel@email.com', 'Avenida Central, 45', 'António Costa', '923456780', '2025-01-20', 1),
(3, '34567890', 123456, 'João Ferreira', '2010-05-10', 'Defesa', 'jogador3.jpg', 'Direito', 'Infantis', 'Sub-13', 1.55, 45.00, 'Portuguesa', '934567890', 'joao@email.com', 'Rua do Comércio, 78', 'Paulo Ferreira', '934567891', '2025-02-05', 1),
(4, '45678901', 123456, 'André Silva', '2008-11-30', 'Guarda-Redes', 'jogador4.jpg', 'Direito', 'Iniciados', 'Sub-15', 1.65, 55.30, 'Portuguesa', '945678901', 'andre@email.com', 'Praça Central, 12', 'Carlos Silva', '945678902', '2025-02-10', 1);

--
-- Estrutura da tabela `treinos`
--

CREATE TABLE IF NOT EXISTS `treinos` (
  `id_treino` int NOT NULL AUTO_INCREMENT,
  `codigo_clube` int NOT NULL,
  `data` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  `local` varchar(200) NOT NULL,
  `escalao` varchar(100) NOT NULL,
  `sub_escalao` varchar(100) DEFAULT NULL,
  `tipo_treino` varchar(100) DEFAULT NULL,
  `plano_treino` text,
  `objetivos` text,
  `equipamentos` text,
  `observacoes` text,
  PRIMARY KEY (`id_treino`),
  KEY `codigo_clube` (`codigo_clube`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `treinos`
--

INSERT INTO `treinos` (`id_treino`, `codigo_clube`, `data`, `hora_inicio`, `hora_fim`, `local`, `escalao`, `sub_escalao`, `tipo_treino`, `plano_treino`, `objetivos`, `equipamentos`, `observacoes`) VALUES
(1, 123456, '2025-06-10', '18:00:00', '19:30:00', 'Campo Municipal', 'Benjamins', 'Sub-10', 'Técnico', 'Aquecimento (15 min)\r\nExercícios de passe (20 min)\r\nExercícios de finalização (25 min)\r\nJogo reduzido (25 min)\r\nRetorno à calma (5 min)', 'Melhorar a técnica de passe e finalização', 'Bolas, cones, coletes', 'Treino focado em aspectos técnicos'),
(2, 123456, '2025-06-12', '18:00:00', '19:30:00', 'Campo Municipal', 'Benjamins', 'Sub-10', 'Tático', 'Aquecimento (15 min)\r\nPosicionamento defensivo (25 min)\r\nTransições defesa-ataque (20 min)\r\nJogo condicionado (25 min)\r\nRetorno à calma (5 min)', 'Melhorar o posicionamento defensivo e as transições', 'Bolas, cones, coletes, balizas pequenas', 'Treino focado em aspectos táticos'),
(3, 123456, '2025-06-11', '17:30:00', '19:00:00', 'Campo Municipal', 'Infantis', 'Sub-13', 'Físico', 'Aquecimento (15 min)\r\nCircuito de resistência (20 min)\r\nExercícios de velocidade (20 min)\r\nJogo condicionado (20 min)\r\nRetorno à calma (15 min)', 'Melhorar a condição física geral', 'Bolas, cones, escadas de agilidade, barreiras', 'Treino focado em aspectos físicos'),
(4, 123456, '2025-06-13', '17:30:00', '19:00:00', 'Campo Municipal', 'Infantis', 'Sub-13', 'Competitivo', 'Aquecimento (15 min)\r\nExercícios de posse de bola (20 min)\r\nSituações de jogo (20 min)\r\nJogo formal (30 min)\r\nRetorno à calma (5 min)', 'Preparação para o jogo do fim de semana', 'Bolas, cones, coletes', 'Treino focado na preparação competitiva');

--
-- Estrutura da tabela `jogos`
--

CREATE TABLE IF NOT EXISTS `jogos` (
  `id_jogo` int NOT NULL AUTO_INCREMENT,
  `codigo_clube` int NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `local` varchar(200) NOT NULL,
  `adversario` varchar(200) NOT NULL,
  `escalao` varchar(100) NOT NULL,
  `sub_escalao` varchar(100) DEFAULT NULL,
  `tipo_jogo` varchar(100) DEFAULT 'Amigável',
  `resultado` varchar(10) DEFAULT NULL,
  `gols_favor` int DEFAULT '0',
  `gols_contra` int DEFAULT '0',
  `plano_jogo` text,
  `observacoes` text,
  PRIMARY KEY (`id_jogo`),
  KEY `codigo_clube` (`codigo_clube`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `jogos`
--

INSERT INTO `jogos` (`id_jogo`, `codigo_clube`, `data`, `hora`, `local`, `adversario`, `escalao`, `sub_escalao`, `tipo_jogo`, `resultado`, `gols_favor`, `gols_contra`, `plano_jogo`, `observacoes`) VALUES
(1, 123456, '2025-06-14', '10:00:00', 'Campo Municipal', 'Sporting FC', 'Benjamins', 'Sub-10', 'Amigável', '2-1', 2, 1, 'Formação 7x7\r\nFoco na pressão alta e transições rápidas\r\nExplorar espaços nas costas da defesa adversária', 'Jogo de preparação para o torneio'),
(2, 123456, '2025-06-21', '10:00:00', 'Estádio da Luz', 'Benfica FC', 'Benjamins', 'Sub-10', 'Torneio', NULL, 0, 0, 'Formação 7x7\r\nJogo mais defensivo\r\nExplorar contra-ataques', 'Primeiro jogo do torneio de verão'),
(3, 123456, '2025-06-15', '11:30:00', 'Campo Municipal', 'Porto FC', 'Infantis', 'Sub-13', 'Amigável', '0-3', 0, 3, 'Formação 11x11\r\nFoco na organização defensiva\r\nTrabalhar saída de bola sob pressão', 'Jogo contra equipa mais forte para desenvolvimento'),
(4, 123456, '2025-06-22', '11:30:00', 'Estádio do Dragão', 'Braga SC', 'Infantis', 'Sub-13', 'Campeonato', NULL, 0, 0, 'Formação 11x11\r\nManter posse de bola\r\nPressão alta na perda', 'Jogo importante para a classificação');

--
-- Estrutura da tabela `convocatorias_treino`
--

CREATE TABLE IF NOT EXISTS `convocatorias_treino` (
  `id_convocatoria` int NOT NULL AUTO_INCREMENT,
  `id_treino` int NOT NULL,
  `id_atleta` int NOT NULL,
  `data_convocatoria` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_convocatoria`),
  KEY `id_treino` (`id_treino`),
  KEY `id_atleta` (`id_atleta`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `convocatorias_treino`
--

INSERT INTO `convocatorias_treino` (`id_convocatoria`, `id_treino`, `id_atleta`, `data_convocatoria`) VALUES
(1, 1, 1, '2025-06-06 19:55:00'),
(2, 1, 2, '2025-06-06 19:55:00'),
(3, 2, 1, '2025-06-06 19:55:00'),
(4, 2, 2, '2025-06-06 19:55:00'),
(5, 3, 3, '2025-06-06 19:55:00'),
(6, 3, 4, '2025-06-06 19:55:00'),
(7, 4, 3, '2025-06-06 19:55:00'),
(8, 4, 4, '2025-06-06 19:55:00');

--
-- Estrutura da tabela `presencas_treino`
--

CREATE TABLE IF NOT EXISTS `presencas_treino` (
  `id_presenca` int NOT NULL AUTO_INCREMENT,
  `id_treino` int NOT NULL,
  `id_atleta` int NOT NULL,
  `presente` tinyint(1) NOT NULL DEFAULT '0',
  `justificacao` text,
  `data_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `avaliacao` int DEFAULT NULL,
  `observacoes` text,
  PRIMARY KEY (`id_presenca`),
  KEY `id_treino` (`id_treino`),
  KEY `id_atleta` (`id_atleta`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `presencas_treino`
--

INSERT INTO `presencas_treino` (`id_presenca`, `id_treino`, `id_atleta`, `presente`, `justificacao`, `data_registro`, `avaliacao`, `observacoes`) VALUES
(1, 1, 1, 1, NULL, '2025-06-06 19:55:00', 4, 'Bom desempenho nos exercícios técnicos'),
(2, 1, 2, 1, NULL, '2025-06-06 19:55:00', 5, 'Excelente participação em todos os exercícios'),
(3, 2, 1, 0, 'Doente', '2025-06-06 19:55:00', NULL, NULL),
(4, 2, 2, 1, NULL, '2025-06-06 19:55:00', 4, 'Boa compreensão dos conceitos táticos'),
(5, 3, 3, 1, NULL, '2025-06-06 19:55:00', 3, 'Dificuldades nos exercícios de resistência'),
(6, 3, 4, 1, NULL, '2025-06-06 19:55:00', 4, 'Bom desempenho físico geral'),
(7, 4, 3, 1, NULL, '2025-06-06 19:55:00', 4, 'Boa participação no jogo formal'),
(8, 4, 4, 0, 'Lesão no tornozelo', '2025-06-06 19:55:00', NULL, NULL);

--
-- Estrutura da tabela `estatisticas_treino`
--

CREATE TABLE IF NOT EXISTS `estatisticas_treino` (
  `id_estatistica` int NOT NULL AUTO_INCREMENT,
  `id_atleta` int NOT NULL,
  `codigo_clube` int NOT NULL,
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
(1, 1, 123456, 6, 2025, 2, 1, 1, 1, 4.00),
(2, 2, 123456, 6, 2025, 2, 2, 0, 0, 4.50),
(3, 3, 123456, 6, 2025, 2, 2, 0, 0, 3.50),
(4, 4, 123456, 6, 2025, 2, 1, 1, 1, 4.00);

--
-- Estrutura da tabela `convocatorias_jogo`
--

CREATE TABLE IF NOT EXISTS `convocatorias_jogo` (
  `id_convocatoria` int NOT NULL AUTO_INCREMENT,
  `id_jogo` int NOT NULL,
  `id_atleta` int NOT NULL,
  `data_convocatoria` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_convocatoria`),
  KEY `id_jogo` (`id_jogo`),
  KEY `id_atleta` (`id_atleta`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `convocatorias_jogo`
--

INSERT INTO `convocatorias_jogo` (`id_convocatoria`, `id_jogo`, `id_atleta`, `data_convocatoria`) VALUES
(1, 1, 1, '2025-06-06 19:55:00'),
(2, 1, 2, '2025-06-06 19:55:00'),
(3, 2, 1, '2025-06-06 19:55:00'),
(4, 2, 2, '2025-06-06 19:55:00'),
(5, 3, 3, '2025-06-06 19:55:00'),
(6, 3, 4, '2025-06-06 19:55:00'),
(7, 4, 3, '2025-06-06 19:55:00'),
(8, 4, 4, '2025-06-06 19:55:00');

--
-- Estrutura da tabela `presencas_jogo`
--

CREATE TABLE IF NOT EXISTS `presencas_jogo` (
  `id_presenca` int NOT NULL AUTO_INCREMENT,
  `id_jogo` int NOT NULL,
  `id_atleta` int NOT NULL,
  `presente` tinyint(1) NOT NULL DEFAULT '0',
  `justificacao` text,
  `data_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_presenca`),
  KEY `id_jogo` (`id_jogo`),
  KEY `id_atleta` (`id_atleta`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `presencas_jogo`
--

INSERT INTO `presencas_jogo` (`id_presenca`, `id_jogo`, `id_atleta`, `presente`, `justificacao`, `data_registro`) VALUES
(1, 1, 1, 1, NULL, '2025-06-06 19:55:00'),
(2, 1, 2, 1, NULL, '2025-06-06 19:55:00'),
(3, 2, 1, 1, NULL, '2025-06-06 19:55:00'),
(4, 2, 2, 1, NULL, '2025-06-06 19:55:00'),
(5, 3, 3, 1, NULL, '2025-06-06 19:55:00'),
(6, 3, 4, 1, NULL, '2025-06-06 19:55:00'),
(7, 4, 3, 1, NULL, '2025-06-06 19:55:00'),
(8, 4, 4, 1, NULL, '2025-06-06 19:55:00');

--
-- Estrutura da tabela `estatisticas_jogo`
--

CREATE TABLE IF NOT EXISTS `estatisticas_jogo` (
  `id_estatistica` int NOT NULL AUTO_INCREMENT,
  `id_jogo` int NOT NULL,
  `id_atleta` int NOT NULL,
  `gols` int NOT NULL DEFAULT '0',
  `assistencias` int NOT NULL DEFAULT '0',
  `cartoes_amarelos` int NOT NULL DEFAULT '0',
  `cartoes_vermelhos` int NOT NULL DEFAULT '0',
  `minutos_jogados` int NOT NULL DEFAULT '0',
  `observacoes` text,
  PRIMARY KEY (`id_estatistica`),
  KEY `id_jogo` (`id_jogo`),
  KEY `id_atleta` (`id_atleta`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `estatisticas_jogo`
--

INSERT INTO `estatisticas_jogo` (`id_estatistica`, `id_jogo`, `id_atleta`, `gols`, `assistencias`, `cartoes_amarelos`, `cartoes_vermelhos`, `minutos_jogados`, `observacoes`) VALUES
(1, 1, 1, 1, 0, 0, 0, 60, 'Bom desempenho, marcou um gol decisivo'),
(2, 1, 2, 0, 1, 0, 0, 60, 'Boa assistência para o gol'),
(3, 2, 1, 0, 0, 0, 0, 60, 'Jogo discreto'),
(4, 2, 2, 0, 0, 0, 0, 60, 'Boa marcação no meio-campo'),
(5, 3, 3, 0, 0, 1, 0, 90, 'Recebeu cartão amarelo por falta tática'),
(6, 3, 4, 0, 0, 0, 0, 90, 'Boa atuação no gol, apesar dos gols sofridos'),
(7, 4, 3, 0, 0, 0, 0, 90, 'Líder na defesa'),
(8, 4, 4, 0, 0, 0, 0, 90, 'Defesas importantes');

--
-- Estrutura da tabela `documentos`
--

CREATE TABLE IF NOT EXISTS `documentos` (
  `id_documento` int NOT NULL AUTO_INCREMENT,
  `id_atleta` int NOT NULL,
  `tipo_documento` varchar(100) NOT NULL,
  `nome_arquivo` varchar(255) NOT NULL,
  `caminho_arquivo` varchar(255) NOT NULL,
  `data_upload` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observacoes` text,
  PRIMARY KEY (`id_documento`),
  KEY `id_atleta` (`id_atleta`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `documentos`
--

INSERT INTO `documentos` (`id_documento`, `id_atleta`, `tipo_documento`, `nome_arquivo`, `caminho_arquivo`, `data_upload`, `observacoes`) VALUES
(1, 1, 'Certidão de Nascimento', 'certidao_pedro.pdf', 'uploads/documentos/certidao_pedro.pdf', '2025-06-06 19:55:00', 'Documento de identificação do atleta'),
(2, 2, 'Atestado Médico', 'atestado_miguel.pdf', 'uploads/documentos/atestado_miguel.pdf', '2025-06-06 19:55:00', 'Atestado de aptidão física para a prática desportiva');

--
-- Estrutura da tabela `ficha_clinica`
--

CREATE TABLE IF NOT EXISTS `ficha_clinica` (
  `id_ficha` int NOT NULL AUTO_INCREMENT,
  `id_atleta` int NOT NULL,
  `tipo_sangue` varchar(5) DEFAULT NULL,
  `alergias` text,
  `medicacao` text,
  `doencas` text,
  `restricoes` text,
  `contato_emergencia` varchar(200) DEFAULT NULL,
  `telefone_emergencia` varchar(20) DEFAULT NULL,
  `observacoes` text,
  `data_criacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_ficha`),
  UNIQUE KEY `id_atleta` (`id_atleta`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `ficha_clinica`
--

INSERT INTO `ficha_clinica` (`id_ficha`, `id_atleta`, `tipo_sangue`, `alergias`, `medicacao`, `doencas`, `restricoes`, `contato_emergencia`, `telefone_emergencia`, `observacoes`, `data_criacao`, `data_atualizacao`) VALUES
(1, 1, 'A+', 'Pólen', 'Anti-histamínico', 'Asma', 'Evitar exercícios intensos em dias de alta polinização', 'Maria Santos', '912345679', 'Ficha clínica inicial', '2025-06-06 19:55:00', '2025-06-06 19:55:00'),
(2, 2, 'O-', 'Nenhum', 'Nenhum', 'Nenhum', 'Nenhum', 'António Costa', '923456780', 'Ficha clínica inicial', '2025-06-06 19:55:00', '2025-06-06 19:55:00');

--
-- Estrutura da tabela `historico`
--

CREATE TABLE IF NOT EXISTS `historico` (
  `id_historico` int NOT NULL AUTO_INCREMENT,
  `id_atleta` int NOT NULL,
  `tipo_evento` varchar(100) NOT NULL,
  `data_evento` date NOT NULL,
  `descricao` text NOT NULL,
  `observacoes` text,
  `data_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_historico`),
  KEY `id_atleta` (`id_atleta`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `historico`
--

INSERT INTO `historico` (`id_historico`, `id_atleta`, `tipo_evento`, `data_evento`, `descricao`, `observacoes`, `data_registro`) VALUES
(1, 1, 'Lesão', '2025-05-20', 'Entorse no tornozelo direito durante o treino', 'Repouso de 1 semana', '2025-06-06 19:55:00'),
(2, 2, 'Convocatória Seleção', '2025-06-01', 'Convocado para a seleção distrital Sub-11', 'Participação em estágio de 3 dias', '2025-06-06 19:55:00');

--
-- Estrutura da tabela `contactos`
--

CREATE TABLE IF NOT EXISTS `contactos` (
  `id_contacto` int NOT NULL AUTO_INCREMENT,
  `codigo_clube` int NOT NULL,
  `nome` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `assunto` varchar(200) NOT NULL,
  `mensagem` text NOT NULL,
  `data_envio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `respondido` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_contacto`),
  KEY `codigo_clube` (`codigo_clube`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  ADD CONSTRAINT `utilizadores_ibfk_1` FOREIGN KEY (`codigo_clube`) REFERENCES `clube` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `escaloes`
--
ALTER TABLE `escaloes`
  ADD CONSTRAINT `escaloes_ibfk_1` FOREIGN KEY (`codigo_clube`) REFERENCES `clube` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `sub_escaloes`
--
ALTER TABLE `sub_escaloes`
  ADD CONSTRAINT `sub_escaloes_ibfk_1` FOREIGN KEY (`id_escalao`) REFERENCES `escaloes` (`id_escalao`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_escaloes_ibfk_2` FOREIGN KEY (`codigo_clube`) REFERENCES `clube` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `atletas`
--
ALTER TABLE `atletas`
  ADD CONSTRAINT `atletas_ibfk_1` FOREIGN KEY (`codigo_clube`) REFERENCES `clube` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `treinos`
--
ALTER TABLE `treinos`
  ADD CONSTRAINT `treinos_ibfk_1` FOREIGN KEY (`codigo_clube`) REFERENCES `clube` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `jogos`
--
ALTER TABLE `jogos`
  ADD CONSTRAINT `jogos_ibfk_1` FOREIGN KEY (`codigo_clube`) REFERENCES `clube` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `convocatorias_treino`
--
ALTER TABLE `convocatorias_treino`
  ADD CONSTRAINT `convocatorias_treino_ibfk_1` FOREIGN KEY (`id_treino`) REFERENCES `treinos` (`id_treino`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `convocatorias_treino_ibfk_2` FOREIGN KEY (`id_atleta`) REFERENCES `atletas` (`id_atleta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `presencas_treino`
--
ALTER TABLE `presencas_treino`
  ADD CONSTRAINT `presencas_treino_ibfk_1` FOREIGN KEY (`id_treino`) REFERENCES `treinos` (`id_treino`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `presencas_treino_ibfk_2` FOREIGN KEY (`id_atleta`) REFERENCES `atletas` (`id_atleta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `estatisticas_treino`
--
ALTER TABLE `estatisticas_treino`
  ADD CONSTRAINT `estatisticas_treino_ibfk_1` FOREIGN KEY (`id_atleta`) REFERENCES `atletas` (`id_atleta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `estatisticas_treino_ibfk_2` FOREIGN KEY (`codigo_clube`) REFERENCES `clube` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `convocatorias_jogo`
--
ALTER TABLE `convocatorias_jogo`
  ADD CONSTRAINT `convocatorias_jogo_ibfk_1` FOREIGN KEY (`id_jogo`) REFERENCES `jogos` (`id_jogo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `convocatorias_jogo_ibfk_2` FOREIGN KEY (`id_atleta`) REFERENCES `atletas` (`id_atleta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `presencas_jogo`
--
ALTER TABLE `presencas_jogo`
  ADD CONSTRAINT `presencas_jogo_ibfk_1` FOREIGN KEY (`id_jogo`) REFERENCES `jogos` (`id_jogo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `presencas_jogo_ibfk_2` FOREIGN KEY (`id_atleta`) REFERENCES `atletas` (`id_atleta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `estatisticas_jogo`
--
ALTER TABLE `estatisticas_jogo`
  ADD CONSTRAINT `estatisticas_jogo_ibfk_1` FOREIGN KEY (`id_jogo`) REFERENCES `jogos` (`id_jogo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `estatisticas_jogo_ibfk_2` FOREIGN KEY (`id_atleta`) REFERENCES `atletas` (`id_atleta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`id_atleta`) REFERENCES `atletas` (`id_atleta`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Limitadores para a tabela `contactos`
--
ALTER TABLE `contactos`
  ADD CONSTRAINT `contactos_ibfk_1` FOREIGN KEY (`codigo_clube`) REFERENCES `clube` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Reativar verificação de chaves estrangeiras
SET FOREIGN_KEY_CHECKS=1;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


