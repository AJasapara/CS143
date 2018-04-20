
-- movie id is unique primary key
-- check that rating is valid
create table Movie(id int, title varchar(100), year int, rating varchar(10), company varchar(50), primary key(id), check(rating IS NULL OR rating='G' OR rating='PG' OR rating='PG-13' OR rating='R' OR rating='NC-17'));
-- id = Movie ID, title = Movie title, year = Release year, rating = MPAA rating, company = Production company


-- actor id is unique primary key
-- check that actor has valid sex 
create table Actor(id int, last varchar(20), first varchar(20), sex varchar(6), dob date, dod date, primary key(id), check(sex IS NULL OR sex='MALE' or sex='FEMALE'));
-- id = Actor ID, last = Last name, first = First name, sex = Sex of the actor, dob = Date of birth, dod = Date of death


-- director id is unique primary key
create table Director(id int, last varchar(20), first varchar(20), dob date, dod date,primary key(id));
-- id = Director ID, last = Last name, first = First name, dob = Date of birth, dod = Date of death


-- MovieGenre mid references movie id
create table MovieGenre(mid int, genre varchar(20),foreign key(mid) references Movie(id)) ENGINE=INNODB;
-- mid = Movie ID, genre = Movie genre


-- MovieDirector mid references movie id
-- MovieDirector did references director id
create table MovieDirector(mid int, did int,foreign key(mid) references Movie(id), foreign key(did) references Director(id)) ENGINE=INNODB;
-- mid = Movie ID, did = Director ID


-- MovieActor mid references movie id
-- MovieActor aid references actor id
create table MovieActor(mid int, aid int, role varchar(50),foreign key(mid) references Movie(id), foreign key(aid) references Actor(id)) ENGINE=INNODB;
-- mid = Movie ID, aid = Actor ID, role = Actor role in movie


-- Review mid references movie id
create table Review(name varchar(20), time timestamp, mid int, rating int, comment varchar(500),foreign key(mid) references Movie(id)) ENGINE=INNODB;
-- name = Reviewer name, time = Review time, mid = Movie ID, rating = Review rating, comment = Review comment

create table MaxPersonID(id int);
-- id = Max ID assigned to all persons

create table MaxMovieID(id int);
-- id = Max ID assigned to all movies