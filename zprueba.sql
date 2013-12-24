-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-09-2013 a las 10:54:40
-- Versión del servidor: 5.5.27
-- Versión de PHP: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `zprueba`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos_en_cursos`
--

CREATE TABLE IF NOT EXISTS `alumnos_en_cursos` (
  `curso_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  KEY `curso_id` (`curso_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `alumnos_en_cursos`
--

LOCK TABLES `alumnos_en_cursos` WRITE;
/*!40000 ALTER TABLE `alumnos_en_cursos` DISABLE KEYS */;
INSERT INTO `alumnos_en_cursos` VALUES (1,1,0),(1,2,0),(1,3,0),(1,4,0),(1,5,0),(2,1,0),(2,2,0),(3,1,1),(3,2,-1),(4,1,0);
/*!40000 ALTER TABLE `alumnos_en_cursos` ENABLE KEYS */;
UNLOCK TABLES;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE IF NOT EXISTS `cursos` (
  `curso_id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_curso` varchar(50) NOT NULL,
  `num_max_pods` int(11) NOT NULL,
  `inicio_curso` date NOT NULL,
  `fin_curso` date NOT NULL,
  `edicion` int(11) NOT NULL,
  `curso_activo` int(11) NOT NULL,
  `dia_mant_semanal` int(11) NOT NULL,
  `hora_inicio_mant_semanal` int(11) NOT NULL,
  `duracion_mant_semanal` int(11) NOT NULL,
  PRIMARY KEY (`curso_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `cursos`
--

LOCK TABLES `cursos` WRITE;
/*!40000 ALTER TABLE `cursos` DISABLE KEYS */;
INSERT INTO `cursos` VALUES (1,'CCNA_prueba',3,'2013-05-27','2013-12-31',0,1,-1,-1,-1),(2,'CCNA_SEC_prueba',1,'2013-05-27','2013-12-31',0,1,-1,-1,-1),(3,'CCNP_prueba',1,'2013-05-01','2013-05-26',0,0,-1,-1,-1),(4,'control',0,'2013-07-01','2018-07-01',0,1,-1,-1,-1);
/*!40000 ALTER TABLE `cursos` ENABLE KEYS */;
UNLOCK TABLES;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_pers`
--

CREATE TABLE IF NOT EXISTS `datos_pers` (
  `datos_pers_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY (`datos_pers_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `datos_pers`
--

LOCK TABLES `datos_pers` WRITE;
/*!40000 ALTER TABLE `datos_pers` DISABLE KEYS */;
INSERT INTO `datos_pers` VALUES (1,1,'Administrador','de Administradores','cuentademails12345@gmail.com'),(2,2,'Pol','de Pol','cuentademails12345@gmail.com'),(3,3,'Salva','de Salva','cuentademails12345@gmail.com'),(4,4,'Cuc','de Cuc','cuentademails12345@gmail.com'),(5,5,'Karin','Cardona','karincardona@hotmail.com');
/*!40000 ALTER TABLE `datos_pers` ENABLE KEYS */;
UNLOCK TABLES;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `entrada` datetime NOT NULL,
  `reserva_id` int(11) NOT NULL,
  `num_pod_lab` int(11) NOT NULL,
  `acceso_lab` datetime NOT NULL,
  `codigo_salida` int(11) NOT NULL,
  `salida` datetime NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`,`curso_id`,`reserva_id`),
  KEY `curso_id` (`curso_id`),
  KEY `reserva_id` (`reserva_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
INSERT INTO `logs` VALUES (1,1,1,'2013-09-26 02:07:49',NULL,NULL,NULL,2,'2013-09-26 02:08:27'),(2,2,1,'2013-09-26 02:09:17',2,2,'2013-09-26 02:11:45',1,'2013-09-26 02:15:24');
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_detalles`
--

CREATE TABLE IF NOT EXISTS `log_detalles` (
  `log_det_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `confir_resv` datetime NOT NULL,
  `confir_pod` int(11) NOT NULL,
  `cancel_resv` datetime NOT NULL,
  PRIMARY KEY (`log_det_id`),
  KEY `log_id` (`log_id`,`curso_id`,`user_id`),
  KEY `curso_id` (`curso_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `log_detalles`
--

LOCK TABLES `log_detalles` WRITE;
/*!40000 ALTER TABLE `log_detalles` DISABLE KEYS */;
INSERT INTO `log_detalles` VALUES (1,1,1,1,'2013-09-26 02:00:00',1,NULL),(2,2,1,2,'2013-09-26 02:00:00',2,NULL);
/*!40000 ALTER TABLE `log_detalles` ENABLE KEYS */;
UNLOCK TABLES;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mantenimiento`
--

CREATE TABLE IF NOT EXISTS `mantenimiento` (
  `outage_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `fecha_outage` date NOT NULL,
  `horario_outage` time NOT NULL,
  `estado_outage` int(11) NOT NULL,
  `num_POD_outage` int(11) NOT NULL,
  PRIMARY KEY (`outage_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `mantenimiento`
--

LOCK TABLES `mantenimiento` WRITE;
/*!40000 ALTER TABLE `mantenimiento` DISABLE KEYS */;
INSERT INTO `mantenimiento` VALUES (1,1,1,'2013-09-26','04:00:00',-1,1),(2,1,1,'2013-09-26','04:00:00',-1,2),(3,1,1,'2013-09-26','04:00:00',-1,3),(4,1,1,'2013-09-26','06:00:00',-1,1);
/*!40000 ALTER TABLE `mantenimiento` ENABLE KEYS */;
UNLOCK TABLES;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes_temp`
--

CREATE TABLE IF NOT EXISTS `mensajes_temp` (
  `mensaje_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `fecha_fin_mensaje` datetime NOT NULL,
  `texto_mensaje` varchar(200) NOT NULL,
  PRIMARY KEY (`mensaje_id`),
  KEY `admin_id` (`admin_id`,`curso_id`),
  KEY `curso_id` (`curso_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `mensajes_temp`
--

LOCK TABLES `mensajes_temp` WRITE;
/*!40000 ALTER TABLE `mensajes_temp` DISABLE KEYS */;
INSERT INTO `mensajes_temp` VALUES (1,1,1,'2013-09-26 06:00:00','Turno de Mantenimiento: 2013-09-26 a las 04:00:00');
/*!40000 ALTER TABLE `mensajes_temp` ENABLE KEYS */;
UNLOCK TABLES;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parametros_basicos`
--

CREATE TABLE IF NOT EXISTS `parametros_basicos` (
  `param_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `duracionTurno` int(11) NOT NULL,
  `numPods` int(11) NOT NULL,
  `numMaxReservas` int(11) NOT NULL,
  `numMaxResvSemana` int(11) NOT NULL,
  `numMaxCanc` int(11) NOT NULL,
  `round_robin` int(11) NOT NULL,
  `idle_timeout` int(11) NOT NULL,
  `estadist_num_resv_activas` int(11) NOT NULL,
  `estadist_num_resv_ejecutadas` int(11) NOT NULL,
  `estadist_num_resv_canceladas` int(11) NOT NULL,
  PRIMARY KEY (`param_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `parametros_basicos`
--

LOCK TABLES `parametros_basicos` WRITE;
/*!40000 ALTER TABLE `parametros_basicos` DISABLE KEYS */;
INSERT INTO `parametros_basicos` VALUES (1,1,2,15,5,99,999,2,600,3,3,2);
/*!40000 ALTER TABLE `parametros_basicos` ENABLE KEYS */;
UNLOCK TABLES;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE IF NOT EXISTS `reservas` (
  `reserva_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `fecha_reserva` date NOT NULL,
  `horario_reserva` time NOT NULL,
  `num_POD` int(11) NOT NULL,
  `estado_reserva` int(11) NOT NULL,
  PRIMARY KEY (`reserva_id`),
  KEY `user_id` (`user_id`,`curso_id`),
  KEY `curso_id` (`curso_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `reservas`
--

LOCK TABLES `reservas` WRITE;
/*!40000 ALTER TABLE `reservas` DISABLE KEYS */;
INSERT INTO `reservas` VALUES (1,1,1,'2013-09-26','02:00:00',1,-1),(2,2,1,'2013-09-26','02:00:00',2,1);
/*!40000 ALTER TABLE `reservas` ENABLE KEYS */;
UNLOCK TABLES;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `admin` int(11) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'admin','aaa',1),(2,'pol','xxx',0),(3,'salva','sss',0),(4,'cuc','yyy',0),(5,'karincardona@hotmail.com','y8b07MCaka',0);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumnos_en_cursos`
--
ALTER TABLE `alumnos_en_cursos`
  ADD CONSTRAINT `alumnos_en_cursos_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `alumnos_en_cursos_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`curso_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `datos_pers`
--
ALTER TABLE `datos_pers`
  ADD CONSTRAINT `datos_pers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_3` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`reserva_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`curso_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `log_detalles`
--
ALTER TABLE `log_detalles`
  ADD CONSTRAINT `log_detalles_ibfk_3` FOREIGN KEY (`log_id`) REFERENCES `logs` (`log_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `log_detalles_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`curso_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `log_detalles_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `mantenimiento`
--
ALTER TABLE `mantenimiento`
  ADD CONSTRAINT `mantenimiento_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `usuarios` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `mensajes_temp`
--
ALTER TABLE `mensajes_temp`
  ADD CONSTRAINT `mensajes_temp_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `usuarios` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mensajes_temp_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`curso_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `parametros_basicos`
--
ALTER TABLE `parametros_basicos`
  ADD CONSTRAINT `parametros_basicos_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `usuarios` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`curso_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
