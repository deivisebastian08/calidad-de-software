-- BASE DE DATOS FIS

CREATE DATABASE myweb;
USE myweb;

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
--  Table structure for `usuario`
-- ----------------------------
CREATE TABLE usuarios (
  usersId 		int(9) NOT NULL AUTO_INCREMENT,
  grupoId 		int(9) NOT NULL,
  nombres 		varchar(150) NOT NULL,
  users 		varchar(20) NOT NULL,
  clave 		varchar(120) NOT NULL,
  nivel 		int(2) NOT NULL,
  estado 		int(1) NOT NULL,
  email 		varchar(100) DEFAULT NULL,
  perfil 		varchar(150) DEFAULT NULL,
  fechaCreada 	datetime DEFAULT NULL,
  PRIMARY KEY (usersId)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO usuarios (usersId, grupoId, nombres, users, clave, nivel, estado, email, perfil, fechaCreada) VALUES
(1, 1, 'Juan Carlos, Pinto Larico', 'root', 'admin', 1, 1, 'jcpintol@hotmail.com', '', NOW());


CREATE TABLE grupos (
  grupoId 		int(9) NOT NULL AUTO_INCREMENT,
  usersId 		int(9) NOT NULL,
  nombreGrupo 	varchar(255) DEFAULT NULL,
  fechaInicio 	date NOT NULL,
  fechaFinal 	date NOT NULL,
  PRIMARY KEY (grupoId)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO grupos (grupoId, usersId, nombreGrupo, fechaInicio, fechaFinal) VALUES
(1, 1, 'Administrador Financiero Portable', NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR));


CREATE TABLE banner (
  idBanner int(3) NOT NULL auto_increment,
  usersId int(3) NOT NULL,
  Titulo varchar(250) DEFAULT NULL,
  Describir varchar(250) default NULL,
  Enlace varchar(250) default NULL,
  Imagen varchar(100) NOT NULL,
  estado int(1) NOT NULL default '0',
  fecha datetime NOT NULL,
  PRIMARY KEY  (`idBanner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE creabaner (
  idCreaBan int(3) NOT NULL auto_increment,
  idWeb int(3) NOT NULL,
  nombre varchar(250) default NULL,
  codigoCall varchar(250) default NULL,
  codigo text NOT NULL,
  usersId int(3) NOT NULL,
  PRIMARY KEY  (`idCreaBan`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ----------------------------
--  PROCEDIMIENTO DEFINIDA PARA `Acceder`
-- ----------------------------
DROP PROCEDURE IF EXISTS `Acceder`;
DELIMITER ;;
CREATE PROCEDURE `Acceder`(IN Usuario varchar(100),IN Claves varchar(200))
BEGIN
    DECLARE rpta VARCHAR(20) DEFAULT NULL;
    DECLARE IdGrupo INTEGER DEFAULT 0;
    DECLARE IdUser INTEGER DEFAULT 0;
    
    -- Verificar si el usuario existe y está activo
    SELECT usersId, grupoId INTO IdUser, IdGrupo 
    FROM usuarios 
    WHERE users = Usuario AND clave = Claves AND estado = '1';
    
    -- Si no se encontró usuario, retornar "No Existe"
    IF IdGrupo = 0 THEN
        SELECT 'No Existe' as usersId;
    ELSE
        -- Verificar si el grupo está activo
        SELECT usersId INTO rpta 
        FROM grupos 
        WHERE 0 < DATEDIFF(fechaFinal, now()) AND grupoId = IdGrupo;
        
        -- Si el grupo está activo, retornar datos del usuario
        IF rpta IS NOT NULL THEN
            SELECT usersId, grupoId, nombres, users, nivel  
            FROM usuarios 
            WHERE usersId = IdUser 
            LIMIT 1;
        ELSE 
            -- Si el grupo ha expirado, retornar "No Existe"
            SELECT 'No Existe' as usersId;
        END IF;
    END IF;
END
;;
DELIMITER ;
