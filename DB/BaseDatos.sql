-- BASE DE DATOS FIS

CREATE DATABASE myweb;
USE myweb;

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
--  Table structure for `usuario`
-- ----------------------------
DROP TABLE IF EXISTS `usuarios`;
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
(1, 1, 'deivis brayan quispe pacompia', 'root', 'admin', 1, 1, 'jcpintol@hotmail.com', '', NOW()),
(2, 1, 'Usuario Editor', 'editor', 'editor123', 2, 1, 'editor@example.com', '', NOW());

-- ----------------------------
--  Table structure for `grupos`
-- ----------------------------
DROP TABLE IF EXISTS `grupos`;
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

-- ----------------------------
--  Table structure for `banner`
-- ----------------------------
DROP TABLE IF EXISTS `banner`;
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

-- ----------------------------
--  Table structure for `creabaner`
-- ----------------------------
DROP TABLE IF EXISTS `creabaner`;
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
    
    SELECT usersId, grupoId INTO IdUser, IdGrupo 
    FROM usuarios 
    WHERE users = Usuario AND clave = Claves AND estado = '1';
    
    IF IdGrupo = 0 THEN
        SELECT 'No Existe' as usersId;
    ELSE
        SELECT usersId INTO rpta 
        FROM grupos 
        WHERE 0 < DATEDIFF(fechaFinal, now()) AND grupoId = IdGrupo;
        
        IF rpta IS NOT NULL THEN
            SELECT usersId, grupoId, nombres, users, nivel  
            FROM usuarios 
            WHERE usersId = IdUser 
            LIMIT 1;
        ELSE 
            SELECT 'No Existe' as usersId;
        END IF;
    END IF;
END
;;
DELIMITER ;

-- ----------------------------
--  Table structure for `visitas`
-- ----------------------------
DROP TABLE IF EXISTS `visitas`;
CREATE TABLE visitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(45),
    so VARCHAR(50),
    navegador VARCHAR(255),
    fecha DATE,
    hora TIME
);

-- ----------------------------
--  Table structure for `noticias`
-- ----------------------------
DROP TABLE IF EXISTS `noticias`;
CREATE TABLE noticias (
  idNoticia INT(3) NOT NULL AUTO_INCREMENT,
  titulo VARCHAR(250) NOT NULL,
  nota TEXT,
  imagen VARCHAR(255) NOT NULL,
  estado INT(1) NOT NULL DEFAULT 1,
  fecha DATETIME NOT NULL,
  PRIMARY KEY (idNoticia)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO noticias (titulo, nota, imagen, fecha) VALUES
('Cultura DevOps y Calidad', 'Integrando el aseguramiento de la calidad en todo el ciclo de vida de DevOps para entregas más rápidas y seguras.', 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?q=80&w=2070&auto=format&fit=crop', NOW()),
('Automatización con Cypress', 'Una alternativa moderna a Selenium para pruebas end-to-end, más rápida y con una arquitectura innovadora.', 'https://images.unsplash.com/photo-1516116216624-53e6973bea1c?q=80&w=2070&auto=format&fit=crop', NOW()),
('QA en la Nube', 'Estrategias para probar aplicaciones nativas de la nube de manera efectiva, aprovechando la escalabilidad y flexibilidad.', 'https://images.unsplash.com/photo-1534972195531-d756b9bfa9f2?q=80&w=2070&auto=format&fit=crop', NOW()),
('El Futuro es Low-Code', 'Cómo las plataformas low-code están cambiando el panorama del desarrollo y el rol del testing en este nuevo paradigma.', 'https://images.unsplash.com/photo-1600880292203-942bb68b2432?q=80&w=2070&auto=format&fit=crop', NOW()),
('Pruebas de Rendimiento', 'Asegura que tu aplicación pueda manejar miles de usuarios simultáneos con herramientas como JMeter y Gatling.', 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?q=80&w=2070&auto=format&fit=crop', NOW()),
('Avances en IA', 'Descubre cómo la inteligencia artificial está revolucionando la industria del software.', 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=2070&auto=format&fit=crop', NOW()),
('Desarrollo Web Moderno', 'Las últimas tendencias y frameworks para crear aplicaciones web de alto impacto.', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?q=80&w=2070&auto=format&fit=crop', NOW()),
('Seguridad Informática', 'Protege tus proyectos con las mejores prácticas de ciberseguridad del 2024.', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=2070&auto=format&fit=crop', NOW()),
('Gestión de Proyectos', 'Metodologías ágiles para entregar software de calidad a tiempo y dentro del presupuesto.', 'https://images.unsplash.com/photo-1542626991-a2f575a7e6a6?q=80&w=2070&auto=format&fit=crop', NOW());
