# MySQL-Front 5.1  (Build 1.5)

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE */;
/*!40101 SET SQL_MODE='STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES */;
/*!40103 SET SQL_NOTES='ON' */;


# Host: www.idear-office.com.ar    Database: virtualu
# ------------------------------------------------------
# Server version 5.0.27-community-nt

#
# Source for table archivo
#

DROP TABLE IF EXISTS `archivo`;
CREATE TABLE `archivo` (
  `archivo_id` int(11) NOT NULL auto_increment,
  `usuario_id` int(11) NOT NULL,
  `archivo_fechaalta` datetime NOT NULL,
  `archivo_titulo` varchar(255) collate latin1_spanish_ci NOT NULL,
  `archivo_archivo` varchar(100) collate latin1_spanish_ci NOT NULL,
  `archivo_descripcion` text collate latin1_spanish_ci,
  `archivo_tipo` char(1) collate latin1_spanish_ci NOT NULL,
  `activo` char(1) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`archivo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table archivo_carrera
#

DROP TABLE IF EXISTS `archivo_carrera`;
CREATE TABLE `archivo_carrera` (
  `archivo_id` int(11) NOT NULL,
  `carrera_id` int(11) NOT NULL,
  PRIMARY KEY  (`archivo_id`,`carrera_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table archivo_comision
#

DROP TABLE IF EXISTS `archivo_comision`;
CREATE TABLE `archivo_comision` (
  `archivo_id` int(11) NOT NULL,
  `comision_id` int(11) NOT NULL,
  PRIMARY KEY  (`archivo_id`,`comision_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table archivo_facultad
#

DROP TABLE IF EXISTS `archivo_facultad`;
CREATE TABLE `archivo_facultad` (
  `archivo_id` int(11) NOT NULL,
  `facultad_id` int(11) NOT NULL,
  PRIMARY KEY  (`archivo_id`,`facultad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table archivo_grupo
#

DROP TABLE IF EXISTS `archivo_grupo`;
CREATE TABLE `archivo_grupo` (
  `archivo_id` int(11) NOT NULL,
  `grupo_id` int(11) NOT NULL,
  PRIMARY KEY  (`archivo_id`,`grupo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table archivo_materia
#

DROP TABLE IF EXISTS `archivo_materia`;
CREATE TABLE `archivo_materia` (
  `archivo_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  PRIMARY KEY  (`archivo_id`,`materia_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table archivo_universidad
#

DROP TABLE IF EXISTS `archivo_universidad`;
CREATE TABLE `archivo_universidad` (
  `archivo_id` int(11) NOT NULL,
  `universidad_id` int(11) NOT NULL,
  PRIMARY KEY  (`archivo_id`,`universidad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table carrera
#

DROP TABLE IF EXISTS `carrera`;
CREATE TABLE `carrera` (
  `carrera_id` int(11) NOT NULL auto_increment,
  `carrera_titulo` varchar(80) collate latin1_spanish_ci NOT NULL,
  `facultad_id` int(11) NOT NULL,
  `activo` char(1) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`carrera_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table comision
#

DROP TABLE IF EXISTS `comision`;
CREATE TABLE `comision` (
  `comision_id` int(11) NOT NULL auto_increment,
  `comision_titulo` varchar(50) collate latin1_spanish_ci NOT NULL,
  `comision_codigo` varchar(15) collate latin1_spanish_ci default NULL,
  `materia_id` int(11) NOT NULL,
  `comision_fechadesde` date NOT NULL,
  `comision_fechahasta` date NOT NULL,
  `activo` char(1) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`comision_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table correccion
#

DROP TABLE IF EXISTS `correccion`;
CREATE TABLE `correccion` (
  `correccion_id` int(11) NOT NULL auto_increment,
  `entrega_id` int(11) NOT NULL,
  `correccion_pagina` int(11) NOT NULL,
  `correccion_posicion_x` int(11) NOT NULL,
  `correccion_posicion_y` int(11) NOT NULL,
  `correccion_tipo` char(1) collate latin1_spanish_ci NOT NULL,
  `correccion_color` varchar(15) collate latin1_spanish_ci NOT NULL,
  `correccion_texto` text collate latin1_spanish_ci NOT NULL,
  `correccion_tam` int(11) NOT NULL,
  `correccion_negrita` char(1) collate latin1_spanish_ci NOT NULL,
  `correccion_cursiva` char(1) collate latin1_spanish_ci NOT NULL,
  `correccion_subrayado` char(1) collate latin1_spanish_ci NOT NULL,
  `usuario_id` int(11) NOT NULL,
  PRIMARY KEY  (`correccion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table entrega
#

DROP TABLE IF EXISTS `entrega`;
CREATE TABLE `entrega` (
  `entrega_id` int(11) NOT NULL auto_increment,
  `tp_id` int(11) NOT NULL,
  `grupo_id` int(11) NOT NULL,
  `entrega_version` int(11) NOT NULL,
  `entrega_archivo` varchar(150) collate latin1_spanish_ci NOT NULL,
  `entrega_observacion` text collate latin1_spanish_ci,
  `entrega_fechacreacion` datetime NOT NULL,
  `entrega_fechaentrega` datetime NOT NULL,
  `entrega_estado` char(1) collate latin1_spanish_ci NOT NULL,
  `activo` char(1) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`entrega_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table event
#

DROP TABLE IF EXISTS `event`;
CREATE TABLE `event` (
  `evento_id` int(11) NOT NULL auto_increment,
  `evento_titulo` varchar(80) collate latin1_spanish_ci NOT NULL,
  `evento_descripcion` text collate latin1_spanish_ci,
  `evento_fecha` date NOT NULL,
  `evento_horainicio` time default NULL,
  `evento_horafin` time default NULL,
  `usuario_id` int(11) NOT NULL,
  `evento_ubicacion` varchar(60) collate latin1_spanish_ci default NULL,
  `comision_id` int(11) NOT NULL,
  PRIMARY KEY  (`evento_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table facultad
#

DROP TABLE IF EXISTS `facultad`;
CREATE TABLE `facultad` (
  `facultad_id` int(11) NOT NULL auto_increment,
  `universidad_id` int(11) NOT NULL,
  `facultad_titulo` varchar(255) collate latin1_spanish_ci NOT NULL,
  `activo` char(1) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`facultad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table grupo
#

DROP TABLE IF EXISTS `grupo`;
CREATE TABLE `grupo` (
  `grupo_id` int(11) NOT NULL auto_increment,
  `grupo_nombre` varchar(50) collate latin1_spanish_ci NOT NULL,
  `comision_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL COMMENT 'usuario creador',
  `activo` char(1) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`grupo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table materia
#

DROP TABLE IF EXISTS `materia`;
CREATE TABLE `materia` (
  `materia_id` int(11) NOT NULL auto_increment,
  `materia_titulo` varchar(255) collate latin1_spanish_ci NOT NULL,
  `carrera_id` int(11) NOT NULL,
  `activo` char(1) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`materia_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table mensaje
#

DROP TABLE IF EXISTS `mensaje`;
CREATE TABLE `mensaje` (
  `mensaje_id` int(11) NOT NULL auto_increment,
  `usuario_id` int(11) NOT NULL,
  `mensaje_fecha` datetime NOT NULL,
  `mensaje_titulo` varchar(255) collate latin1_spanish_ci NOT NULL,
  `mensaje_mensaje` text collate latin1_spanish_ci NOT NULL,
  `mensaje_tipo` char(1) collate latin1_spanish_ci NOT NULL,
  `mensaje_activo` char(1) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`mensaje_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table mensaje_carrera
#

DROP TABLE IF EXISTS `mensaje_carrera`;
CREATE TABLE `mensaje_carrera` (
  `mensaje_id` int(11) NOT NULL,
  `carrera_id` int(11) NOT NULL,
  PRIMARY KEY  (`mensaje_id`,`carrera_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table mensaje_comision
#

DROP TABLE IF EXISTS `mensaje_comision`;
CREATE TABLE `mensaje_comision` (
  `mensaje_id` int(11) NOT NULL,
  `comision_id` int(11) NOT NULL,
  PRIMARY KEY  (`mensaje_id`,`comision_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table mensaje_facultad
#

DROP TABLE IF EXISTS `mensaje_facultad`;
CREATE TABLE `mensaje_facultad` (
  `mensaje_id` int(11) NOT NULL,
  `facultad_id` int(11) NOT NULL,
  PRIMARY KEY  (`mensaje_id`,`facultad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table mensaje_grupo
#

DROP TABLE IF EXISTS `mensaje_grupo`;
CREATE TABLE `mensaje_grupo` (
  `mensaje_id` int(11) NOT NULL,
  `grupo_id` int(11) NOT NULL,
  PRIMARY KEY  (`mensaje_id`,`grupo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table mensaje_materia
#

DROP TABLE IF EXISTS `mensaje_materia`;
CREATE TABLE `mensaje_materia` (
  `mensaje_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  PRIMARY KEY  (`mensaje_id`,`materia_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table mensaje_universidad
#

DROP TABLE IF EXISTS `mensaje_universidad`;
CREATE TABLE `mensaje_universidad` (
  `mensaje_id` int(11) NOT NULL,
  `universidad_id` int(11) NOT NULL,
  PRIMARY KEY  (`mensaje_id`,`universidad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table mensaje_usuario
#

DROP TABLE IF EXISTS `mensaje_usuario`;
CREATE TABLE `mensaje_usuario` (
  `mensaje_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  PRIMARY KEY  (`mensaje_id`,`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table nota
#

DROP TABLE IF EXISTS `nota`;
CREATE TABLE `nota` (
  `entrega_id` int(11) NOT NULL,
  `nota_nota` varchar(150) collate latin1_spanish_ci NOT NULL,
  `nota_fechacorreccion` datetime NOT NULL,
  `nota_observacion` text collate latin1_spanish_ci,
  `usuario_id` int(11) NOT NULL,
  PRIMARY KEY  (`entrega_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table pais
#

DROP TABLE IF EXISTS `pais`;
CREATE TABLE `pais` (
  `pais_id` int(11) NOT NULL auto_increment,
  `pais_titulo` varchar(50) collate latin1_spanish_ci NOT NULL,
  `activo` char(1) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`pais_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table provincias_estados
#

DROP TABLE IF EXISTS `provincias_estados`;
CREATE TABLE `provincias_estados` (
  `prov_id` int(5) unsigned NOT NULL auto_increment,
  `estado` varchar(100) NOT NULL,
  `pais_id` varchar(100) NOT NULL default '0',
  PRIMARY KEY  (`prov_id`),
  KEY `fk_pais_id` (`pais_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Source for table tipodni
#

DROP TABLE IF EXISTS `tipodni`;
CREATE TABLE `tipodni` (
  `tipodni_id` int(11) NOT NULL auto_increment,
  `tipodni_titulo` varchar(5) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`tipodni_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table trabajo_practico
#

DROP TABLE IF EXISTS `trabajo_practico`;
CREATE TABLE `trabajo_practico` (
  `tp_id` int(11) NOT NULL auto_increment,
  `usuario_id` int(11) NOT NULL,
  `comision_id` int(11) NOT NULL,
  `tp_titulo` varchar(255) collate latin1_spanish_ci NOT NULL,
  `tp_fechaalta` datetime NOT NULL,
  `tp_fechaentrega` datetime NOT NULL,
  `tp_archivo` varchar(100) collate latin1_spanish_ci NOT NULL,
  `tp_descripcion` text collate latin1_spanish_ci,
  `activo` char(1) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`tp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table universidad
#

DROP TABLE IF EXISTS `universidad`;
CREATE TABLE `universidad` (
  `universidad_id` int(11) NOT NULL auto_increment,
  `prov_id` int(11) NOT NULL,
  `universidad_titulo` varchar(255) collate latin1_spanish_ci NOT NULL,
  `universidad_direccion` varchar(255) collate latin1_spanish_ci default NULL,
  `universidad_ciudad` varchar(255) collate latin1_spanish_ci default NULL,
  `universidad_telefono` varchar(100) collate latin1_spanish_ci default NULL,
  `universidad_email` varchar(255) collate latin1_spanish_ci default NULL,
  `universidad_web` varchar(255) collate latin1_spanish_ci default NULL,
  `activo` char(1) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`universidad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table usuario
#

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `usuario_id` int(11) NOT NULL auto_increment,
  `usuario_nombre` varchar(50) collate latin1_spanish_ci NOT NULL,
  `usuario_apellido` varchar(50) collate latin1_spanish_ci NOT NULL,
  `prov_id` int(11) NOT NULL,
  `tipo_usuario` char(1) collate latin1_spanish_ci NOT NULL,
  `usuario_email` varchar(40) collate latin1_spanish_ci NOT NULL,
  `usuario_password` varchar(40) collate latin1_spanish_ci NOT NULL,
  `tipodni_id` int(11) NOT NULL,
  `usuario_dni` int(12) NOT NULL,
  `usuario_ciudad` varchar(100) collate latin1_spanish_ci default NULL,
  `usuario_fechanac` date NOT NULL,
  `usuario_telefono` varchar(50) collate latin1_spanish_ci default NULL,
  `usuario_celular` varchar(50) collate latin1_spanish_ci default NULL,
  `usuario_notificaciones` char(1) character set latin1 NOT NULL default 'Y',
  `activo` char(1) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table usuario_comision
#

DROP TABLE IF EXISTS `usuario_comision`;
CREATE TABLE `usuario_comision` (
  `usuario_id` int(11) NOT NULL,
  `comision_id` int(11) NOT NULL,
  `usuario_tipo` char(1) collate latin1_spanish_ci NOT NULL,
  `activo` char(1) collate latin1_spanish_ci NOT NULL default 'Y',
  PRIMARY KEY  (`usuario_id`,`comision_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

#
# Source for table usuario_grupo
#

DROP TABLE IF EXISTS `usuario_grupo`;
CREATE TABLE `usuario_grupo` (
  `usuario_id` int(11) NOT NULL,
  `grupo_id` int(11) NOT NULL,
  PRIMARY KEY  (`usuario_id`,`grupo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
