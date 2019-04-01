CREATE TABLE bbs.post (
    id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    name VARCHAR(10),
    comment VARCHAR(100),
    color VARCHAR(6),
    delete_password VARCHAR(100),
    picture  VARCHAR(100),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
