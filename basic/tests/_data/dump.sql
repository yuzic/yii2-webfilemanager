BEGIN TRANSACTION;
CREATE TABLE "file_directory" (
    `id`    INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    `parent_id`   INTEGER  REFERENCES file_directory(id) ON DELETE CASCADE,
    `path`   VARCHAR(150) NOT NULL,
    `name`    INTEGER NOT NULL,
    `created_at`    INTEGER NOT NULL,
    `modified_at`    INTEGER NOT NULL
);
INSERT INTO `file_directory` VALUES (1,null,'files','files',0,0);
INSERT INTO `file_directory` VALUES (2, 1 ,'directory1','files/directory1', 0, 0);

CREATE TABLE "file_list" (
    `id`    INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    `file_directory_id`   INTEGER REFERENCES file_directory(id) ON DELETE CASCADE,
    `name`    INTEGER NOT NULL,
    `size`    INTEGER NOT NULL,
    `remote_ip`    INTEGER NOT NULL,
    `created_at`    INTEGER NOT NULL,
    `modified_at`    INTEGER NOT NULL,
    `status_id`    INTEGER NOT NULL
);

INSERT INTO `file_list` VALUES (1, 2, 'file1.zip', '323254534','4324234', 0,0 ,1);
INSERT INTO `file_list` VALUES (2, 2, 'file1.zip', '323254534','4324234', 0,0 ,1);

COMMIT;