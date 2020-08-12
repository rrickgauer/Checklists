DROP TABLE IF EXISTS Items, Checklists, Users;

CREATE TABLE Users (
    id           INT UNSIGNED NOT NULL UNIQUE auto_increment,
    email        CHAR(50) NOT NULL UNIQUE,
    name_first   CHAR(40) NOT NULL,
    name_last    CHAR(60) NOT NULL,
    password     CHAR(255) NOT NULL,
    date_created DATETIME NOT NULL,
    PRIMARY KEY (id)
)
engine = innodb;

CREATE TABLE Checklists (
    id            INT UNSIGNED NOT NULL UNIQUE auto_increment,
    user_id       INT UNSIGNED NOT NULL,
    name          CHAR(100) NOT NULL,
    date_created  DATETIME NOT NULL,
    date_modified DATETIME,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES Users(id) ON UPDATE CASCADE ON DELETE CASCADE
)
engine=innodb;

CREATE TABLE Items (
    id            INT UNSIGNED NOT NULL UNIQUE auto_increment,
    checklist_id  INT UNSIGNED NOT NULL,
    content       CHAR(250),
    completed     ENUM ('y', 'n') NOT NULL DEFAULT 'n',
    date_created  DATETIME NOT NULL,
    date_modified DATETIME,
    rank          DECIMAL (7, 7) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (checklist_id) REFERENCES Checklists(id) ON UPDATE CASCADE ON DELETE CASCADE
)
engine = innodb; 

