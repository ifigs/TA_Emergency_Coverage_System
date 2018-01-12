CREATE TABLE Account (
    username VARCHAR(50) PRIMARY KEY,
    salt VARCHAR(20),
    hashedpw VARCHAR(100),
    firstname VARCHAR(20),
    lastname VARCHAR(30),
    accounttype VARCHAR(10),
    phone VARCHAR(10),
    email VARCHAR(100) UNIQUE,
    dept1 VARCHAR(10),
    dept2 VARCHAR(10)
);

CREATE TABLE Permission(
    email VARCHAR(100) PRIMARY KEY,
    permcode VARCHAR(8),
    accounttype VARCHAR(10)
);

CREATE TABLE Qualifications(
    username VARCHAR(50),
    dept1 VARCHAR(10),
    dept2 VARCHAR(10)
);
