CREATE TABLE `user` (
    user_id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    fname VARCHAR(250) NOT NULL UNIQUE,
    lname VARCHAR(250) NOT NULL,
    gender VARCHAR(20) NOT NULL,
    age INT NOT NULL,
    coll VARCHAR(250) NOT NULL,
    department VARCHAR(250) NOT NULL,
    username VARCHAR(250) NOT NULL UNIQUE,
    password VARCHAR(250) NOT NULL,
    phone VARCHAR(250) NOT NULL,
    role VARCHAR(250) NOT NULL,
    date VARCHAR(250) NOT NULL
);


CREATE TABLE event (
    event_id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    title VARCHAR(250) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    location VARCHAR(250) NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES  user(user_id)
);

CREATE TABLE assrole (
  rol_id int(11) NOT NULL,
  role_name varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO assrole (rol_id, role_name) VALUES
(7, 'Academic Staff'),
(8, 'College Staff'),
(9, 'Student'),
(10, 'Research and Community Service Staff'),
(11, 'Registrar Staff'),
(12, 'Instructor'),
(13, 'Admin'),
(16, 'Department Head');

CREATE TABLE login (
    log_id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    logged_by INT NOT NULL,
    date VARCHAR(250) NOT NULL,
    FOREIGN KEY (logged_by) REFERENCES user(user_id)
);
