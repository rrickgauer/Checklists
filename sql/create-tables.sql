DROP TABLE IF EXISTS Items, Checklists, Users, Security_Questions;

CREATE TABLE Security_Questions (
    id       INT UNSIGNED NOT NULL UNIQUE AUTO_INCREMENT,
    question CHAR(250) NOT NULL UNIQUE,
    PRIMARY KEY (id)
) engine = innodb;

CREATE TABLE Users (
    id                       INT UNSIGNED NOT NULL UNIQUE AUTO_INCREMENT,
    email                    CHAR(50) NOT NULL UNIQUE,
    name_first               CHAR(40) NOT NULL,
    name_last                CHAR(60) NOT NULL,
    password                 CHAR(255) NOT NULL,
    date_created             DATETIME NOT NULL,
    security_question_id     INT UNSIGNED NOT NULL,
    security_question_answer CHAR(250) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (security_question_id) REFERENCES Security_Questions (id) ON UPDATE CASCADE ON DELETE CASCADE
) engine = innodb;

CREATE TABLE Checklists (
    id                   INT UNSIGNED NOT NULL UNIQUE AUTO_INCREMENT,
    user_id              INT UNSIGNED NOT NULL,
    name                 CHAR(100) NOT NULL,
    description          VARCHAR(250),
    date_created         DATETIME NOT NULL,
    date_modified        DATETIME,
    show_completed_items ENUM('y', 'n') NOT NULL DEFAULT 'y',
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES Users(id) ON UPDATE CASCADE ON DELETE CASCADE
) engine=innodb;

CREATE TABLE Items (
    id            INT UNSIGNED NOT NULL UNIQUE AUTO_INCREMENT,
    checklist_id  INT UNSIGNED NOT NULL,
    content       CHAR(250),
    completed     ENUM ('y', 'n') NOT NULL DEFAULT 'n',
    date_created  DATETIME NOT NULL,
    date_modified DATETIME,
    rank          DECIMAL (7, 7) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (checklist_id) REFERENCES Checklists(id) ON UPDATE CASCADE ON DELETE CASCADE
) engine = innodb;