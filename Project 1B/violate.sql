-- Movie with id = 2 exists
-- ERROR 1062 (23000) at line 2: Duplicate entry '2' for key 'PRIMARY'
INSERT INTO Movie VALUES (2, 'Test', 2018, 'PG-13', 'Lakeshore Entertainment');

-- Invalid rating
-- MySQL doesn't support CHECK so no error
INSERT INTO Movie VALUES (7, 'Test', 2018, 'KK', 'Lakeshore Entertainment');

-- Invalid year
-- MySQL doesn't support CHECK so no error
INSERT INTO Movie VALUES (7, 'Test', 12, 'KK', 'Lakeshore Entertainment');

-- Actor with id = 1 exists
-- ERROR 1062 (23000) at line 10: Duplicate entry '1' for key 'PRIMARY'
INSERT INTO Actor VALUES (1, 'Last', 'First', 'Male', '1982-01-06', NULL);

-- Invalid sex
-- MySQL doesn't support CHECK so no error
INSERT INTO Actor VALUES (4, 'Last', 'First', 'Hi', '1999-01-22', NULL);

-- Director with id = 16 exists
-- ERROR 1062 (23000) at line 18: Duplicate entry '16' for key 'PRIMARY'
INSERT INTO Director VALUES (16, 'Last', 'First', '1999-01-22', NULL);

-- References a movie with id = 10 that doesn't exist
-- ERROR 1452 (23000) at line 19: Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieGenre`, CONSTRAINT `MovieGenre_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))
INSERT INTO MovieGenre VALUES (10, 'Drama');

-- References a movie with id = 10 that doesn't exist
-- ERROR 1452 (23000) at line 23: Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieDirector`, CONSTRAINT `MovieDirector_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))
INSERT INTO MovieDirector VALUES (10, 16);

-- References a director with id = 5 that doesn't exist
-- Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieDirector`, CONSTRAINT `MovieDirector_ibfk_2` FOREIGN KEY (`did`) REFERENCES `Director` (`id`))
INSERT INTO MovieDirector VALUES (2, 5);

-- References a movie with id = 10 that doesn't exist
-- ERROR 1452 (23000) at line 31: Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieActor`, CONSTRAINT `MovieActor_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))
INSERT INTO MovieActor VALUES (10, 1, 'Doorman');

-- References an actor with id = 2 that doesn't exist
-- ERROR 1452 (23000) at line 35: Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieActor`, CONSTRAINT `MovieActor_ibfk_2` FOREIGN KEY (`aid`) REFERENCES `Actor` (`id`))
INSERT INTO MovieActor VALUES (2, 2, 'Doorman');

-- References a movie with id = 10 that doesn't exist
-- ERROR 1452 (23000) at line 39: Cannot add or update a child row: a foreign key constraint fails (`CS143`.`Review`, CONSTRAINT `Review_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))
INSERT INTO Review VALUES ('Jon Snow', '2018-11-11 11:11:11', 10, 5, 'Very good');
