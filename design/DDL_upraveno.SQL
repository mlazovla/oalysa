SET FOREIGN_KEY_CHECKS=0;




CREATE TABLE Action
(
	id INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(63) COMMENT 'Name of action was done in Log.',
	type VARCHAR(15) NOT NULL COMMENT 'visit edit insert delete login',
	PRIMARY KEY (id)

) 
;


CREATE TABLE Attachment
(
	id INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(127) NOT NULL,
	file VARCHAR(255) COMMENT 'Where is file locate in file system.',
	topic_id INT NOT NULL,
	user_id INT COMMENT 'Owner',
	PRIMARY KEY (id),
	UNIQUE UQ_Attachment_file(file),
	KEY (user_id),
	KEY (topic_id)

) 
;


CREATE TABLE Comentary
(
	id INT NOT NULL AUTO_INCREMENT,
	user_id INT NOT NULL,
	topic_id INT NOT NULL,
	answer_on INT COMMENT 'This comentary is answer on another comentary.',
	created_at DATETIME(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
	content TEXT NOT NULL,
	PRIMARY KEY (id),
	KEY (user_id),
	KEY (topic_id),
	KEY (answer_on)

) 
;


CREATE TABLE Grade
(
	id INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(63),
	grade_next INT,
	PRIMARY KEY (id),
	UNIQUE UQ_Grade_name(name),
	UNIQUE UQ_Grade_grade_next(grade_next),
	INDEX IXFK_Grade_Grade (grade_next ASC)

) 
;


CREATE TABLE UsersGroup
(
	id INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(127) NOT NULL,
	PRIMARY KEY (id)

) 
;


CREATE TABLE Log
(
	id BIGINT NOT NULL AUTO_INCREMENT,
	user_id INT,
	topic_id INT,
	action_id INT,
	note TEXT,
	PRIMARY KEY (id),
	KEY (action_id),
	KEY (user_id),
	KEY (topic_id)

) 
;


CREATE TABLE News
(
	id INT NOT NULL AUTO_INCREMENT,
	created_at DATETIME(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
	content TEXT,
	user_id INT,
	PRIMARY KEY (id),
	INDEX IXFK_News_User (user_id ASC)

) 
;


CREATE TABLE Role
(
	id INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(50),
	inherite INT,
	PRIMARY KEY (id),
	INDEX IXFK_Role_Role (inherite ASC)

) 
;


CREATE TABLE Subject
(
	id INT NOT NULL AUTO_INCREMENT,
	shortcut VARCHAR(15) NOT NULL COMMENT 'Subject shortcut.',
	name VARCHAR(127) NOT NULL COMMENT 'Subject full name.',
	description TEXT,
	PRIMARY KEY (id),
	UNIQUE UQ_subject_shortcut(shortcut)

)  COMMENT='Table of school subject'
;


CREATE TABLE Subject2Grade
(
	id INT NOT NULL AUTO_INCREMENT,
	Subject_id INT,
	Grade_id INT,
	PRIMARY KEY (id),
	INDEX IXFK_Subject2Grade_Subject (Subject_id ASC),
	INDEX IXFK_Subject2Grade_Grade (Grade_id ASC)

) 
;


CREATE TABLE Topic
(
	id INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(127) NOT NULL,
	created_at DATETIME(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
	subject2grade_id INT NOT NULL,
	user_id INT,
	isPublic TINYINT NOT NULL DEFAULT 0,
	anotation TEXT,
	content TEXT,
	PRIMARY KEY (id),
	KEY (subject2grade_id),
	KEY (user_id)

) 
;


CREATE TABLE User
(
	id INT NOT NULL AUTO_INCREMENT,
	login VARCHAR(63),
	password VARCHAR(255) COMMENT 'hash(password)',
	name VARCHAR(127),
	email VARCHAR(127),
	role_id INT,
	PRIMARY KEY (id),
	UNIQUE UQ_User_login(login),
	INDEX IXFK_User_Role (role_id ASC)

) 
;


CREATE TABLE User2UsersGroup
(
	id INT NOT NULL AUTO_INCREMENT,
	UsersGroup_id INT NOT NULL,
	User_id INT NOT NULL,
	PRIMARY KEY (id),
	INDEX IXFK_User2UsersGroup_User (User_id ASC),
	INDEX IXFK_User2UsersGroup_UsersGroup (UsersGroup_id ASC)

)  COMMENT='Decomposition of Users in UsersGroups.'
;



SET FOREIGN_KEY_CHECKS=1;


ALTER TABLE Attachment ADD CONSTRAINT FK_Attachment_User 
	FOREIGN KEY (user_id) REFERENCES User (id)
;

ALTER TABLE Attachment ADD CONSTRAINT FK_Attachment_Topic 
	FOREIGN KEY (topic_id) REFERENCES Topic (id)
	ON DELETE SET NULL
;

ALTER TABLE Comentary ADD CONSTRAINT FK_Comentary_User 
	FOREIGN KEY (user_id) REFERENCES User (id)
	ON DELETE NO ACTION
;

ALTER TABLE Comentary ADD CONSTRAINT FK_Comentary_Topic 
	FOREIGN KEY (topic_id) REFERENCES Topic (id)
	ON DELETE CASCADE
;

ALTER TABLE Comentary ADD CONSTRAINT FK_Comentary_Comentary 
	FOREIGN KEY (answer_on) REFERENCES Comentary (id)
	ON DELETE CASCADE
;

ALTER TABLE Grade ADD CONSTRAINT FK_Grade_Grade 
	FOREIGN KEY (grade_next) REFERENCES Grade (id)
;

ALTER TABLE Log ADD CONSTRAINT FK_Log_Action 
	FOREIGN KEY (action_id) REFERENCES Action (id)
;

ALTER TABLE Log ADD CONSTRAINT FK_Log_User 
	FOREIGN KEY (user_id) REFERENCES User (id)
	ON DELETE SET NULL
;

ALTER TABLE Log ADD CONSTRAINT FK_Log_Topic 
	FOREIGN KEY (topic_id) REFERENCES Topic (id)
	ON DELETE NO ACTION
;

ALTER TABLE News ADD CONSTRAINT FK_News_User 
	FOREIGN KEY (user_id) REFERENCES User (id)
;

ALTER TABLE Role ADD CONSTRAINT FK_Role_Role 
	FOREIGN KEY (inherite) REFERENCES Role (id)
;

ALTER TABLE Subject2Grade ADD CONSTRAINT FK_Subject2Grade_Subject 
	FOREIGN KEY (Subject_id) REFERENCES Subject (id)
	ON DELETE SET NULL
;

ALTER TABLE Subject2Grade ADD CONSTRAINT FK_Subject2Grade_Grade 
	FOREIGN KEY (Grade_id) REFERENCES Grade (id)
;

ALTER TABLE Topic ADD CONSTRAINT FK_Topic_Subject2Grade 
	FOREIGN KEY (subject2grade_id) REFERENCES Subject2Grade (id)
;

ALTER TABLE Topic ADD CONSTRAINT FK_Topic_User 
	FOREIGN KEY (user_id) REFERENCES User (id)
	ON DELETE SET NULL
;

ALTER TABLE User ADD CONSTRAINT FK_User_Role 
	FOREIGN KEY (role_id) REFERENCES Role (id)
;

ALTER TABLE User2UsersGroup ADD CONSTRAINT FK_User2UsersGroup_User 
	FOREIGN KEY (User_id) REFERENCES User (id)
;

ALTER TABLE User2UsersGroup ADD CONSTRAINT FK_User2UsersGroup_UsersGroup 
	FOREIGN KEY (UsersGroup_id) REFERENCES UsersGroup (id)
;
